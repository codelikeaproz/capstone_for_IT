# Location Service Architecture Diagram

## System Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          CLIENT / USER INTERFACE                             │
│                                                                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │   Web Form   │  │  Mobile App  │  │   AJAX Call  │  │  API Client  │   │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘   │
└─────────┼──────────────────┼──────────────────┼──────────────────┼──────────┘
          │                  │                  │                  │
          │                  └──────────┬───────┘                  │
          │                             │                          │
          └─────────────────────────────┼──────────────────────────┘
                                        │
                                        ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                            ROUTES (web.php)                                  │
│                                                                               │
│  GET  /incidents/create                                                      │
│  POST /incidents/store                                                       │
│  GET  /api/municipalities ────────────────────┐                             │
│  GET  /api/barangays      ────────────────┐   │                             │
└───────────────────────────────────────────┼───┼─────────────────────────────┘
                                            │   │
                                            ▼   ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         CONTROLLER LAYER                                     │
│                     (IncidentController.php)                                 │
│                                                                               │
│  ┌────────────────────────────────────────────────────────────┐            │
│  │  • create()         - Show incident creation form          │            │
│  │  • store()          - Store new incident                   │            │
│  │  • getMunicipalities() - Return municipalities (API)       │            │
│  │  • getBarangays()   - Return barangays (API)               │            │
│  └────────────────────────────────────────────────────────────┘            │
│                              │                                               │
│                              │ Uses ↓                                        │
│                              │                                               │
└──────────────────────────────┼───────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                          SERVICE LAYER                                       │
│                     (LocationService.php)                                    │
│                                                                               │
│  ┌────────────────────────────────────────────────────────────┐            │
│  │  Data Retrieval Methods:                                    │            │
│  │  • getMunicipalities()                                      │            │
│  │  • getBarangays($municipality)                              │            │
│  │  • getAllMunicipalitiesWithBarangays()                      │            │
│  │                                                              │            │
│  │  Validation Methods:                                        │            │
│  │  • municipalityExists($municipality)                        │            │
│  │  • barangayExists($municipality, $barangay)                 │            │
│  │                                                              │            │
│  │  Search Methods:                                            │            │
│  │  • searchMunicipalities($query)                             │            │
│  │  • searchBarangays($municipality, $query)                   │            │
│  │                                                              │            │
│  │  Helper Methods:                                            │            │
│  │  • getMunicipalitiesForSelect()                             │            │
│  │  • getBarangaysForSelect($municipality)                     │            │
│  └────────────────────────────────────────────────────────────┘            │
│                              │                                               │
│                              │ Reads from ↓                                  │
│                              │                                               │
└──────────────────────────────┼───────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                          DATA LAYER                                          │
│                      (config/locations.php)                                  │
│                                                                               │
│  return [                                                                    │
│      'municipalities' => [                                                   │
│          'Valencia City' => ['Bagontaas', 'Balatukan', ...],                │
│          'Malaybalay City' => ['Aglayan', 'Bangcud', ...],                  │
│          'Don Carlos' => ['Poblacion', 'Bukidnon', ...],                    │
│          ... (22 municipalities total)                                       │
│      ]                                                                       │
│  ];                                                                          │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
                               │
                               │ Saves to ↓
                               │
┌─────────────────────────────────────────────────────────────────────────────┐
│                         DATABASE LAYER                                       │
│                       (incidents table)                                      │
│                                                                               │
│  ┌────────────────────────────────────────────────────────────┐            │
│  │  Field           │  Type          │  Description            │            │
│  │──────────────────┼────────────────┼─────────────────────────│            │
│  │  id              │  bigint        │  Primary key            │            │
│  │  incident_number │  varchar       │  INC-YYYY-XXX           │            │
│  │  municipality    │  varchar       │  Selected municipality  │            │
│  │  barangay        │  varchar       │  Selected barangay ✨   │            │
│  │  location        │  varchar       │  Detailed location      │            │
│  │  latitude        │  decimal       │  GPS coordinate         │            │
│  │  longitude       │  decimal       │  GPS coordinate         │            │
│  │  ...             │  ...           │  Other fields           │            │
│  └────────────────────────────────────────────────────────────┘            │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Diagram

### Flow 1: User Creates Incident (Form Load)

