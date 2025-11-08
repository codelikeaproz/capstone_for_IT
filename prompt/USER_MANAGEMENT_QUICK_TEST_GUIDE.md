# 🧪 User Management - Quick Test Guide

**Purpose**: Quick reference for testing the newly implemented User Management module  
**Date**: January 2025

---

## 🚀 Quick Start

### 1. Access User Management
```
URL: http://localhost:8000/users
Required: Admin role
```

### 2. Test Credentials
Use an existing admin account or create one via seeder:
```bash
php artisan db:seed --class=BukidnonAlertSeeder
```

---

## ✅ Testing Checklist

### Basic CRUD Operations

#### ✓ View User List
- [ ] Navigate to `/users`
- [ ] Verify statistics cards display correctly
- [ ] Check user table shows all users
- [ ] Verify pagination works

#### ✓ Create New User
- [ ] Click "Add New User" button
- [ ] Fill in all required fields:
  - First Name: Test
  - Last Name: User
  - Email: testuser@example.com
  - Password: password123
  - Role: Staff
  - Municipality: Malaybalay City
- [ ] Check "Active Account" checkbox
- [ ] Click "Create User"
- [ ] Verify success message appears
- [ ] Verify user appears in list

#### ✓ View User Details
- [ ] Click on a user's name or eye icon
- [ ] Verify all information displays correctly
- [ ] Check statistics show correct counts
- [ ] Verify recent activity timeline appears

#### ✓ Edit User
- [ ] Click "Edit User" button
- [ ] Modify user information
- [ ] Change password (optional)
- [ ] Click "Update User"
- [ ] Verify changes saved correctly

#### ✓ Delete User
- [ ] Click delete button (trash icon)
- [ ] Confirm deletion in modal
- [ ] Verify user is removed from list
- [ ] Try to delete your own account (should fail)

---

### Advanced Features

#### ✓ Search & Filters
- [ ] Search by name: Enter "John"
- [ ] Search by email: Enter "@example.com"
- [ ] Filter by role: Select "Staff"
- [ ] Filter by municipality: Select "Malaybalay City"
- [ ] Filter by status: Select "Active"
- [ ] Click "Apply Filters"
- [ ] Verify results match filters
- [ ] Click "Reset" to clear filters

#### ✓ Quick Actions (from User Details page)

**Toggle Status**
- [ ] Click "Deactivate Account" button
- [ ] Confirm action
- [ ] Verify status changes to "Inactive"
- [ ] Click "Activate Account"
- [ ] Verify status changes back to "Active"

**Change Role**
- [ ] Click "Change Role" button
- [ ] Select new role from dropdown
- [ ] Click "Change Role"
- [ ] Verify role badge updates

**Change Municipality**
- [ ] Click "Change Municipality" button
- [ ] Select new municipality
- [ ] Click "Change Municipality"
- [ ] Verify municipality updates

**Reset Password**
- [ ] Click "Reset Password" button
- [ ] Enter new password: newpassword123
- [ ] Confirm password
- [ ] Click "Reset Password"
- [ ] Verify success message

**Verify Email** (if unverified)
- [ ] Click "Verify Email" button
- [ ] Verify email status changes to "Verified"

**Unlock Account** (if locked)
- [ ] Click "Unlock Account" button
- [ ] Verify account is unlocked

---

### Security Tests

#### ✓ Admin-Only Access
- [ ] Log out
- [ ] Log in as non-admin user (staff/responder/citizen)
- [ ] Try to access `/users`
- [ ] Verify 403 Forbidden error

#### ✓ Self-Protection
- [ ] Log in as admin
- [ ] Navigate to your own user profile
- [ ] Verify delete button is hidden
- [ ] Try to deactivate your own account
- [ ] Verify appropriate error message

#### ✓ Last Admin Protection
- [ ] Ensure only one admin exists
- [ ] Try to delete the admin
- [ ] Verify error: "Cannot delete the last administrator"
- [ ] Try to deactivate the admin
- [ ] Verify error: "Cannot deactivate the last active administrator"

---

### UI/UX Tests

#### ✓ Responsive Design
- [ ] Test on desktop (1920x1080)
- [ ] Test on tablet (768x1024)
- [ ] Test on mobile (375x667)
- [ ] Verify all elements are accessible
- [ ] Check table scrolls horizontally on mobile

#### ✓ Toast Notifications
- [ ] Create user → Success toast appears
- [ ] Update user → Success toast appears
- [ ] Delete user → Success toast appears
- [ ] Toggle status → Success toast appears
- [ ] Verify toasts auto-dismiss after 3 seconds

#### ✓ Modals
- [ ] Delete confirmation modal works
- [ ] Reset password modal works
- [ ] Change role modal works
- [ ] Change municipality modal works
- [ ] All modals can be closed with backdrop click

