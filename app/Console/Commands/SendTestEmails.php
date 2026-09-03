<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendTestEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-send {email=syifakul.anm@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test emails to trigger Notification Logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $this->info("Mengirim 5 email testing ke {$email}...");

        $emails = [
            'Registrasi' => [
                'subject' => 'Kode OTP Registrasi LUMINA',
                'message' => "Ini adalah email tes sungguhan untuk kategori Registrasi.\nOTP Anda: 123456"
            ],
            'Belum Melakukan Pembayaran' => [
                'subject' => 'Pesanan Baru Anda Menunggu Pembayaran',
                'message' => "Ini adalah email tes sungguhan untuk pesanan yang belum dibayar.\nMohon segera bayar pesanan Anda."
            ],
            'Pembayaran Selesai' => [
                'subject' => 'Pembayaran Diterima untuk Pesanan #ORD-99211',
                'message' => "Ini adalah email tes sungguhan bahwa pembayaran Anda telah kami terima."
            ],
            'Pesanan Selesai' => [
                'subject' => 'Pesanan Anda #ORD-99211 Telah Selesai',
                'message' => "Ini adalah email tes sungguhan bahwa pesanan Anda sudah sampai dan selesai."
            ],
            'Contact' => [
                'subject' => 'Terima Kasih Telah Menghubungi Kami - Contact',
                'message' => "Ini adalah email tes sungguhan untuk balasan form contact."
            ],
        ];

        foreach ($emails as $category => $data) {
            $this->line("Mengirim email kategori: {$category}");
            try {
                \Illuminate\Support\Facades\Mail::raw($data['message'], function ($msg) use ($email, $data) {
                    $msg->to($email)
                        ->subject($data['subject']);
                });
                $this->info("OK! Email '{$data['subject']}' berhasil dikirim.");
            } catch (\Exception $e) {
                $this->error("Gagal mengirim email: " . $e->getMessage());
            }
        }

        $this->info("Selesai! Silakan cek Gmail Anda, dan cek halaman Admin Notification Logs.");
    }
}
