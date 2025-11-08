# Location Service Guide

## Overview

This guide explains the Location Service implementation for managing Bukidnon municipalities and barangays in the Incident Management System.

## Architecture

### Previous Implementation ❌
- **Location**: `IncidentController::municipalities()` static method
- **Issue**: Business logic mixed with controller
- **Maintainability**: Hard to update and reuse

### New Implementation ✅
- **Config File**: `config/locations.php` - Centralized data storage
- **Service Class**: `app/Services/LocationService.php` - Reusable business logic
- **Controller**: `IncidentController` - Only handles HTTP requests
- **Best Practice**: Separation of concerns

---

## Files Structure

```
├── app/
│   ├── Services/
│   │   └── LocationService.php         # Service class for location logic
│   ├── Http/Controllers/
│   │   └── IncidentController.php      # Controller with API endpoints
│   └── Models/
│       └── Incident.php                # Model with barangay field
├── config/
│   └── locations.php                   # Centralized location data
├── database/migrations/
│   └── 2025_10_18_060407_add_barangay_to_incidents_table.php
└── resources/views/Incident/
    └── create.blade.php                # Form with dynamic barangay loading
```

---

## 1. Configuration File

**File**: `config/locations.php`

Contains all municipalities and their barangays in a structured array format.

```php
return [
    'municipalities' => [
        'Valencia City' => [
            'Bagontaas',
            'Balatukan',
            // ... more barangays
        ],
        'Malaybalay City' => [
            'Aglayan',
            'Bangcud',
            // ... more barangays
        ],
        // ... more municipalities
    ],
];
```

### To Add/Update Locations:
1. Open `config/locations.php`
2. Add/modify municipality or barangays
3. No code changes needed!

---

## 2. LocationService Class

**File**: `app/Services/LocationService.php`

Provides static methods to access location data.

### Available Methods:

#### Get All Municipalities
```php
use App\Services\LocationService;

$municipalities = LocationService::getMunicipalities();
// Returns: ['Baungon', 'Cabanglasan', 'Damulog', ...]
```

#### Get Barangays for a Municipality
```php
$barangays = LocationService::getBarangays('Valencia City');
// Returns: ['Bagontaas', 'Balatukan', 'Banlag', ...]
```

#### Get Municipalities for Select Dropdown
```php
$options = LocationService::getMunicipalitiesForSelect();
// Returns: ['Baungon' => 'Baungon', 'Cabanglasan' => 'Cabanglasan', ...]
```

#### Get Barangays for Select Dropdown
```php
$options = LocationService::getBarangaysForSelect('Valencia City');
// Returns: ['Bagontaas' => 'Bagontaas', 'Balatukan' => 'Balatukan', ...]
```

#### Check if Municipality Exists
```php
$exists = LocationService::municipalityExists('Valencia City');
// Returns: true or false
```

#### Check if Barangay Exists in Municipality
```php
$exists = LocationService::barangayExists('Valencia City', 'Bagontaas');
// Returns: true or false
```

#### Search Municipalities
```php
$results = LocationService::searchMunicipalities('val');
// Returns: ['Valencia City']
```

#### Search Barangays
```php
$results = LocationService::searchBarangays('Valencia City', 'bag');
// Returns: ['Bagontaas']
```

---

## 3. Controller API Endpoints

### Get All Municipalities
**Endpoint**: `GET /api/municipalities`

**Response**:
```json
{
    "success": true,
    "municipalities": [
        "Baungon",
        "Cabanglasan",
        "Damulog",
        ...
    ]
}
```

### Get Barangays for Municipality
**Endpoint**: `GET /api/barangays?municipality=Valencia City`

**Parameters**:
- `municipality` (required): Municipality name

**Response**:
```json
{
    "success": true,
    "municipality": "Valencia City",
    "barangays": [
        "Bagontaas",
        "Balatukan",
        "Banlag",
        ...
    ]
}
```

---

## 4. Using in Blade Views

### Simple Dropdown (Static)
```blade
<select name="municipality">
    <option value="">Select municipality</option>
    @foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
        <option value="{{ $municipality }}">{{ $municipality }}</option>
    @endforeach
</select>
```

### Dynamic Barangay Loading (AJAX)
```blade
<select name="municipality" id="municipality-select">
    <option value="">Select municipality</option>
    @foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
        <option value="{{ $municipality }}">{{ $municipality }}</option>
    @endforeach
</select>

<select name="barangay" id="barangay-select" disabled>
    <option value="">Select municipality first</option>
</select>

<script>
document.getElementById('municipality-select').addEventListener('change', function() {
    const municipality = this.value;
    const barangaySelect = document.getElementById('barangay-select');
    
    if (!municipality) {
        barangaySelect.innerHTML = '<option value="">Select municipality first</option>';
        barangaySelect.disabled = true;
        return;
    }
    
    barangaySelect.innerHTML = '<option value="">Loading...</option>';
    
    fetch(`/api/barangays?municipality=${encodeURIComponent(municipality)}`)
        .then(response => response.json())
        .then(data => {
            barangaySelect.innerHTML = '<option value="">Select barangay</option>';
            data.barangays.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay;
                option.textContent = barangay;
                barangaySelect.appendChild(option);
            });
            barangaySelect.disabled = false;
        });
});
</script>
```