```
┌─────────┐       ┌────────────┐       ┌──────────────┐       ┌────────────┐
│         │       │            │       │              │       │            │
│  User   │──────▶│ Controller │──────▶│ LocationSvc  │──────▶│   Config   │
│         │       │            │       │              │       │            │
└─────────┘       └────────────┘       └──────────────┘       └────────────┘
   Load            create()              getMunicipalities()    locations.php
   Form            method                                       
                                                                
                   Returns                                      Returns
                   View with                                    22 municipalities
                   municipality                                 array
                   list                                         
```

### Flow 2: User Selects Municipality (AJAX)

```
┌─────────┐       ┌────────────┐       ┌──────────────┐       ┌────────────┐
│         │       │            │       │              │       │            │
│  User   │──────▶│ JavaScript │──────▶│ API Endpoint │──────▶│ LocationSvc│
│         │       │   AJAX     │       │ /api/barangays      │              │
└─────────┘       └────────────┘       └──────────────┘       └──────────────┘
   Select          fetch()              getBarangays()         getBarangays()
   Municipality                         controller method      service method
                                                                
                   ◀──────JSON─────────◀──────JSON─────────◀───Array
                   Populate             Return barangays       From config
                   barangay             for municipality       
                   dropdown                                    
```

### Flow 3: User Submits Form

```
┌─────────┐       ┌────────────┐       ┌──────────────┐       ┌────────────┐
│         │       │            │       │              │       │            │
│  User   │──────▶│ Controller │──────▶│  Validation  │──────▶│  Database  │
│         │       │            │       │              │       │            │
└─────────┘       └────────────┘       └──────────────┘       └────────────┘
   Submit          store()              Validate               INSERT
   Form            method               municipality,          incident
                                        barangay               with barangay
                                                               
                   Optional:                                   
                   LocationService                             
                   can validate                                
                   existence                                   
```

---

## Component Interaction Matrix

```
┌──────────────────┬────────┬────────┬────────┬────────┬────────┐
│   Component      │  View  │ Route  │ Ctrl   │ Service│ Config │
├──────────────────┼────────┼────────┼────────┼────────┼────────┤
│ Blade View       │   -    │  Uses  │  Uses  │  Uses  │   -    │
├──────────────────┼────────┼────────┼────────┼────────┼────────┤
│ Routes           │   -    │   -    │  Uses  │   -    │   -    │
├──────────────────┼────────┼────────┼────────┼────────┼────────┤
│ Controller       │  Uses  │   -    │   -    │  Uses  │   -    │
├──────────────────┼────────┼────────┼────────┼────────┼────────┤
│ LocationService  │   -    │   -    │   -    │   -    │  Uses  │
├──────────────────┼────────┼────────┼────────┼────────┼────────┤
│ Config           │   -    │   -    │   -    │   -    │   -    │
└──────────────────┴────────┴────────┴────────┴────────┴────────┘
```

---

## Dependency Graph

```
                    ┌──────────────┐
                    │  locations.  │
                    │  php (config)│
                    └──────┬───────┘
                           │
                           │ Read by
                           │
                    ┌──────▼───────┐
                    │  Location    │
                    │  Service     │
                    └──────┬───────┘
                           │
                  ┌────────┴────────┐
                  │                 │
           Used by│                 │Used by
                  │                 │
        ┌─────────▼──────┐   ┌──────▼────────┐
        │  Incident      │   │  Blade Views  │
        │  Controller    │   │               │
        └─────────┬──────┘   └───────────────┘
                  │
          Returns │
                  │
        ┌─────────▼──────┐
        │  JSON Response │
        │  or View       │
        └────────────────┘
```

---

## Sequence Diagram: Creating Incident

