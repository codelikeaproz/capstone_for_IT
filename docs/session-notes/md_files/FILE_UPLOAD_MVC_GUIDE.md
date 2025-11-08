# 📁 File Upload MVC Implementation Guide

## Overview

This guide explains the **Laravel MVC approach** for handling file uploads, following best practices with minimal JavaScript and server-side validation.

---

## 🎯 MVC Principles Applied

### **Model**: Incident.php
- Defines data structure
- Handles data casting (JSON for arrays)
- No business logic for uploads

### **View**: create.blade.php
- Simple HTML form with file inputs
- Minimal JavaScript (only file count display)
- Server-side validation errors displayed
- No client-side validation or preview generation

### **Controller**: IncidentController.php
- Handles ALL file validation
- Processes file uploads
- Stores files to disk
- Returns validation errors to view

---

## 🏗️ Architecture

```
User Selects Files
       ↓
Browser (View)
  - Standard file input
  - Simple file count display (minimal JS)
       ↓
Form Submit
       ↓
Laravel Controller
  - Validate files (size, type, count)
  - Store files to storage/app/public
  - Save paths to database
       ↓
Model
  - Cast JSON arrays
  - Store file paths
       ↓
Success/Error Response
  - Redirect back with success
  - Or return with validation errors
```

---

## 📝 Implementation Details

### 1. View (Blade Template)

#### Simple File Input
```blade
<input
    type="file"
    name="photos[]"
    id="photo-input"
    class="file-input file-input-bordered w-full"
    accept="image/jpeg,image/png,image/jpg,image/gif"
    multiple
    required
    onchange="showPhotoCount(this)"
>
```

**Key Features:**
- ✅ Standard HTML5 file input
- ✅ DaisyUI styling (`file-input`)
- ✅ Multiple file selection
- ✅ Accept attribute for file types
- ✅ Simple onchange handler

#### Minimal JavaScript
```javascript
// Only shows file count - NO validation, NO preview
function showPhotoCount(input) {
    const count = input.files.length;
    const display = document.getElementById('photo-count-display');
    if (count > 0) {
        display.textContent = `${count} file${count > 1 ? 's' : ''} selected`;
    } else {
        display.textContent = '';
    }
}
```

**Total JavaScript**: ~10 lines (vs 300+ lines with preview)

#### Validation Error Display
```blade
@error('photos')
    <label class="label">
        <span class="label-text-alt text-error">{{ $message }}</span>
    </label>
@enderror

@error('photos.*')
    <label class="label">
        <span class="label-text-alt text-error">{{ $message }}</span>
    </label>
@enderror
```

**Benefits:**
- Server-side validation messages
- Automatic Laravel error bag integration
- User-friendly custom messages

---

### 2. Controller (Business Logic)

#### Validation Rules
```php
$validated = $request->validate([
    // Photos - REQUIRED
    'photos' => 'required|array|max:5',
    'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB
    
    // Videos - OPTIONAL
    'videos' => 'nullable|array|max:2',
    'videos.*' => 'mimes:mp4,webm,mov,quicktime|max:10240', // 10MB
], [
    // Custom error messages
    'photos.required' => 'Please upload at least one photo of the incident.',
    'photos.max' => 'You can upload a maximum of 5 photos.',
    'photos.*.max' => 'Each photo must not exceed 2MB in size.',
    'videos.max' => 'You can upload a maximum of 2 videos.',
    'videos.*.max' => 'Each video must not exceed 10MB in size.',
]);
```

**Validation Rules Explained:**
- `required|array|max:5` - At least 1, maximum 5 files
- `image|mimes:...` - Must be valid image file
- `max:2048` - Max size in KB (2MB)
- Custom messages for better UX

#### File Storage
```php
// Handle photo uploads
$photoPaths = [];
if ($request->hasFile('photos')) {
    foreach ($request->file('photos') as $photo) {
        // Store in storage/app/public/incident_photos
        $path = $photo->store('incident_photos', 'public');
        $photoPaths[] = $path;
    }
}

// Handle video uploads
$videoPaths = [];
if ($request->hasFile('videos')) {
    foreach ($request->file('videos') as $video) {
        $path = $video->store('incident_videos', 'public');
        $videoPaths[] = $path;
    }
}

// Save to database
$validated['photos'] = $photoPaths;
$validated['videos'] = $videoPaths;
$incident = Incident::create($validated);
```

