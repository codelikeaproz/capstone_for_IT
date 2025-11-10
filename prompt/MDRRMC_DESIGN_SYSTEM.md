# MDRRMC Design System
## Municipal Disaster Risk Reduction Management - UI/UX Guidelines

> **Version 1.0** | Last Updated: October 2025
> **Purpose**: Government Emergency Management System
> **Target Users**: Municipal staff, responders, administrators, and the public

---

## Table of Contents

1. [Design Philosophy](#design-philosophy)
2. [Color System](#color-system)
3. [Typography](#typography)
4. [Iconography](#iconography)
5. [Layout & Grid](#layout--grid)
6. [Components](#components)
7. [Navigation](#navigation)
8. [Accessibility](#accessibility)
9. [Responsive Design](#responsive-design)
10. [Government Compliance](#government-compliance)

---

## Design Philosophy

### Core Principles

**1. Clarity Over Creativity**
- Information must be immediately understandable
- No ambiguity in critical situations
- Clear visual hierarchy
- Purpose before aesthetics

**2. Accessibility First**
- WCAG 2.1 Level AA compliance minimum
- High contrast for readability
- Support for screen readers
- Keyboard navigation
- Works in low-bandwidth scenarios

**3. Trust & Authority**
- Professional government aesthetic
- Consistent with Philippine government standards
- Builds public confidence
- Clear accountability

**4. Crisis-Ready**
- Optimized for high-stress situations
- Clear call-to-action buttons
- Minimal cognitive load
- Fast loading times
- Works offline when possible

**5. Mobile-First**
- Responders use mobile devices
- Touch-friendly targets (minimum 44x44px)
- Optimized for field use
- Works in bright sunlight
- Works with gloves

---

## Color System

### Primary Palette

#### Emergency Red
```css
--emergency-red: #DC2626;      /* Critical alerts, danger actions */
--emergency-red-light: #FCA5A5;
--emergency-red-dark: #991B1B;
```
**Usage**: Critical incidents, delete actions, high-severity alerts
**Accessibility**: AAA contrast on white backgrounds

#### Government Blue
```css
--gov-blue: #1E40AF;           /* Primary actions, headers */
--gov-blue-light: #93C5FD;
--gov-blue-dark: #1E3A8A;
```
**Usage**: Primary buttons, links, navigation, trust elements
**Accessibility**: AAA contrast on white backgrounds

#### Success Green
```css
--success-green: #16A34A;      /* Completed actions, resolved incidents */
--success-green-light: #86EFAC;
--success-green-dark: #166534;
```
**Usage**: Success messages, resolved status, confirmation

#### Warning Orange
```css
--warning-orange: #EA580C;     /* Medium severity, warnings */
--warning-orange-light: #FDBA74;
--warning-orange-dark: #C2410C;
```
**Usage**: Warnings, medium-severity incidents, caution states

### Neutral Palette

```css
--gray-900: #111827;  /* Primary text */
--gray-800: #1F2937;  /* Secondary text */
--gray-700: #374151;  /* Tertiary text */
--gray-600: #4B5563;  /* Placeholder text */
--gray-500: #6B7280;  /* Disabled text */
--gray-400: #9CA3AF;  /* Borders */
--gray-300: #D1D5DB;  /* Dividers */
--gray-200: #E5E7EB;  /* Backgrounds */
--gray-100: #F3F4F6;  /* Subtle backgrounds */
--gray-50:  #F9FAFB;  /* Page backgrounds */
--white:    #FFFFFF;
```

### Incident Type Colors

```css
--traffic-accident: #F97316;      /* Orange */
--medical-emergency: #DC2626;     /* Red */
--fire-incident: #EF4444;         /* Bright Red */
--natural-disaster: #6366F1;      /* Indigo */
--criminal-activity: #7C3AED;     /* Purple */
--other-incident: #64748B;        /* Slate */
```

### Semantic Colors

```css
--info: #0EA5E9;       /* Info messages */
--success: #10B981;    /* Success states */
--warning: #F59E0B;    /* Warning states */
--error: #EF4444;      /* Error states */
```

### Color Usage Rules

1. **Emergency Red**: Use ONLY for critical/urgent situations
2. **Never use red and green together** (colorblind accessibility)
3. **Text contrast minimum**: 4.5:1 for normal text, 3:1 for large text
4. **Background contrast**: Minimum 3:1 for UI components
5. **Color + Icon**: Never rely on color alone; always pair with icons/text

---

## Typography

### Font Families

#### Primary: Poppins (Self-hosted)
```css
font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
```
**Why**: Clean, professional, excellent readability, modern geometric sans-serif optimized for screens
**Weights Available**: 
- Regular (400) - Body text
- Bold (700) - Headings and emphasis

#### Monospace: JetBrains Mono
```css
font-family: 'JetBrains Mono', 'Courier New', monospace;
```
**Usage**: Incident numbers, codes, technical data

### Type Scale

```css
/* Display - Large headings */
--text-4xl: 2.25rem;  /* 36px - Page titles */
--text-3xl: 1.875rem; /* 30px - Section headers */
--text-2xl: 1.5rem;   /* 24px - Card headers */
--text-xl:  1.25rem;  /* 20px - Subheadings */

/* Body text */
--text-base: 1rem;    /* 16px - Base text */
--text-sm:   0.875rem;/* 14px - Helper text */
--text-xs:   0.75rem; /* 12px - Labels, captions */

/* Font weights */
--font-normal: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;

/* Line heights */
--leading-tight: 1.25;
--leading-normal: 1.5;
--leading-relaxed: 1.75;
```

### Typography Rules

1. **Base font size**: 16px (never smaller)
2. **Minimum touch target**: 44x44px for interactive text
3. **Line length**: 45-75 characters per line for readability
4. **Line height**: 1.5 for body text, 1.25 for headings
5. **Avoid ALL CAPS**: Use title case for headings
6. **Emphasize with weight**, not color or size
7. **Critical info**: Bold + Icon + Color

### Hierarchy Examples

```html
<!-- Page Title -->
<h1 class="text-3xl font-bold text-gray-900">Incident Dashboard</h1>

<!-- Section Header -->
<h2 class="text-xl font-semibold text-gray-800">Active Incidents</h2>

<!-- Card Title -->
<h3 class="text-lg font-medium text-gray-800">Incident Details</h3>

<!-- Body Text -->
<p class="text-base text-gray-700 leading-relaxed">...</p>

<!-- Helper Text -->
<span class="text-sm text-gray-600">Last updated 5 minutes ago</span>

<!-- Label -->
<label class="text-sm font-medium text-gray-700">Incident Type</label>
```

---

## Iconography

### Icon System: Font Awesome 6

**Why**: Comprehensive emergency/government icons, widely supported, accessible

### Icon Sizes

```css
--icon-xs:  0.75rem;  /* 12px - Inline icons */
--icon-sm:  1rem;     /* 16px - Button icons */
--icon-base: 1.25rem; /* 20px - Standard icons */
--icon-lg:  1.5rem;   /* 24px - Feature icons */
--icon-xl:  2rem;     /* 32px - Hero icons */
--icon-2xl: 3rem;     /* 48px - Large displays */
```

### Emergency Icons

```html
<!-- Critical/Emergency -->
<i class="fas fa-exclamation-triangle text-emergency-red"></i>

<!-- Medical -->
<i class="fas fa-heartbeat text-error"></i>
<i class="fas fa-ambulance"></i>
<i class="fas fa-hospital"></i>

<!-- Fire -->
<i class="fas fa-fire text-emergency-red"></i>
<i class="fas fa-fire-extinguisher"></i>

<!-- Traffic -->
<i class="fas fa-car-crash text-warning-orange"></i>
<i class="fas fa-car"></i>

<!-- Natural Disaster -->
<i class="fas fa-cloud-bolt text-gray-700"></i>
<i class="fas fa-water text-info"></i>
<i class="fas fa-house-flood-water"></i>

<!-- Criminal -->
<i class="fas fa-shield-alt text-criminal-activity"></i>
<i class="fas fa-user-shield"></i>

<!-- Response -->
<i class="fas fa-truck-medical"></i>
<i class="fas fa-helicopter"></i>
<i class="fas fa-users text-gov-blue"></i>
```

### Icon Usage Rules

1. **Always pair with text** for critical actions
2. **Consistent sizing** within the same context
3. **Semantic colors** matching incident types
4. **High contrast** minimum 3:1 against background
5. **Touch targets** minimum 44x44px for mobile
6. **Loading states** use spinning icons consistently

### Status Icons

```html
<!-- Pending -->
<i class="fas fa-clock text-warning"></i>

<!-- Active/In Progress -->
<i class="fas fa-spinner fa-spin text-info"></i>

<!-- Resolved -->
<i class="fas fa-check-circle text-success"></i>

<!-- Closed -->
<i class="fas fa-archive text-gray-500"></i>
```

---

## Layout & Grid

### Grid System

#### Container Widths
```css
/* Full width sections */
--container-full: 100%;

/* Constrained content */
--container-xl: 1280px;  /* Wide dashboards */
--container-lg: 1024px;  /* Standard pages */
--container-md: 768px;   /* Forms */
--container-sm: 640px;   /* Login, single column */
```

#### Spacing Scale

```css
--space-0:  0;
--space-1:  0.25rem;  /* 4px - Tight spacing */
--space-2:  0.5rem;   /* 8px - Small gaps */
--space-3:  0.75rem;  /* 12px - */
--space-4:  1rem;     /* 16px - Standard gap */
--space-6:  1.5rem;   /* 24px - Section spacing */
--space-8:  2rem;     /* 32px - Large sections */
--space-12: 3rem;     /* 48px - Major sections */
--space-16: 4rem;     /* 64px - Page sections */
```

#### Grid Columns

```html
<!-- Dashboard Layout: 4 columns on desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
  <!-- Stat cards -->
</div>

<!-- Form Layout: 2 columns -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <!-- Form fields -->
</div>

<!-- List Layout: Single column -->
<div class="grid grid-cols-1 gap-4">
  <!-- List items -->
</div>
```

### Page Structure

```html
<div class="min-h-screen bg-gray-50">
  <!-- Sidebar (fixed) -->
  <aside class="w-64 bg-white shadow-lg">
    <!-- Navigation -->
  </aside>

  <!-- Main Content Area -->
  <main class="flex-1 overflow-auto">
    <!-- Page Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4">
      <h1 class="text-2xl font-bold text-gray-900">Page Title</h1>
      <p class="text-sm text-gray-600">Description</p>
    </header>

    <!-- Content -->
    <div class="px-6 py-8">
      <!-- Page content -->
    </div>
  </main>
</div>
```

### Layout Rules

1. **Consistent padding**: Use spacing scale multiples
2. **Vertical rhythm**: Consistent spacing between sections
3. **White space**: Don't fear empty space - aids comprehension
4. **Content width**: Limit to 75 characters for readability
5. **Card-based**: Use cards to group related information
6. **Sticky headers**: Keep important context visible

---

## Components

### Buttons

#### Primary Button
```html
<button class="btn btn-primary gap-2">
  <i class="fas fa-paper-plane"></i>
  <span>Submit Report</span>
</button>
```

**Style**:
- Background: `--gov-blue`
- Text: White
- Padding: 12px 24px
- Min height: 44px
- Font weight: 600

#### Secondary Button
```html
<button class="btn btn-outline gap-2">
  <i class="fas fa-eye"></i>
  <span>View Details</span>
</button>
```

#### Danger Button
```html
<button class="btn btn-error gap-2">
  <i class="fas fa-trash"></i>
  <span>Delete Incident</span>
</button>
```

#### Button States

```html
<!-- Loading -->
<button class="btn btn-primary" disabled>
  <i class="fas fa-spinner fa-spin"></i>
  <span>Processing...</span>
</button>

<!-- Disabled -->
<button class="btn btn-primary" disabled>
  <span class="opacity-50">Submit Report</span>
</button>
```

#### Button Sizing

```html
<!-- Small -->
<button class="btn btn-sm">Action</button>

<!-- Normal (default) -->
<button class="btn">Action</button>

<!-- Large -->
<button class="btn btn-lg">Action</button>
```

### Cards

```html
<div class="card bg-white shadow-lg">
  <div class="card-body">
    <!-- Card Header -->
    <div class="flex items-center justify-between mb-4">
      <h2 class="card-title text-xl font-semibold text-gray-900">
        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
        Incident Details
      </h2>
      <span class="badge badge-warning">Active</span>
    </div>

    <!-- Divider -->
    <div class="divider my-2"></div>

    <!-- Card Content -->
    <div class="space-y-4">
      <!-- Content -->
    </div>

    <!-- Card Actions -->
    <div class="card-actions justify-end mt-6 gap-2">
      <button class="btn btn-outline btn-sm">Cancel</button>
      <button class="btn btn-primary btn-sm">Confirm</button>
    </div>
  </div>
</div>
```

### Alerts

```html
<!-- Critical Alert -->
<div class="alert alert-error shadow-lg">
  <div>
    <i class="fas fa-exclamation-triangle text-xl"></i>
    <div>
      <h3 class="font-bold">Critical Incident</h3>
      <div class="text-sm">5 active critical incidents require immediate attention</div>
    </div>
  </div>
  <button class="btn btn-sm">View</button>
</div>

<!-- Warning Alert -->
<div class="alert alert-warning shadow-lg">
  <i class="fas fa-exclamation-circle"></i>
  <span>Weather advisory in effect for Bukidnon region</span>
</div>

<!-- Info Alert -->
<div class="alert alert-info shadow-lg">
  <i class="fas fa-info-circle"></i>
  <span>System update scheduled for tonight at 2:00 AM</span>
</div>

<!-- Success Alert -->
<div class="alert alert-success shadow-lg">
  <i class="fas fa-check-circle"></i>
  <span>Incident INC-2025-001 successfully resolved</span>
</div>
```

### Forms

```html
<form class="space-y-6">
  <!-- Form Section -->
  <div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Form Field -->
      <div class="form-control">
        <label class="label">
          <span class="label-text font-medium">Incident Type <span class="text-error">*</span></span>
        </label>
        <select class="select select-bordered w-full">
          <option value="">Select incident type</option>
          <option value="traffic">Traffic Accident</option>
          <option value="medical">Medical Emergency</option>
        </select>
        <!-- Error State -->
        <label class="label">
          <span class="label-text-alt text-error">
            <i class="fas fa-exclamation-circle"></i> Please select an incident type
          </span>
        </label>
      </div>

      <!-- Text Input -->
      <div class="form-control">
        <label class="label">
          <span class="label-text font-medium">Location <span class="text-error">*</span></span>
        </label>
        <input type="text" class="input input-bordered w-full" placeholder="Enter location">
      </div>
    </div>
  </div>

  <!-- Form Actions -->
  <div class="flex justify-end gap-3">
    <button type="button" class="btn btn-outline">
      <i class="fas fa-times"></i>
      <span>Cancel</span>
    </button>
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-paper-plane"></i>
      <span>Submit Report</span>
    </button>
  </div>
</form>
```

### Tables

```html
<div class="overflow-x-auto bg-white rounded-lg shadow">
  <table class="table table-zebra w-full">
    <!-- Table Header -->
    <thead class="bg-gray-100">
      <tr>
        <th class="font-semibold text-gray-700">Incident #</th>
        <th class="font-semibold text-gray-700">Type</th>
        <th class="font-semibold text-gray-700">Severity</th>
        <th class="font-semibold text-gray-700">Status</th>
        <th class="font-semibold text-gray-700">Actions</th>
      </tr>
    </thead>

    <!-- Table Body -->
    <tbody>
      <tr class="hover">
        <td class="font-mono font-bold text-gov-blue">INC-2025-001</td>
        <td>
          <div class="flex items-center gap-2">
            <i class="fas fa-car text-warning-orange"></i>
            <span>Traffic Accident</span>
          </div>
        </td>
        <td>
          <span class="badge badge-error">Critical</span>
        </td>
        <td>
          <span class="badge badge-warning">Active</span>
        </td>
        <td>
          <div class="flex gap-2">
            <button class="btn btn-ghost btn-sm">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-ghost btn-sm">
              <i class="fas fa-edit"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### Stats Cards

```html
<div class="stats shadow bg-white">
  <div class="stat">
    <div class="stat-figure text-primary">
      <i class="fas fa-exclamation-triangle text-4xl"></i>
    </div>
    <div class="stat-title text-gray-600">Total Incidents</div>
    <div class="stat-value text-primary">152</div>
    <div class="stat-desc">
      <span class="text-error font-semibold">12 Active</span> •
      <span class="text-warning font-semibold">3 Critical</span>
    </div>
  </div>
</div>
```

### Badges

```html
<!-- Severity Badges -->
<span class="badge badge-error badge-lg">Critical</span>
<span class="badge badge-warning badge-lg">High</span>
<span class="badge badge-info badge-lg">Medium</span>
<span class="badge badge-success badge-lg">Low</span>

<!-- Status Badges -->
<span class="badge badge-warning">Pending</span>
<span class="badge badge-info">Active</span>
<span class="badge badge-success">Resolved</span>
<span class="badge badge-neutral">Closed</span>
```

### Modals

```html
<!-- Confirmation Modal -->
<dialog id="confirmModal" class="modal">
  <div class="modal-box max-w-md">
    <!-- Modal Header -->
    <h3 class="font-bold text-lg text-error mb-4">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      Confirm Deletion
    </h3>

    <!-- Modal Content -->
    <p class="py-4 text-gray-700">
      Are you sure you want to delete incident <strong>INC-2025-001</strong>?
      This action cannot be undone.
    </p>

    <!-- Modal Actions -->
    <div class="modal-action">
      <form method="dialog" class="flex gap-2">
        <button class="btn btn-outline">Cancel</button>
        <button class="btn btn-error">
          <i class="fas fa-trash"></i>
          Delete Incident
        </button>
      </form>
    </div>
  </div>
  <form method="dialog" class="modal-backdrop">
    <button>close</button>
  </form>
</dialog>
```

---

## Navigation

### Sidebar Navigation

```html
<aside class="sidebar bg-white shadow-lg h-screen overflow-y-auto">
  <!-- Logo -->
  <div class="px-6 py-4 border-b border-gray-200">
    <div class="flex items-center gap-3">
      <img src="/logo.png" alt="Logo" class="w-10 h-10">
      <div>
        <h1 class="font-bold text-lg text-gov-blue">BukidnonAlert</h1>
        <p class="text-xs text-gray-600">MDRRMC System</p>
      </div>
    </div>
  </div>

  <!-- Navigation Menu -->
  <nav class="menu p-4">
    <!-- Active Item -->
    <li>
      <a href="/dashboard" class="active bg-gov-blue text-white">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Regular Items -->
    <li>
      <a href="/incidents" class="hover:bg-gray-100">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Incidents</span>
        <span class="badge badge-error badge-sm">12</span>
      </a>
    </li>

    <!-- Submenu -->
    <li>
      <details open>
        <summary>
          <i class="fas fa-users"></i>
          <span>User Management</span>
        </summary>
        <ul>
          <li><a href="/users">All Users</a></li>
          <li><a href="/users/create">Add User</a></li>
        </ul>
      </details>
    </li>

    <!-- Divider -->
    <li class="menu-title">
      <span>System</span>
    </li>

    <li>
      <a href="/settings">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
      </a>
    </li>
  </nav>
</aside>
```

### Top Navigation (Mobile)

```html
<div class="navbar bg-white shadow-lg lg:hidden">
  <!-- Mobile Menu -->
  <div class="navbar-start">
    <button class="btn btn-ghost" onclick="toggleMobileMenu()">
      <i class="fas fa-bars text-xl"></i>
    </button>
  </div>

  <!-- Logo -->
  <div class="navbar-center">
    <span class="font-bold text-lg text-gov-blue">BukidnonAlert</span>
  </div>

  <!-- Actions -->
  <div class="navbar-end">
    <button class="btn btn-ghost btn-circle">
      <i class="fas fa-bell"></i>
      <span class="badge badge-error badge-sm absolute top-2 right-2">3</span>
    </button>
  </div>
</div>
```

### Breadcrumbs

```html
<div class="text-sm breadcrumbs">
  <ul>
    <li><a href="/">Home</a></li>
    <li><a href="/incidents">Incidents</a></li>
    <li class="font-semibold">INC-2025-001</li>
  </ul>
</div>
```

---

## Accessibility

### WCAG 2.1 Level AA Compliance

#### Color Contrast
- **Normal text**: Minimum 4.5:1
- **Large text** (18px+): Minimum 3:1
- **UI components**: Minimum 3:1
- **Focus indicators**: Minimum 3:1

#### Keyboard Navigation
```html
<!-- Visible focus states -->
<button class="btn focus:ring-4 focus:ring-gov-blue focus:ring-offset-2">
  Action
</button>

<!-- Skip navigation -->
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 btn btn-primary">
  Skip to main content
</a>
```

#### Screen Reader Support
```html
<!-- Aria labels -->
<button aria-label="Delete incident INC-2025-001">
  <i class="fas fa-trash" aria-hidden="true"></i>
</button>

<!-- Status updates -->
<div role="status" aria-live="polite">
  <span class="sr-only">Incident successfully created</span>
</div>

<!-- Loading states -->
<button disabled aria-busy="true">
  <span class="sr-only">Loading...</span>
  <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
</button>
```

#### Form Accessibility
```html
<div class="form-control">
  <label for="incident-type" class="label">
    <span class="label-text">Incident Type <span class="text-error">*</span></span>
  </label>
  <select
    id="incident-type"
    name="incident_type"
    class="select select-bordered"
    aria-required="true"
    aria-describedby="incident-type-error"
  >
    <option value="">Select type</option>
  </select>
  <div id="incident-type-error" class="label" role="alert">
    <span class="label-text-alt text-error">
      Please select an incident type
    </span>
  </div>
</div>
```

#### Alternative Text
```html
<!-- Images -->
<img src="/map.png" alt="Incident location map showing Bukidnon province">

<!-- Icons with meaning -->
<i class="fas fa-exclamation-triangle" aria-label="Warning"></i>

<!-- Decorative icons -->
<i class="fas fa-star" aria-hidden="true"></i>
```

---

## Responsive Design

### Breakpoints

```css
/* Mobile First Approach */
/* xs: 0-639px (default) */
/* sm: 640px+ */
@media (min-width: 640px) { }

/* md: 768px+ */
@media (min-width: 768px) { }

/* lg: 1024px+ */
@media (min-width: 1024px) { }

/* xl: 1280px+ */
@media (min-width: 1280px) { }

/* 2xl: 1536px+ */
@media (min-width: 1536px) { }
```

### Mobile Optimizations

```html
<!-- Stack on mobile, side-by-side on desktop -->
<div class="flex flex-col lg:flex-row gap-4">
  <div class="flex-1"><!-- Content --></div>
  <div class="flex-1"><!-- Content --></div>
</div>

<!-- Hide on mobile, show on desktop -->
<div class="hidden lg:block">
  <!-- Desktop-only content -->
</div>

<!-- Show on mobile, hide on desktop -->
<div class="block lg:hidden">
  <!-- Mobile-only content -->
</div>

<!-- Touch-friendly buttons -->
<button class="btn w-full sm:w-auto min-h-[44px]">
  Submit
</button>
```

### Touch Targets

- **Minimum size**: 44x44px
- **Spacing**: 8px minimum between targets
- **Tap feedback**: Visual change on touch

```html
<button class="btn min-h-[44px] min-w-[44px] active:scale-95 transition-transform">
  <i class="fas fa-plus"></i>
</button>
```

---

## Government Compliance

### Philippine Government Standards

#### Official Seals & Logos
- Use official MDRRMC/LGU logos
- Maintain proper clearspace (2x logo height)
- Never distort or alter colors
- Always use high-resolution versions

#### Data Privacy
- Display privacy policy link
- Cookie consent (if applicable)
- Data retention notices
- User consent for data collection

```html
<footer class="bg-gray-100 border-t border-gray-300 py-6">
  <div class="container mx-auto px-4">
    <div class="flex flex-wrap justify-between items-center gap-4">
      <div class="flex items-center gap-4">
        <img src="/gov-seal.png" alt="Government Seal" class="h-12">
        <div class="text-sm text-gray-700">
          <p class="font-semibold">Municipal Disaster Risk Reduction Management Council</p>
          <p>Province of Bukidnon, Philippines</p>
        </div>
      </div>
      <div class="flex gap-4 text-sm">
        <a href="/privacy" class="link link-primary">Privacy Policy</a>
        <a href="/terms" class="link link-primary">Terms of Service</a>
        <a href="/contact" class="link link-primary">Contact Us</a>
      </div>
    </div>
  </div>
</footer>
```

#### Transparency
- Display last update times
- Show data sources
- Clear accountability (who reported, who responded)
- Audit trail visibility (for authorized users)

---

## Component Library Quick Reference

### Button Variants
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-accent">Accent</button>
<button class="btn btn-ghost">Ghost</button>
<button class="btn btn-link">Link</button>
<button class="btn btn-error">Error</button>
<button class="btn btn-warning">Warning</button>
<button class="btn btn-info">Info</button>
<button class="btn btn-success">Success</button>
```

### Input Variants
```html
<input type="text" class="input input-bordered">
<input type="text" class="input input-bordered input-primary">
<input type="text" class="input input-bordered input-error">
<textarea class="textarea textarea-bordered"></textarea>
<select class="select select-bordered">
  <option>Option 1</option>
</select>
```

### Loading States
```html
<!-- Button loading -->
<button class="btn btn-primary loading">Loading</button>

<!-- Spinner -->
<span class="loading loading-spinner loading-lg text-primary"></span>

<!-- Dots -->
<span class="loading loading-dots loading-lg text-primary"></span>
```

---

## Best Practices Checklist

### Before Launch
- [ ] All text has minimum 4.5:1 contrast
- [ ] All interactive elements are 44x44px minimum
- [ ] Keyboard navigation works throughout
- [ ] Screen reader tested
- [ ] Mobile responsive on actual devices
- [ ] Forms have proper validation
- [ ] Error messages are clear and actionable
- [ ] Loading states for all async actions
- [ ] Success confirmations for all actions
- [ ] Consistent spacing using design tokens
- [ ] Icons paired with text for critical actions
- [ ] All images have alt text
- [ ] Privacy policy accessible
- [ ] Works without JavaScript (graceful degradation)
- [ ] Tested on slow connections
- [ ] Cross-browser tested (Chrome, Firefox, Safari, Edge)

---

## Maintenance & Updates

### Version Control
- Document all design changes
- Keep changelog updated
- Maintain component library
- Version design tokens

### Testing Requirements
- **Accessibility audit** (quarterly)
- **Mobile testing** (before each release)
- **Performance testing** (monthly)
- **User testing** (bi-annually with actual responders)

### Feedback Loop
- Collect user feedback
- Monitor usage analytics
- Iterate based on real-world use
- Emergency scenario testing

---

**Document Version**: 1.0
**Last Updated**: October 2025
**Maintained By**: MDRRMC Development Team
**Review Cycle**: Quarterly

For questions or updates, contact the design team.

