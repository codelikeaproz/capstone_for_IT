-- DATABASE NORMALIZATION AUDIT
-- Generated: 2025-11-18
-- Purpose: Identify normalization opportunities

-- ===================================
-- 1. MUNICIPALITY ANALYSIS
-- ===================================

-- Find all unique municipalities across all tables
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
ORDER BY municipality, source;

-- Detect municipality inconsistencies (case sensitivity, typos)
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
ORDER BY total_references DESC, municipality;

-- ===================================
-- 2. ENUM VALUE DISTRIBUTION
-- ===================================

-- Incident Types
SELECT incident_type, COUNT(*) as count,
       ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM incidents
GROUP BY incident_type
ORDER BY count DESC;

-- Severity Levels
SELECT severity_level, COUNT(*) as count,
       ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM incidents
GROUP BY severity_level
ORDER BY count DESC;

-- Incident Status
SELECT status, COUNT(*) as count,
       ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM incidents
GROUP BY status
ORDER BY count DESC;

-- Vehicle Types
SELECT vehicle_type, COUNT(*) as count,
       ROUND(COUNT(*) * 100.0 / SUM(COUNT(*) OVER (), 2) as percentage
FROM vehicles
GROUP BY vehicle_type
ORDER BY count DESC;

-- Vehicle Status
SELECT status, COUNT(*) as count,
       ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM vehicles
GROUP BY status
ORDER BY count DESC;

-- Medical Status Distribution
SELECT medical_status, COUNT(*) as count,
       ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM victims
GROUP BY medical_status
ORDER BY count DESC;

-- Request Types
SELECT request_type, COUNT(*) as count,
       ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM requests
GROUP BY request_type
ORDER BY count DESC;

-- User Roles
SELECT role, COUNT(*) as count,
       ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER (), 2) as percentage
FROM users
GROUP BY role
ORDER BY count DESC;

-- ===================================
-- 3. DATA INTEGRITY CHECKS
-- ===================================

-- Check for incidents with mismatched victim counts
SELECT
    i.id,
    i.incident_number,
    i.casualty_count as stored_count,
    COUNT(v.id) as actual_victim_count,
    (i.casualty_count - COUNT(v.id)) as difference
FROM incidents i
LEFT JOIN victims v ON i.id = v.incident_id
GROUP BY i.id, i.incident_number, i.casualty_count
HAVING i.casualty_count != COUNT(v.id)
ORDER BY ABS(i.casualty_count - COUNT(v.id)) DESC
LIMIT 20;

-- Check for injury count mismatches
SELECT
    i.id,
    i.incident_number,
    i.injury_count as stored_injury_count,
    COUNT(CASE WHEN v.medical_status IN ('minor_injury', 'major_injury', 'critical') THEN 1 END) as actual_injury_count,
    (i.injury_count - COUNT(CASE WHEN v.medical_status IN ('minor_injury', 'major_injury', 'critical') THEN 1 END)) as difference
FROM incidents i
LEFT JOIN victims v ON i.id = v.incident_id
GROUP BY i.id, i.incident_number, i.injury_count
HAVING i.injury_count != COUNT(CASE WHEN v.medical_status IN ('minor_injury', 'major_injury', 'critical') THEN 1 END)
ORDER BY ABS(difference) DESC
LIMIT 20;

-- Check for fatality count mismatches
SELECT
    i.id,
    i.incident_number,
    i.fatality_count as stored_fatality_count,
    COUNT(CASE WHEN v.medical_status = 'deceased' THEN 1 END) as actual_fatality_count,
    (i.fatality_count - COUNT(CASE WHEN v.medical_status = 'deceased' THEN 1 END)) as difference
FROM incidents i
LEFT JOIN victims v ON i.id = v.incident_id
GROUP BY i.id, i.incident_number, i.fatality_count
HAVING i.fatality_count != COUNT(CASE WHEN v.medical_status = 'deceased' THEN 1 END)
ORDER BY ABS(difference) DESC
LIMIT 20;

-- ===================================
-- 4. HOSPITAL REFERENCES (for lookup table)
-- ===================================

SELECT hospital_referred, COUNT(*) as count
FROM victims
WHERE hospital_referred IS NOT NULL
GROUP BY hospital_referred
ORDER BY count DESC;

-- ===================================
-- 5. JSON FIELD ANALYSIS
-- ===================================

-- Count incidents with media files
SELECT
    COUNT(*) as total_incidents,
    COUNT(CASE WHEN photos IS NOT NULL AND photos::text != '[]' THEN 1 END) as has_photos,
    COUNT(CASE WHEN videos IS NOT NULL AND videos::text != '[]' THEN 1 END) as has_videos,
    COUNT(CASE WHEN documents IS NOT NULL AND documents::text != '[]' THEN 1 END) as has_documents
FROM incidents;

-- Count vehicles with equipment
SELECT
    COUNT(*) as total_vehicles,
    COUNT(CASE WHEN equipment_list IS NOT NULL AND equipment_list::text != '[]' THEN 1 END) as has_equipment
FROM vehicles;

-- Count requests with documents
SELECT
    COUNT(*) as total_requests,
    COUNT(CASE WHEN supporting_documents IS NOT NULL AND supporting_documents::text != '[]' THEN 1 END) as has_supporting_docs,
    COUNT(CASE WHEN generated_reports IS NOT NULL AND generated_reports::text != '[]' THEN 1 END) as has_generated_reports
FROM requests;

-- ===================================
-- 6. SUMMARY STATISTICS
-- ===================================

SELECT 'Total Records Summary' as metric;
SELECT 'Incidents' as table_name, COUNT(*) as count FROM incidents
UNION ALL
SELECT 'Vehicles' as table_name, COUNT(*) as count FROM vehicles
UNION ALL
SELECT 'Victims' as table_name, COUNT(*) as count FROM victims
UNION ALL
SELECT 'Requests' as table_name, COUNT(*) as count FROM requests
UNION ALL
SELECT 'Users' as table_name, COUNT(*) as count FROM users;
