<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;     // Import untuk HTTP Request
use Illuminate\Support\Facades\Log;      // Import untuk Logging
use Illuminate\Support\Facades\Storage;  // Import untuk Storage

class SupportController extends Controller
{
    /**
     * Halaman Utama Help Center (FAQ Statis)
     */
    public function index()
    {
        $faqs = [
            'Membership & Tiers' => [
                [
                    'id' => 'faq-tier-1',
                    'question' => 'Apa keuntungan menaikkan Tier Membership?',
                    'answer' => 'Semakin tinggi Tier Anda (Starter > Professional > Ultimate), Anda akan mendapatkan benefit eksklusif seperti: Diskon khusus layanan, Prioritas antrian (Fast Track), Kuota revisi lebih banyak, dan akses ke layanan premium tertentu.'
                ],
                [
                    'id' => 'faq-tier-2',
                    'question' => 'Bagaimana cara mencapai Tier Professional dan Ultimate?',
                    'answer' => 'Tier dihitung berdasarkan Total Pembelian Token (Lifetime Purchased). <br>• <b>Starter:</b> 0 - 49 Token<br>• <b>Professional:</b> 50 - 199 Token<br>• <b>Ultimate:</b> 200+ Token.<br>Status akan otomatis berubah saat Anda mencapai target tersebut.'
                ],
                [
                    'id' => 'faq-tier-3',
                    'question' => 'Apakah status Tier bisa turun?',
                    'answer' => 'Tidak. Pencapaian Tier dihitung berdasarkan akumulasi pembelian seumur hidup (Lifetime). Jadi, meskipun saldo Anda habis terpakai, status Tier Anda tidak akan turun.'
                ],
            ],
            'Billing & Token' => [
                [
                    'id' => 'faq-bill-1',
                    'question' => 'Bagaimana cara melakukan Top Up Toratix?',
                    'answer' => 'Masuk ke menu <b>My Wallet</b>, klik tombol "Top Up", pilih paket token yang diinginkan, dan selesaikan pembayaran. Invoice akan otomatis terbit dan saldo masuk setelah verifikasi.'
                ],
                [
                    'id' => 'faq-bill-2',
                    'question' => 'Apakah saldo Toratix memiliki masa aktif?',
                    'answer' => 'Tidak. Saldo Toratix bersifat <b>Lifetime</b> (seumur hidup) dan tidak akan hangus selama akun Anda aktif.'
                ],
                [
                    'id' => 'faq-bill-3',
                    'question' => 'Bagaimana kebijakan Refund (Pengembalian Dana)?',
                    'answer' => 'Jika Anda membatalkan project yang statusnya masih <b>"Queue"</b>, Token akan dikembalikan 100% ke wallet secara otomatis. Namun, project yang sudah berjalan (Active/In Progress) tidak dapat dibatalkan.'
                ],
            ],
            'Project & Services' => [
                [
                    'id' => 'faq-proj-1',
                    'question' => 'Berapa lama estimasi pengerjaan project?',
                    'answer' => 'Estimasi waktu bergantung pada jenis layanan. Rata-rata 3-5 hari kerja untuk desain grafis, dan 7-14 hari kerja untuk web development. Deadline pasti akan tertera saat Anda membuat request.'
                ],
                [
                    'id' => 'faq-proj-2',
                    'question' => 'Bagaimana alur revisi desain?',
                    'answer' => 'Anda dapat mengajukan revisi melalui fitur Chat di halaman detail project. Kami menyediakan kuota revisi mayor sebanyak 2x dan revisi minor (typo/warna) sepuasnya (unlimited).'
                ],
                [
                    'id' => 'faq-proj-3',
                    'question' => 'File format apa yang akan saya terima?',
                    'answer' => 'Anda akan menerima file master sesuai industri standar. Untuk desain (AI, EPS, PSD, PNG, PDF), untuk Web (Source Code/Deploy), dan dokumen pendukung lainnya.'
                ]
            ]
        ];

        return view('client.support.index', compact('faqs'));
    }

    /**
     * MENAMPILKAN HALAMAN INTERFACE CHAT AI
     */
    public function showAiChat()
    {
        return view('client.support.chat-ai');
    }

    /**
     * MEMPROSES CHAT DENGAN AI (GOOGLE GEMINI API)
     */
    public function chatAi(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userQuestion = $request->message;
        $apiKey = env('GEMINI_API_KEY');

        // Cek API Key
        if (!$apiKey) {
            return response()->json(['status' => 'error', 'message' => 'Server Error: API Key AI belum dikonfigurasi.'], 500);
        }

        // 2. Ambil Knowledge Base dari File
        $fileName = 'vektora_knowledge.txt';

        // Kita gunakan Storage disk 'local' yang mengarah ke folder /storage/app/
        if (Storage::disk('local')->exists($fileName)) {
            $contextData = Storage::disk('local')->get($fileName);
        } else {
            // Jika file TIDAK ditemukan: Log Error tapi jangan crash
            Log::error("Gemini Error: File {$fileName} tidak ditemukan di storage/app/. Pastikan file tersebut sudah dibuat secara manual.");

            // Fallback context agar AI tetap bisa merespon sopan
            $contextData = "Database pengetahuan Vektora sedang tidak tersedia. Harap arahkan user untuk menghubungi Admin Support via WhatsApp jika ada pertanyaan mendesak.";
        }

        // 3. Susun Prompt
        $prompt = "
            Anda adalah 'Vektora AI Assistant' (Vektorai).
            Jawab pertanyaan user HANYA berdasarkan informasi berikut:
            
            --- KNOWLEDGE BASE START ---
            $contextData
            --- KNOWLEDGE BASE END ---
            
            ATURAN:
            1. Jika jawaban tidak ada di Knowledge Base, katakan jujur: 'Maaf, saya belum memiliki informasi tersebut. Silakan hubungi Admin via WhatsApp.'
            2. Gunakan bahasa Indonesia yang ramah (Sapa dengan 'Kak').
            
            PERTANYAAN USER: $userQuestion
        ";

        // 4. MODEL AI (Gemini 2.5 Flash)
        $modelName = 'gemini-2.5-flash';

        try {
            // Kirim Request (withoutVerifying untuk Localhost SSL Fix)
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);

            if ($response->failed()) {
                Log::error("Gemini API Error: " . $response->body());
                return response()->json(['status' => 'error', 'message' => 'Gagal menghubungi Vektorai (API Error).'], 500);
            }

            $json = $response->json();

            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                $botReply = $json['candidates'][0]['content']['parts'][0]['text'];
                return response()->json([
                    'status' => 'success',
                    'reply' => nl2br(e($botReply))
                ]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Vektorai tidak dapat menjawab saat ini.'], 500);
            }
        } catch (\Exception $e) {
            Log::error("Chat AI Exception: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem internal.'], 500);
        }
    }
}
