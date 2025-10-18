<?php

/**
 * LocationService Usage Examples
 *
 * This file contains practical examples of how to use the LocationService
 * in various scenarios throughout your application.
 */

namespace Examples;

use App\Services\LocationService;
use Illuminate\Http\Request;

class LocationServiceExamples
{
    /**
     * Example 1: Display all municipalities in a dropdown
     */
    public function displayMunicipalityDropdown()
    {
        $municipalities = LocationService::getMunicipalities();

        return view('example', compact('municipalities'));

        // In Blade:
        // <select name="municipality">
        //     @foreach($municipalities as $municipality)
        //         <option value="{{ $municipality }}">{{ $municipality }}</option>
        //     @endforeach
        // </select>
    }

    /**
     * Example 2: Get barangays for a specific municipality
     */
    public function getBarangaysForMunicipality(string $municipality)
    {
        $barangays = LocationService::getBarangays($municipality);

        return response()->json([
            'municipality' => $municipality,
            'barangays' => $barangays,
            'count' => count($barangays)
        ]);
    }

    /**
     * Example 3: Validate municipality and barangay before saving
     */
    public function validateLocation(Request $request)
    {
        $municipality = $request->input('municipality');
        $barangay = $request->input('barangay');

        // Check if municipality exists
        if (!LocationService::municipalityExists($municipality)) {
            return back()->withErrors([
                'municipality' => 'The selected municipality is invalid.'
            ]);
        }

        // Check if barangay exists in the selected municipality
        if (!LocationService::barangayExists($municipality, $barangay)) {
            return back()->withErrors([
                'barangay' => 'The selected barangay does not exist in ' . $municipality
            ]);
        }

        return response()->json(['message' => 'Location is valid!']);
    }

    /**
     * Example 4: Search for municipalities
     */
    public function searchMunicipalities(string $query)
    {
        $results = LocationService::searchMunicipalities($query);

        return response()->json([
            'query' => $query,
            'results' => $results,
            'count' => count($results)
        ]);
    }

    /**
     * Example 5: Search for barangays within a municipality
     */
    public function searchBarangays(string $municipality, string $query)
    {
        // First verify the municipality exists
        if (!LocationService::municipalityExists($municipality)) {
            return response()->json(['error' => 'Municipality not found'], 404);
        }

        $results = LocationService::searchBarangays($municipality, $query);

        return response()->json([
            'municipality' => $municipality,
            'query' => $query,
            'results' => $results,
            'count' => count($results)
        ]);
    }

    /**
     * Example 6: Get all locations in a structured format
     */
    public function getAllLocationsStructured()
    {
        $allLocations = LocationService::getAllMunicipalitiesWithBarangays();

        // Transform to a more API-friendly format
        $structured = [];
        foreach ($allLocations as $municipality => $barangays) {
            $structured[] = [
                'municipality' => $municipality,
                'barangay_count' => count($barangays),
                'barangays' => $barangays
            ];
        }

        return response()->json([
            'total_municipalities' => count($structured),
            'locations' => $structured
        ]);
    }

    /**
     * Example 7: Generate statistics about locations
     */
    public function getLocationStatistics()
    {
        $municipalities = LocationService::getAllMunicipalitiesWithBarangays();

        $stats = [
            'total_municipalities' => count($municipalities),
            'total_barangays' => 0,
            'municipality_details' => []
        ];

        foreach ($municipalities as $municipality => $barangays) {
            $barangayCount = count($barangays);
            $stats['total_barangays'] += $barangayCount;
            $stats['municipality_details'][] = [
                'name' => $municipality,
                'barangay_count' => $barangayCount
            ];
        }

        // Sort by barangay count (descending)
        usort($stats['municipality_details'], function($a, $b) {
            return $b['barangay_count'] - $a['barangay_count'];
        });

        return response()->json($stats);
    }

    /**
     * Example 8: Custom validation rule usage
     */
    public function customValidation(Request $request)
    {
        $validated = $request->validate([
            'municipality' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!LocationService::municipalityExists($value)) {
                        $fail("The selected {$attribute} is invalid.");
                    }
                }
            ],
            'barangay' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $municipality = $request->input('municipality');
                    if (!LocationService::barangayExists($municipality, $value)) {
                        $fail("The selected {$attribute} is invalid for the chosen municipality.");
                    }
                }
            ]
        ]);

        return response()->json([
            'message' => 'Validation passed!',
            'data' => $validated
        ]);
    }

    /**
     * Example 9: Filter data by location
     */
    public function filterIncidentsByLocation(string $municipality, ?string $barangay = null)
    {
        // Assuming you have an Incident model
        $query = \App\Models\Incident::where('municipality', $municipality);

        if ($barangay) {
            $query->where('barangay', $barangay);
        }

        $incidents = $query->get();

        return response()->json([
            'municipality' => $municipality,
            'barangay' => $barangay ?? 'All',
            'incident_count' => $incidents->count(),
            'incidents' => $incidents
        ]);
    }

    /**
     * Example 10: Create a location hierarchy for navigation
     */
    public function createLocationHierarchy()
    {
        $municipalities = LocationService::getAllMunicipalitiesWithBarangays();

        $hierarchy = [];
        foreach ($municipalities as $municipality => $barangays) {
            $hierarchy[] = [
                'label' => $municipality,
                'value' => $municipality,
                'children' => array_map(function($barangay) use ($municipality) {
                    return [
                        'label' => $barangay,
                        'value' => $barangay,
                        'parent' => $municipality
                    ];
                }, $barangays)
            ];
        }

        return response()->json($hierarchy);
    }

    /**
     * Example 11: Autocomplete endpoint for municipalities
     */
    public function autocomplete(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $municipalities = LocationService::searchMunicipalities($query);

        $suggestions = array_map(function($municipality) {
            return [
                'id' => $municipality,
                'text' => $municipality,
                'value' => $municipality
            ];
        }, $municipalities);

        return response()->json(['suggestions' => array_values($suggestions)]);
    }

    /**
     * Example 12: Generate location breadcrumbs
     */
    public function generateBreadcrumbs(string $municipality, ?string $barangay = null)
    {
        $breadcrumbs = [
            ['label' => 'Bukidnon', 'url' => '/locations']
        ];

        if (LocationService::municipalityExists($municipality)) {
            $breadcrumbs[] = [
                'label' => $municipality,
                'url' => '/locations/' . urlencode($municipality)
            ];

            if ($barangay && LocationService::barangayExists($municipality, $barangay)) {
                $breadcrumbs[] = [
                    'label' => $barangay,
                    'url' => '/locations/' . urlencode($municipality) . '/' . urlencode($barangay)
                ];
            }
        }

        return response()->json(['breadcrumbs' => $breadcrumbs]);
    }
}

