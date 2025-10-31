<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #CE9704 0%, #B8860B 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">Reborn Rentals</h1>
    </div>
    
    <div style="background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 10px 10px;">
        <h2 style="color: #333; margin-top: 0;">Payment Verification Code</h2>
        
        <p>Hello{{ $userName ? ' ' . $userName : '' }},</p>
        
        <p>You have requested a verification code to complete your payment. Please use the following code to verify your transaction:</p>
        
        <div style="background: #f5f5f5; border: 2px dashed #CE9704; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0;">
            <div style="font-size: 36px; font-weight: bold; color: #CE9704; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                {{ $code }}
            </div>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            <strong>Important:</strong> This code will expire in 10 minutes. Do not share this code with anyone.
        </p>
        
        <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #666; font-size: 12px;">
            If you did not request this verification code, please ignore this email or contact our support team.
        </p>
        
        <p style="margin-top: 20px; color: #666; font-size: 12px;">
            Best regards,<br>
            <strong>Reborn Rentals Team</strong>
        </p>
    </div>
</body>
</html>

