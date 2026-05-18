<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your OTP - Playaxisclub</title>
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
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px 12px; font-size: 14px; border-bottom: 1px solid #e3e6f0; }
        .info-table td:first-child { color: #858796; width: 40%; }
        .info-table td:last-child { color: #5a5c69; font-weight: 600; }
        .footer { background: #f8f9fc; padding: 16px 32px; text-align: center;
                  font-size: 12px; color: #858796; }
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
            <h1>Player Portal Login OTP</h1>
        </div>

        <div class="body">
            <p>Hello, <strong>{{ $playerName }}</strong></p>
            <p>A login request was made for your Playaxisclub Player Portal account. Use the OTP below to complete verification:</p>

            <div class="otp-box">
                <span>{{ $otp }}</span>
            </div>

            <p>This OTP is valid for <strong>10 minutes</strong>. Do not share it with anyone.</p>

            <table class="info-table">
                <tr>
                    <td>&#128197; Date</td>
                    <td>May 29, 30, 31</td>
                </tr>
                <tr>
                    <td>&#128205; Venue</td>
                    <td>Drona Badminton Academy, Ladies Recreation Club, Atchutha Ramayya Street, Ramaraopeta, Kakinada (533004)</td>
                </tr>
            </table>

            <p>If you did not request this, you can safely ignore this email — your account remains secure.</p>
        </div>

        <div class="footer">
            This is an automated message from <strong>Playaxisclub</strong>.<br>
            For any queries, reach us at <a href="mailto:support@playaxisclub.com" style="color:#4e73df;">support@playaxisclub.com</a>
            <br><br>
            &copy; {{ date('Y') }} Playaxisclub. All rights reserved.
        </div>

    </div>
</body>
</html>