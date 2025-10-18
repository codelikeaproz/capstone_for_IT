# 📸 Image Upload - Quick Reference

## 🚀 Quick Implementation

### HTML Structure
```blade
<!-- Hidden File Input -->
<input type="file" id="photo-input" name="photos[]" class="hidden" multiple accept="image/*">

<!-- Upload Button -->
<button type="button" onclick="document.getElementById('photo-input').click()">
    Choose Photos
</button>

<!-- Preview Container -->
<div id="photo-preview-container" class="hidden">
    <h3>Uploaded Photos (<span id="photo-count">0</span>/5)</h3>
    <button onclick="clearAllPhotos()">Clear All</button>
    <div id="photo-preview-grid"></div>
</div>
```

### JavaScript
```javascript
// Constants
const MAX_PHOTOS = 5;
const MAX_SIZE = 2 * 1024 * 1024; // 2MB

let photoFiles = [];

// Handle file selection
document.getElementById('photo-input').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    
    // Validate and add files
    for (let file of files) {
        if (photoFiles.length >= MAX_PHOTOS) break;
        if (file.size > MAX_SIZE) continue;
        photoFiles.push(file);
    }
    
    updatePreview();
});

// Generate previews
function updatePreview() {
    const grid = document.getElementById('photo-preview-grid');
    grid.innerHTML = '';
    
    photoFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            grid.innerHTML += `
                <div class="preview-card">
                    <img src="${e.target.result}" alt="Preview">
                    <button onclick="removePhoto(${index})">×</button>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    });
}

// Remove photo
function removePhoto(index) {
    photoFiles.splice(index, 1);
    updatePreview();
}
```

---

## 🎨 Styling (Tailwind + DaisyUI)

### Grid Layout
```html
<!-- Responsive grid -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
    <!-- Preview cards here -->
</div>
```

### Preview Card
```html
<div class="relative group bg-base-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
    <!-- Image with aspect ratio -->
    <div class="aspect-square relative">
        <img src="..." class="w-full h-full object-cover">
        
        <!-- Hover overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center">
            <!-- Remove button -->
            <button class="btn btn-circle btn-sm btn-error opacity-0 group-hover:opacity-100">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <!-- File info -->
    <div class="p-2 bg-base-100">
        <p class="text-xs truncate">filename.jpg</p>
        <p class="text-xs text-base-content/50">150 KB</p>
    </div>
</div>
```

---

## ⚡ Key Functions

### Format File Size
```javascript
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
```

### Sync Files with Input
```javascript
function updatePhotoInput() {
    const input = document.getElementById('photo-input');
    const dataTransfer = new DataTransfer();
    
    photoFiles.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
}
```

### Validate Files
```javascript
function validateFile(file) {
    // Check size
    if (file.size > MAX_SIZE) {
        showErrorToast(`${file.name} exceeds size limit`);
        return false;
    }
    
    // Check type
    if (!file.type.match('image.*')) {
        showErrorToast(`${file.name} is not an image`);
        return false;
    }
    
    return true;
}
```

---

## 🔧 Common Configurations

### Change Limits
```javascript
const MAX_PHOTOS = 10;  // Allow 10 photos
const MAX_SIZE = 5 * 1024 * 1024;  // 5MB per photo
```

### Accept Specific Types
```html
<!-- Only JPG and PNG -->
<input type="file" accept="image/jpeg,image/png">

<!-- Any image -->
<input type="file" accept="image/*">

<!-- Multiple types -->
<input type="file" accept="image/*,video/*">
```

### Mobile Camera
```html
<!-- Open camera on mobile -->
<input type="file" accept="image/*" capture="environment">
```

---

## 🐛 Troubleshooting

### Issue: DataTransfer not supported
```javascript
if (typeof DataTransfer === 'undefined') {
    // Fallback: Don't allow removing files
    console.warn('DataTransfer not supported');
}
```

### Issue: FileReader not supported
```javascript
if (!window.FileReader) {
    alert('File preview not supported in this browser');
    // Show file list instead of previews
}
```

### Issue: Memory with large files
```javascript
// Revoke object URLs after use
const objectURL = URL.createObjectURL(file);
img.onload = () => URL.revokeObjectURL(objectURL);
```

---

## 📱 Mobile Considerations

### Touch-Friendly Buttons
```html
<!-- Minimum 44x44px tap target -->
<button class="btn btn-sm">Remove</button>
```

### Responsive Grid
```css
/* Mobile: 2 columns */
grid-cols-2

/* Tablet: 3 columns */
md:grid-cols-3

/* Desktop: 5 columns */
lg:grid-cols-5
```

### Photo Capture
```html
<!-- Rear camera -->
<input type="file" capture="environment">

