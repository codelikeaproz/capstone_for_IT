# MDRRMC Design Guidelines
## Municipal Disaster Risk Reduction Management System

> **Updated**: October 2025 | **Version**: 2.0
> **For**: Government Emergency Response System

---

## Technology Stack
- **Backend Framework**: Laravel 12 (PHP 8.2+) with MVC (Model-View-Controller)
- **Frontend**: Blade template with minimized JavaScript
- **Styling**: Tailwind CSS 4.0 with DaisyUI
- **Database**: PostgreSQL (Centralized)

---

## Design Philosophy for Emergency Systems

### 1. **Clarity Over Creativity**
Emergency management systems must prioritize immediate comprehension over visual appeal. Every design decision should ask: "Can a stressed responder understand this in 3 seconds?"

### 2. **Mobile-First, Crisis-Ready**
Field responders use mobile devices in challenging conditions:
- Bright sunlight visibility
- One-handed operation
- Works with gloves
- Minimal data usage
- Offline capability

### 3. **Accessibility is Non-Negotiable**
Government systems must serve ALL citizens:
- WCAG 2.1 Level AA minimum
- Screen reader compatible
- High contrast modes
- Keyboard navigation
- Multiple language support (future)

### 4. **Government Standards**
Maintain professionalism and trust:
- Philippine government color compliance
- Official seal usage guidelines
- Data privacy transparency
- Clear accountability

---

## Color System

### Primary Emergency Palette

```css
/* Critical/Emergency - Use sparingly! */
--emergency-red: #DC2626;
--emergency-red-light: #FCA5A5;

/* Government Primary */
--gov-blue: #1E40AF;
--gov-blue-light: #93C5FD;

/* Success/Resolved */
--success-green: #16A34A;

/* Warning/Caution */
--warning-orange: #EA580C;
```

### Incident Type Colors (DaisyUI Classes)

```html
<!-- Traffic Accident -->
<span class="badge badge-warning">Traffic Accident</span>

<!-- Medical Emergency -->
<span class="badge badge-error">Medical Emergency</span>

<!-- Fire Incident -->
<span class="badge badge-error">Fire Incident</span>

<!-- Natural Disaster -->
<span class="badge" style="background-color: #6366F1; color: white;">Natural Disaster</span>

<!-- Criminal Activity -->
<span class="badge" style="background-color: #7C3AED; color: white;">Criminal Activity</span>
```

### Severity Levels

```html
<span class="badge badge-error badge-lg">Critical</span>
<span class="badge badge-warning badge-lg">High</span>
<span class="badge badge-info badge-lg">Medium</span>
<span class="badge badge-success badge-lg">Low</span>
```

### Status Colors

```html
<span class="badge badge-warning">Pending</span>
<span class="badge badge-info">Active</span>
<span class="badge badge-success">Resolved</span>
<span class="badge badge-neutral">Closed</span>
```

### Color Usage Rules

**✅ DO:**
- Use emergency red ONLY for critical/urgent situations
- Pair colors with icons (never color alone)
- Maintain 4.5:1 contrast minimum for text
- Use semantic colors consistently

**❌ DON'T:**
- Use red and green together (colorblind users)
- Rely on color alone to convey meaning
- Use low-contrast color combinations
- Change semantic colors arbitrarily

---

## Typography

### Font Stack
```css
/* Primary Font */
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

/* Monospace (for incident numbers, codes) */
font-family: 'JetBrains Mono', 'Courier New', monospace;
```

### Type Scale (Tailwind Classes)

```html
<!-- Page Title -->
<h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>

<!-- Section Header -->
<h2 class="text-xl font-semibold text-gray-800">Active Incidents</h2>

<!-- Card Title -->
<h3 class="text-lg font-medium text-gray-800">Incident Details</h3>

<!-- Body Text -->
<p class="text-base text-gray-700 leading-relaxed">Description text...</p>

<!-- Helper/Label Text -->
<span class="text-sm text-gray-600">Last updated 5 minutes ago</span>

<!-- Small Labels -->
<label class="text-sm font-medium text-gray-700">Field Label</label>
```

### Typography Rules

