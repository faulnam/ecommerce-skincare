<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'syifakul.anm@gmail.com';
        $userId = \App\Models\User::where('email', $email)->value('id');

        $logs = [
            [
                'user_id' => $userId,
                'email' => $email,
                'category' => 'Registrasi',
                'subject' => 'Kode OTP Registrasi LUMINA',
                'message' => 'Halo! Berikut adalah kode OTP Anda untuk menyelesaikan pendaftaran di LUMINA: 123456. Kode ini berlaku selama 5 menit.',
                'status' => 'sent',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $userId,
                'email' => $email,
                'category' => 'Belum Melakukan Pembayaran',
                'subject' => 'Pesanan Baru Anda Menunggu Pembayaran',
                'message' => 'Terima kasih telah berbelanja di LUMINA! Pesanan Anda dengan ID #ORD-99211 sedang menunggu pembayaran. Silakan selesaikan pembayaran Anda sebelum batas waktu habis.',
                'status' => 'sent',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'user_id' => $userId,
                'email' => $email,
                'category' => 'Pembayaran Selesai',
                'subject' => 'Pembayaran Diterima untuk Pesanan #ORD-99211',
                'message' => 'Hore! Pembayaran Anda untuk pesanan #ORD-99211 telah kami terima. Kami akan segera memproses dan mengirimkan pesanan Anda. Terima kasih!',
                'status' => 'sent',
                'created_at' => now()->subHours(12),
                'updated_at' => now()->subHours(12),
            ],
            [
                'user_id' => $userId,
                'email' => $email,
                'category' => 'Pesanan Selesai',
                'subject' => 'Pesanan Anda #ORD-99211 Telah Selesai',
                'message' => 'Pesanan Anda telah berhasil dikirim dan diselesaikan. Semoga Anda puas dengan produk LUMINA dari kami. Jangan lupa untuk memberikan ulasan!',
                'status' => 'sent',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'user_id' => $userId,
                'email' => $email,
                'category' => 'Contact',
                'subject' => 'Terima Kasih Telah Menghubungi Kami',
                'message' => 'Pesan Anda telah kami terima. Tim support LUMINA akan segera membalas pesan Anda dalam 1x24 jam kerja.',
                'status' => 'sent',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \App\Models\NotificationLog::insert($logs);
    }
}
