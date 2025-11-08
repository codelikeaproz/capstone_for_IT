# BukidnonAlert - Manual Testing Checklist
## Quick Reference Guide for UI/UX Testing

**Server:** http://127.0.0.1:8000
**Status:** ✅ Running
**Your Task:** Test each feature and check the boxes

---

## 🚀 Getting Started

### Step 1: Get Admin Login Credentials
```bash
# Run this command to find admin user:
php artisan tinker --execute="echo User::where('role', 'admin')->first()->email;"
```

### Step 2: Open Application
```
URL: http://127.0.0.1:8000
```

---

## ✅ Testing Checklist

### 1. Authentication (5 minutes)

**Login Flow:**
- [ ] Navigate to login page
- [ ] Enter wrong credentials → Should show error
- [ ] Enter correct admin credentials → Should redirect to dashboard
- [ ] Check if your name appears in header
- [ ] Click logout → Should return to login
- [ ] Try accessing /incidents without login → Should redirect to login

**Expected Result:** All authentication flows work correctly

---

### 2. Dashboard (5 minutes)

**Statistics Display:**
- [ ] Dashboard loads without errors
- [ ] See "Total Incidents" card (should show 8)
- [ ] See "Active Incidents" card
- [ ] See "Critical Incidents" card
- [ ] See "Vehicle Statistics" section
- [ ] See "Victim Statistics" section
- [ ] Check "Recent Incidents" list displays

**Filters:**
- [ ] Change date range filter (7 days, 30 days, 90 days)
- [ ] Verify statistics update

**Expected Result:** Dashboard shows real data from database

---

### 3. Incident Management (15 minutes)

**View Incidents:**
- [ ] Click "Incidents" in sidebar
- [ ] Incident list loads (should show 8 incidents)
- [ ] See incident cards with:
  - [ ] Incident number (e.g., INC-2025-001)
  - [ ] Type badge
  - [ ] Severity indicator
  - [ ] Status badge
  - [ ] Date and location

**Filters:**
- [ ] Test municipality filter → Incidents filter correctly
- [ ] Test severity filter (critical, high, medium, low)
- [ ] Test status filter (pending, active, resolved, closed)
- [ ] Test incident type filter

**Create Incident:**
- [ ] Click "Create Incident" button
- [ ] Form loads with all sections:
  - [ ] Basic Information section visible
  - [ ] Municipality dropdown works
  - [ ] Barangay dropdown works (select municipality first)
  - [ ] GPS coordinates fields visible
  - [ ] Severity dropdown works
  - [ ] Status dropdown works

**Incident Types (Test Type-Specific Fields):**
- [ ] Select "Traffic Accident" → Additional fields appear
  - [ ] License plates input
  - [ ] Vehicle count
  - [ ] Road conditions
- [ ] Select "Medical Emergency" → Medical fields appear
  - [ ] Patient count
  - [ ] Ambulance needed checkbox
  - [ ] Medical emergency type
- [ ] Select "Fire Incident" → Fire fields appear
  - [ ] Building type
  - [ ] Fire spread level
- [ ] Try each incident type to verify dynamic fields work

**Media Upload:**
- [ ] Upload a photo (< 2MB)
- [ ] Verify photo preview appears
- [ ] Try uploading > 2MB → Should show error
- [ ] Upload up to 5 photos
- [ ] Try 6th photo → Should prevent or warn

**Victim Management (Inline):**
- [ ] Click "Add Victim" button
- [ ] Victim form appears
- [ ] Fill victim details:
  - [ ] First name, last name
  - [ ] Age, gender
  - [ ] Contact number
  - [ ] Medical status dropdown
- [ ] Save victim
- [ ] Victim appears in list below form
- [ ] Add another victim → Should work

**Assignment:**
- [ ] Assign staff dropdown works
- [ ] Assign vehicle dropdown shows available vehicles
- [ ] Select staff and vehicle

**Submit:**
- [ ] Click "Create Incident"
- [ ] Success message appears (toast notification)
- [ ] Redirected to incident details page
- [ ] Verify all data saved correctly

**View Incident:**
- [ ] From incidents list, click an incident
- [ ] Incident details page loads
- [ ] See all information:
  - [ ] Basic info
  - [ ] Type-specific details
  - [ ] Photos gallery (if uploaded)
  - [ ] Victims list (if added)
  - [ ] Assigned staff and vehicle
  - [ ] Status

**Edit Incident:**
- [ ] Click "Edit" button
- [ ] Edit form loads with existing data
- [ ] Change some fields
- [ ] Click "Update"
- [ ] Success message appears
- [ ] Changes saved correctly

