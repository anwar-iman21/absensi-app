<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $attendances = Attendance::with('employee')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->latest('tanggal')
            ->get();

        $rekap = Employee::where('status_aktif','aktif')->get()->map(function ($emp) use ($bulan, $tahun) {
            $abs = $emp->attendances()->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->get();
            return [
                'employee'   => $emp,
                'hadir'      => $abs->where('status','hadir')->count(),
                'izin'       => $abs->where('status','izin')->count(),
                'sakit'      => $abs->where('status','sakit')->count(),
                'alfa'       => $abs->where('status','alfa')->count(),
                'cuti'       => $abs->where('status','cuti')->count(),
                'terlambat'  => $abs->where('terlambat',1)->count(),
            ];
        });

        return view('admin.reports.attendance', compact('attendances','rekap','bulan','tahun'));
    }

    public function payroll(Request $request)
    {
        $tahun    = $request->tahun ?? now()->year;
        $payrolls = Payroll::with('employee')
            ->whereYear('estimasi_gajian', $tahun)
            ->latest()
            ->get();
        return view('admin.reports.payroll', compact('payrolls','tahun'));
    }

    public function leave(Request $request)
    {
        $bulan  = $request->bulan ?? now()->month;
        $tahun  = $request->tahun ?? now()->year;
        $leaves = LeaveRequest::with('employee')
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun)
            ->latest()
            ->get();
        return view('admin.reports.leave', compact('leaves','bulan','tahun'));
    }

    public function exportAttendancePdf(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;
        $rekap = Employee::where('status_aktif','aktif')->get()->map(function ($emp) use ($bulan,$tahun) {
            $abs = $emp->attendances()->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun)->get();
            return ['employee'=>$emp,'hadir'=>$abs->where('status','hadir')->count(),'izin'=>$abs->where('status','izin')->count(),'sakit'=>$abs->where('status','sakit')->count(),'alfa'=>$abs->where('status','alfa')->count(),'cuti'=>$abs->where('status','cuti')->count()];
        });
        $namaBulan = Carbon::createFromDate($tahun,$bulan,1)->translatedFormat('F Y');
        $pdf = Pdf::loadView('admin.reports.attendance-pdf', compact('rekap','namaBulan'))->setPaper('a4','landscape');
        return $pdf->download("laporan-absensi-{$bulan}-{$tahun}.pdf");
    }

    public function exportAttendanceExcel(Request $request)
    {
        // Implementasi export Excel — buat class AttendanceExport terpisah jika perlu
        return back()->with('info', 'Fitur export Excel dapat dikembangkan dengan Maatwebsite Excel.');
    }

    public function exportPayrollPdf(Request $request)
    {
        $tahun    = $request->tahun ?? now()->year;
        $payrolls = Payroll::with('employee')->whereYear('estimasi_gajian',$tahun)->get();
        $pdf = Pdf::loadView('admin.reports.payroll-pdf', compact('payrolls','tahun'))->setPaper('a4','landscape');
        return $pdf->download("laporan-gaji-{$tahun}.pdf");
    }
}
