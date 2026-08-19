<?php
namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Payroll;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;
        if (!$employee) abort(403, 'Data karyawan tidak ditemukan.');

        $bulan = now()->month;
        $tahun = now()->year;

        $stats = [
            'hadir'     => $employee->attendances()->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->where('status','hadir')->count(),
            'izin'      => $employee->attendances()->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->where('status','izin')->count(),
            'sakit'     => $employee->attendances()->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->where('status','sakit')->count(),
            'alfa'      => $employee->attendances()->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->where('status','alfa')->count(),
            'terlambat' => $employee->attendances()->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->where('terlambat',1)->count(),
        ];

        $payroll         = Payroll::where('employee_id',$employee->id)->where('status','proses')->latest()->first();
        $recentAbsensi   = $employee->attendances()->latest()->take(10)->get();
        $absensiHariIni  = $employee->attendances()->where('tanggal', Carbon::today())->first();

        return view('karyawan.dashboard.index', compact('employee','stats','payroll','recentAbsensi','absensiHariIni'));
    }

    public function profile()
    {
        $employee = auth()->user()->employee->load('shift');
        return view('karyawan.dashboard.profile', compact('employee'));
    }

    public function qrcode()
    {
        $employee = auth()->user()->employee;
        return view('karyawan.dashboard.qrcode', compact('employee'));
    }
}