#### ✓ Icons & Badges
- [ ] All Font Awesome icons load correctly
- [ ] Role badges show correct colors:
  - Admin: Red (badge-error)
  - Staff: Blue (badge-primary)
  - Responder: Yellow (badge-warning)
  - Citizen: Gray (badge-neutral)
- [ ] Status badges show correct colors:
  - Active: Green (badge-success)
  - Inactive: Red (badge-error)

---

### Data Validation Tests

#### ✓ Create User Validation
- [ ] Try to submit empty form → Errors appear
- [ ] Enter invalid email → Error appears
- [ ] Enter short password (< 8 chars) → Error appears
- [ ] Passwords don't match → Error appears
- [ ] Use existing email → Error appears

#### ✓ Edit User Validation
- [ ] Try to clear required fields → Errors appear
- [ ] Change email to existing one → Error appears
- [ ] Enter mismatched passwords → Error appears

---

### Performance Tests

#### ✓ Large Dataset
- [ ] Create 50+ users (use seeder or factory)
- [ ] Navigate to user list
- [ ] Verify page loads quickly (< 2 seconds)
- [ ] Test pagination with large dataset
- [ ] Test search with large dataset
- [ ] Test filters with large dataset

#### ✓ AJAX Actions
- [ ] Toggle status → Response < 1 second
- [ ] Change role → Response < 1 second
- [ ] Change municipality → Response < 1 second
- [ ] Reset password → Response < 1 second

---

## 🐛 Common Issues & Solutions

### Issue: Routes not found (404)
**Solution**: Clear route cache
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: Views not updating
**Solution**: Clear view cache
```bash
php artisan view:clear
```

### Issue: CSS not loading
**Solution**: Rebuild assets
```bash
npm run dev
# or
npm run build
```

### Issue: "Class UserController not found"
**Solution**: Clear config cache
```bash
php artisan config:clear
composer dump-autoload
```

### Issue: Database errors
**Solution**: Check migrations
```bash
php artisan migrate:status
php artisan migrate
```

---

## 📊 Expected Results

### Statistics Cards
- **Total Users**: Count of all users
- **Active Users**: Count of is_active = true
- **Administrators**: Count of role = 'admin'
- **Inactive Users**: Count of is_active = false

### User Table Columns
1. User (Avatar + Name + Phone)
2. Email (with verification badge)
3. Role (colored badge)
4. Municipality
5. Status (clickable badge)
6. Email Verified (badge)
7. Last Login (relative time)
8. Actions (View, Edit, Delete)

### User Details Page Sections
1. **Header**: Avatar, name, role, status
2. **Personal Information**: Name, email, phone, address
3. **Account Information**: Role, municipality, status, verification, last login
4. **Activity Statistics**: 4 stat cards
5. **Recent Activity**: Timeline of last 20 activities
6. **Quick Actions**: 6 action buttons
7. **Role Permissions**: List of permissions

---

## 🎯 Success Criteria

All tests should pass with:
- ✅ No errors in browser console
- ✅ No PHP errors in Laravel log
- ✅ All AJAX requests return 200 status
- ✅ All forms validate correctly
- ✅ All security checks work
- ✅ UI is responsive on all devices
- ✅ Toast notifications appear and dismiss
- ✅ Activity logs are created for all actions

---

## 📝 Test Report Template

```
Date: _______________
Tester: _______________
Environment: _______________

CRUD Operations:
[ ] View List - PASS/FAIL
[ ] Create User - PASS/FAIL
[ ] View Details - PASS/FAIL
[ ] Edit User - PASS/FAIL
[ ] Delete User - PASS/FAIL

Advanced Features:
[ ] Search & Filters - PASS/FAIL
[ ] Quick Actions - PASS/FAIL
[ ] Role Assignment - PASS/FAIL
[ ] Municipality Assignment - PASS/FAIL
[ ] Status Toggle - PASS/FAIL

Security:
[ ] Admin-Only Access - PASS/FAIL
[ ] Self-Protection - PASS/FAIL
[ ] Last Admin Protection - PASS/FAIL

UI/UX:
[ ] Responsive Design - PASS/FAIL
[ ] Toast Notifications - PASS/FAIL
[ ] Modals - PASS/FAIL
[ ] Icons & Badges - PASS/FAIL

Overall Result: PASS/FAIL
Notes: _______________
```

---

## 🚀 Next Steps After Testing

1. **If all tests pass**:
   - Deploy to staging environment
   - Conduct user acceptance testing
   - Train administrators
   - Deploy to production

2. **If tests fail**:
   - Document issues
   - Create bug reports
   - Fix issues
   - Re-test

---

**Testing Status**: Ready for Testing  
**Estimated Testing Time**: 30-45 minutes  
**Priority**: HIGH
