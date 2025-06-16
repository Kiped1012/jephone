<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - JEPhone</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2d4a8a;
        }
        .logo {
            color: #2d4a8a;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .title {
            color: #2d4a8a;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .reset-button {
            display: inline-block;
            background-color: #2d4a8a;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .reset-button:hover {
            background-color: #1e3366;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .link-fallback {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">JEPhone</div>
            <p>Sistem Penjualan Sparepart</p>
        </div>

        <h2 class="title">Reset Password</h2>

        <div class="content">
            <p>Halo,</p>
            
            <p>Kami menerima permintaan untuk mereset password akun Anda yang terdaftar dengan email: <strong>{{ $email }}</strong></p>
            
            <p>Untuk mereset password Anda, silakan klik tombol di bawah ini:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
            </div>

            <div class="warning">
                <strong>⚠️ Penting:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Link ini hanya berlaku selama <strong>1 jam</strong> setelah email ini dikirim</li>
                    <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                    <li>Jangan bagikan link ini kepada siapa pun</li>
                </ul>
            </div>

            <p>Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan menempelkan link berikut ke browser Anda:</p>
            
            <div class="link-fallback">
                {{ $resetUrl }}
            </div>

            <p>Jika Anda mengalami kesulitan atau tidak meminta reset password ini, silakan hubungi administrator sistem.</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem JEPhone.</p>
            <p>Mohon jangan membalas email ini.</p>
            <p>&copy; {{ date('Y') }} JEPhone. All rights reserved.</p>
        </div>
    </div>
</body>
</html>