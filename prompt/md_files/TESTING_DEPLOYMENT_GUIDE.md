# BukidnonAlert: Testing & Deployment Guide

## 🚀 Quick Start - Run This First!

### **Step 1: Run Migrations** 🔥 **CRITICAL**

```powershell
# Navigate to project
cd "d:\1_Capstone_Project Laravel\capstone_project"

# Run migrations (adds new fields)
php artisan migrate

# Expected output:
# Migrating: 2025_10_18_145839_add_medical_fields_to_victims_table
# Migrated:  2025_10_18_145839_add_medical_fields_to_victims_table (XX.XXms)
# Migrating: 2025_10_18_145911_add_incident_type_fields_to_incidents_table
# Migrated:  2025_10_18_145911_add_incident_type_fields_to_incidents_table (XX.XXms)
```

### **Step 2: Clear All Caches**

```powershell
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### **Step 3: Verify Installation**

```powershell
# Check if migrations ran successfully
php artisan migrate:status

# You should see:
# ✓ 2025_10_18_145839_add_medical_fields_to_victims_table
# ✓ 2025_10_18_145911_add_incident_type_fields_to_incidents_table
```

---

## 🧪 Testing the New Incident Reporting System

### **Test 1: Traffic Accident Report**

1. **Navigate to Create Incident**
   - Go to: `/incidents/create`
   - You should see the new clean form with progress steps

2. **Fill Basic Information**
   - **Incident Type**: Select "Traffic Accident"
   - **Severity**: Select any level
   - **Date/Time**: Pick current date/time
   - **Municipality**: Select any municipality
   - **Barangay**: Wait for it to load, then select
   - **Location**: Enter detailed location
   - **Description**: Enter at least 20 characters

3. **Verify Conditional Display**
   - ✅ Traffic Accident section should appear automatically
   - ✅ Check "Vehicle(s) involved"
   - ✅ Vehicle details fields should show
   - Enter vehicle count: 2
   - Enter license plates: ABC-1234, XYZ-5678
   - Enter vehicle details
   - Select road condition and weather

4. **Upload Media**
   - Upload 1-5 photos (max 2MB each)
   - Verify preview shows correctly
   - Optionally upload 1-2 videos

5. **Submit**
   - Click "Submit Incident Report"
   - Should redirect to incident details page
   - Check incident number format: INC-2025-XXX

**Expected Result:** ✅ Incident created with vehicle-specific fields populated

---

### **Test 2: Medical Emergency Report**

1. **Create New Incident**
   - **Incident Type**: Select "Medical Emergency"
   - Verify Medical Emergency section appears

2. **Fill Medical Fields**
   - **Emergency Type**: Select "Heart Attack"
   - **Patient Count**: Enter 1
   - Check "Ambulance Requested"
   - Enter patient symptoms

3. **Fill Casualty Information**
   - Casualties: 1
   - Injuries: 1
   - Fatalities: 0

4. **Upload Photos & Submit**

**Expected Result:** ✅ Incident created with medical emergency fields

---

### **Test 3: Fire Incident Report**

1. **Create New Incident**
   - **Incident Type**: Select "Fire Incident"
   - Verify Fire Incident section appears

2. **Fill Fire-Specific Fields**
   - **Building Type**: Select "Residential"
   - **Fire Spread Level**: Select "Spreading"
   - **Buildings Affected**: Enter 3
   - Check "Evacuation Required"
   - Verify evacuated count field appears
   - Enter evacuated count: 50
   - Enter fire cause description

3. **Fill Property Damage**
   - Estimate: 500000
   - Damage description: Enter details

4. **Submit**

**Expected Result:** ✅ Incident created with fire-specific fields

---

### **Test 4: Natural Disaster Report**

1. **Create New Incident**
   - **Incident Type**: Select "Natural Disaster"
   - Verify Natural Disaster section appears

2. **Fill Disaster-Specific Fields**
   - **Disaster Type**: Select "Flood"
   - **Affected Area Size**: 5.5 (sq km)
   - **Families Affected**: 150
   - **Structures Damaged**: 75
   - Check "Shelter Needed"
   - Enter infrastructure damage description

3. **Submit**

**Expected Result:** ✅ Incident created with disaster-specific fields

---

### **Test 5: Criminal Activity Report**

1. **Create New Incident**
   - **Incident Type**: Select "Criminal Activity"
   - Verify Criminal Activity section appears

2. **Fill Crime-Specific Fields**
   - **Crime Type**: Select "Theft/Robbery"
   - Check "Police Notified"
   - Verify case number field appears
   - Enter case number: 2025-001234
   - Enter suspect description

3. **Submit**

**Expected Result:** ✅ Incident created with crime-specific fields

---

## ✅ Validation Testing

### **Test Error Handling**

1. **Required Fields Missing**
   - Try submitting form without incident type
   - Expected: Error message "Please select an incident type."

2. **Invalid Date**
   - Enter future date
   - Expected: Error "Incident date cannot be in the future."

3. **No Photos**
   - Try submitting without photos
   - Expected: Error "Please upload at least one photo of the incident."

4. **Too Many Photos**
   - Try uploading 6 photos
   - Expected: Error "Maximum 5 photos allowed."

5. **File Size Exceeded**
   - Try uploading a photo larger than 2MB
   - Expected: Error showing file name and size limit

6. **Conditional Validation**
   - Select "Traffic Accident"
   - Check "Vehicle(s) involved"
   - Don't fill vehicle details
   - Expected: Error "Vehicle details required when vehicle is involved"

---

## 🔍 Database Verification

### **Check Incident Data**

```sql
-- View latest incident with all fields
SELECT * FROM incidents ORDER BY id DESC LIMIT 1;