**Storage Strategy:**
- Files stored in `storage/app/public/`
- Organized by type (`incident_photos/`, `incident_videos/`)
- Laravel generates unique filenames automatically
- Paths saved as JSON array in database

---

### 3. Model (Data Structure)

#### Fillable Fields
```php
protected $fillable = [
    // ... other fields
    'photos',
    'videos',
    'documents',
];
```

#### Type Casting
```php
protected $casts = [
    'photos' => 'array',
    'videos' => 'array',
    'documents' => 'array',
];
```

**Benefits:**
- Automatic JSON encoding/decoding
- Arrays in PHP, JSON in database
- No manual serialize/unserialize

---

### 4. Database Schema

#### Migration
```php
// In incidents table migration
$table->json('photos')->nullable();
$table->json('videos')->nullable();
```

**Example Data:**
```json
{
  "photos": [
    "incident_photos/abc123.jpg",
    "incident_photos/def456.png"
  ],
  "videos": [
    "incident_videos/xyz789.mp4"
  ]
}
```

---

## ✅ Benefits of MVC Approach

### 1. **Separation of Concerns**
```
View      → Only displays form and errors
Controller → Handles ALL validation and storage
Model      → Defines data structure
```

### 2. **Server-Side Validation**
```php
✅ File size checked by Laravel
✅ File type verified by Laravel
✅ Count limit enforced by Laravel
✅ No way to bypass validation
```

### 3. **Minimal JavaScript**
```javascript
// Before: 300+ lines of preview code
// After:  10 lines of file count display
```

### 4. **Better Security**
```php
✅ All validation server-side
✅ Files stored securely
✅ MIME type verification
✅ No client-side bypass possible
```

### 5. **Easier Maintenance**
```
✅ Less code to maintain
✅ Standard Laravel patterns
✅ Easy to understand
✅ No complex JS logic
```

### 6. **Better Error Handling**
```php
// Automatic validation errors
if ($validator->fails()) {
    return back()
        ->withErrors($validator)
        ->withInput();
}
```

---

## 🔄 User Experience Flow

### Happy Path
```
1. User clicks file input
2. Selects files (e.g., 3 photos)
3. Sees "3 files selected" (minimal JS)
4. Fills other form fields
5. Clicks "Submit"
6. Controller validates files
7. Files uploaded successfully
8. Redirected to incident page
9. Success message shown
```

### Error Path
```
1. User selects 6 photos (exceeds limit)
2. Fills form
3. Clicks "Submit"
4. Controller validation fails
5. Returns to form with errors
6. Shows "You can upload a maximum of 5 photos"
7. Form data preserved (except files)
8. User re-selects correct number
9. Success
```

---

## 🎨 UI/UX Considerations

### File Count Display
```blade
<span id="photo-count-display" class="label-text-alt text-primary font-medium"></span>
```
Shows: "3 files selected"

### Validation Feedback
```blade
{{-- Laravel automatically shows errors --}}
@error('photos')
    <span class="text-error">{{ $message }}</span>
@enderror
```

### Note on Re-selection
```blade
@if(old('photos'))
    <div class="alert alert-info">
        <p>{{ count(old('photos')) }} photo(s) were previously selected. 
           Please select them again.</p>
    </div>
@endif
```

**Note:** Browsers don't allow pre-populating file inputs for security reasons

---

## 📊 Comparison Table

| Feature | MVC Approach (This) | JavaScript Preview Approach |
|---------|---------------------|----------------------------|
| **JavaScript** | ~10 lines | ~300 lines |
| **Validation** | Server-side only | Client + Server |
| **Preview** | No | Yes |
| **Security** | ✅ High | ⚠️ Can be bypassed |
| **Maintenance** | ✅ Easy | ⚠️ Complex |
| **File Removal** | N/A (re-select) | Individual removal |
| **User Feedback** | File count only | Full preview grid |
| **Laravel Best Practice** | ✅ Yes | ⚠️ Mixed |

