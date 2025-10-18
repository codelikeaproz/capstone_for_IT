<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SystemLogsController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        // Get filter parameters
        $search = $request->get('search', '');
        $logType = $request->get('log_type', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $perPage = 10; // Fixed pagination limit

        // Build query for logs
        $logsQuery = DB::table('activity_log')
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->select(
                'activity_log.id',
                'activity_log.log_name',
                'activity_log.description',
                'activity_log.subject_type',
                'activity_log.subject_id',
                'activity_log.causer_id',
                'activity_log.properties',
                'activity_log.created_at',
                'activity_log.updated_at',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.role',
                'users.municipality',
                DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as causer_name")
            );

        // Apply filters
        if ($search) {
            $logsQuery->where(function($query) use ($search) {
                $query->where('users.first_name', 'LIKE', "%{$search}%")
                      ->orWhere('users.last_name', 'LIKE', "%{$search}%")
                      ->orWhere('users.email', 'LIKE', "%{$search}%")
                      ->orWhere('activity_log.description', 'LIKE', "%{$search}%")
                      ->orWhere('activity_log.log_name', 'LIKE', "%{$search}%");
            });
        }

        if ($logType) {
            switch ($logType) {
                case 'login':
                    $logsQuery->where('activity_log.log_name', 'login');
                    break;
                case 'activity':
                    $logsQuery->where('activity_log.log_name', '!=', 'login')
                              ->where('activity_log.log_name', '!=', 'created')
                              ->where('activity_log.log_name', '!=', 'updated')
                              ->where('activity_log.log_name', '!=', 'deleted');
                    break;
                case 'created':
                    $logsQuery->where('activity_log.description', 'LIKE', '%created%');
                    break;
                case 'updated':
                    $logsQuery->where('activity_log.description', 'LIKE', '%updated%');
                    break;
                case 'deleted':
                    $logsQuery->where('activity_log.description', 'LIKE', '%deleted%');
                    break;
            }
        }

        if ($dateFrom) {
            $logsQuery->whereDate('activity_log.created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $logsQuery->whereDate('activity_log.created_at', '<=', $dateTo);
        }

        // Order by newest first
        $logsQuery->orderBy('activity_log.created_at', 'desc');

        // Paginate results
        $logs = $logsQuery->paginate($perPage)->appends($request->except('page'));

        // Prepare log data for frontend
        $logs->getCollection()->transform(function ($log) {
            return $this->prepareLogData($log);
        });

        // Get statistics
        $stats = $this->getLogStatistics();

        // Get recent user activity (for the sidebar)
        $userActivity = $this->getUserActivity();

        // Return view with data
        return view('SystemLogs.Index', compact(
            'logs', 'stats', 'userActivity', 'search', 'logType', 'dateFrom', 'dateTo'
        ));
    }

    private function prepareLogData($log)
    {
        // Parse properties if exists
        $properties = $log->properties ? json_decode($log->properties, true) : null;

        // Determine causer name
        $causerName = ($log->first_name && $log->last_name)
            ? trim($log->first_name . ' ' . $log->last_name)
            : 'System';

        // Add prepared data to log object
        $log->log_details = [
            'id' => $log->id,
            'description' => $log->description,
            'log_name' => $log->log_name,
            'subject_type' => $log->subject_type,
            'subject_id' => $log->subject_id,
            'causer' => $causerName,
            'email' => $log->email,
            'role' => $log->role,
            'municipality' => $log->municipality,
            'created_at' => $log->created_at,
            'properties' => $properties,
        ];

        return $log;
    }

    private function getLogStatistics()
    {
        $totalLogs = DB::table('activity_log')->count();
        $todayLogs = DB::table('activity_log')->whereDate('created_at', today())->count();

        $successfulLogins = DB::table('activity_log')
            ->where('log_name', 'login')
            ->where('description', 'LIKE', '%Successful%')
            ->count();

        $failedLogins = DB::table('activity_log')
            ->where('log_name', 'login')
            ->where('description', 'LIKE', '%Failed%')
            ->count();

        $activeUsersToday = DB::table('activity_log')
            ->join('users', 'activity_log.causer_id', '=', 'users.id')
            ->whereDate('activity_log.created_at', today())
            ->distinct('users.id')
            ->count('users.id');

        return [
            'total_logs' => $totalLogs,
            'today_logs' => $todayLogs,
            'successful_logins' => $successfulLogins,
            'failed_logins' => $failedLogins,
            'active_users_today' => $activeUsersToday,
        ];
    }

    private function getUserActivity()
    {
        return DB::table('activity_log')
            ->join('users', 'activity_log.causer_id', '=', 'users.id')
            ->select('users.first_name', 'users.last_name', 'activity_log.description', 'activity_log.created_at')
            ->latest('activity_log.created_at')
            ->take(15)
            ->get();
    }
}