-- Check traffic accident fields
SELECT 
    incident_number,
    incident_type,
    vehicle_count,
    license_plates,
    driver_information
FROM incidents 
WHERE incident_type = 'traffic_accident'
ORDER BY id DESC LIMIT 1;

-- Check medical emergency fields
SELECT 
    incident_number,
    medical_emergency_type,
    ambulance_requested,
    patient_count,
    patient_symptoms
FROM incidents 
WHERE incident_type = 'medical_emergency'
ORDER BY id DESC LIMIT 1;

-- Check fire incident fields
SELECT 
    incident_number,
    building_type,
    fire_spread_level,
    evacuation_required,
    evacuated_count,
    fire_cause
FROM incidents 
WHERE incident_type = 'fire_incident'
ORDER BY id DESC LIMIT 1;
```

### **Check Victim Fields (After Adding Victims)**

```sql
SELECT 
    first_name,
    last_name,
    age,
    age_category,
    gender,
    is_pregnant,
    pregnancy_trimester,
    requires_special_care,
    blood_pressure,
    heart_rate,
    temperature
FROM victims 
ORDER BY id DESC LIMIT 1;
```

---

## 🐛 Troubleshooting

### **Migration Errors**

**Error: "Column already exists"**
```powershell
# Rollback last migrations
php artisan migrate:rollback

# Re-run migrations
php artisan migrate
```

**Error: "SQLSTATE[42S21]: Column already exists"**
```powershell
# Check which migrations have run
php artisan migrate:status

# If needed, rollback specific migration
php artisan migrate:rollback --step=1
```

### **Form Not Displaying Correctly**

**Issue: Conditional sections not showing**
1. Check browser console for JavaScript errors
2. Verify `incident_type` select has id="incident_type"
3. Clear browser cache (Ctrl+F5)

**Issue: Barangays not loading**
1. Check network tab in browser DevTools
2. Verify route `/api/barangays` exists
3. Check JavaScript console for fetch errors

### **Validation Errors**

**Issue: Form always shows "Field is required" even when filled**
1. Check field names match validation rules
2. Verify `old()` helper is working
3. Check `@error` directives match field names

### **File Upload Issues**

**Issue: Photos not uploading**
1. Check `php.ini` settings:
   - `upload_max_filesize = 10M`
   - `post_max_size = 20M`
   - `max_file_uploads = 20`
2. Verify storage is linked:
   ```powershell
   php artisan storage:link
   ```
3. Check file permissions on `storage/app/public/`

---

## 📊 Performance Testing

### **Load Testing**

```powershell
# Test incident creation time
php artisan tinker

