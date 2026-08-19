<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'total_karyawan'    => Employee::where('status_aktif','aktif')->count(),
            'hadir_hari_ini'    => Attendance::where('tanggal', $today)->where('status','hadir')->count(),
            'izin_hari_ini'     => Attendance::where('tanggal', $today)->whereIn('status',['izin','sakit','cuti'])->count(),
            'alfa_hari_ini'     => Attendance::where('tanggal', $today)->where('status','alfa')->count(),
            'terlambat_hari_ini'=> Attendance::where('tanggal', $today)->where('terlambat', 1)->count(),
            'pending_izin'      => LeaveRequest::where('status','pending')->count(),
            'gajian_bulan_ini'  => Payroll::whereMonth('estimasi_gajian', $today->month)
                                          ->whereYear('estimasi_gajian', $today->year)
                                          ->where('status','proses')->count(),
        ];

        // Absensi 7 hari terakhir untuk grafik
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData[] = [
                'tanggal' => $date->format('d/m'),
                'hadir'   => Attendance::where('tanggal', $date)->where('status','hadir')->count(),
                'alfa'    => Attendance::where('tanggal', $date)->where('status','alfa')->count(),
                'izin'    => Attendance::where('tanggal', $date)->whereIn('status',['izin','sakit','cuti'])->count(),
            ];
        }

        // Absensi terbaru
        $recentAttendances = Attendance::with('employee')
            ->where('tanggal', $today)
            ->latest()
            ->take(10)
            ->get();

        // Pengajuan izin pending
        $pendingLeaves = LeaveRequest::with('employee')
            ->where('status','pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact('stats','chartData','recentAttendances','pendingLeaves'));
    }
}
