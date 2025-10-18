# 📍 Location Service Documentation

> **Complete Guide to Municipality and Barangay Management in Bukidnon Incident Management System**

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Quick Start](#quick-start)
3. [Documentation Files](#documentation-files)
4. [Installation](#installation)
5. [Usage Examples](#usage-examples)
6. [API Reference](#api-reference)
7. [Best Practices](#best-practices)
8. [FAQs](#faqs)
9. [Troubleshooting](#troubleshooting)
10. [Contributing](#contributing)

---

## 🎯 Overview

The **Location Service** is a comprehensive solution for managing municipalities and barangays in Bukidnon province. It replaces the old controller-based approach with a proper service-oriented architecture following Laravel best practices.

### What's Included

- ✅ **22 Municipalities** with complete barangay lists
- ✅ **Service Class** for business logic
- ✅ **API Endpoints** for dynamic data loading
- ✅ **Database Integration** with Incident model
- ✅ **Dynamic Forms** with AJAX barangay loading
- ✅ **Comprehensive Documentation** and examples

### Key Features

| Feature | Description |
|---------|-------------|
| **Centralized Data** | All location data in one config file |
| **Reusable Service** | Can be used anywhere in your app |
| **API Ready** | RESTful endpoints for external apps |
| **Type Safe** | Full PHP type hints |
| **Well Documented** | Multiple documentation files |
| **Backward Compatible** | Old code still works |

---

## 🚀 Quick Start

### 1. Basic Usage in Controller

```php
use App\Services\LocationService;

// Get all municipalities
$municipalities = LocationService::getMunicipalities();

// Get barangays for a municipality
$barangays = LocationService::getBarangays('Valencia City');

// Check if exists
if (LocationService::municipalityExists('Valencia City')) {
    // Do something
}
```

### 2. Usage in Blade View

```blade
<!-- Municipality Dropdown -->
<select name="municipality">
    @foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
        <option value="{{ $municipality }}">{{ $municipality }}</option>
    @endforeach
</select>
```

### 3. API Usage (JavaScript)

```javascript
// Get barangays for selected municipality
fetch('/api/barangays?municipality=Valencia City')
    .then(response => response.json())
    .then(data => {
        console.log(data.barangays);
    });
```

---

## 📚 Documentation Files

This project includes comprehensive documentation:

| File | Purpose | For Who |
|------|---------|---------|
| **README_LOCATION_SERVICE.md** | This file - overview and quick links | Everyone |
| **QUICK_REFERENCE.md** | Quick reference card with common tasks | Developers |
| **LOCATION_SERVICE_GUIDE.md** | Complete guide with all details | Developers |
| **REFACTORING_SUMMARY.md** | Why and how we refactored | Technical leads |
| **ARCHITECTURE_DIAGRAM.md** | Visual architecture diagrams | Architects |
| **examples/LocationServiceExamples.php** | 12 practical code examples | Developers |

### Reading Order

1. **New to the project?** Start with this README
2. **Need quick code?** Check `QUICK_REFERENCE.md`
3. **Building features?** See `examples/LocationServiceExamples.php`
4. **Need details?** Read `LOCATION_SERVICE_GUIDE.md`
5. **Understanding architecture?** View `ARCHITECTURE_DIAGRAM.md`
6. **Wondering why?** See `REFACTORING_SUMMARY.md`

---

## 💾 Installation

The Location Service is already installed and configured. However, if you're setting up a new environment:

### Step 1: Verify Files Exist

```bash
# Check if files exist
ls config/locations.php
ls app/Services/LocationService.php
```

### Step 2: Run Migration

```bash
# Add barangay field to incidents table
php artisan migrate
```

### Step 3: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Test

```bash
php artisan tinker
>>> \App\Services\LocationService::getMunicipalities()
```

---

## 💡 Usage Examples

### Example 1: Display Municipality Dropdown

```blade
<form>
    <select name="municipality" id="municipality">
        <option value="">Select Municipality</option>
        @foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
            <option value="{{ $municipality }}">{{ $municipality }}</option>
        @endforeach
    </select>
</form>
```

### Example 2: Dynamic Barangay Loading

```blade
<select name="municipality" id="municipality">
    @foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
        <option value="{{ $municipality }}">{{ $municipality }}</option>
    @endforeach
</select>

<select name="barangay" id="barangay" disabled>
    <option value="">Select municipality first</option>
</select>

<script>
document.getElementById('municipality').addEventListener('change', function() {
    const barangaySelect = document.getElementById('barangay');
    
    if (!this.value) {
        barangaySelect.disabled = true;
        return;
    }
    
    fetch(`/api/barangays?municipality=${encodeURIComponent(this.value)}`)
        .then(r => r.json())
        .then(data => {
            barangaySelect.innerHTML = '<option value="">Select barangay</option>';
            data.barangays.forEach(barangay => {
                barangaySelect.innerHTML += `<option value="${barangay}">${barangay}</option>`;
            });
            barangaySelect.disabled = false;
        });
});
</script>
```

### Example 3: Validate Location in Controller

```php
use App\Services\LocationService;
use Illuminate\Http\Request;

public function store(Request $request)
{
    // Basic validation
    $validated = $request->validate([
        'municipality' => 'required|string',
        'barangay' => 'required|string',
    ]);
    
    // Additional validation using LocationService
    if (!LocationService::municipalityExists($validated['municipality'])) {
        return back()->withErrors([
            'municipality' => 'The selected municipality is invalid.'
        ]);
    }
    
    if (!LocationService::barangayExists($validated['municipality'], $validated['barangay'])) {
        return back()->withErrors([
            'barangay' => 'The selected barangay is invalid for this municipality.'
        ]);
    }
    
    // Proceed with storing the incident
    $incident = Incident::create($validated);
    
    return redirect()->route('incidents.show', $incident);
}
```

### Example 4: Search Functionality

```php
use App\Services\LocationService;

public function search(Request $request)
{
    $query = $request->input('q');
    
    // Search municipalities
    $municipalities = LocationService::searchMunicipalities($query);
    
    // Search barangays in a specific municipality
    $municipality = $request->input('municipality');
    $barangays = LocationService::searchBarangays($municipality, $query);
    
    return response()->json([
        'municipalities' => $municipalities,
        'barangays' => $barangays
    ]);
}
```

---

## 🔌 API Reference

### Endpoints

| Method | Endpoint | Parameters | Description |
|--------|----------|------------|-------------|
| GET | `/api/municipalities` | None | Get all municipalities |
| GET | `/api/barangays` | `municipality` (required) | Get barangays for municipality |

### Response Format

#### Get Municipalities

**Request:**
```http
GET /api/municipalities
```

**Response:**
```json
{
    "success": true,
    "municipalities": [
        "Baungon",
        "Cabanglasan",
        "Damulog",
        "..."
    ]
}
```

#### Get Barangays

**Request:**
```http
GET /api/barangays?municipality=Valencia%20City
```

**Response:**
```json
{
    "success": true,
    "municipality": "Valencia City",
    "barangays": [
        "Bagontaas",
        "Balatukan",
        "Banlag",
        "..."
    ]
}
```

### Service Methods

| Method | Parameters | Return | Description |
|--------|------------|--------|-------------|
| `getMunicipalities()` | None | `array` | All municipalities |
| `getBarangays($muni)` | `string $municipality` | `array` | Barangays for municipality |
| `municipalityExists($muni)` | `string $municipality` | `bool` | Check if municipality exists |
| `barangayExists($muni, $brgy)` | `string $municipality, string $barangay` | `bool` | Check if barangay exists |
| `searchMunicipalities($q)` | `string $query` | `array` | Search municipalities |
| `searchBarangays($muni, $q)` | `string $municipality, string $query` | `array` | Search barangays |

See `LOCATION_SERVICE_GUIDE.md` for complete method documentation.

---

## ✅ Best Practices

### DO ✅

1. **Use LocationService methods**
   ```php
   // ✅ Good
   $municipalities = LocationService::getMunicipalities();
   ```

2. **Store location data in config**
   ```php
   // ✅ Edit config/locations.php
   'New Municipality' => ['Barangay 1', 'Barangay 2'],
   ```

3. **Validate user input**
   ```php
   // ✅ Good
   if (!LocationService::municipalityExists($municipality)) {
       // Handle error
   }
   ```

4. **Use API endpoints for dynamic loading**
   ```javascript
   // ✅ Good
   fetch('/api/barangays?municipality=' + municipality)
   ```

### DON'T ❌

1. **Don't hardcode municipalities in views**
   ```php
   // ❌ Bad
   <option value="Valencia City">Valencia City</option>
   <option value="Malaybalay City">Malaybalay City</option>
   ```

2. **Don't put location data in controllers**
   ```php
   // ❌ Bad
   public function getMunicipalities() {
       return ['Valencia City', 'Malaybalay City'];
   }
   ```

3. **Don't skip validation**
   ```php
   // ❌ Bad - accepting any value
   $incident->municipality = $request->municipality;
   ```

4. **Don't query database for static location data**
   ```php
   // ❌ Bad
   $municipalities = Municipality::all();
   ```

---

## ❓ FAQs

### Q: How do I add a new municipality?

**A:** Edit `config/locations.php`:

```php
'municipalities' => [
    // ... existing municipalities
    'New Municipality' => [
        'Barangay 1',
        'Barangay 2',
    ],
],
```

Then run: `php artisan config:clear`

### Q: How do I add a barangay to an existing municipality?

**A:** Edit `config/locations.php`:

```php
'Valencia City' => [
    'Bagontaas',
    'Balatukan',
    'New Barangay', // Add here
],
```

Then run: `php artisan config:clear`

### Q: Can I use LocationService in Jobs or Commands?

**A:** Yes! LocationService can be used anywhere:

```php
use App\Services\LocationService;

class ProcessIncidentJob
{
    public function handle()
    {
        $municipalities = LocationService::getMunicipalities();
        // Process...
    }
}
```

### Q: How do I migrate from the old IncidentController::municipalities()?

**A:** Replace:

```php
// Old
@foreach(IncidentController::municipalities() as $municipality)

// New
@foreach(\App\Services\LocationService::getMunicipalities() as $municipality)
```

### Q: Is the old method still available?

**A:** Yes, for backward compatibility, but it's deprecated. Update your code when possible.

### Q: Can I cache the location data?

**A:** Yes! See `LOCATION_SERVICE_GUIDE.md` section 10 for caching examples.

---

## 🔧 Troubleshooting

### Problem: Changes to config/locations.php not reflecting

**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
```

### Problem: API endpoint returns 404

**Solution:**
- Verify you're authenticated (routes are protected)
- Check route exists: `php artisan route:list | grep api`
- Ensure middleware allows access

### Problem: Barangays not loading in dropdown

**Solution:**
- Open browser console and check for JavaScript errors
- Verify API endpoint URL is correct
- Check if municipality name is exact match (case-sensitive)
- Ensure select element has correct ID

### Problem: Validation fails for valid municipality/barangay

**Solution:**
- Check spelling and case (must match exactly)
- Verify municipality exists in config
- Ensure barangay belongs to selected municipality

### Problem: Old values not restoring after validation error

**Solution:**
```blade
<!-- Add old() helper -->
<option value="{{ $municipality }}" {{ old('municipality') == $municipality ? 'selected' : '' }}>
```

---

## 🤝 Contributing

### Adding New Location Data

1. Fork or branch from main
2. Edit `config/locations.php`
3. Follow existing format
4. Test thoroughly
5. Submit PR with description

### Reporting Issues

Include:
- What you were trying to do
- What happened
- Error messages
- Steps to reproduce

### Suggesting Improvements

Open an issue with:
- Current behavior
- Proposed improvement
- Use case
- Example code if applicable

---

## 📞 Support

### Documentation
- **Quick Reference**: `QUICK_REFERENCE.md`
- **Full Guide**: `LOCATION_SERVICE_GUIDE.md`
- **Examples**: `examples/LocationServiceExamples.php`
- **Architecture**: `ARCHITECTURE_DIAGRAM.md`

### Testing
```bash
# Tinker
php artisan tinker
>>> \App\Services\LocationService::getMunicipalities()

# Test API
curl http://localhost:8000/api/municipalities
curl "http://localhost:8000/api/barangays?municipality=Valencia%20City"
```

### Commands
```bash
# Clear config cache
php artisan config:clear

# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# List routes
php artisan route:list

# Run migrations
php artisan migrate
```

---

## 📝 Summary

### What You Get

✅ **22 Municipalities** with complete barangay lists  
✅ **LocationService** with 12+ helper methods  
✅ **API Endpoints** for dynamic data loading  
✅ **Database Integration** with barangay field  
✅ **Dynamic Forms** with AJAX loading  
✅ **Comprehensive Documentation** (6 files)  
✅ **12 Usage Examples** for common scenarios  
✅ **Best Practices** and troubleshooting guide  

### Benefits

🎯 **Maintainable** - Easy to update and extend  
🎯 **Reusable** - Use anywhere in your application  
🎯 **Scalable** - Ready for future enhancements  
🎯 **Best Practice** - Follows Laravel conventions  
🎯 **Well Documented** - Extensive documentation  
🎯 **Tested** - Verified and working  

---

## 🎓 Next Steps

1. **Read the Quick Reference** for immediate use
2. **Review the Examples** for your specific use case
3. **Check the Architecture** to understand the system
4. **Implement in your features** using best practices
5. **Contribute improvements** when you find them

---

## 📄 License

This is part of the Bukidnon Incident Management System.

---

**Last Updated:** October 18, 2025  
**Version:** 1.0.0  
**Maintainer:** Development Team

For questions or support, refer to the documentation files or contact the development team.

