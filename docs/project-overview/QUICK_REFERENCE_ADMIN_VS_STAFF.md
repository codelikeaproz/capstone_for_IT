# 🚀 Quick Reference: Admin vs Staff

## At a Glance Comparison

---

## 👨‍💼 ADMIN ROLE

### Access Level: **SYSTEM-WIDE** 🌍

#### ✅ CAN DO:
- 🔑 **User Management**: Create, edit, delete users
- 🌍 **All Municipalities**: Access data from anywhere
- 🚗 **Vehicle Delete**: Permanently remove vehicles
- 📊 **System Analytics**: View system-wide reports
- 🔍 **System Logs**: Access activity logs
- 🏥 **Full CRUD**: All resources, all municipalities

#### 🎯 Primary Responsibilities:
1. Manage user accounts and roles
2. Monitor system performance
3. Configure system settings
4. Oversee all municipalities
5. Handle escalated issues

#### 📱 Dashboard Access:
- Route: `/admin-dashboard`
- Shows: System-wide statistics
- Features: Municipality comparison, user activity

---

## 👨‍💻 STAFF ROLE

### Access Level: **MUNICIPALITY-ONLY** 🏘️

#### ✅ CAN DO:
- 📋 **Incident Management**: Full CRUD (own municipality)
- 🚗 **Vehicle Management**: Create, view, edit (cannot delete)
- 👥 **Victim Management**: Full CRUD (own municipality)
- 📝 **Request Management**: Handle requests in own municipality
- 📊 **Analytics**: View reports for own municipality

#### ❌ CANNOT DO:
- ❌ User management
- ❌ Access other municipalities
- ❌ Delete vehicles
- ❌ System logs
- ❌ System-wide analytics

#### 🎯 Primary Responsibilities:
1. Respond to incidents in assigned municipality
2. Manage emergency vehicles
3. Track victims and their status
4. Process assistance requests
5. Report to supervisors

#### 📱 Dashboard Access:
- Route: `/staff-dashboard`
- Shows: Municipality-specific data
- Features: My tasks, team activity, quick actions

---

## 🔐 Access Matrix

| Feature | Admin | Staff |
|:--------|:-----:|:-----:|
| **USER MANAGEMENT** |
| View all users | ✅ | ❌ |
| Create users | ✅ | ❌ |
| Edit users | ✅ | ❌ |
| Delete users | ✅ | ❌ |
| Assign roles | ✅ | ❌ |
| **INCIDENT MANAGEMENT** |
| View all municipalities | ✅ | ❌ |
| View own municipality | ✅ | ✅ |
| Create incidents | ✅ | ✅ |
| Edit incidents | ✅ | ✅* |
| Delete incidents | ✅ | ✅* |
| **VEHICLE MANAGEMENT** |
| View all vehicles | ✅ | ❌ |
| View own municipality | ✅ | ✅ |
| Create vehicles | ✅ | ✅ |
| Edit vehicles | ✅ | ✅* |
| Delete vehicles | ✅ | ❌ |
| **VICTIM MANAGEMENT** |
| View all victims | ✅ | ❌ |
| View own municipality | ✅ | ✅ |
| Create victims | ✅ | ✅ |
| Edit victims | ✅ | ✅* |
| Delete victims | ✅ | ✅* |
| **SYSTEM FEATURES** |
| System logs | ✅ | ❌ |
| System analytics | ✅ | ❌ |
| Municipality reports | ✅ | ✅* |

**\* = Own municipality only**

---

## 🔑 Login Credentials Format

### Admin:
```
Email: admin@municipality.gov.ph
Role: admin
Municipality: [Any - Can access all]
```

### Staff:
```
Email: staff@municipality.gov.ph
Role: staff
Municipality: [Assigned municipality only]
```

---

## 🛣️ Route Access

### Admin-Only Routes:
```
✅ /users                    (User list)
✅ /users/create             (Create user)
✅ /users/{id}               (View user)
✅ /users/{id}/edit          (Edit user)
✅ /admin-dashboard          (Admin dashboard)
✅ /system-logs              (System logs)
```

### Staff Routes:
```
✅ /staff-dashboard          (Staff dashboard)
✅ /incidents                (Incidents - filtered)
✅ /vehicles                 (Vehicles - filtered)
✅ /victims                  (Victims - filtered)
✅ /reports                  (Reports - filtered)
❌ /users                    (403 Forbidden)
❌ /system-logs              (403 Forbidden)
```

---

## 💻 Code Checks

### Check User Role (Blade):
```blade
@if(auth()->user()->isAdmin())
    <!-- Admin only content -->
@endif

@if(auth()->user()->isStaff())
    <!-- Staff only content -->
@endif
```

### Check User Role (Controller):
```php
if (Auth::user()->isAdmin()) {
    // Admin logic
}

if (Auth::user()->isStaff()) {
    // Staff logic
}
```

