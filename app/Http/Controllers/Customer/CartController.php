<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show cart
     */
    public function index()
    {
        if (auth()->check()) {
            $cartItems = auth()->user()->cart()->with(['product', 'variant'])->get();
        } else {
            // Guest cart from session
            $guestCart = session()->get('guest_cart', []);
            $cartItems = collect();
            
            foreach ($guestCart as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $variant = isset($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
                    $cartItems->push((object)[
                        'id' => $item['product_id'] . '_' . ($item['variant_id'] ?? 'null'),
                        'product' => $product,
                        'variant' => $variant,
                        'quantity' => $item['quantity'],
                        'subtotal' => $this->calculateSubtotal($product, $variant, $item['quantity'])
                    ]);
                }
            }
        }
        
        // Calculate total using discounted prices
        $total = $cartItems->sum(function ($item) {
            return $item->subtotal;
        });

        return view('customer.cart.index', compact('cartItems', 'total'));
    }

    private function calculateSubtotal($product, $variant, $quantity)
    {
        $isEligibleForFree = false;
        if (!auth()->check()) {
            $isEligibleForFree = true;
        } else {
            $isEligibleForFree = auth()->user()->role === 'customer' 
                && !auth()->user()->welcome_bonus_claimed 
                && !auth()->user()->orders()->whereNotIn('status', ['pending', 'cancelled', 'failed'])->exists();
        }

        if ($product->is_free_event && $isEligibleForFree) {
            return 0;
        }

        if ($variant) {
            $price = $variant->final_price;
        } else {
            $price = $product->hasActiveDiscount() ? $product->discounted_price : $product->price;
        }
        return $price * $quantity;
    }

    /**
     * Add to cart
     */
    public function add(Request $request, Product $product)
    {
        try {
            $quantity = $request->input('quantity', 1);
            $variantId = $request->input('variant_id');

            // Free Product Event Limit Check
            if ($product->is_free_event) {
                if (!auth()->check()) {
                    $msg = \App\Models\Setting::where('key', 'free_event_error_message')->value('value') ?? 'Produk gratis hanya bisa di checkout oleh user yang sudah login. Silakan login terlebih dahulu.';
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $msg], 400);
                    }
                    return back()->with('error', $msg);
                }

                $isEligibleForFree = auth()->user()->role === 'customer' 
                    && !auth()->user()->welcome_bonus_claimed 
                    && !auth()->user()->orders()->whereNotIn('status', ['pending', 'cancelled', 'failed'])->exists();

                if ($isEligibleForFree) {
                    $currentFreeQty = 0;
                    
                    // Check cart for free products
                    $currentFreeQty += Cart::where('user_id', auth()->id())
                        ->whereHas('product', function($q) {
                            $q->where('is_free_event', true);
                        })->sum('quantity');

                    // Check pending orders for free products
                    $currentFreeQty += \App\Models\OrderItem::whereHas('order', function($q) {
                            $q->where('user_id', auth()->id())->where('status', 'pending');
                        })
                        ->whereHas('product', function($q) {
                            $q->where('is_free_event', true);
                        })->sum('quantity');

                    if ($currentFreeQty + $quantity > 1) {
                        $msg = 'Anda hanya dapat mengambil 1 stok produk gratis. Anda sudah memiliki produk gratis di keranjang atau di pesanan yang menunggu pembayaran.';
                        if ($request->expectsJson() || $request->ajax()) {
                            return response()->json(['success' => false, 'message' => $msg], 400);
                        }
                        return back()->with('error', $msg);
                    }
                }
            }

            // Jika produk punya varian DAN ada varian aktif, WAJIB pilih varian
            $hasActiveVariants = $product->has_variants && $product->activeVariants()->exists();
            
            if ($hasActiveVariants && !$variantId) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Silakan pilih varian produk terlebih dahulu.'], 400);
                }
                return back()->with('error', 'Silakan pilih varian produk terlebih dahulu.');
            }

            // Validate variant belongs to product
            $variant = null;
            if ($variantId) {
                $variant = ProductVariant::where('id', $variantId)
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->first();
                
                if (!$variant) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Varian tidak valid atau tidak aktif.'], 400);
                    }
                    return back()->with('error', 'Varian tidak valid atau tidak aktif.');
                }
                
                if ($variant->stock < $quantity) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Stok varian tidak mencukupi.'], 400);
                    }
                    return back()->with('error', 'Stok varian tidak mencukupi.');
                }
            } else {
                // Produk tanpa varian atau varian tidak dipilih
                if ($product->stock < $quantity) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi.'], 400);
                    }
                    return back()->with('error', 'Stok tidak mencukupi.');
                }
            }

            if (auth()->check()) {
                // Logged in user - save to database
                $cartItem = Cart::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variantId)
                    ->first();

                if ($cartItem) {
                    $newQuantity = $cartItem->quantity + $quantity;
                    $maxStock = $variant ? $variant->stock : $product->stock;
                    
                    if ($maxStock < $newQuantity) {
                        if ($request->expectsJson() || $request->ajax()) {
                            return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi.'], 400);
                        }
                        return back()->with('error', 'Stok tidak mencukupi.');
                    }
                    
                    $cartItem->update(['quantity' => $newQuantity]);
                } else {
                    Cart::create([
                        'user_id' => auth()->id(),
                        'product_id' => $product->id,
                        'product_variant_id' => $variantId,
                        'quantity' => $quantity,
                    ]);
                }
            } else {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'redirect' => '/login', 'message' => 'Silakan login terlebih dahulu untuk memasukkan produk ke keranjang.'], 401);
                }
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk memasukkan produk ke keranjang.');
            }

            if ($request->expectsJson() || $request->ajax()) {
                $cartCount = auth()->check() 
                    ? auth()->user()->cartItems()->sum('quantity') 
                    : array_sum(array_column(session()->get('guest_cart', []), 'quantity'));
                
                return response()->json([
                    'success' => true, 
                    'message' => 'Produk berhasil ditambahkan ke keranjang.',
                    'cart_count' => $cartCount
                ]);
            }
            return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
        } catch (\Exception $e) {
            \Log::error('Add to cart error: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update cart quantity
     */
    public function update(Request $request, $cartId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if (auth()->check()) {
            $cart = Cart::findOrFail($cartId);
            
            if ($cart->user_id !== auth()->id()) {
                abort(403);
            }

            // Free Event Limit Check
            if ($cart->product->is_free_event && $request->quantity > 1) {
                return back()->with('error', 'Anda hanya dapat mengambil maksimal 1 stok produk gratis.');
            }

            // Check stock
            if ($cart->product->stock < $request->quantity) {
                return back()->with('error', 'Stok tidak mencukupi.');
            }

            $cart->update(['quantity' => $request->quantity]);
        } else {
            // Guest cart
            $guestCart = session()->get('guest_cart', []);
            
            if (isset($guestCart[$cartId])) {
                $product = Product::find($guestCart[$cartId]['product_id']);
                
                // Free Event Limit Check
                if ($product && $product->is_free_event && $request->quantity > 1) {
                    return back()->with('error', 'Anda hanya dapat mengambil maksimal 1 stok produk gratis.');
                }

                if ($product->stock < $request->quantity) {
                    return back()->with('error', 'Stok tidak mencukupi.');
                }
                
                $guestCart[$cartId]['quantity'] = $request->quantity;
                session()->put('guest_cart', $guestCart);
            }
        }

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    /**
     * Remove from cart
     */
    public function remove($cartId)
    {
        if (auth()->check()) {
            $cart = Cart::findOrFail($cartId);
            
            if ($cart->user_id !== auth()->id()) {
                abort(403);
            }

            $cart->delete();
        } else {
            // Guest cart
            $guestCart = session()->get('guest_cart', []);
            unset($guestCart[$cartId]);
            session()->put('guest_cart', $guestCart);
        }

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        if (auth()->check()) {
            Cart::where('user_id', auth()->id())->delete();
        } else {
            session()->forget('guest_cart');
        }

        return back()->with('success', 'Keranjang berhasir dikosongkan.');
    }

    /**
     * Get cart count (for AJAX)
     */
    public function count()
    {
        if (auth()->check()) {
            $count = Cart::where('user_id', auth()->id())->sum('quantity');
        } else {
            $guestCart = session()->get('guest_cart', []);
            $count = array_sum(array_column($guestCart, 'quantity'));
        }
        
        return response()->json(['count' => $count]);
    }

    /**
     * Merge guest cart to user cart after login
     */
    public function mergeGuestCart()
    {
        if (!auth()->check()) {
            return;
        }

        $guestCart = session()->get('guest_cart', []);
        
        if (empty($guestCart)) {
            return;
        }

        foreach ($guestCart as $item) {
            $cartItem = Cart::where('user_id', auth()->id())
                ->where('product_id', $item['product_id'])
                ->where('product_variant_id', $item['variant_id'])
                ->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $item['quantity']);
            } else {
                Cart::create([
                    'user_id' => auth()->id(),
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        }

        session()->forget('guest_cart');
    }
}
