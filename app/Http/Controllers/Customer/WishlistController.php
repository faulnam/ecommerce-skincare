<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Show wishlist
     */
    public function index()
    {
        if (auth()->check()) {
            $wishlistItems = auth()->user()->wishlist()->with('product')->get();
        } else {
            // Guest wishlist from session
            $guestWishlist = session()->get('guest_wishlist', []);
            $wishlistItems = collect();
            
            foreach ($guestWishlist as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $wishlistItems->push((object)[
                        'id' => $productId,
                        'product' => $product,
                    ]);
                }
            }
        }

        return view('customer.wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add to wishlist
     */
    public function add(Request $request, Product $product)
    {
        try {
            if (auth()->check()) {
                // Logged in user - save to database
                $exists = Wishlist::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->exists();

                if ($exists) {
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'Produk sudah ada di wishlist.'], 400);
                    }
                    return back()->with('info', 'Produk sudah ada di wishlist.');
                }

                Wishlist::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                ]);
            } else {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'redirect' => '/login', 'message' => 'Silakan login terlebih dahulu untuk menyimpan wishlist.'], 401);
                }
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk menyimpan wishlist.');
            }

            if ($request->expectsJson()) {
                $wishlistCount = auth()->check() 
                    ? auth()->user()->wishlistItems()->count() 
                    : count(session()->get('guest_wishlist', []));
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Produk berhasil ditambahkan ke wishlist.',
                    'wishlist_count' => $wishlistCount
                ]);
            }
            return back()->with('success', 'Produk berhasil ditambahkan ke wishlist.');
        } catch (\Exception $e) {
            \Log::error('Add to wishlist error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove from wishlist
     */
    public function remove(Request $request, $productIdentifier)
    {
        // Resolve product from numeric ID or slug (home AJAX sends slug, wishlist form sends ID)
        $product = null;
        if (is_numeric($productIdentifier)) {
            $product = Product::find($productIdentifier);
        }
        if (!$product) {
            $product = Product::where('slug', $productIdentifier)->first();
        }

        if (!$product) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
            }
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        $productId = $product->id;

        if (auth()->check()) {
            $wishlist = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->first();
            
            if ($wishlist) {
                $wishlist->delete();
            }
        } else {
            // Guest wishlist
            $guestWishlist = session()->get('guest_wishlist', []);
            $guestWishlist = array_diff($guestWishlist, [$productId]);
            session()->put('guest_wishlist', array_values($guestWishlist));
        }

        $wishlistCount = auth()->check()
            ? auth()->user()->wishlistItems()->count()
            : count(session()->get('guest_wishlist', []));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari wishlist.',
                'wishlist_count' => $wishlistCount
            ]);
        }

        return back()->with('success', 'Produk berhasil dihapus dari wishlist.');
    }

    /**
     * Clear wishlist
     */
    public function clear()
    {
        if (auth()->check()) {
            Wishlist::where('user_id', auth()->id())->delete();
        } else {
            session()->forget('guest_wishlist');
        }

        return back()->with('success', 'Wishlist berhasil dikosongkan.');
    }

    /**
     * Get wishlist count (for AJAX)
     */
    public function count()
    {
        if (auth()->check()) {
            $count = Wishlist::where('user_id', auth()->id())->count();
        } else {
            $guestWishlist = session()->get('guest_wishlist', []);
            $count = count($guestWishlist);
        }
        
        return response()->json(['count' => $count]);
    }

    /**
     * Check if product is in wishlist (for AJAX)
     */
    public function check(Product $product)
    {
        $inWishlist = false;

        if (auth()->check()) {
            $inWishlist = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();
        } else {
            $guestWishlist = session()->get('guest_wishlist', []);
            $inWishlist = in_array($product->id, $guestWishlist);
        }

        return response()->json(['in_wishlist' => $inWishlist]);
    }

    /**
     * Merge guest wishlist to user wishlist after login
     */
    public function mergeGuestWishlist()
    {
        if (!auth()->check()) {
            return;
        }

        $guestWishlist = session()->get('guest_wishlist', []);
        
        if (empty($guestWishlist)) {
            return;
        }

        foreach ($guestWishlist as $productId) {
            $exists = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->exists();

            if (!$exists) {
                Wishlist::create([
                    'user_id' => auth()->id(),
                    'product_id' => $productId,
                ]);
            }
        }

        session()->forget('guest_wishlist');
    }
}
