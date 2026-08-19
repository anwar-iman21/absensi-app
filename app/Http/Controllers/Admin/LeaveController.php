<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with('employee');
        if ($request->status) $query->where('status', $request->status);
        $leaves = $query->latest()->paginate(15)->withQueryString();
        return view('admin.leave.index', compact('leaves'));
    }

    public function approve(Request $request, int $id)
    {
        $leave = LeaveRequest::with('employee')->findOrFail($id);

        $leave->update([
            'status'       => 'disetujui',
            'approved_by'  => auth()->id(),
            'catatan_admin'=> $request->catatan_admin,
        ]);

        // Buat record absensi untuk setiap hari izin
        $start = \Carbon\Carbon::parse($leave->tanggal_mulai);
        $end   = \Carbon\Carbon::parse($leave->tanggal_akhir);

        while ($start->lte($end)) {
            Attendance::updateOrCreate(
                ['employee_id' => $leave->employee_id, 'tanggal' => $start->toDateString()],
                ['status' => $leave->jenis, 'keterangan' => $leave->keterangan, 'device_scan' => 'Approval Admin']
            );
            $start->addDay();
        }

        // Update payroll aktif
        $payroll = Payroll::where('employee_id', $leave->employee_id)->where('status','proses')->first();
        if ($payroll) $payroll->hitungGaji();

        ActivityLog::catat('Approve Izin', "Izin {$leave->employee->nama_lengkap} disetujui", 'LeaveRequest', $leave->id);

        return back()->with('success', "Pengajuan izin {$leave->employee->nama_lengkap} disetujui.");
    }

    public function reject(Request $request, int $id)
    {
        $leave = LeaveRequest::with('employee')->findOrFail($id);
        $leave->update([
            'status'        => 'ditolak',
            'approved_by'   => auth()->id(),
            'catatan_admin' => $request->catatan_admin,
        ]);

        ActivityLog::catat('Tolak Izin', "Izin {$leave->employee->nama_lengkap} ditolak", 'LeaveRequest', $leave->id);

        return back()->with('success', "Pengajuan izin {$leave->employee->nama_lengkap} ditolak.");
    }
}
