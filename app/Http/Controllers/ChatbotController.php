<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    private ?string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.9router.api_key', '');
        $this->apiUrl = (string) config('services.9router.base_url', 'http://localhost:20128/v1/chat/completions');
        $this->model  = (string) config('services.9router.model', 'gemini-1.5-flash');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $userMessage  = $request->input('message');
        $history      = $request->input('history', []);

        // Cek jika user meminta bantuan admin
        $msgLower = strtolower($userMessage);
        if (str_contains($msgLower, 'admin') || str_contains($msgLower, 'bantuan manusia') || str_contains($msgLower, 'customer service') || str_contains($msgLower, 'cs')) {
            $sessionId = (string) Str::uuid();
            $chat = LiveChat::create([
                'session_id' => $sessionId,
                'status' => 'waiting',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Simpan history agar admin bisa membaca konteks
            foreach ($history as $item) {
                LiveChatMessage::create([
                    'live_chat_id' => $chat->id,
                    'sender_type' => $item['role'] === 'user' ? 'user' : 'bot',
                    'message' => $item['text'],
                ]);
            }

            // Simpan pesan terakhir dari user
            LiveChatMessage::create([
                'live_chat_id' => $chat->id,
                'sender_type' => 'user',
                'message' => $userMessage,
            ]);

            return response()->json([
                'reply' => 'Mengalihkan obrolan ke admin... Mohon tunggu sebentar ya! 👩‍💼',
                'transfer_to_admin' => true,
                'session_id' => $sessionId,
            ]);
        }

        // Ambil produk relevan dari database
        $relevantProducts = $this->getRelevantProducts($userMessage);
        $systemPrompt     = $this->buildSystemPrompt($relevantProducts);

        // Bangun conversation history
        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt,
        ];
        $messages[] = [
            'role' => 'assistant',
            'content' => 'Baik! Saya siap membantu sebagai sales dan customer service Hijab. Saya akan dengan senang hati membantu pelanggan menemukan produk terbaik untuk kebutuhan mereka.',
        ];

        foreach ($history as $item) {
            $messages[] = [
                'role' => $item['role'] === 'user' ? 'user' : 'assistant',
                'content' => $item['text'],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        try {
            $response = Http::timeout(45)->withoutVerifying()->withToken($this->apiKey)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.4,
                'max_tokens' => 800,
                'stream' => false,
            ]);

            if ($response->failed()) {
                $errorDetails = $response->body();
                \Log::error('Gemini/9Router API Error: ' . $errorDetails);

                if ($response->status() === 429) {
                    return response()->json(['error' => 'Sedang ramai, coba lagi dalam 30 detik ya! ⏳'], 429);
                }

                // Fallback: tetap bantu user walaupun AI down
                return $this->fallbackResponse($userMessage, $relevantProducts);
            }

            \Log::info('9Router Raw Response: ' . $response->body());

            $rawBody = trim($response->body());
            // Bersihkan "data: [DONE]" jika 9Router menyelipkannya di akhir
            $rawBody = preg_replace('/data:\s*\[DONE\]\s*$/i', '', $rawBody);
            $data    = json_decode($rawBody, true);

            $rawReply = $data['choices'][0]['message']['content'] ?? '';

            // Parse JSON dari respons Gemini
            $jsonText = trim($rawReply);
            $jsonText = preg_replace('/^```(?:json)?\s*/i', '', $jsonText);
            $jsonText = preg_replace('/\s*```$/', '', $jsonText);

            $parsed = json_decode($jsonText, true);
            $replyText = '';
            $showProducts = false;
            $recommendedSlugs = [];

            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                $replyText = $parsed['reply'] ?? $rawReply;
                $showProducts = !empty($parsed['show_products']);
                $recommendedSlugs = $parsed['recommended_slugs'] ?? [];
            } else {
                $replyText = $rawReply;
                $showProducts = $relevantProducts->isNotEmpty();
            }

            // Bersihkan markdown dari reply
            $replyText = str_replace(['**', '*', '- '], ['', '', '• '], $replyText);

            $productsToSend = collect();
            if ($showProducts) {
                $slugsToShow = array_filter($recommendedSlugs);
                if (!empty($slugsToShow)) {
                    $productsToSend = Product::whereIn('slug', $slugsToShow)
                        ->where('is_active', true)
                        ->get();
                }
                if ($productsToSend->isEmpty() && $relevantProducts->isNotEmpty()) {
                    $productsToSend = $relevantProducts->take(8);
                }
                if ($productsToSend->isEmpty()) {
                    $productsToSend = Product::active()->limit(6)->get();
                }
            }

            $formattedProducts = $productsToSend->map(fn($p) => [
                'name'     => $p->name,
                'slug'     => $p->slug,
                'price'    => 'Rp ' . number_format($p->price, 0, ',', '.'),
                'discount' => ($p->discount_percent ?? 0) > 0 ? ($p->discount_percent . '%') : null,
                'category' => $p->category,
                'image'    => $p->image_url,
                'brand'    => $p->brand,
                'url'      => $p->detail_url,
            ])->values();

            return response()->json([
                'reply'    => $replyText ?: 'Maaf, saya tidak bisa menjawab saat ini.',
                'products' => $formattedProducts,
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Gemini/9Router Connection Error: ' . $e->getMessage());
            return $this->fallbackResponse($userMessage, $relevantProducts);
        } catch (\Throwable $e) {
            \Log::error('Chatbot Exception: ' . $e->getMessage());
            return $this->fallbackResponse($userMessage, $relevantProducts);
        }
    }

    /**
     * Fallback response ketika Gemini API tidak bisa diakses
     */
    private function fallbackResponse(string $userMessage, $relevantProducts)
    {
        $msg = strtolower($userMessage);
        $reply = '';
        $showProducts = false;

        if (str_contains($msg, 'hijab') || str_contains($msg, 'jilbab') || str_contains($msg, 'bergo') || str_contains($msg, 'pashmina')) {
            $reply = 'Halo! Kami punya berbagai koleksi hijab cantik dari merek terbaik. ';
            if ($relevantProducts->isNotEmpty()) {
                $reply .= 'Berikut beberapa pilihan yang bisa kamu cek: ✨';
            } else {
                $reply .= 'Silakan lihat katalog hijab kami di halaman produk ya!';
            }
            $showProducts = true;
        } elseif (str_contains($msg, 'dress') || str_contains($msg, 'gamis') || str_contains($msg, 'tunik')) {
            $reply = 'Kami menyediakan gamis dan dress cantik untuk berbagai acara. ';
            $reply .= 'Berikut rekomendasi pakaian kami: ✨';
            $showProducts = true;
        } elseif (str_contains($msg, 'rok') || str_contains($msg, 'skirt') || str_contains($msg, 'celana') || str_contains($msg, 'bawahan')) {
            $reply = 'Koleksi bawahan rok dan celana kami sangat nyaman digunakan. Cek pilihan berikut: ✨';
            $showProducts = true;
        } elseif (str_contains($msg, 'tas') || str_contains($msg, 'bag')) {
            $reply = 'Kami punya tas cantik untuk melengkapi outfit kamu. Silakan cek pilihan ini: ✨';
            $showProducts = true;
        } elseif (str_contains($msg, 'sepatu') || str_contains($msg, 'heels') || str_contains($msg, 'sandal')) {
            $reply = 'Sepatu dan heels kami tersedia untuk menunjang penampilan maksimal. Cek di bawah ya: ✨';
            $showProducts = true;
        } elseif (str_contains($msg, 'aksesori') || str_contains($msg, 'accessories') || str_contains($msg, 'bros') || str_contains($msg, 'ciput')) {
            $reply = 'Berbagai aksesori hijab tersedia: ciput, bros, manset, dan lainnya. Lihat pilihan ini: ✨';
            $showProducts = true;
        } elseif (str_contains($msg, 'order') || str_contains($msg, 'cara beli') || str_contains($msg, 'cara order') || str_contains($msg, 'pembayaran') || str_contains($msg, 'bayar')) {
            $reply = "Cara ordernya gampang! 🛒\n\n" .
                     "1. Pilih produk di website\n" .
                     "2. Tambahkan ke keranjang\n" .
                     "3. Checkout & pilih metode pengiriman\n" .
                     "4. Lakukan pembayaran (transfer bank, e-wallet, atau COD)\n\n" .
                     "Kami kirim via JNE, J&T, dan SiCepat ke seluruh Indonesia. Ada yang bisa dibantu lagi?";
        } elseif (str_contains($msg, 'promo') || str_contains($msg, 'diskon') || str_contains($msg, 'voucher')) {
            $reply = 'Promo dan voucher terbaru bisa dicek langsung di halaman produk atau banner website kami. Jangan lupa klaim voucher di akunmu ya! 🎉';
        } elseif (str_contains($msg, 'pengiriman') || str_contains($msg, 'kirim') || str_contains($msg, 'ongkir')) {
            $reply = 'Kami kirim ke seluruh Indonesia via JNE, J&T, dan SiCepat. Ongkir dihitung otomatis saat checkout berdasarkan berat & tujuan. 📦';
        } elseif (str_contains($msg, 'cod')) {
            $reply = 'COD (Bayar di Tempat) tersedia untuk area tertentu. Kamu bisa pilih opsi COD saat checkout kalau tujuanmu mendukung. ✅';
        } elseif (str_contains($msg, 'halo') || str_contains($msg, 'hi') || str_contains($msg, 'hello') || str_contains($msg, 'hai')) {
            $reply = 'Halo! 👋 Saya Hijab AI. Mau cari hijab, gamis, tas, sepatu atau aksesori muslimah? Saya siap bantu!';
        } else {
            $reply = 'Maaf, saya sedang dalam mode offline. Tapi saya masih bisa bantu cari produk fashion! Mau lihat produk apa nih? ✨';
            $showProducts = $relevantProducts->isNotEmpty();
        }

        $productsToSend = collect();
        if ($showProducts) {
            $productsToSend = $relevantProducts->isNotEmpty()
                ? $relevantProducts->take(8)
                : Product::active()->limit(6)->get();
        }

        $formattedProducts = $productsToSend->map(fn($p) => [
            'name'     => $p->name,
            'slug'     => $p->slug,
            'price'    => 'Rp ' . number_format($p->price, 0, ',', '.'),
            'discount' => ($p->discount_percent ?? 0) > 0 ? ($p->discount_percent . '%') : null,
            'category' => $p->category,
            'image'    => $p->image_url,
            'brand'    => $p->brand,
            'url'      => $p->detail_url,
        ])->values();

        return response()->json([
            'reply'    => $reply,
            'products' => $formattedProducts,
        ]);
    }

    /**
     * Ambil produk relevan dari database berdasarkan keyword pesan user
     */
    private function getRelevantProducts(string $userMessage)
    {
        $message = strtolower($userMessage);
        $query = Product::active();

        // Keyword mapping
        $keywords = [
            'hijab' => ['hijab', 'jilbab', 'pashmina', 'bergo', 'kerudung'],
            'dress' => ['dress', 'gamis', 'tunik', 'abaya'],
            'bawahan' => ['rok', 'skirt', 'celana', 'kulot', 'bawahan'],
            'tas' => ['tas', 'bag', 'backpack', 'handbag', 'sling'],
            'sepatu' => ['sepatu', 'heels', 'sandal', 'shoes'],
            'aksesori' => ['aksesori', 'aksesoris', 'accessories', 'ciput', 'bros', 'manset', 'jarum'],
        ];

        $matchedCategories = [];
        foreach ($keywords as $category => $terms) {
            foreach ($terms as $term) {
                if (str_contains($message, $term)) {
                    $matchedCategories[] = $category;
                    break;
                }
            }
        }
        $matchedCategories = array_unique($matchedCategories);

        // Filter berdasarkan kategori
        if (in_array('hijab', $matchedCategories)) {
            $query->whereIn('category', ['original', 'arrivals']);
        } elseif (in_array('sepatu', $matchedCategories)) {
            $query->where('category', 'shoes');
        } elseif (in_array('aksesori', $matchedCategories) || in_array('tas', $matchedCategories) || in_array('dress', $matchedCategories) || in_array('bawahan', $matchedCategories)) {
            $query->where('category', '!=', 'shoes'); // Just a generic filter for other items if not specifically categorized
        }

        // NOTE: kolom 'level' sudah di-drop (migration 2026_07_05_000001).
        // Filter berdasarkan level dinonaktifkan agar query tidak error (Unknown column 'level').

        // Parsing budget / harga dari pesan user
        $budget = $this->parseBudget($message);
        if ($budget > 0) {
            $query->where('price', '<=', $budget);
        }

        // Jika tidak ada keyword spesifik, ambil produk featured/popular
        if (empty($matchedCategories) && $budget <= 0) {
            $query->where(function ($q) {
                $q->where('is_featured', true)->orWhere('stock', '>', 0);
            });
        }

        $products = $query->limit(15)
            ->get(['name','slug','price','discount_percent','stock','category','brand','description','shape','balance','play_style','image']);

        // Jika filter terlalu ketat dan tidak ada hasil, ambil semua produk aktif sebagai fallback
        if ($products->isEmpty()) {
            $products = Product::active()
                ->limit(10)
                ->get(['name','slug','price','discount_percent','stock','category','brand','description','shape','balance','play_style','image']);
        }

        return $products;
    }

    /**
     * Parse budget / harga maksimal dari pesan user
     */
    private function parseBudget(string $message): int
    {
        $msg = strtolower($message);

        // Pola dengan unit + konteks keyword: "budget 2 juta", "max 1.5jt", "harga 500 ribu"
        $patternCtxUnit = '/(?:dibawah|budget|max|maksimal|kurang dari|under|harga|kira|sekitar|range)\s*[:\s]*(?:rp\.?\s*)?(\d+(?:[.,]\d+)?)\s*(juta|jt|rb|ribu|million)/iu';
        if (preg_match($patternCtxUnit, $msg, $matches)) {
            $amount = (float) str_replace(',', '.', $matches[1]);
            $unit = strtolower($matches[2]);
            if (in_array($unit, ['juta', 'jt', 'million'])) {
                return (int) ($amount * 1_000_000);
            } elseif (in_array($unit, ['ribu', 'rb'])) {
                return (int) ($amount * 1_000);
            }
        }

        // Pola tanpa konteks keyword tapi dengan unit: "2 juta", "Rp 2 juta"
        $patternUnitOnly = '/(?:rp\.?\s*)?(\d+(?:[.,]\d+)?)\s*(juta|jt|rb|ribu|million)/iu';
        if (preg_match($patternUnitOnly, $msg, $matches)) {
            $amount = (float) str_replace(',', '.', $matches[1]);
            $unit = strtolower($matches[2]);
            if (in_array($unit, ['juta', 'jt', 'million'])) {
                return (int) ($amount * 1_000_000);
            } elseif (in_array($unit, ['ribu', 'rb'])) {
                return (int) ($amount * 1_000);
            }
        }

        // Untuk angka besar (7+ digit), WAJIB ada konteks keyword agar tidak tangkap nomor HP
        $ctx = 'dibawah|budget|max|maksimal|kurang dari|under|harga|kira|sekitar|range|antara|dari|rp';

        if (preg_match('/(?:' . $ctx . ')\s*[:\s]*(?:rp\.?\s*)?(\d{7,})/iu', $msg, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/(?:' . $ctx . ')\s*[:\s]*(?:rp\.?\s*)?(\d{1,3}(?:\.\d{3})+)/iu', $msg, $matches)) {
            return (int) str_replace('.', '', $matches[1]);
        }

        return 0;
    }

    /**
     * Bangun system prompt lengkap dengan persona sales + data produk
     */
    private function buildSystemPrompt($products): string
    {
        $lines = [];
        if ($products->isNotEmpty()) {
            foreach ($products as $p) {
                $line = "[{$p->slug}]\nNama: {$p->name}\nHarga: Rp " . number_format($p->price, 0, ',', '.');
                if ($p->discount_percent > 0) {
                    $line .= "\nDiskon: {$p->discount_percent}%";
                }
                $line .= "\nMerek: {$p->brand} | Stok: " . ($p->stock > 0 ? 'tersedia' : 'habis');
                $line .= "\nKategori: {$p->category}";
                if ($p->shape) $line .= "\nBentuk: {$p->shape}";
                if ($p->balance) $line .= "\nBalance: {$p->balance}";
                if ($p->play_style) $line .= "\nGaya main: {$p->play_style}";
                $line .= "\n---";
                $lines[] = $line;
            }
        }
        $productContext = $lines ? implode("\n", $lines) : "Tidak ada data produk saat ini.";

        return "Kamu adalah Hijab AI, sales representative dan customer service fashion muslimah.\n\nIDENTITAS:\n- Nama: Hijab AI\n- Peran: Sales & Customer Service Fashion Muslimah\n- Bahasa: Indonesia natural, ramah, sopan, elegan\n- Gunakan emoji secukupnya ✨🌸\n- JANGAN gunakan format markdown (**, *, -)\n\nTENTANG TOKO:\n- Spesialis fashion muslimah: hijab, gamis, tunik, bawahan, tas, sepatu, dan aksesoris muslimah\n- Pengiriman seluruh Indonesia\n- Pembayaran: transfer bank, e-wallet, COD (tergantung area)\n- Pengiriman: JNE, J&T, SiCepat\n\nKAPAN MEREKOMENDASIKAN PRODUK:\n- HANYA rekomendasikan produk jika user bertanya soal produk, harga, bahan, warna, atau rekomendasi fashion\n- Jika user tanya cara order, promo, pengiriman, atau hal non-produk → jawab saja tanpa rekomendasi produk\n- Jika user tanya produk → WAJIB sebut minimal 3 produk spesifik dari data yang tersedia\n\nFORMAT RESPONS:\nSelalu kembalikan JSON dengan struktur ini:\n{\n  \"reply\": \"teks jawaban untuk user\",\n  \"show_products\": true/false,\n  \"recommended_slugs\": [\"slug-1\", \"slug-2\", \"slug-3\"]\n}\n\n- \"show_products\": true HANYA jika user tanya soal produk, harga, atau rekomendasi fashion\n- \"recommended_slugs\": array slug produk yang direkomendasikan (kosong jika show_products false)\n- \"reply\": jawaban natural dalam bahasa Indonesia, TANPA tag REKOMENDASI\n\nATURAN PENTING:\n- Jangan mengarang produk atau harga yang tidak ada di data\n- Jika stok habis, tetap boleh disebut tapi beri tahu stok kosong\n- Jika budget disebut, prioritaskan produk yang harganya sesuai atau di bawah budget\n\nDATA PRODUK TERSEDIA (gunakan HANYA data ini, jangan mengarang):\n\n" . $productContext . "\n\nPASTIKAN responsmu selalu dalam format JSON yang valid seperti contoh di atas. Jangan tambahkan teks lain di luar JSON.";
    }

    /**
     * Parse rekomendasi produk dari teks reply
     */
    private function parseProductRecommendations(string $reply): array
    {
        $slugs = [];

        // Format utama: REKOMENDASI: Nama Produk | slug-produk
        if (preg_match_all('/REKOMENDASI[\s:]*(.+?)\|\s*([a-z0-9\-]+)/iu', $reply, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $slug = trim($match[2]);
                $slug = preg_replace('/[^a-z0-9\-]/i', '', $slug);
                if ($slug) $slugs[] = $slug;
            }
        }

        // Fallback 1: cari baris yang mengandung REKOMENDASI lalu ambil teks setelah |
        if (empty($slugs)) {
            if (preg_match_all('/REKOMENDASI.*?\|\s*([a-z0-9\-]+)/iu', $reply, $matches)) {
                foreach ($matches[1] as $slug) {
                    $slug = preg_replace('/[^a-z0-9\-]/i', '', trim($slug));
                    if ($slug) $slugs[] = $slug;
                }
            }
        }

        // Fallback 2: cari slug pattern di akhir baris (huruf-angka-dash)
        if (empty($slugs)) {
            if (preg_match_all('/\|\s*([a-z][a-z0-9\-]*)/iu', $reply, $matches)) {
                foreach ($matches[1] as $slug) {
                    $slug = trim($slug);
                    if ($slug) $slugs[] = $slug;
                }
            }
        }

        return array_unique(array_filter($slugs));
    }
}
