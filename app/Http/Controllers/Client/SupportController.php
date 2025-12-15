<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        // Data FAQ Lengkap
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
}