**Delete Incident (Admin Only):**
- [ ] Click "Delete" button
- [ ] Confirmation dialog appears
- [ ] Confirm deletion
- [ ] Success message appears
- [ ] Incident removed from list

**Expected Result:** Full incident CRUD workflow functional

---

### 4. Vehicle Management (10 minutes)

**View Vehicles:**
- [ ] Click "Vehicles" in sidebar
- [ ] Vehicle list loads (should show 29 vehicles)
- [ ] See statistics cards at top:
  - [ ] Total vehicles: 29
  - [ ] Available: 18
  - [ ] In-use: (some number)
  - [ ] Maintenance: (some number)
  - [ ] Low fuel: (if any)

**Filters:**
- [ ] Filter by municipality
- [ ] Filter by vehicle type (ambulance, fire truck, etc.)
- [ ] Filter by status (available, in_use, maintenance)

**Create Vehicle:**
- [ ] Click "Add Vehicle" or "Create Vehicle"
- [ ] Form loads
- [ ] Fill vehicle details:
  - [ ] Vehicle number (unique)
  - [ ] License plate (unique)
  - [ ] Vehicle type dropdown
  - [ ] Municipality dropdown
  - [ ] Fuel capacity
  - [ ] Equipment list (optional)
- [ ] Submit form
- [ ] Success message appears
- [ ] New vehicle appears in list

**View Vehicle:**
- [ ] Click a vehicle card
- [ ] Vehicle details page loads
- [ ] See:
  - [ ] Vehicle information
  - [ ] Current status badge
  - [ ] Fuel level indicator
  - [ ] Assigned driver (if any)
  - [ ] Current incident (if assigned)
  - [ ] Recent incident history

**Edit Vehicle:**
- [ ] Click "Edit" button
- [ ] Edit form loads
- [ ] Change some fields
- [ ] Update fuel level to 20% (should trigger low fuel alert)
- [ ] Click "Update"
- [ ] Success message appears

**Vehicle Assignment:**
- [ ] Find an available vehicle
- [ ] Look for "Assign to Incident" button or dropdown
- [ ] Select an active incident
- [ ] Assign vehicle
- [ ] Vehicle status changes to "in_use"
- [ ] Go to incident details → Vehicle appears assigned

**Vehicle Release:**
- [ ] Find a vehicle assigned to incident
- [ ] Click "Release" button
- [ ] Vehicle status changes back to "available"
- [ ] Incident no longer shows vehicle

**Maintenance Update:**
- [ ] Select a vehicle
- [ ] Change status to "maintenance"
- [ ] Add maintenance notes
- [ ] Set next maintenance due date
- [ ] Submit
- [ ] Vehicle status updates

**Expected Result:** Vehicle fleet management fully functional

---

### 5. User Management (10 minutes) - Admin Only

**View Users:**
- [ ] Click "Users" or "User Management"
- [ ] User list loads (should show 23 users)
- [ ] See user cards/table with:
  - [ ] Name
  - [ ] Email
  - [ ] Role badge
  - [ ] Municipality
  - [ ] Status (Active/Inactive)

**Create User:**
- [ ] Click "Create User"
- [ ] Form loads
- [ ] Fill user details:
  - [ ] First name, last name
  - [ ] Email (must be unique)
  - [ ] Password
  - [ ] Role dropdown (admin, staff, responder, citizen)
  - [ ] Municipality dropdown
  - [ ] Is Active checkbox
- [ ] Submit form
- [ ] Success message
- [ ] New user appears in list

**View User:**
- [ ] Click a user
- [ ] User profile page loads
- [ ] See all user information
- [ ] See user activity history (if available)

**Edit User:**
- [ ] Click "Edit" button
- [ ] Edit form loads
- [ ] Change role
- [ ] Change municipality
- [ ] Update other details
- [ ] Submit
- [ ] Changes saved

**Toggle User Status:**
- [ ] Find active user
- [ ] Click "Deactivate" or toggle switch
- [ ] User status changes to "Inactive"
- [ ] Verify user cannot login when inactive

**Delete User:**
- [ ] Select a user
- [ ] Click "Delete"
- [ ] Confirmation dialog
- [ ] Confirm
- [ ] User removed from list

**Expected Result:** Complete user management working

---

### 6. Victim Management (5 minutes)

**View Victims:**
- [ ] Navigate to Victims section (if standalone)
- [ ] Or view from Incident details page
- [ ] Victim list loads (should show 72 victims)

**Filters:**
- [ ] Filter by medical status
- [ ] Filter by incident

**Create Victim:**
- [ ] If standalone page, click "Create Victim"
- [ ] Or use inline form in incident creation
- [ ] Fill victim details
- [ ] Select medical status
- [ ] Link to incident
- [ ] Submit
- [ ] Victim created

