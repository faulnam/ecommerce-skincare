<?php

return [
    'receiver_email' => env('CONTACT_RECEIVER_EMAIL', env('MAIL_FROM_ADDRESS')),
    'receiver_name' => env('CONTACT_RECEIVER_NAME', config('app.name', 'LUMINA Skincare Support')),
];
