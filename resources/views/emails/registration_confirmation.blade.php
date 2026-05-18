<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registration Confirmed</title>
  <style>
    body { margin: 0; padding: 0; background: #f4f5f7; font-family: 'Segoe UI', Arial, sans-serif; }
    .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .header { background: #1f3fae; padding: 36px 40px 28px; text-align: center; }
    .header h1 { margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
    .header p  { margin: 8px 0 0; color: #c7d3f5; font-size: 13px; }
    .badge { display: inline-block; margin-top: 14px; background: #f59e0b; color: #ffffff; font-size: 12px; font-weight: 700; padding: 5px 18px; border-radius: 20px; letter-spacing: 1px; text-transform: uppercase; }
    .body { padding: 36px 40px; }
    .greeting { font-size: 16px; color: #1e293b; margin-bottom: 8px; }
    .greeting strong { color: #1f3fae; }
    .subtitle { font-size: 13px; color: #64748b; margin-bottom: 28px; }
    /* ID Card */
    .id-card { background: #f0f4ff; border: 1.5px solid #c7d3f5; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; }
    .id-card .section-title { font-size: 11px; font-weight: 700; color: #1f3fae; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; }
    .id-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #dde4f5; }
    .id-row:last-child { border-bottom: none; }
    .id-label { font-size: 12px; color: #64748b; font-weight: 500; }
    .id-value { font-size: 13px; color: #1f3fae; font-weight: 700; }
    /* Payment Card */
    .pay-card { background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 12px; padding: 20px 24px; margin-bottom: 28px; }
    .pay-card .section-title { font-size: 11px; font-weight: 700; color: #16a34a; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; }
    .pay-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #d1fae5; }
    .pay-row:last-child { border-bottom: none; }
    .pay-label { font-size: 12px; color: #64748b; font-weight: 500; }
    .pay-value { font-size: 13px; color: #16a34a; font-weight: 700; }
    .pay-value.amount { font-size: 16px; color: #15803d; }
    /* Note */
    .note { background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 14px 18px; font-size: 12px; color: #78350f; line-height: 1.6; margin-bottom: 28px; }
    /* Footer */
    .footer { background: #f8faff; border-top: 1px solid #e2e8f0; padding: 24px 40px; text-align: center; }
    .footer p { margin: 0; font-size: 11px; color: #94a3b8; line-height: 1.8; }
    .footer a { color: #1f3fae; text-decoration: none; font-weight: 600; }
    @media (max-width: 600px) {
      .body, .header, .footer { padding: 24px 20px; }
      .id-row, .pay-row { flex-direction: column; align-items: flex-start; gap: 2px; }
    }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- HEADER -->
  <div class="header">
    <h1> Ekalavya Badminton Tournament</h1>
    <p>Registration & Payment Confirmation</p>
    <span class="badge"> Confirmed</span>
  </div>

  <!-- BODY -->
  <div class="body">
    <p class="greeting">Hi, <strong>{{ $details['player_name'] }}</strong>!</p>
    <p class="subtitle">
      Your registration for the <strong>Ekalavya Badminton Tournament</strong> has been
      successfully confirmed. Please save this email ,your IDs below will be needed on the event day.
    </p>

    <!-- REGISTRATION IDs -->
    <div class="id-card">
      <div class="section-title"> Registration Details</div>

      <div class="id-row">
        <span class="id-label">PLAYAXISCLUB ID :</span>
        <span class="id-value">{{ $details['type'] === 'singles' ? $details['player_id'] : $details['player1_id'] }}</span>
      </div>

      <div class="id-row">
        <span class="id-label">Team ID :</span>
        <span class="id-value">{{ $details['season_id'] }}</span>
      </div>

      <div class="id-row">
        <span class="id-label">Category :</span>
        <span class="id-value">{{ ucfirst($details['type']) }}</span>
      </div>

      <div class="id-row">
        <span class="id-label">Tournament Dates :</span>
        <span class="id-value">May 29th, 30th &amp; 31st 2026</span>
      </div>
    </div>

    <!-- PAYMENT DETAILS -->
    <div class="pay-card">
      <div class="section-title"> Payment Details</div>

      <div class="pay-row">
        <span class="pay-label">Amount Paid: </span>
        <!--<span class="pay-value amount">₹{{ number_format($details['amount'], 2) }}</span>-->
        <span class="pay-value amount">&#8377;{{ number_format((int)$details['amount'], 0) }}</span>
      </div>
      <div class="pay-row">
        <span class="pay-label">Payment ID :</span>
        <span class="pay-value">{{ $details['razorpay_payment_id'] }}</span>
      </div>
      <div class="pay-row">
        <span class="pay-label">Order ID :</span>
        <span class="pay-value">{{ $details['razorpay_order_id'] }}</span>
      </div>
      <div class="pay-row">
        <span class="pay-label">Date & Time :</span>
        <span class="pay-value">{{ $details['payment_date'] }}</span>
      </div>
      <div class="pay-row">
        <span class="pay-label">Status :</span>
        <span class="pay-value"> Success</span>
      </div>
    </div>

    <!-- NOTE -->
    <div class="note">
      📌 <strong>Important:</strong> Please carry a printed or digital copy of this email and a valid
      Aadhaar card to the venue on the day of the tournament. Your PLAYAXISCLUB ID is your entry pass.
    </div>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    <p>
      This is an automated confirmation from <strong>PlayAxis Club</strong>.<br/>
      For any queries, reach us at <a href="mailto:support@playaxisclub.com">support@playaxisclub.com</a><br/>
      <br/>
      &copy; {{ date('Y') }} PlayAxis Club | All rights reserved
    </p>
  </div>

</div>
</body>
</html>