**Update Medical Status:**
- [ ] View victim details
- [ ] Change medical status
- [ ] Add medical treatment notes
- [ ] Add hospital referred
- [ ] Submit
- [ ] Status updates

**Expected Result:** Victim tracking functional

---

### 7. Heat Map (5 minutes)

**Load Heat Map:**
- [ ] Click "Heat Maps" or "Heat Map" in sidebar
- [ ] Map loads (Leaflet.js)
- [ ] See Bukidnon area centered
- [ ] Incident markers appear on map

**Markers:**
- [ ] Hover over marker → Tooltip appears with incident info
- [ ] Click marker → Popup opens with:
  - [ ] Incident number
  - [ ] Type
  - [ ] Severity
  - [ ] Date
  - [ ] Location
  - [ ] Victim count

**Color Coding:**
- [ ] Critical incidents: Red markers
- [ ] High severity: Orange markers
- [ ] Medium severity: Yellow markers
- [ ] Low severity: Green markers

**Filters:**
- [ ] Click "Filters" button
- [ ] Filter panel opens
- [ ] Filter by date range
- [ ] Filter by severity
- [ ] Filter by municipality
- [ ] Markers update based on filters

**Refresh:**
- [ ] Click "Refresh" button
- [ ] Map reloads with latest data

**Expected Result:** Interactive heat map working correctly

---

### 8. Analytics Dashboard (2 minutes) - EXPECTED TO BE INCOMPLETE

⚠️ **Note:** Based on gap analysis, analytics dashboard is just a placeholder

**Test:**
- [ ] Navigate to "Analytics" (if menu item exists)
- [ ] Page loads
- [ ] Check what displays:
  - [ ] Is it just placeholder text?
  - [ ] Are there any charts?
  - [ ] Are there any graphs?

**Document:**
```
What you see: _________________________________
Expected: Charts, graphs, trend analysis
Status: ☐ Functional  ☐ Placeholder  ☐ Not found
```

**Expected Result:** Placeholder only (known issue)

---

### 9. Request Management (5 minutes) - If Available

**Citizen Requests:**
- [ ] Navigate to "Requests" section
- [ ] Requests list loads
- [ ] See request cards with:
  - [ ] Request number
  - [ ] Request type
  - [ ] Status
  - [ ] Requester name
  - [ ] Date submitted

**Create Request:**
- [ ] Click "Create Request"
- [ ] Fill request form
- [ ] Submit
- [ ] Request created

**Process Request:**
- [ ] View pending request
- [ ] Assign to staff
- [ ] Approve or reject
- [ ] Status updates

**Expected Result:** Request workflow functional (if implemented)

---

### 10. System Logs (2 minutes) - Admin Only

**Activity Logs:**
- [ ] Navigate to "System Logs" or "Activity Logs"
- [ ] Log list loads (should show 245 activities)
- [ ] See recent activities:
  - [ ] User actions
  - [ ] Timestamp
  - [ ] Changes made

**Expected Result:** Audit trail visible

---

### 11. UI/UX Quality Checks (10 minutes)

**Navigation:**
- [ ] Sidebar opens/closes smoothly
- [ ] All menu items clickable
- [ ] Active page highlighted in menu
- [ ] Breadcrumbs work (if present)

**Forms:**
- [ ] All input fields accessible
- [ ] Labels clearly visible
- [ ] Placeholders helpful
- [ ] Required field indicators (*) visible
- [ ] Validation messages appear on error
- [ ] Success messages appear on save
- [ ] Cancel buttons work

**Tables/Lists:**
- [ ] Headers clear
- [ ] Data aligned properly
- [ ] Pagination works (if > 15 items)
- [ ] Search works (if available)
- [ ] Sort columns work (if available)

**Buttons:**
- [ ] Primary action buttons stand out
- [ ] Hover states visible
- [ ] Loading states show (if implemented)
- [ ] Disabled state clear

**Modals/Dialogs:**
- [ ] Open smoothly
- [ ] Close button works
- [ ] Click outside to close works (if enabled)
- [ ] Content readable

**Notifications:**
- [ ] Success toasts appear (green)
- [ ] Error toasts appear (red)
- [ ] Warning toasts appear (yellow)
- [ ] Auto-dismiss after few seconds
- [ ] Can be manually closed

**Expected Result:** Professional, polished UI

---

### 12. Responsive Design (10 minutes)

**Desktop (1920x1080):**
- [ ] Open in full screen
- [ ] Layout looks good
- [ ] All elements visible
- [ ] No horizontal scroll
- [ ] Sidebar fully visible

**Laptop (1366x768):**
- [ ] Resize browser to ~1366px wide
- [ ] Layout adapts
- [ ] Content still readable
- [ ] No overlapping elements