1. **Minimum font size**: 16px for body text (never smaller)
2. **Line height**: 1.5 for paragraphs, 1.25 for headings
3. **Line length**: 45-75 characters maximum
4. **Emphasis**: Use **bold** for importance, not color or size alone
5. **Avoid all caps**: Use title case for readability

---

## Iconography

### Icon System: Font Awesome 6

### Emergency Icons

```html
<!-- Critical Alert -->
<i class="fas fa-exclamation-triangle text-error"></i>

<!-- Medical -->
<i class="fas fa-heartbeat text-error"></i>
<i class="fas fa-ambulance"></i>

<!-- Fire -->
<i class="fas fa-fire text-error"></i>

<!-- Traffic -->
<i class="fas fa-car-crash text-warning"></i>

<!-- Natural Disaster -->
<i class="fas fa-cloud-bolt"></i>
<i class="fas fa-water"></i>

<!-- Criminal Activity -->
<i class="fas fa-shield-alt" style="color: #7C3AED;"></i>

<!-- Response Vehicles -->
<i class="fas fa-truck-medical"></i>
<i class="fas fa-helicopter"></i>
```

### Status Icons

```html
<!-- Pending -->
<i class="fas fa-clock text-warning"></i>

<!-- In Progress -->
<i class="fas fa-spinner fa-spin text-info"></i>

<!-- Completed -->
<i class="fas fa-check-circle text-success"></i>

<!-- Closed -->
<i class="fas fa-archive text-gray-500"></i>
```

### Icon Usage Rules

1. **Always pair with text** for critical actions
2. **Minimum touch target**: 44x44px for mobile
3. **Consistent sizing** within same context
4. **Semantic colors** (red for danger, green for success)
5. **High contrast**: 3:1 minimum against background

---

## Spacing and Alignment

### Spacing Scale (Tailwind)

```css
/* Use these spacing values consistently */
space-1  = 4px   /* Tight spacing */
space-2  = 8px   /* Small gaps */
space-3  = 12px  /* Default small */
space-4  = 16px  /* Standard gap */
space-6  = 24px  /* Section spacing */
space-8  = 32px  /* Large sections */
space-12 = 48px  /* Major sections */
```

### Layout Patterns

```html
<!-- Card with consistent spacing -->
<div class="card bg-white shadow-lg">
  <div class="card-body p-6 space-y-4">
    <h2 class="card-title text-xl font-semibold mb-4">Title</h2>
    <div class="divider my-2"></div>
    <div class="space-y-4">
      <!-- Content with consistent vertical rhythm -->
    </div>
  </div>
</div>

<!-- Form with grid layout -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div class="form-control">
    <!-- Form field -->
  </div>
</div>
```

### Vertical Rhythm

```html
<!-- Use space-y-* for vertical spacing -->
<div class="space-y-6">
  <section class="bg-white p-6 rounded-lg shadow">Section 1</section>
  <section class="bg-white p-6 rounded-lg shadow">Section 2</section>
  <section class="bg-white p-6 rounded-lg shadow">Section 3</section>
</div>
```

---

## Form Design

### Form Structure

```html
<form class="space-y-6">
  <!-- Form Section -->
  <div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Section Title</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Required Field -->
      <div class="form-control">
        <label class="label">
          <span class="label-text font-medium">
            Field Name <span class="text-error">*</span>
          </span>
        </label>
        <input
          type="text"
          class="input input-bordered w-full focus:outline-primary"
          placeholder="Enter value"
          required
        >
        <!-- Error Message -->
        @error('field_name')
        <label class="label">
          <span class="label-text-alt text-error">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
          </span>
        </label>
        @enderror
      </div>

      <!-- Select Field -->
      <div class="form-control">
        <label class="label">
          <span class="label-text font-medium">Dropdown</span>
        </label>
        <select class="select select-bordered w-full">
          <option value="">Select option</option>
          <option value="1">Option 1</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Form Actions -->
  <div class="border-t border-gray-200 pt-6">
    <div class="flex flex-col sm:flex-row justify-end items-center gap-3">
      <a href="/back" class="btn btn-outline w-full sm:w-auto gap-2">
        <i class="fas fa-times"></i>
        <span>Cancel</span>
      </a>
      <button type="submit" class="btn btn-primary w-full sm:w-auto gap-2">
        <i class="fas fa-paper-plane"></i>
        <span>Submit Report</span>
      </button>
    </div>
  </div>
</form>
```

