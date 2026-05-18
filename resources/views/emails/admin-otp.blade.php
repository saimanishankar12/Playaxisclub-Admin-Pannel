<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset OTP</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 40px 0; }
        .container { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 8px;
                     box-shadow: 0 2px 12px rgba(0,0,0,.08); overflow: hidden; }
        .header { background: #4e73df; padding: 28px 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .body { padding: 32px; color: #5a5c69; }
        .otp-box { background: #f8f9fc; border: 2px dashed #4e73df; border-radius: 8px;
                   text-align: center; padding: 18px 0; margin: 24px 0; }
        .otp-box span { font-size: 38px; font-weight: 700; letter-spacing: 10px; color: #4e73df; }
        .footer { background: #f8f9fc; padding: 16px 32px; text-align: center;
                  font-size: 12px; color: #858796; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset OTP</h1>
        </div>
        <div class="body">
            <p>Hello,{{$name}}</p>
            <p>We received a request to reset your admin account password. Use the OTP below to proceed:</p>
            <div class="otp-box">
                <span>{{ $otp }}</span>
            </div>
            <p>This OTP is valid for <strong>5 minutes</strong>. Do not share it with anyone.</p>
            <p>If you did not request a password reset, please ignore this email or contact support immediately.</p>
        </div>
        <div class="footer">
    This is an automated confirmation from <strong>Playaxisclub</strong>.<br>
    For any queries, reach us at <a href="mailto:support@playaxisclub.com" style="color:#4e73df;">support@playaxisclub.com</a>
    <br><br>
    &copy; {{ date('Y') }} Playaxisclub. All rights reserved.
</div>
    </div>
</body>
</html>