**Tablet (768x1024):**
- [ ] Resize to tablet size or use device toolbar (F12)
- [ ] Sidebar collapses or becomes hamburger menu
- [ ] Content stacks vertically
- [ ] Cards adjust width
- [ ] Forms still usable

**Mobile (375x667):**
- [ ] Resize to mobile size
- [ ] Mobile menu appears
- [ ] All content accessible
- [ ] Buttons large enough to tap
- [ ] Forms stack vertically
- [ ] Tables scroll or cards replace tables
- [ ] Text readable without zooming

**Expected Result:** Responsive across all screen sizes

---

### 13. Browser Compatibility (15 minutes)

**Chrome:**
- [ ] Open in Chrome
- [ ] All features work
- [ ] Styles load correctly
- [ ] No console errors (F12)

**Firefox:**
- [ ] Open in Firefox
- [ ] All features work
- [ ] Styles load correctly
- [ ] No console errors

**Edge:**
- [ ] Open in Edge
- [ ] All features work
- [ ] Styles load correctly
- [ ] No console errors

**Safari (if available):**
- [ ] Open in Safari
- [ ] All features work
- [ ] Styles load correctly

**Expected Result:** Works on all major browsers

---

### 14. Error Handling (5 minutes)

**Test Error Scenarios:**
- [ ] Submit form with empty required fields → Validation errors
- [ ] Upload file too large → Error message
- [ ] Try to access page without permission → 403 Forbidden
- [ ] Navigate to non-existent page → 404 Not Found
- [ ] Duplicate entry (if applicable) → Unique constraint error

**Expected Result:** Friendly error messages

---

### 15. Performance (5 minutes)

**Page Load Times:**
- [ ] Dashboard loads in < 3 seconds
- [ ] Incidents list loads in < 3 seconds
- [ ] Vehicle list loads in < 3 seconds
- [ ] Heat map loads in < 5 seconds
- [ ] Forms appear instantly

**Interactions:**
- [ ] Buttons respond immediately
- [ ] Dropdowns open quickly
- [ ] Modals open smoothly
- [ ] Form submissions process in < 2 seconds
- [ ] No lag when typing in forms

**Expected Result:** Snappy, responsive interface

---

## 🐛 Bug Reporting

### When you find a bug, document it like this:

```
BUG #1
------
Title: [Short description, e.g., "Delete button not working on vehicles page"]

Severity: ☐ Critical  ☐ High  ☐ Medium  ☐ Low

Category: [Authentication / Incident / Vehicle / User / UI / etc.]

Steps to Reproduce:
1. Login as admin
2. Go to Vehicles page
3. Click on a vehicle
4. Click "Delete" button
5. [What happens?]

Expected Behavior:
Confirmation dialog should appear, then vehicle deleted

Actual Behavior:
Nothing happens / Error message / Page crashes

Screenshot: [Attach if possible]

Browser: Chrome / Firefox / Edge / Safari
Screen Size: Desktop / Tablet / Mobile
```

---

## ✅ Testing Complete!

### After finishing all tests:

1. **Count Results:**
   - Total Tests: ______
   - Passed: ______
   - Failed: ______
   - Success Rate: _____%

2. **Critical Issues Found:**
   - List any show-stopper bugs here

3. **Minor Issues Found:**
   - List any small bugs here

4. **Overall Assessment:**
   - ☐ Ready for Beta Testing
   - ☐ Needs bug fixes first
   - ☐ Needs major work

5. **Best Features:**
   - What worked really well?

6. **Biggest Problems:**
   - What needs the most work?

---

## 📊 Quick Test Results Summary

After testing, fill this out:

| Feature | Status | Notes |
|---------|--------|-------|
| Authentication | ☐ Pass ☐ Fail | |
| Dashboard | ☐ Pass ☐ Fail | |
| Incidents (CRUD) | ☐ Pass ☐ Fail | |
| Vehicles (CRUD) | ☐ Pass ☐ Fail | |
| Users (CRUD) | ☐ Pass ☐ Fail | |
| Victims | ☐ Pass ☐ Fail | |
| Heat Map | ☐ Pass ☐ Fail | |
| Analytics | ☐ Pass ☐ Fail | |
| Requests | ☐ Pass ☐ Fail | |
| Responsive Design | ☐ Pass ☐ Fail | |
| Performance | ☐ Pass ☐ Fail | |

---

**Happy Testing! 🚀**

**Remember:**
- Take your time
- Check every checkbox
- Document every bug
- Take screenshots of issues
- Note what works well too!

**Server Running At:** http://127.0.0.1:8000
**To Stop Server:** Press Ctrl+C in the terminal

---

**Testing Date:** __________________
**Tested By:** __________________
**Time Taken:** __________ minutes
