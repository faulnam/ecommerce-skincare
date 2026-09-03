<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Contact Baru</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h2 style="margin-bottom: 12px;">Pesan Contact Baru</h2>

    <p style="margin: 0 0 8px;"><strong>Nama:</strong> {{ $payload['name'] }}</p>
    <p style="margin: 0 0 8px;"><strong>Email:</strong> {{ $payload['email'] }}</p>
    <p style="margin: 0 0 8px;"><strong>Subjek:</strong> {{ $payload['subject'] }}</p>

    <div style="margin-top: 14px; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb;">
        <p style="margin: 0 0 6px;"><strong>Pesan:</strong></p>
        <p style="margin: 0; white-space: pre-line;">{{ $payload['message'] }}</p>
    </div>

    <p style="margin-top: 16px; font-size: 12px; color: #6b7280;">
        Email ini dikirim otomatis dari form Contact LUMINA.
    </p>
</body>
</html>
