<?php

namespace App\Services;

class LocationService
{
    /**
     * Get all municipalities in Bukidnon.
     *
     * @return array
     */
    public static function getMunicipalities(): array
    {
        return array_keys(config('locations.municipalities'));
    }

    /**
     * Get all municipalities with their barangays.
     *
     * @return array
     */
    public static function getAllMunicipalitiesWithBarangays(): array
    {
        return config('locations.municipalities');
    }

    /**
     * Get barangays for a specific municipality.
     *
     * @param string $municipality
     * @return array
     */
    public static function getBarangays(string $municipality): array
    {
        $municipalities = config('locations.municipalities');

        return $municipalities[$municipality] ?? [];
    }

    /**
     * Check if a municipality exists.
     *
     * @param string $municipality
     * @return bool
     */
    public static function municipalityExists(string $municipality): bool
    {
        return array_key_exists($municipality, config('locations.municipalities'));
    }

    /**
     * Check if a barangay exists in a specific municipality.
     *
     * @param string $municipality
     * @param string $barangay
     * @return bool
     */
    public static function barangayExists(string $municipality, string $barangay): bool
    {
        $barangays = self::getBarangays($municipality);
        return in_array($barangay, $barangays);
    }

    /**
     * Get municipalities as key-value pairs for select dropdowns.
     *
     * @return array
     */
    public static function getMunicipalitiesForSelect(): array
    {
        $municipalities = self::getMunicipalities();
        return array_combine($municipalities, $municipalities);
    }

    /**
     * Get barangays as key-value pairs for select dropdowns.
     *
     * @param string $municipality
     * @return array
     */
    public static function getBarangaysForSelect(string $municipality): array
    {
        $barangays = self::getBarangays($municipality);
        return array_combine($barangays, $barangays);
    }

    /**
     * Search municipalities by name.
     *
     * @param string $search
     * @return array
     */
    public static function searchMunicipalities(string $search): array
    {
        $municipalities = self::getMunicipalities();

        return array_filter($municipalities, function($municipality) use ($search) {
            return stripos($municipality, $search) !== false;
        });
    }

    /**
     * Search barangays by name within a municipality.
     *
     * @param string $municipality
     * @param string $search
     * @return array
     */
    public static function searchBarangays(string $municipality, string $search): array
    {
        $barangays = self::getBarangays($municipality);

        return array_filter($barangays, function($barangay) use ($search) {
            return stripos($barangay, $search) !== false;
        });
    }
}