### Form Field Best Practices

1. **Labels**: Always visible, never placeholder-only
2. **Required indicators**: Red asterisk (*) next to label
3. **Help text**: Below field in smaller, gray text
4. **Error messages**: Red text with icon, below field
5. **Field width**: Full width on mobile, appropriate on desktop
6. **Autocomplete**: Enable where appropriate
7. **Tab order**: Logical top-to-bottom, left-to-right

---

## Card Layouts

### Standard Card

```html
<div class="card bg-white shadow-lg">
  <div class="card-body">
    <!-- Header with icon and badge -->
    <div class="flex items-center justify-between mb-4">
      <h2 class="card-title text-xl font-semibold">
        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
        Card Title
      </h2>
      <span class="badge badge-warning">Status</span>
    </div>

    <!-- Divider -->
    <div class="divider my-2"></div>

    <!-- Content -->
    <div class="space-y-4">
      <p class="text-gray-700">Card content goes here...</p>
    </div>

    <!-- Actions -->
    <div class="card-actions justify-end mt-6 gap-2">
      <button class="btn btn-outline btn-sm">Cancel</button>
      <button class="btn btn-primary btn-sm">Confirm</button>
    </div>
  </div>
</div>
```

### Stats Card

```html
<div class="stats shadow bg-white">
  <div class="stat">
    <div class="stat-figure text-primary">
      <i class="fas fa-exclamation-triangle text-4xl"></i>
    </div>
    <div class="stat-title text-gray-600">Total Incidents</div>
    <div class="stat-value text-primary text-3xl">152</div>
    <div class="stat-desc text-sm">
      <span class="text-error font-semibold">12 Active</span> •
      <span class="text-warning">3 Critical</span>
    </div>
  </div>
</div>
```

---

## Buttons and Actions

### Button Variants

```html
<!-- Primary Action -->
<button class="btn btn-primary gap-2">
  <i class="fas fa-plus"></i>
  <span>Create New</span>
</button>

<!-- Secondary Action -->
<button class="btn btn-outline gap-2">
  <i class="fas fa-eye"></i>
  <span>View Details</span>
</button>

<!-- Danger Action -->
<button class="btn btn-error gap-2">
  <i class="fas fa-trash"></i>
  <span>Delete</span>
</button>

<!-- Ghost Action (subtle) -->
<button class="btn btn-ghost btn-sm">
  <i class="fas fa-edit"></i>
</button>
```

### Button States

```html
<!-- Loading State -->
<button class="btn btn-primary" disabled>
  <i class="fas fa-spinner fa-spin"></i>
  <span>Processing...</span>
</button>

<!-- Disabled State -->
<button class="btn btn-primary" disabled>
  <span class="opacity-50">Submit</span>
</button>

<!-- Success State (temporary) -->
<button class="btn btn-success">
  <i class="fas fa-check"></i>
  <span>Saved!</span>
</button>
```

### Button Sizing

```html
<!-- Large (default for primary actions) -->
<button class="btn btn-lg btn-primary">Large Button</button>

<!-- Normal (default) -->
<button class="btn btn-primary">Normal Button</button>

<!-- Small (for compact spaces) -->
<button class="btn btn-sm btn-primary">Small</button>

<!-- Full width on mobile -->
<button class="btn btn-primary w-full sm:w-auto">Responsive</button>
```

### Button Best Practices

1. **Primary action per screen**: Only one primary button per view
2. **Icon + Text**: Use both for clarity (icon alone for icon buttons only)
3. **Loading feedback**: Show spinner during async operations
4. **Disable during processing**: Prevent double-submission
5. **Minimum size**: 44x44px for touch targets
6. **Spacing**: Minimum 8px between buttons

---

## Responsive Design

### Breakpoint Strategy

