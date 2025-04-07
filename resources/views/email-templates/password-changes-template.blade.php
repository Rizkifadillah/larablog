<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Password Changed</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      padding: 20px;
      border-radius: 10px;
    }
    .header {
      text-align: center;
      padding-bottom: 20px;
    }
    .content {
      color: #333333;
      font-size: 16px;
      line-height: 1.6;
    }
    .info-box {
      background-color: #f0f0f0;
      padding: 15px;
      border-radius: 8px;
      margin: 20px 0;
      font-family: monospace;
    }
    .footer {
      text-align: center;
      font-size: 13px;
      color: #888888;
      padding-top: 20px;
    }
    @media (max-width: 600px) {
      .container {
        padding: 15px;
      }
      .info-box {
        font-size: 14px;
        padding: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2>Password Changed Successfully</h2>
    </div>
    <div class="content">
      <p>Hello <strong>{{ $user->name }}</strong>,</p>
      <p>Your password has been successfully updated. Below are your login details:</p>

      <div class="info-box">
        <p><strong>Username/Email:</strong> {{ $user->email }}</p>
        <p><strong>New Password:</strong> {{ $new_password }}</p>
      </div>

      <p>We recommend changing your password periodically and avoiding using the same password across multiple services.</p>
      <p>If you did not request this change, please contact our support team immediately.</p>

      <p>Best regards,<br><strong>Support Team</strong></p>
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} Jayatama. All rights reserved.
    </div>
  </div>
</body>
</html>
