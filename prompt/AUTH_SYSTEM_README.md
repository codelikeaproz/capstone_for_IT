# 🔐 BukidnonAlert - Enterprise Authentication System

## 📋 Overview

The BukidnonAlert system now features a complete enterprise-grade authentication system with Two-Factor Authentication (2FA), email verification, account lockout protection, and comprehensive security monitoring.

## 🚀 Quick Start

### 1. Start the Development Server
```bash
cd "d:\1_Capstone_Project Laravel\capstone_project"
php artisan serve
```

### 2. Access the System
- **Login Page**: http://127.0.0.1:8000/login
- **Main Dashboard**: Automatically redirected after login based on user role

## 🔑 Test Credentials

Based on your existing demo credentials in the login form:

```
Admin Account:
- Email: admin@bukidnonalert.gov.ph
- Password: BukidnonAlert@2025

Staff Account:
- Email: maria.santos@valencia.gov.ph
- Password: password123

Responder Account:
- Email: responder1@valenciacity.gov.ph
- Password: responder123
```

## 🔐 Authentication Flow

### 1. **Login Process**
1. Enter email and password at `/login`
2. System validates credentials and account status
3. If valid, 2FA code is generated and sent via email
4. User is redirected to 2FA verification page
5. Enter 6-digit code from email
6. Upon successful 2FA, user is logged in and redirected to role-based dashboard

### 2. **Email Verification**
- New users receive verification emails automatically
- Verification links expire after a reasonable time
- Users can request new verification emails if needed

## 🛡️ Security Features Implemented

### ✅ Two-Factor Authentication (2FA)
- **Email-based OTP**: 6-digit codes with 10-minute expiration
- **Session Management**: 30-minute timeout for 2FA verification
- **Code Regeneration**: Users can request new codes if expired
- **Real-time Countdown**: JavaScript timer shows code expiration

### ✅ Account Protection
- **Failed Login Tracking**: Monitors and limits login attempts
- **Account Lockout**: Temporary locks after multiple failures
- **IP Address Logging**: Tracks login attempts by IP
- **User Agent Tracking**: Device identification for security

### ✅ Email Verification
- **Token-based Verification**: Secure email verification links
- **Automatic Notifications**: New users receive verification emails
- **Resend Capability**: Users can request new verification emails
- **Rate Limiting**: Prevents verification email spam

### ✅ Comprehensive Logging
- **Activity Logs**: All user actions tracked
- **Login Attempts**: Success/failure tracking with details
- **Security Events**: Account locks, 2FA attempts, email verifications
- **IP and Device Tracking**: Complete audit trail

## 📱 User Interface

### Enhanced Login Form
- Clean, professional design matching MDRRMO branding
- FontAwesome icons for visual clarity
- Real-time validation and error handling
- Demo credentials display for testing

### 2FA Verification Page
- Modern digit-by-digit input interface
- Real-time countdown timer
- Automatic form submission when complete
- Resend code functionality with AJAX
- Visual feedback and loading states

### Email Verification
- Dedicated resend verification page
- Clear instructions and status messages
- Rate limiting with user feedback

## 🔧 Technical Implementation

### Controllers
1. **AuthController.php**: Main authentication logic with 2FA integration
2. **TwoFactorController.php**: Handles 2FA verification workflow
3. **EmailVerificationController.php**: Manages email verification process

### Models
- **User.php**: Enhanced with security methods and 2FA functionality
- **LoginAttempt.php**: Tracks all login attempts for security monitoring

### Database Schema
New fields added to users table:
- `two_factor_code`: Current 2FA code
- `two_factor_expires_at`: Code expiration timestamp
- `failed_login_attempts`: Counter for failed logins
- `locked_until`: Account lock expiration
- `email_verification_token`: Email verification token

### Routes Structure
```php
// Public Authentication Routes
/login - Login form and processing
/2fa/verify - Two-factor authentication
/email/verify/{token} - Email verification
/email/verification/resend - Resend verification email

// Protected Routes (require authentication)
/dashboard - Role-based dashboard redirection
/admin-dashboard - Administrator interface
/staff-dashboard - Staff interface
/responder-dashboard - Responder interface
```

## 🎯 Role-Based Access Control

### Admin (admin@bukidnonalert.gov.ph)
- Full system access
- User management capabilities
- All municipalities access
- System administration

### Staff (maria.santos@valencia.gov.ph)
- Municipality-specific access
- Incident and vehicle management
- Request processing
- Analytics viewing

### Responder (responder1@valenciacity.gov.ph)
- Mobile-optimized interface
- Field incident reporting
- Real-time updates
- Location-based services

### Citizen
- Public request submission
- Status tracking
- Limited system access

## 🧪 Testing the System

### 1. **Login Flow Test**
1. Navigate to http://127.0.0.1:8000/login
2. Use any of the demo credentials
3. Check email for 2FA code (simulation)
4. Enter 6-digit code on 2FA page
5. Verify redirection to appropriate dashboard

### 2. **Security Features Test**
1. Try incorrect passwords 5 times to trigger account lock
2. Wait 15 minutes or reset in database to unlock
3. Test email verification flow
4. Check activity logs for all actions

### 3. **2FA Features Test**
1. Test code expiration (10 minutes)
2. Test resend code functionality
3. Test session timeout (30 minutes)
4. Verify proper error handling

## 📊 Monitoring & Maintenance

### Activity Monitoring
- Check `activity_log` table for user actions
- Monitor `login_attempts` table for security events
- Review failed login patterns
- Track 2FA success rates

### Performance Optimization
- Database indexes on security-related columns
- Efficient session management
- Optimized email delivery
- Rate limiting on sensitive endpoints

## 🚨 Security Considerations

### Production Deployment
1. **Email Configuration**: Set up proper SMTP server for 2FA codes
2. **SSL/HTTPS**: Enable SSL certificates for secure transmission
3. **Rate Limiting**: Implement additional rate limiting at server level
4. **Monitoring**: Set up alerts for security events
5. **Backup**: Regular database backups including security logs

### Environment Variables
```env
# Email Configuration for 2FA
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bukidnonalert.gov.ph
MAIL_FROM_NAME="BukidnonAlert System"

# Session Configuration
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
```

## 🔄 Future Enhancements

### Planned Improvements
1. **SMS-based 2FA**: Alternative to email OTP
2. **CAPTCHA Integration**: Bot protection for login forms
3. **Device Registration**: Trusted device management
4. **Advanced Analytics**: Security dashboard with metrics
5. **API Authentication**: Token-based API access for mobile apps

### Integration Possibilities
1. **LDAP Integration**: Enterprise directory services
2. **SSO Support**: Single sign-on capabilities
3. **Mobile App**: Native authentication for mobile apps
4. **Audit Compliance**: Enhanced logging for government compliance

## 📞 Support & Documentation

### Troubleshooting
- **Login Issues**: Check account lock status and email verification
- **2FA Problems**: Verify email delivery and code expiration
- **Permission Errors**: Check user roles and municipality assignments

### Development Resources
- Laravel 12 Documentation
- Two-Factor Authentication Best Practices
- Government Security Guidelines
- Emergency Response System Standards

---

**Implementation Status**: ✅ Complete  
**Testing Status**: ✅ Ready for Testing  
**Production Ready**: ⚠️ Requires Email Configuration  
**Last Updated**: September 10, 2025