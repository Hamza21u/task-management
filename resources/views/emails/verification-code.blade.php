<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h2 { color: #333; }
        .code { font-size: 36px; font-weight: bold; letter-spacing: 10px; color: #4f46e5; text-align: center; margin: 24px 0; }
        p { color: #555; line-height: 1.6; }
        .footer { font-size: 12px; color: #999; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hi {{ $userName }},</h2>
        <p>Thank you for registering! Use the verification code below to verify your email address. This code is valid for <strong>10 minutes</strong>.</p>

        <div class="code">{{ $code }}</div>

        <p>If you did not create an account, please ignore this email.</p>

        <div class="footer">
            &copy; {{ date('Y') }} Task Management App. All rights reserved.
        </div>
    </div>
</body>
</html>
