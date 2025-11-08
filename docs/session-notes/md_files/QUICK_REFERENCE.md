# Location Service - Quick Reference Card

## 🚀 Quick Start

### In Blade Views
```php
// Get all municipalities
@foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
    <option value="{{ $municipality }}">{{ $municipality }}</option>
@endforeach

// Get barangays (in PHP block)
@php
    $barangays = \App\Services\LocationService::getBarangays('Valencia City');
@endphp
```

### In Controllers
```php
use App\Services\LocationService;

// Get municipalities
$municipalities = LocationService::getMunicipalities();

// Get barangays
$barangays = LocationService::getBarangays('Valencia City');

// Check existence
$exists = LocationService::municipalityExists('Valencia City');
$exists = LocationService::barangayExists('Valencia City', 'Bagontaas');
```

### API Endpoints
```
GET /api/municipalities
GET /api/barangays?municipality=Valencia+City
```

---

## 📦 Available Methods

| Method | Return Type | Description |
|--------|-------------|-------------|
| `getMunicipalities()` | `array` | All municipalities |
| `getBarangays($municipality)` | `array` | Barangays for municipality |
| `municipalityExists($municipality)` | `bool` | Check if municipality exists |
| `barangayExists($muni, $brgy)` | `bool` | Check if barangay exists |
| `searchMunicipalities($query)` | `array` | Search municipalities |
| `searchBarangays($muni, $query)` | `array` | Search barangays |
| `getMunicipalitiesForSelect()` | `array` | Key-value pairs for select |
| `getBarangaysForSelect($muni)` | `array` | Key-value pairs for select |
| `getAllMunicipalitiesWithBarangays()` | `array` | Full nested array |

---

## 🎯 Common Use Cases

### 1. Simple Dropdown
```blade
<select name="municipality">
    @foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
        <option value="{{ $municipality }}">{{ $municipality }}</option>
    @endforeach
</select>
```

### 2. Dynamic Barangay Loading
```javascript
fetch('/api/barangays?municipality=' + municipality)
    .then(r => r.json())
    .then(data => {
        // data.barangays contains the array
    });
```

### 3. Validation in Controller
```php
if (!LocationService::municipalityExists($municipality)) {
    return back()->withErrors(['municipality' => 'Invalid municipality']);
}
```

### 4. Search Autocomplete
```php
public function autocomplete(Request $request) {
    $results = LocationService::searchMunicipalities($request->input('q'));
    return response()->json($results);
}
```

---

## 📁 File Structure
```
config/
  └── locations.php              # Data storage

app/
  ├── Services/
  │   └── LocationService.php    # Business logic
  ├── Http/Controllers/
  │   └── IncidentController.php # API endpoints
  └── Models/
      └── Incident.php           # With barangay field

database/migrations/
  └── 2025_10_18_*_add_barangay_to_incidents_table.php
```

---

## 🔧 Updating Data

### Add a Municipality
```php
// config/locations.php
'New Municipality' => [
    'Barangay 1',
    'Barangay 2',
],
```

### Add a Barangay
```php
// config/locations.php
'Existing Municipality' => [
    'Existing Barangay 1',
    'New Barangay',  // Add here
],
```

### Clear Cache After Changes
```bash
php artisan config:clear
```

---

## 🧪 Testing

### In Tinker
```bash
php artisan tinker

>>> \App\Services\LocationService::getMunicipalities()
>>> \App\Services\LocationService::getBarangays('Valencia City')
```

### API Test
```bash
curl http://your-app.test/api/municipalities
curl "http://your-app.test/api/barangays?municipality=Valencia%20City"
```

---

## ⚠️ Common Issues

### Issue: Changes not reflecting
```bash
php artisan config:clear
php artisan cache:clear
```

### Issue: API returns empty
- Check if user is authenticated (routes are protected)
- Verify municipality name is exact match

### Issue: JavaScript not loading barangays
- Check browser console for errors
- Verify API endpoint URL is correct
- Check if municipality select has correct ID

---

## 📚 Need More Help?

- **Full Guide**: See `LOCATION_SERVICE_GUIDE.md`
- **Examples**: See `examples/LocationServiceExamples.php`
- **Summary**: See `REFACTORING_SUMMARY.md`

---

## 🎓 Best Practices

✅ **DO:**
- Use `LocationService` methods
- Store data in `config/locations.php`
- Validate municipality/barangay combinations
- Use API endpoints for dynamic loading

❌ **DON'T:**
- Hard-code municipality lists
- Put location data in controllers
- Skip validation
- Query database for static location data

---

**Quick Support:**
- Check documentation files in project root
- Run `php artisan config:clear` after config changes
- Use `php artisan tinker` to test methods
- Check routes: `php artisan route:list | grep api`