>>> $start = microtime(true);
>>> $incident = App\Models\Incident::create([
...     'incident_number' => 'TEST-001',
...     'incident_type' => 'traffic_accident',
...     'severity_level' => 'medium',
...     'status' => 'pending',
...     'location' => 'Test Location',
...     'municipality' => 'Malaybalay City',
...     'barangay' => 'Poblacion',
...     'description' => 'Test incident for performance testing',
...     'incident_date' => now(),
...     'reported_by' => 1,
...     'photos' => ['test.jpg'],
... ]);
>>> $end = microtime(true);
>>> echo "Time: " . ($end - $start) . " seconds";
```

**Expected:** < 0.1 seconds for database insert

---

## 🔒 Security Testing

### **Test Access Controls**

1. **Unauthenticated Access**
   - Logout
   - Try accessing `/incidents/create`
   - Expected: Redirect to login

2. **Municipality Isolation**
   - Login as staff from Municipality A
   - Create incident in Municipality A
   - Login as staff from Municipality B
   - Try accessing incident from Municipality A
   - Expected: 403 Forbidden

3. **Role-Based Features**
   - Login as Responder
   - Check if assignment fields are hidden
   - Expected: Assignment section not visible

---

## 📝 Acceptance Criteria Checklist

### **Incident Creation**
- [ ] Form loads without errors
- [ ] All incident types display correctly
- [ ] Conditional fields show/hide properly
- [ ] Municipality → Barangay cascade works
- [ ] GPS location capture works
- [ ] Photo upload and preview works
- [ ] Video upload and preview works
- [ ] Form validation works correctly
- [ ] Error messages are clear
- [ ] Success redirect works
- [ ] Incident number generated correctly
- [ ] Activity log recorded

### **Conditional Display**
- [ ] Traffic Accident fields show for traffic_accident
- [ ] Medical Emergency fields show for medical_emergency
- [ ] Fire Incident fields show for fire_incident
- [ ] Natural Disaster fields show for natural_disaster
- [ ] Criminal Activity fields show for criminal_activity
- [ ] Environmental conditions show for applicable types

### **Data Integrity**
- [ ] All fields save to correct database columns
- [ ] JSON fields (license_plates) save correctly
- [ ] Boolean fields save as 1/0
- [ ] Decimal fields save with correct precision
- [ ] Photos array stores file paths correctly
- [ ] Videos array stores file paths correctly

### **User Experience**
- [ ] Form is intuitive and easy to use
- [ ] Progress steps update correctly
- [ ] File upload provides feedback
- [ ] Validation errors are inline
- [ ] Form state preserved on error
- [ ] Loading states shown appropriately

---

## 🚀 Deployment Checklist

### **Pre-Deployment**
- [ ] Run all tests successfully
- [ ] Check for linter errors
- [ ] Review activity logs
- [ ] Backup database
- [ ] Document any custom configurations

### **Deployment Steps**
1. [ ] Pull latest code
2. [ ] Run `composer install --optimize-autoloader --no-dev`
3. [ ] Run `php artisan migrate`
4. [ ] Run `php artisan config:cache`
5. [ ] Run `php artisan route:cache`
6. [ ] Run `php artisan view:cache`
7. [ ] Clear application cache
8. [ ] Test critical paths
9. [ ] Monitor error logs

### **Post-Deployment**
- [ ] Verify incident creation works
- [ ] Check all incident types
- [ ] Test file uploads
- [ ] Verify conditional display
- [ ] Check activity logs
- [ ] Monitor performance
- [ ] Review error logs

---

## 📞 Support & Issues

### **Common Issues**

**Issue: "Class 'App\Services\IncidentService' not found"**
```powershell
composer dump-autoload
php artisan config:clear
```

**Issue: "Route [api.barangays] not defined"**
```powershell
php artisan route:clear
php artisan route:cache
```

**Issue: "Column not found: 1054 Unknown column"**
```powershell
# Migrations not run
php artisan migrate

# Or check migration status
php artisan migrate:status
```

---

## 🎯 Next Phase Testing (After Victim Management)

### **Test Victim Creation**
1. Create incident
2. Add victim with pregnancy details
3. Verify age_category auto-calculated
4. Verify requires_special_care auto-flagged
5. Check casualty counts updated

### **Test Medical Emergency Flow**
1. Create medical emergency incident
2. Add female victim
3. Check "Is pregnant"
4. Verify trimester and complications fields appear
5. Enter vitals (BP, HR, Temp, RR)
6. Submit and verify all saved

---

**Document Version**: 1.0  
**Created**: October 18, 2025  
**Last Updated**: After controller integration  
**Status**: Ready for Testing