---

## 🔧 Configuration

### Change File Limits
```php
// In Controller validation
'photos' => 'required|array|max:10',  // Allow 10 photos
'photos.*' => 'image|max:5120',       // 5MB per photo
```

### Change Storage Location
```php
// Store in different directory
$path = $photo->store('incidents/photos', 'public');

// Store with custom name
$filename = Str::random(40) . '.' . $photo->extension();
$path = $photo->storeAs('incident_photos', $filename, 'public');
```

### Add File Type Restrictions
```php
// Only JPG and PNG
'photos.*' => 'image|mimes:jpeg,png|max:2048',

// Only MP4 videos
'videos.*' => 'mimes:mp4|max:10240',
```

---

## 🐛 Common Issues & Solutions

### Issue: Files not uploading

**Check:**
1. Form has `enctype="multipart/form-data"`
2. Input name is `photos[]` (array)
3. PHP `upload_max_filesize` in php.ini
4. Laravel `post_max_size` setting

### Issue: Validation always fails

**Check:**
1. File size limits in validation
2. MIME type restrictions
3. PHP memory limit
4. Storage permissions

### Issue: Files saved but paths not in database

**Check:**
1. Field in model's `$fillable`
2. Field has proper cast (`'array'`)
3. Migration added column

---

## 📚 Laravel Documentation References

- [File Uploads](https://laravel.com/docs/10.x/requests#files)
- [File Storage](https://laravel.com/docs/10.x/filesystem)
- [Validation](https://laravel.com/docs/10.x/validation#rule-file)
- [Eloquent Casts](https://laravel.com/docs/10.x/eloquent-mutators#array-and-json-casting)

---

## 🎓 Best Practices Summary

### ✅ DO:
1. **Validate on server-side** - Always use Laravel validation
2. **Use standard file inputs** - Native browser functionality
3. **Store files properly** - Use Laravel's storage system
4. **Cast JSON arrays** - In model for database storage
5. **Show clear errors** - Use `@error` directives
6. **Limit file sizes** - Protect server resources
7. **Verify MIME types** - Security measure

### ❌ DON'T:
1. **Rely on client-side validation only**
2. **Store files in public directory directly**
3. **Forget to add `enctype="multipart/form-data"`"
4. **Skip file type validation**
5. **Allow unlimited file sizes**
6. **Forget to handle storage errors**
7. **Mix too much JavaScript with server logic**

---

## 🚀 Performance Tips

### 1. Optimize Storage
```php
// Compress images before storing (optional)
use Intervention\Image\Facades\Image;

$image = Image::make($photo);
$image->resize(1920, null, function ($constraint) {
    $constraint->aspectRatio();
    $constraint->upsize();
});
$path = 'incident_photos/' . Str::random(40) . '.jpg';
Storage::disk('public')->put($path, $image->encode('jpg', 85));
```

### 2. Queue Large Uploads
```php
// Process in background
dispatch(new ProcessIncidentMedia($incident, $photoPaths));
```

### 3. Serve Files Efficiently
```php
// In routes/web.php
Route::get('/storage/{path}', function ($path) {
    return response()->file(storage_path('app/public/' . $path));
})->where('path', '.*');
```

---

## 📝 Summary

### What We Built:
✅ MVC-compliant file upload system  
✅ Server-side validation only  
✅ Minimal JavaScript (~10 lines)  
✅ Multiple file support (photos + videos)  
✅ Proper Laravel storage  
✅ User-friendly error messages  
✅ Clean, maintainable code  

### Benefits:
🎯 **Secure** - All validation server-side  
🎯 **Simple** - Minimal code to maintain  
🎯 **Standard** - Follows Laravel best practices  
🎯 **Reliable** - No client-side bypass  
🎯 **Clean** - MVC separation of concerns  

---

**Last Updated:** October 18, 2025  
**Version:** 1.0.0  
**Approach:** Laravel MVC Best Practices

