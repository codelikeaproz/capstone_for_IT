@echo off
echo ========================================
echo  Media Gallery Storage Link Fix
echo ========================================
echo.

echo [1/4] Checking current storage link status...
if exist "public\storage" (
    echo    ✓ Storage link exists
) else (
    echo    ✗ Storage link does NOT exist
)
echo.

echo [2/4] Clearing Laravel caches...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo    ✓ Caches cleared
echo.

echo [3/4] Removing old storage link (if exists)...
if exist "public\storage" (
    rmdir "public\storage"
    echo    ✓ Old link removed
) else (
    echo    - No old link to remove
)
echo.

echo [4/4] Creating new storage link...
php artisan storage:link
echo.

echo ========================================
echo  Verification
echo ========================================
echo.

if exist "public\storage" (
    echo ✓ SUCCESS: Storage link created!
    echo.
    echo Next steps:
    echo 1. Refresh your browser
    echo 2. Check if images now display
    echo 3. Check browser console for any errors
) else (
    echo ✗ FAILED: Storage link not created
    echo.
    echo Troubleshooting:
    echo 1. Run this script as Administrator
    echo 2. Check if storage/app/public directory exists
    echo 3. Check file permissions
)
echo.

echo ========================================
pause