---

## 5. Using in Controllers

### Example: Creating an Incident
```php
use App\Services\LocationService;

public function store(Request $request)
{
    $validated = $request->validate([
        'municipality' => 'required|string',
        'barangay' => 'required|string',
        // ... other fields
    ]);
    
    // Optional: Validate municipality and barangay exist
    if (!LocationService::municipalityExists($validated['municipality'])) {
        return back()->withErrors(['municipality' => 'Invalid municipality']);
    }
    
    if (!LocationService::barangayExists($validated['municipality'], $validated['barangay'])) {
        return back()->withErrors(['barangay' => 'Invalid barangay']);
    }
    
    $incident = Incident::create($validated);
    
    return redirect()->route('incidents.show', $incident);
}
```

---

## 6. Database Schema

### Migration: Add Barangay Field
```php
Schema::table('incidents', function (Blueprint $table) {
    $table->string('barangay')->nullable()->after('municipality');
});
```

### Incident Model Fillable Fields
```php
protected $fillable = [
    // ... other fields
    'municipality',
    'barangay',
    // ... other fields
];
```

---

## 7. Best Practices

### ✅ DO:
- Use `LocationService` methods to access location data
- Keep location data in `config/locations.php`
- Use API endpoints for dynamic loading
- Validate municipality and barangay in form requests

### ❌ DON'T:
- Hard-code municipality/barangay lists in views
- Mix location logic with controller logic
- Store location data in database (unless dynamic)
- Skip validation for municipality/barangay

---

## 8. Migration from Old Code

### Old Code (Deprecated):
```php
// In Controller
public static function municipalities() {
    return [
        'Valencia City' => 'Valencia City',
        'Malaybalay City' => 'Malaybalay City',
        // ...
    ];
}

// In View
@foreach(IncidentController::municipalities() as $municipality)
    <option value="{{ $municipality }}">{{ $municipality }}</option>
@endforeach
```

### New Code:
```php
// In View (or Controller)
@foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
    <option value="{{ $municipality }}">{{ $municipality }}</option>
@endforeach
```

**Note**: The old `IncidentController::municipalities()` method is still available for backward compatibility but marked as deprecated.

---

## 9. Testing

### Test LocationService Methods
```php
use App\Services\LocationService;

// Get municipalities
$municipalities = LocationService::getMunicipalities();
dd($municipalities);

// Get barangays
$barangays = LocationService::getBarangays('Valencia City');
dd($barangays);

// Check existence
$exists = LocationService::municipalityExists('Valencia City');
dd($exists); // true

$exists = LocationService::barangayExists('Valencia City', 'Bagontaas');
dd($exists); // true
```

### Test API Endpoints
```bash
# Get municipalities
curl http://your-app.test/api/municipalities

# Get barangays
curl "http://your-app.test/api/barangays?municipality=Valencia%20City"
```

---

## 10. Future Enhancements

### Suggested Improvements:
1. **Caching**: Cache location data for better performance
2. **Database Storage**: Move to database for dynamic management
3. **Admin Panel**: Create CRUD for municipalities/barangays
4. **Validation Rules**: Custom validation rule `ValidBarangay`
5. **Localization**: Support multiple languages

### Example: Adding Caching
```php
public static function getMunicipalities(): array
{
    return cache()->remember('municipalities', 3600, function () {
        return array_keys(config('locations.municipalities'));
    });
}
```

---

## 11. Troubleshooting

### Issue: Barangays not loading
**Solution**: Check if municipality name matches exactly (case-sensitive)

### Issue: Config changes not reflecting
**Solution**: Clear config cache
```bash
php artisan config:clear
```

### Issue: API endpoint returns 404
**Solution**: Check if routes are registered and user is authenticated

### Issue: Old values not restoring
**Solution**: Check Blade syntax for `old()` helper in select options

---

## 12. Summary

### What Changed:
1. ✅ Created `config/locations.php` for centralized location data
2. ✅ Created `app/Services/LocationService.php` for business logic
3. ✅ Added API endpoints: `/api/municipalities` and `/api/barangays`
4. ✅ Added `barangay` field to incidents table and model
5. ✅ Updated incident forms with dynamic barangay loading
6. ✅ Updated validation rules to include barangay

### Benefits:
- **Maintainable**: Easy to update locations
- **Reusable**: Service can be used anywhere
- **Scalable**: Easy to add features (caching, database, etc.)
- **Best Practice**: Proper separation of concerns
- **User-Friendly**: Dynamic barangay loading

---

## Support

For questions or issues, please contact the development team or refer to the Laravel documentation:
- [Laravel Services](https://laravel.com/docs/10.x/container#introduction)
- [Laravel Configuration](https://laravel.com/docs/10.x/configuration)
- [Laravel Validation](https://laravel.com/docs/10.x/validation)