```html
<!-- Stack vertically on mobile, horizontal on desktop -->
<div class="flex flex-col lg:flex-row gap-4">
  <div class="flex-1">Column 1</div>
  <div class="flex-1">Column 2</div>
</div>

<!-- 1 column mobile, 2 tablet, 4 desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
  <div>Card 1</div>
  <div>Card 2</div>
  <div>Card 3</div>
  <div>Card 4</div>
</div>

<!-- Hide/show based on screen size -->
<div class="hidden lg:block">Desktop only</div>
<div class="block lg:hidden">Mobile only</div>
```

### Mobile Optimizations

1. **Full-width buttons** on mobile
2. **Stack form fields** vertically
3. **Collapsible sections** for long forms
4. **Bottom navigation** for primary actions
5. **Larger touch targets** (minimum 44x44px)
6. **Minimize text input** (use dropdowns/radios when possible)

---

## JavaScript Usage

### Minimize JavaScript - MVC Principles

**✅ Good JavaScript Use:**
- UI interactions (dropdowns, modals, tabs)
- Form validation feedback
- Dynamic content loading (AJAX)
- Map interactions
- Real-time updates

**❌ Avoid JavaScript For:**
- Business logic (belongs in controller/model)
- Data validation (use server-side)
- Authentication/authorization
- Database operations
- Routing

### JavaScript Patterns

```html
@push('scripts')
<script>
// Clean, organized, well-commented code
document.addEventListener('DOMContentLoaded', function() {
    // Municipality/barangay dropdown dependency
    const municipalitySelect = document.getElementById('municipality');
    const barangaySelect = document.getElementById('barangay');

    if (municipalitySelect && barangaySelect) {
        municipalitySelect.addEventListener('change', function() {
            const municipality = this.value;

            // Show loading state
            barangaySelect.disabled = true;
            barangaySelect.innerHTML = '<option>Loading...</option>';

            // Fetch barangays
            fetch(`/api/barangays?municipality=${municipality}`)
                .then(response => response.json())
                .then(data => {
                    // Populate dropdown
                    barangaySelect.innerHTML = '<option value="">Select barangay</option>';
                    data.barangays.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = barangay;
                        barangaySelect.appendChild(option);
                    });
                    barangaySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    barangaySelect.innerHTML = '<option value="">Error loading</option>';
                });
        });
    }
});
</script>
@endpush
```

---

## Accessibility Requirements

### WCAG 2.1 Level AA Compliance

#### Color Contrast
```html
<!-- ✅ Good: High contrast -->
<p class="text-gray-900 bg-white">Clear text</p>

<!-- ❌ Bad: Low contrast -->
<p class="text-gray-400 bg-gray-300">Hard to read</p>
```

#### Keyboard Navigation
```html
<!-- All interactive elements must be keyboard accessible -->
<button class="btn focus:ring-4 focus:ring-primary focus:ring-offset-2">
  Accessible Button
</button>

<!-- Skip navigation for keyboard users -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 btn btn-primary">
  Skip to main content
</a>
```

#### Screen Reader Support
```html
<!-- Descriptive aria-labels -->
<button aria-label="Delete incident INC-2025-001">
  <i class="fas fa-trash" aria-hidden="true"></i>
</button>

<!-- Status announcements -->
<div role="status" aria-live="polite">
  <span class="sr-only">Incident successfully created</span>
</div>

<!-- Form accessibility -->
<label for="incident-type" class="label">
  <span class="label-text">Incident Type <span class="text-error">*</span></span>
</label>
<select
  id="incident-type"
  name="incident_type"
  aria-required="true"
  aria-describedby="incident-type-error"
  class="select select-bordered"
>
  <option value="">Select type</option>
</select>
<div id="incident-type-error" role="alert" class="label">
  <span class="label-text-alt text-error">Please select an incident type</span>
</div>
```

#### Alternative Text
```html
<!-- Descriptive alt text -->
<img src="/incident-map.png" alt="Map showing incident location in Valencia, Bukidnon">

<!-- Decorative images -->
<img src="/decorative.png" alt="" role="presentation">

<!-- Icon-only buttons -->
<button aria-label="Edit incident">
  <i class="fas fa-edit" aria-hidden="true"></i>
</button>
```

