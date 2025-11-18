<?php

/**
 * Database Normalization Audit Script
 * Run with: php audit_database.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "DATABASE NORMALIZATION AUDIT REPORT\n";
echo "Generated: " . date('Y-m-d H:i:s') . "\n";
echo "Database: " . config('database.default') . "\n";
echo "=================================================================\n\n";

// ===================================
// 1. MUNICIPALITY ANALYSIS
// ===================================
echo "1. MUNICIPALITY ANALYSIS\n";
echo str_repeat("=", 65) . "\n\n";

try {
    echo "Municipalities across all tables:\n";
    echo str_repeat("-", 65) . "\n";
    printf("%-20s %-30s %s\n", "SOURCE", "MUNICIPALITY", "COUNT");
    echo str_repeat("-", 65) . "\n";

    $municipalities = DB::select("
        SELECT 'incidents' as source, municipality, COUNT(*) as count
        FROM incidents
        WHERE municipality IS NOT NULL
        GROUP BY municipality
        UNION ALL
        SELECT 'vehicles' as source, municipality, COUNT(*) as count
        FROM vehicles
        WHERE municipality IS NOT NULL
        GROUP BY municipality
        UNION ALL
        SELECT 'requests' as source, municipality, COUNT(*) as count
        FROM requests
        WHERE municipality IS NOT NULL
        GROUP BY municipality
        UNION ALL
        SELECT 'users' as source, municipality, COUNT(*) as count
        FROM users
        WHERE municipality IS NOT NULL
        GROUP BY municipality
        ORDER BY municipality, source
    ");

    foreach ($municipalities as $row) {
        printf("%-20s %-30s %d\n", $row->source, $row->municipality, $row->count);
    }

    echo "\n\nMunicipality Summary (Total References):\n";
    echo str_repeat("-", 65) . "\n";
    printf("%-40s %s\n", "MUNICIPALITY", "TOTAL");
    echo str_repeat("-", 65) . "\n";

    $summary = DB::select("
        SELECT municipality, COUNT(*) as total_references
        FROM (
            SELECT municipality FROM incidents WHERE municipality IS NOT NULL
            UNION ALL
            SELECT municipality FROM vehicles WHERE municipality IS NOT NULL
            UNION ALL
            SELECT municipality FROM requests WHERE municipality IS NOT NULL
            UNION ALL
            SELECT municipality FROM users WHERE municipality IS NOT NULL
        ) AS all_municipalities
        GROUP BY municipality
        ORDER BY total_references DESC, municipality
    ");

    foreach ($summary as $row) {
        printf("%-40s %d\n", $row->municipality, $row->total_references);
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ===================================
// 2. ENUM VALUE DISTRIBUTION
// ===================================
echo "\n\n2. ENUM VALUE DISTRIBUTION\n";
echo str_repeat("=", 65) . "\n\n";

// Incident Types
try {
    echo "Incident Types:\n";
    echo str_repeat("-", 65) . "\n";
    printf("%-30s %-10s %s\n", "TYPE", "COUNT", "PERCENTAGE");
    echo str_repeat("-", 65) . "\n";

    $types = DB::select("
        SELECT incident_type,
               COUNT(*) as count,
               ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM incidents), 2) as percentage
        FROM incidents
        GROUP BY incident_type
        ORDER BY count DESC
    ");

    foreach ($types as $row) {
        printf("%-30s %-10d %.2f%%\n", $row->incident_type, $row->count, $row->percentage);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Severity Levels
try {
    echo "\n\nSeverity Levels:\n";
    echo str_repeat("-", 65) . "\n";
    printf("%-30s %-10s %s\n", "SEVERITY", "COUNT", "PERCENTAGE");
    echo str_repeat("-", 65) . "\n";

    $severities = DB::select("
        SELECT severity_level,
               COUNT(*) as count,
               ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM incidents), 2) as percentage
        FROM incidents
        GROUP BY severity_level
        ORDER BY count DESC
    ");

    foreach ($severities as $row) {
        printf("%-30s %-10d %.2f%%\n", $row->severity_level, $row->count, $row->percentage);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Vehicle Types
try {
    echo "\n\nVehicle Types:\n";
    echo str_repeat("-", 65) . "\n";
    printf("%-30s %-10s %s\n", "TYPE", "COUNT", "PERCENTAGE");
    echo str_repeat("-", 65) . "\n";

    $vehicleTypes = DB::select("
        SELECT vehicle_type,
               COUNT(*) as count,
               ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM vehicles), 2) as percentage
        FROM vehicles
        GROUP BY vehicle_type
        ORDER BY count DESC
    ");

    foreach ($vehicleTypes as $row) {
        printf("%-30s %-10d %.2f%%\n", $row->vehicle_type, $row->count, $row->percentage);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Request Types
try {
    echo "\n\nRequest Types:\n";
    echo str_repeat("-", 65) . "\n";
    printf("%-40s %-10s %s\n", "TYPE", "COUNT", "PERCENTAGE");
    echo str_repeat("-", 65) . "\n";

    $requestTypes = DB::select("
        SELECT request_type,
               COUNT(*) as count,
               ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM requests), 2) as percentage
        FROM requests
        GROUP BY request_type
        ORDER BY count DESC
    ");

    foreach ($requestTypes as $row) {
        printf("%-40s %-10d %.2f%%\n", $row->request_type, $row->count, $row->percentage);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// User Roles
try {
    echo "\n\nUser Roles:\n";
    echo str_repeat("-", 65) . "\n";
    printf("%-30s %-10s %s\n", "ROLE", "COUNT", "PERCENTAGE");
    echo str_repeat("-", 65) . "\n";

    $roles = DB::select("
        SELECT role,
               COUNT(*) as count,
               ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM users), 2) as percentage
        FROM users
        GROUP BY role
        ORDER BY count DESC
    ");

    foreach ($roles as $row) {
        printf("%-30s %-10d %.2f%%\n", $row->role, $row->count, $row->percentage);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ===================================
// 3. DATA INTEGRITY CHECKS
// ===================================
echo "\n\n3. DATA INTEGRITY CHECKS\n";
echo str_repeat("=", 65) . "\n\n";

// Check casualty count mismatches
try {
    echo "Incidents with mismatched casualty counts:\n";
    echo str_repeat("-", 65) . "\n";

    $mismatches = DB::select("
        SELECT
            i.id,
            i.incident_number,
            i.casualty_count as stored_count,
            COUNT(v.id) as actual_count,
            (i.casualty_count - COUNT(v.id)) as difference
        FROM incidents i
        LEFT JOIN victims v ON i.id = v.incident_id
        GROUP BY i.id, i.incident_number, i.casualty_count
        HAVING i.casualty_count != COUNT(v.id)
        ORDER BY ABS(i.casualty_count - COUNT(v.id)) DESC
        LIMIT 10
    ");

    if (count($mismatches) > 0) {
        printf("%-15s %-12s %-12s %s\n", "INCIDENT #", "STORED", "ACTUAL", "DIFF");
        echo str_repeat("-", 65) . "\n";
        foreach ($mismatches as $row) {
            printf("%-15s %-12d %-12d %+d\n",
                $row->incident_number,
                $row->stored_count,
                $row->actual_count,
                $row->difference
            );
        }
    } else {
        echo "✓ No mismatches found\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ===================================
// 4. HOSPITAL REFERENCES
// ===================================
echo "\n\n4. HOSPITAL REFERENCES (For Lookup Table)\n";
echo str_repeat("=", 65) . "\n\n";

try {
    $hospitals = DB::select("
        SELECT hospital_referred, COUNT(*) as count
        FROM victims
        WHERE hospital_referred IS NOT NULL
        GROUP BY hospital_referred
        ORDER BY count DESC
    ");

    if (count($hospitals) > 0) {
        printf("%-40s %s\n", "HOSPITAL NAME", "REFERENCES");
        echo str_repeat("-", 65) . "\n";
        foreach ($hospitals as $row) {
            printf("%-40s %d\n", $row->hospital_referred, $row->count);
        }
    } else {
        echo "No hospital references found\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ===================================
// 5. SUMMARY STATISTICS
// ===================================
echo "\n\n5. SUMMARY STATISTICS\n";
echo str_repeat("=", 65) . "\n\n";

try {
    $stats = DB::select("
        SELECT 'Incidents' as table_name, COUNT(*) as count FROM incidents
        UNION ALL
        SELECT 'Vehicles' as table_name, COUNT(*) as count FROM vehicles
        UNION ALL
        SELECT 'Victims' as table_name, COUNT(*) as count FROM victims
        UNION ALL
        SELECT 'Requests' as table_name, COUNT(*) as count FROM requests
        UNION ALL
        SELECT 'Users' as table_name, COUNT(*) as count FROM users
    ");

    printf("%-30s %s\n", "TABLE", "RECORDS");
    echo str_repeat("-", 65) . "\n";
    foreach ($stats as $row) {
        printf("%-30s %d\n", $row->table_name, $row->count);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 65) . "\n";
echo "END OF AUDIT REPORT\n";
echo str_repeat("=", 65) . "\n";