<!-- Front camera -->
<input type="file" capture="user">
```

---

## ✅ Validation Checklist

- [ ] File count limit enforced
- [ ] File size validated
- [ ] File type checked
- [ ] Error messages shown
- [ ] Preview updates correctly
- [ ] Remove works properly
- [ ] Clear all works
- [ ] Count displays correctly
- [ ] Files sync with input
- [ ] Form submits correctly

---

## 🎯 Best Practices

### DO ✅
```javascript
// Validate before adding
if (file.size > MAX_SIZE) return;

// Show user feedback
showErrorToast('File too large');

// Format file sizes
formatFileSize(file.size);

// Use semantic HTML
<button type="button">...</button>

// Handle errors gracefully
try { ... } catch (e) { ... }
```

### DON'T ❌
```javascript
// Don't add files without validation
photoFiles.push(file);  // Bad

// Don't use alerts for feedback
alert('File too large');  // Bad - use toast

// Don't forget to sync input
// Always call updatePhotoInput()

// Don't submit without files
// Add form validation
```

---

## 📊 Performance Tips

### Lazy Load Previews
```javascript
// Only generate preview when visible
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            loadPreview(entry.target);
        }
    });
});
```

### Debounce Updates
```javascript
const debouncedUpdate = debounce(updatePreview, 300);
```

### Limit Preview Size
```javascript
// Resize image before preview
function resizeImage(file, maxWidth = 400) {
    // Create canvas and resize
    // Return smaller data URL
}
```

---

## 🔌 Integration with Laravel

### Backend Validation
```php
// In controller
$request->validate([
    'photos' => 'required|array|max:5',
    'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
]);
```

### Store Files
```php
// Store uploaded photos
$photoPaths = [];
if ($request->hasFile('photos')) {
    foreach ($request->file('photos') as $photo) {
        $path = $photo->store('incident_photos', 'public');
        $photoPaths[] = $path;
    }
}

$incident->photos = $photoPaths;
$incident->save();
```

### Display Errors
```blade
@error('photos')
    <span class="text-error">{{ $message }}</span>
@enderror

@error('photos.*')
    <span class="text-error">{{ $message }}</span>
@enderror
```

---

## 🎨 Custom Styling

### CSS Variables
```css
:root {
    --preview-card-bg: #f3f4f6;
    --preview-card-hover: #e5e7eb;
    --remove-btn-bg: #ef4444;
}
```

### Custom Classes
```css
.preview-card {
    position: relative;
    border-radius: 0.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.preview-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.preview-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0);
    transition: background 0.3s ease;
}

.preview-card:hover .preview-overlay {
    background: rgba(0,0,0,0.4);
}
```

---

## 📝 Testing Checklist

### Functionality
- [ ] Upload single file
- [ ] Upload multiple files
- [ ] Remove individual file
- [ ] Clear all files
- [ ] Validation works
- [ ] Preview displays
- [ ] Count updates
- [ ] Form submits

### Edge Cases
- [ ] Max files reached
- [ ] File too large
- [ ] Invalid file type
- [ ] Empty selection
- [ ] Remove last file
- [ ] Add after removing

### Browser Compatibility
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobile browsers

### Responsive
- [ ] Mobile view (320px)
- [ ] Tablet view (768px)
- [ ] Desktop view (1024px+)
- [ ] Touch interactions work

---

## 🆘 Quick Fixes

### Preview not showing
```javascript
// Check console for errors
console.log(photoFiles);

// Verify FileReader support
if (!window.FileReader) {
    console.error('FileReader not supported');
}
```

### Files not submitting
```javascript
// Ensure input has files
console.log(document.getElementById('photo-input').files);

// Verify form enctype
<form enctype="multipart/form-data">
```

### Remove not working
```javascript
// Check if files array is updating
console.log('Before:', photoFiles.length);
photoFiles.splice(index, 1);
console.log('After:', photoFiles.length);

// Ensure updatePhotoInput() is called
updatePhotoInput();
```

---

## 📚 Resources

- **FileReader API**: [MDN Docs](https://developer.mozilla.org/en-US/docs/Web/API/FileReader)
- **DataTransfer API**: [MDN Docs](https://developer.mozilla.org/en-US/docs/Web/API/DataTransfer)
- **File API**: [MDN Docs](https://developer.mozilla.org/en-US/docs/Web/API/File)

---

## 🎉 Final Checklist

Before deploying:
- [ ] All validations working
- [ ] Error messages user-friendly
- [ ] Mobile responsive
- [ ] Touch interactions work
- [ ] Loading states handled
- [ ] Backend validation matches frontend
- [ ] File storage configured
- [ ] Tests passing
- [ ] Documentation updated

---

**Quick Start Time:** 15-30 minutes  
**Difficulty:** Intermediate  
**Dependencies:** None (vanilla JS)  
**Browser Support:** Modern browsers (IE11+ with polyfills)

