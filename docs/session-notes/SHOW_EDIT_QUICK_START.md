# 🚀 Show & Edit Page Quick Start Guide

## What Was Enhanced?

### 📄 Show Page (Incident Details)
Now displays **incident-type specific information** with:
- Traffic accidents → Vehicle count, license plates, driver info
- Medical emergencies → Patient count, symptoms, ambulance status
- Fire incidents → Building type, fire spread, evacuation details
- Natural disasters → Affected area, families, infrastructure damage
- Criminal activities → Crime type, police notification, case number

**Plus:**
- Enhanced victim cards with medical details, vital signs, pregnancy info
- Photo gallery with lightbox (click to zoom)
- Video player for incident videos
- Interactive timeline showing incident lifecycle
- Quick actions sidebar (edit, print, navigate)
- Print-optimized layout for PDF export

### ✏️ Edit Page
Now has **conditional form sections** based on incident type:
- Select incident type → relevant fields appear automatically
- All new incident-type specific fields included
- Supports video uploads
- Barangay field added
- License plate processing (comma-separated)

---

## 🎯 How to Test

### 1. View Enhanced Incident Details

```bash
# Navigate to any incident
http://localhost:8000/incidents/{id}
```

**What to check:**
- Incident-specific details card appears based on type
- Victim cards show medical information
- Photos appear in grid (click to zoom)
- Videos play inline
- Timeline shows incident events
- Quick actions work (edit, print)

### 2. Edit an Incident

```bash
# Edit any incident
http://localhost:8000/incidents/{id}/edit
```

**What to check:**
- Change incident type → different fields appear
- All fields retain their values
- Validation works on submit
- License plates saved correctly (comma-separated input)

### 3. Create Traffic Accident

```bash
# Create new incident
http://localhost:8000/incidents/create
```

**Steps:**
1. Select incident type: **Traffic Accident**
2. Fill basic info
3. **Traffic Accident Details** section appears
4. Enter: Vehicle count, license plates (e.g., "ABC-123, XYZ-456"), driver info
5. Add victim (optional)
6. Upload photos/videos
7. Submit

**Expected Result:**
- Incident created with traffic-specific data
- Show page displays vehicle details card
- License plates appear as badges

### 4. Create Medical Emergency with Pregnancy

```bash
# Create new incident
http://localhost:8000/incidents/create
```

**Steps:**
1. Select incident type: **Medical Emergency**
2. Fill basic info
3. **Medical Emergency Details** section appears
4. Select emergency type, patient count, check "Ambulance Requested"
5. Add victim:
   - Select gender: **Female**
   - Check **"Is Pregnant?"**
   - Pregnancy fields appear (trimester, complications)
   - Add vital signs (blood pressure, heart rate)
6. Submit

**Expected Result:**
- Incident created with medical emergency data
- Show page displays:
  - Medical emergency details card
  - Victim card with pregnancy information highlighted
  - Vital signs displayed in blue card

### 5. Test Print Function

```bash
# View any incident
http://localhost:8000/incidents/{id}
```

**Steps:**
1. Click **Print** button (top right or sidebar)
2. Browser print dialog opens
3. Check print preview

**Expected Result:**
- Clean layout without buttons
- All information visible
- Professional format for PDF export

---

## 🔍 Key Features to Explore

### Incident Show Page

#### 1. **Incident-Type Specific Cards**
Each incident type shows a custom card:
- 🚗 Traffic: Red car-crash icon, vehicle details
- 🚑 Medical: Red ambulance icon, patient info
- 🔥 Fire: Orange fire icon, evacuation status
- 🌊 Disaster: Blue cloud icon, affected area statistics
- 🛡️ Crime: Red shield icon, police information

#### 2. **Enhanced Victim Cards**
Color-coded by status:
- **Red border**: Critical condition
- **Gray border**: Deceased
- **White**: Stable/Treated

Shows:
- Age with category badge (child, teen, adult, elderly)
- Blood type in red
- Pregnancy information in purple card
- Vital signs in blue card (blood pressure, heart rate, temperature)
- Medical history in yellow card (allergies, conditions, medications)

#### 3. **Media Gallery**
- **Photos**: Grid layout, click to view full size in lightbox
- **Videos**: Inline player with controls
- Responsive (adapts to screen size)

#### 4. **Interactive Timeline**
Shows:
- Incident reported (blue circle)
- Staff assigned (green check)
- Vehicle dispatched (orange truck)
- Incident resolved (green checkmark)

#### 5. **Quick Actions**
- Edit incident details
- Add victim/patient
- Print report
- Navigate to location (Google Maps)

### Incident Edit Page

#### 1. **Conditional Form Sections**
Change incident type to see different fields appear:
- **Traffic Accident**: Vehicle count, license plates, driver info
- **Medical Emergency**: Emergency type, patient count, ambulance checkbox
- **Fire**: Building type, fire spread, evacuation checkbox
- **Disaster**: Disaster type, affected area, shelter checkbox
- **Crime**: Crime type, police notification, case number

#### 2. **Smart Field Processing**
- **License Plates**: Enter comma-separated, saved as array
- **Checkboxes**: One-click enable/disable
- **File Uploads**: Photos (up to 5), videos (up to 2)