### Filter by Municipality (Controller):
```php
// Automatically filter for staff
if (Auth::user()->role !== 'admin') {
    $query->where('municipality', Auth::user()->municipality);
}
```

---

## 🎨 UI Differences

### Admin UI:
- 🌍 Municipality dropdown: **All municipalities**
- 👥 Sidebar: **Shows User Management**
- 📊 Dashboard: **System-wide statistics**
- 🎨 Badge color: `badge-error` (Red)

### Staff UI:
- 🏘️ Municipality: **Pre-selected, locked**
- 👥 Sidebar: **No User Management**
- 📊 Dashboard: **Municipality statistics only**
- 🎨 Badge color: `badge-primary` (Blue)

---

## ⚡ Quick Actions

### Admin Quick Tasks:
```
1. Create new staff user
2. Review system logs
3. Generate system-wide report
4. Assign staff to municipalities
5. Monitor all active incidents
```

### Staff Quick Tasks:
```
1. Report new incident
2. Assign vehicle to incident
3. Update victim status
4. Process assistance request
5. View my assigned tasks
```

---

## 🔔 Notifications

### Admin Receives:
- New user registrations
- Critical incidents (all municipalities)
- System errors
- Performance alerts

### Staff Receives:
- New incidents (own municipality)
- Task assignments
- Request updates
- Vehicle status changes

---

## 📞 Support Escalation

### Staff Issues → Admin
```
1. Staff encounters system issue
2. Contact administrator
3. Admin reviews system logs
4. Admin resolves or escalates
```

### Admin Issues → IT
```
1. Admin encounters system issue
2. Check system logs
3. Review error messages
4. Contact IT support
```

---

## 🎯 Success Indicators

### Admin Success:
- ✅ All municipalities operational
- ✅ Zero unauthorized access attempts
- ✅ All staff accounts active
- ✅ System uptime > 99%

### Staff Success:
- ✅ All assigned incidents processed
- ✅ Response time < target
- ✅ Accurate data entry
- ✅ Proper vehicle utilization

---

## 🚨 Common Issues

### Issue: "403 Forbidden"
**For Staff:**
- ✅ Normal: Trying to access `/users`
- ✅ Normal: Trying to delete vehicles
- ❌ Problem: Accessing own municipality incidents

**Solution:** Ensure you're accessing only allowed routes

### Issue: "Cannot see other municipalities"
**For Staff:**
- ✅ Normal: This is by design
- Cannot view/edit data from other municipalities

**For Admin:**
- ❌ Problem: Should see all municipalities
- Solution: Check role assignment

---

## 📱 Mobile Access

### Admin:
- Full desktop features
- May use mobile for monitoring
- Not optimized for field work

### Staff:
- Desktop for detailed work
- Mobile responsive
- Can use tablets in field

---

## 🔐 Security Best Practices

### Admin:
1. 🔑 Use strong passwords
2. 🔐 Enable 2FA (if available)
3. 📝 Review activity logs weekly
4. 🚫 Never share credentials
5. 🔒 Lock screen when away

### Staff:
1. 🔑 Use unique password
2. 🚫 Don't share login
3. 🔒 Lock screen in field
4. 📱 Report lost devices
5. ✅ Log out after shift

---

## 📊 Performance Expectations

### Admin:
- Dashboard load: < 3 seconds
- User list load: < 2 seconds
- Report generation: < 30 seconds

### Staff:
- Dashboard load: < 2 seconds
- Incident list: < 1 second
- Form submission: < 1 second

---

## 🎓 Training Requirements

### Admin Training:
- System administration (4 hours)
- User management (2 hours)
- Report generation (2 hours)
- Security protocols (1 hour)

### Staff Training:
- Basic operations (2 hours)
- Incident reporting (1 hour)
- Vehicle management (1 hour)
- System navigation (1 hour)

---

## 📄 Documentation References

**Detailed Docs:**
- `ROLE_BASED_ACCESS_CONTROL.md` - Full RBAC documentation
- `STAFF_ROLE_TESTING_GUIDE.md` - Testing procedures
- `STAFF_ROLE_IMPLEMENTATION_SUMMARY.md` - Implementation details

**Code References:**
- `app/Models/User.php` - Role methods (lines 145-168)
- `routes/web.php` - Route protection (lines 156-166)
- `app/Http/Middleware/` - Middleware files

---

## ✅ Checklist for New Users

### New Admin:
- [ ] Account created by super admin
- [ ] Email verified
- [ ] Password changed from default
- [ ] 2FA enabled (if available)
- [ ] Reviewed system overview
- [ ] Completed admin training

### New Staff:
- [ ] Account created by admin
- [ ] Municipality assigned
- [ ] Email verified
- [ ] Password changed from default
- [ ] Reviewed staff dashboard
- [ ] Completed staff training

---

**Last Updated:** October 24, 2025
**Version:** 1.0
**Status:** ✅ Production Ready

---

