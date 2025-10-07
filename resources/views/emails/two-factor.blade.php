<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Security Code - BukidnonAlert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            background-color: #c14a09;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .title {
            color: #c14a09;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            color: #666;
            font-size: 16px;
            margin: 5px 0 0 0;
        }
        .code-section {
            background-color: #f8f9fa;
            border: 2px dashed #c14a09;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #c14a09;
            letter-spacing: 8px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .code-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .expires {
            color: #e74c3c;
            font-size: 14px;
            font-weight: bold;
        }
        .content {
            margin: 20px 0;
            line-height: 1.8;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            background-color: #c14a09;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔐</div>
            <h1 class="title">Security Verification Required</h1>
            <p class="subtitle">BukidnonAlert MDRRMO System</p>
        </div>

        <div class="content">
            <p>Hello <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,</p>
            
            <p>A login attempt was made to your BukidnonAlert account. To complete the login process, please use the verification code below:</p>
        </div>

        <div class="code-section">
            <div class="code-label">Your 6-digit verification code:</div>
            <div class="code">{{ $code }}</div>
            <div class="expires">⏰ Expires in {{ $expiresInMinutes }} minutes</div>
        </div>

        <div class="content">
            <p><strong>What to do next:</strong></p>
            <ol>
                <li>Return to the login page where you were prompted for the code</li>
                <li>Enter the 6-digit code exactly as shown above</li>
                <li>Complete your login to access the system</li>
            </ol>
        </div>

        <div class="warning">
            <div class="warning-title">🛡️ Security Notice</div>
            <p><strong>If you did not initiate this login attempt:</strong></p>
            <ul>
                <li>Do not share this code with anyone</li>
                <li>Contact your system administrator immediately</li>
                <li>Change your password as soon as possible</li>
            </ul>
        </div>

        <div class="content">
            <p><strong>Login Details:</strong></p>
            <ul>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Time:</strong> {{ now()->format('M d, Y - g:i A') }}</li>
                <li><strong>Role:</strong> {{ ucfirst($user->role) }}</li>
                @if($user->municipality)
                <li><strong>Municipality:</strong> {{ $user->municipality }}</li>
                @endif
            </ul>
        </div>

        <div class="footer">
            <p><strong>BukidnonAlert - Municipal Disaster Risk Reduction and Management Office</strong></p>
            <p>This is an automated security email. Please do not reply to this message.</p>
            <p>If you're having trouble with the verification process, please contact your system administrator.</p>
            
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
            
            <p style="color: #999; font-size: 11px;">
                This email was sent to {{ $user->email }} for account security verification.<br>
                © {{ date('Y') }} BukidnonAlert MDRRMO System. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>