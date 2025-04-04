<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password</title>
  <style>
    /* Reset email styles */
    body {
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    h2 {
      color: #333333;
    }
    p {
      color: #555555;
      line-height: 1.6;
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 20px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .footer {
      text-align: center;
      font-size: 12px;
      color: #999999;
      margin-top: 30px;
    }
    @media screen and (max-width: 600px) {
      .container {
        margin: 10px;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Password Reset Request</h2>
    <p>Hello, {{  $user->name }}</p>
    <p>We received a request to reset your password. If this was you, please click the button below to reset your password:</p>
    <a href="{{  $actionLink }}" target="_blank" class="btn">Reset Password</a>
    <p>If you didn’t request a password reset, you can ignore this email. This link will expire in 24 hours.</p>
    <p>Thanks,<br>Your Company Team</p>
    <div class="footer">
      &copy; {{ date('Y') }} Jayatama. All rights reserved.
    </div>
  </div>
</body>
</html>