---

## 📝 Example Scenarios

### Scenario 1: Multi-Vehicle Traffic Accident

```
1. Create Incident
   - Type: Traffic Accident
   - Severity: High
   - Location: National Highway, Malaybalay City
   - Description: Head-on collision

2. Traffic Details
   - Vehicle Count: 3
   - License Plates: ABC-1234, XYZ-5678, DEF-9012
   - Driver Info: Driver 1 (ABC-1234): John Doe, License #123456...

3. Add Victims
   - Victim 1: Jane Smith, 35, Female, Injured, Stable
   - Victim 2: Bob Jones, 42, Male, Critical, Head trauma

4. Upload Media
   - 3 photos of accident scene
   - 1 video showing vehicle positions

Result: Comprehensive incident with all traffic-specific data
```

### Scenario 2: Pregnant Woman Medical Emergency

```
1. Create Incident
   - Type: Medical Emergency
   - Severity: Critical
   - Emergency Type: Labor/Delivery complications

2. Medical Details
   - Patient Count: 1
   - Ambulance Requested: ✓
   - Symptoms: Severe abdominal pain, bleeding

3. Add Patient
   - Name: Maria Santos, 28, Female
   - Is Pregnant: ✓
   - Trimester: Third
   - Expected Delivery: 2025-11-15
   - Complications: Premature labor
   - Vital Signs:
     * Blood Pressure: 140/90
     * Heart Rate: 110 bpm
     * Temperature: 37.8°C
   - Blood Type: O+
   - Allergies: Penicillin

Result: Detailed medical record with pregnancy focus
```

### Scenario 3: Fire with Evacuation

```
1. Create Incident
   - Type: Fire
   - Severity: Critical
   - Location: Commercial District, Valencia City

2. Fire Details
   - Building Type: Commercial
   - Fire Spread: Widespread
   - Evacuation Required: ✓
   - People Evacuated: 45
   - Buildings Affected: 3
   - Suspected Cause: Electrical short circuit

3. Add Victims
   - 2 minor injuries from smoke inhalation
   - 1 critical burn victim

Result: Complete fire incident with evacuation tracking
```

---

## 🎨 Visual Features

### Status Badges
- 🔴 **Critical**: Red badge
- 🟠 **High**: Orange badge
- 🔵 **Medium**: Blue badge
- 🟢 **Low**: Green badge

### Incident Type Icons
- 🚗 **Traffic**: fa-car-crash
- 🚑 **Medical**: fa-ambulance
- 🔥 **Fire**: fa-fire
- 🌊 **Disaster**: fa-cloud-showers-heavy
- 🛡️ **Crime**: fa-shield-alt

### Victim Status Colors
- **Critical**: Red border + red badge
- **Stable**: Green badge
- **Treated**: Blue badge
- **Deceased**: Gray border + gray badge

---

## ⚠️ Common Issues & Solutions

### Issue 1: Fields not appearing when changing incident type
**Solution**: Refresh the page. Server-side rendering requires page reload when changing incident type on edit page.

### Issue 2: License plates not saving as array
**Solution**: Ensure you're using commas to separate plates: "ABC-123, XYZ-456"

### Issue 3: Lightbox not opening for photos
**Solution**: Ensure JavaScript is enabled. Check browser console for errors.

### Issue 4: Video not playing
**Solution**: 
- Check video format (supported: MP4, MOV, AVI, WMV)
- Ensure file size < 10MB
- Try different browser

### Issue 5: Print layout looks wrong
**Solution**: Use Chrome or Firefox for best print results. Check print preview before printing.

---

## 📊 Database Changes

**New Tables/Fields:**
- `incidents` table: 24 new fields (vehicle_count, medical_emergency_type, etc.)
- `victims` table: 18 new fields (is_pregnant, blood_pressure, vital signs, etc.)

**Migrations Applied:**
```bash
✓ 2025_10_18_145839_add_medical_fields_to_victims_table
✓ 2025_10_18_145911_add_incident_type_fields_to_incidents_table
```

---

## 🚦 Quick Testing Checklist

### Show Page
- [ ] Incident-type card displays correctly
- [ ] Victim cards show all information
- [ ] Photos open in lightbox
- [ ] Videos play
- [ ] Timeline displays events
- [ ] Print button works
- [ ] Mobile responsive

### Edit Page
- [ ] Incident type changes show/hide fields
- [ ] All fields save correctly
- [ ] Validation works
- [ ] File uploads work
- [ ] Redirects to show page after save

---

## 🎓 Tips for Users

1. **Always fill incident-specific fields** for better reporting
2. **Add victim details immediately** for medical emergencies
3. **Upload photos/videos** as evidence
4. **Use print function** to share reports with stakeholders
5. **Update status regularly** using quick update form

---

## 📞 Need Help?

- Check `SHOW_EDIT_ENHANCEMENT_SUMMARY.md` for detailed documentation
- Review `IMPLEMENTATION_PROGRESS.md` for technical details
- Check Laravel logs: `storage/logs/laravel.log`

---

**Ready to test!** 🚀

Navigate to: `http://localhost:8000/incidents` and explore the enhanced features!

