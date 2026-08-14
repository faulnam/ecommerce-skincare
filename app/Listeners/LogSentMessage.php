<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use App\Models\NotificationLog;
use App\Models\User;

class LogSentMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Mail\Events\MessageSent  $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        try {
            $message = $event->message;
            $email = method_exists($message, 'getOriginalMessage') ? $message->getOriginalMessage() : $message;
            
            $subject = method_exists($email, 'getSubject') ? $email->getSubject() : '';
            
            if (method_exists($email, 'getTextBody') && method_exists($email, 'getHtmlBody')) {
                $body = $email->getTextBody() ?: strip_tags($email->getHtmlBody());
            } elseif (method_exists($email, 'getBody')) {
                $body = $email->getBody() ? $email->getBody()->bodyToString() : '';
                $body = strip_tags(quoted_printable_decode($body));
            } else {
                $body = '';
            }
            
            // Get recipients
            $to = method_exists($email, 'getTo') ? $email->getTo() : [];
            $emails = [];
            if ($to) {
                foreach ($to as $address) {
                    $emails[] = method_exists($address, 'getAddress') ? $address->getAddress() : (string) $address;
                }
            }

            // Categorize based on subject
            $category = 'Other';
            $subjectLower = strtolower($subject);
            if (str_contains($subjectLower, 'registrasi') || str_contains($subjectLower, 'otp')) {
                $category = 'Registrasi';
            } elseif (str_contains($subjectLower, 'contact') || str_contains($subjectLower, 'pesan')) {
                $category = 'Contact';
            } elseif (str_contains($subjectLower, 'pesanan') || str_contains($subjectLower, 'order') || str_contains($subjectLower, 'invoice')) {
                if (str_contains($subjectLower, 'baru') || str_contains($subjectLower, 'menunggu pembayaran')) {
                    $category = 'Belum Melakukan Pembayaran';
                } elseif (str_contains($subjectLower, 'pembayaran') || str_contains($subjectLower, 'diterima')) {
                    $category = 'Pembayaran Selesai';
                } elseif (str_contains($subjectLower, 'selesai') || str_contains($subjectLower, 'dikirim')) {
                    $category = 'Pesanan Selesai';
                } else {
                    $category = 'Other';
                }
            }

            foreach ($emails as $email) {
                $user = User::where('email', $email)->first();
                
                NotificationLog::create([
                    'user_id' => $user ? $user->id : null,
                    'email' => $email,
                    'category' => $category,
                    'subject' => $subject,
                    'message' => strip_tags($body),
                    'status' => 'sent',
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal melog notifikasi: ' . $e->getMessage());
        }
    }
}