---

## Component Library Quick Reference

### Alerts

```html
<!-- Error Alert -->
<div class="alert alert-error shadow-lg">
  <div>
    <i class="fas fa-exclamation-triangle"></i>
    <span>Critical incident requires immediate attention</span>
  </div>
  <button class="btn btn-sm">View</button>
</div>

<!-- Warning -->
<div class="alert alert-warning shadow-lg">
  <i class="fas fa-exclamation-circle"></i>
  <span>Weather advisory in effect</span>
</div>

<!-- Info -->
<div class="alert alert-info shadow-lg">
  <i class="fas fa-info-circle"></i>
  <span>System update scheduled for tonight</span>
</div>

<!-- Success -->
<div class="alert alert-success shadow-lg">
  <i class="fas fa-check-circle"></i>
  <span>Incident resolved successfully</span>
</div>
```

### Tables

```html
<div class="overflow-x-auto bg-white rounded-lg shadow">
  <table class="table table-zebra w-full">
    <thead class="bg-gray-100">
      <tr>
        <th class="font-semibold">Incident #</th>
        <th class="font-semibold">Type</th>
        <th class="font-semibold">Status</th>
        <th class="font-semibold">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr class="hover" data-incident-id="1">
        <td class="font-mono font-bold text-primary">INC-2025-001</td>
        <td>
          <div class="flex items-center gap-2">
            <i class="fas fa-car text-warning"></i>
            <span>Traffic Accident</span>
          </div>
        </td>
        <td><span class="badge badge-warning">Active</span></td>
        <td>
          <div class="flex gap-2">
            <button class="btn btn-ghost btn-sm">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### Modals

```html
<dialog id="confirmModal" class="modal">
  <div class="modal-box">
    <h3 class="font-bold text-lg text-error mb-4">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      Confirm Action
    </h3>
    <p class="py-4">Are you sure you want to proceed?</p>
    <div class="modal-action">
      <form method="dialog" class="flex gap-2">
        <button class="btn btn-outline">Cancel</button>
        <button class="btn btn-error">Confirm</button>
      </form>
    </div>
  </div>
</dialog>
```

---

## Best Practices Checklist

### Before Committing Code
- [ ] All text has minimum 4.5:1 contrast ratio
- [ ] Interactive elements are 44x44px minimum
- [ ] Keyboard navigation works
- [ ] Icons paired with text for important actions
- [ ] Form fields have proper labels
- [ ] Error messages are clear and actionable
- [ ] Loading states for async actions
- [ ] Responsive on mobile (test on real device)
- [ ] Follows spacing scale
- [ ] Uses semantic colors consistently
- [ ] No JavaScript for business logic
- [ ] Works without JavaScript (graceful degradation)

### Before Production
- [ ] Screen reader tested
- [ ] Colorblind simulation tested
- [ ] Cross-browser tested (Chrome, Firefox, Safari, Edge)
- [ ] Tested on slow 3G connection
- [ ] Privacy policy accessible
- [ ] Government compliance met
- [ ] Offline functionality (where applicable)
- [ ] Performance optimized
- [ ] Security reviewed

---

## Resources

### Design References
- **Philippine Government**: [DICT Design Standards](https://dict.gov.ph)
- **Accessibility**: [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)
- **Emergency UI**: [FEMA Design System](https://www.fema.gov)
- **DaisyUI**: [Component Documentation](https://daisyui.com)
- **Tailwind CSS**: [Official Documentation](https://tailwindcss.com)

### Tools
- **Contrast Checker**: [WebAIM](https://webaim.org/resources/contrastchecker/)
- **Colorblind Simulator**: [Coblis](https://www.color-blindness.com/coblis-color-blindness-simulator/)
- **Screen Reader**: NVDA (Windows), VoiceOver (Mac)
- **Performance**: Lighthouse (Chrome DevTools)

---

**Maintained By**: MDRRMC Development Team
**Version**: 2.0
**Last Updated**: October 2025
**Review Cycle**: Quarterly

By following these guidelines, we ensure a consistent, accessible, and professional emergency management system that serves the people of Bukidnon effectively.