```
User          Browser         Controller        LocationService      Config        Database
 │               │                 │                   │               │               │
 │  Click        │                 │                   │               │               │
 │  "Create      │                 │                   │               │               │
 │  Incident"    │                 │                   │               │               │
 │───────────────▶                 │                   │               │               │
 │               │  GET /incidents/create              │               │               │
 │               │─────────────────▶                   │               │               │
 │               │                 │  getMunicipalities()              │               │
 │               │                 │───────────────────▶               │               │
 │               │                 │                   │  Read data    │               │
 │               │                 │                   │───────────────▶               │
 │               │                 │                   │  Return array │               │
 │               │                 │                   │◀───────────────               │
 │               │                 │  Return array     │               │               │
 │               │                 │◀───────────────────               │               │
 │               │  Return HTML    │                   │               │               │
 │               │  with municipalities                │               │               │
 │               │◀─────────────────                   │               │               │
 │  Display      │                 │                   │               │               │
 │  Form         │                 │                   │               │               │
 │◀───────────────                 │                   │               │               │
 │               │                 │                   │               │               │
 │  Select       │                 │                   │               │               │
 │  Municipality │                 │                   │               │               │
 │───────────────▶                 │                   │               │               │
 │               │  GET /api/barangays?municipality=X  │               │               │
 │               │─────────────────▶                   │               │               │
 │               │                 │  getBarangays(X)  │               │               │
 │               │                 │───────────────────▶               │               │
 │               │                 │                   │  Read data    │               │
 │               │                 │                   │───────────────▶               │
 │               │                 │                   │  Return array │               │
 │               │                 │                   │◀───────────────               │
 │               │                 │  Return array     │               │               │
 │               │                 │◀───────────────────               │               │
 │               │  Return JSON    │                   │               │               │
 │               │◀─────────────────                   │               │               │
 │  Update       │                 │                   │               │               │
 │  Barangay     │                 │                   │               │               │
 │  Dropdown     │                 │                   │               │               │
 │◀───────────────                 │                   │               │               │
 │               │                 │                   │               │               │
 │  Fill Form    │                 │                   │               │               │
 │  & Submit     │                 │                   │               │               │
 │───────────────▶                 │                   │               │               │
 │               │  POST /incidents/store              │               │               │
 │               │─────────────────▶                   │               │               │
 │               │                 │  Validate data    │               │               │
 │               │                 │───────────────────────────────────────────────────▶
 │               │                 │                   │               │  INSERT       │
 │               │                 │                   │               │  incident     │
 │               │                 │  Redirect         │               │◀──────────────│
 │               │◀─────────────────                   │               │               │
 │  Show         │                 │                   │               │               │
 │  Success      │                 │                   │               │               │
 │◀───────────────                 │                   │               │               │
```

---

## File Dependency Tree

```
capstone_project/
│
├── config/
│   └── locations.php ──────────────────────┐
│                                            │
├── app/                                     │
│   ├── Services/                           │
│   │   └── LocationService.php ◀───────────┘
│   │           │                            
│   │           │ Used by                    
│   │           │                            
│   ├── Http/Controllers/                   
│   │   └── IncidentController.php ◀────────┘
│   │           │                            
│   │           │ Returns to                 
│   │           │                            
│   └── Models/                              
│       └── Incident.php                     
│               │                            
│               │ Stores in                  
│               │                            
├── database/                                
│   └── migrations/                          
│       └── *_add_barangay_to_incidents_table.php
│               │                            
│               │ Modifies                   
│               │                            
│           incidents table                  
│                                            
├── resources/views/                         
│   └── Incident/                            
│       └── create.blade.php ◀───────────────┘
│               │ Calls API                  
│               │                            
└── routes/                                  
    └── web.php ─────────────────────────────┘
            Routes to Controller             
```

---

## Class Diagram

```
┌─────────────────────────────────────────┐
│          LocationService                │
├─────────────────────────────────────────┤
│ + getMunicipalities(): array            │
│ + getBarangays(string): array           │
│ + municipalityExists(string): bool      │
│ + barangayExists(string, string): bool  │
│ + searchMunicipalities(string): array   │
│ + searchBarangays(string, string): array│
│ + getMunicipalitiesForSelect(): array   │
│ + getBarangaysForSelect(string): array  │
│ + getAllMunicipalitiesWithBarangays()   │
└──────────────────┬──────────────────────┘
                   │
                   │ Used by
                   │
┌──────────────────▼──────────────────────┐
│       IncidentController                │
├─────────────────────────────────────────┤
│ + index(Request): View                  │
│ + create(): View                        │
│ + store(Request): RedirectResponse      │
│ + getMunicipalities(): JsonResponse     │
│ + getBarangays(Request): JsonResponse   │
│ + municipalities(): array (deprecated)  │
└──────────────────┬──────────────────────┘
                   │
                   │ Manages
                   │
┌──────────────────▼──────────────────────┐
│            Incident (Model)             │
├─────────────────────────────────────────┤
│ # fillable: array                       │
│ # casts: array                          │
│ + assignedStaff(): BelongsTo            │
│ + assignedVehicle(): BelongsTo          │
│ + reporter(): BelongsTo                 │
│ + victims(): HasMany                    │
└─────────────────────────────────────────┘
```

---

## Legend

```
▶  Flow direction
│  Dependency/Connection
┌┐ Component boundary
─  Horizontal line
Uses/Calls/Depends on
```

This architecture follows the **SOLID principles** and **Laravel best practices** for a maintainable, scalable application.

