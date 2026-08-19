<?php
namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $employee = auth()->user()->employee;
        $leaves   = $employee->leaveRequests()->latest()->paginate(10);
        return view('karyawan.leave.index', compact('employee','leaves'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis'         => 'required|in:izin,sakit,cuti',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'    => 'required|string|max:500',
            'file_bukti'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $employee    = auth()->user()->employee;
        $start       = Carbon::parse($request->tanggal_mulai);
        $end         = Carbon::parse($request->tanggal_akhir);
        $jumlahHari  = $start->diffInDays($end) + 1;

        $fileBukti = null;
        if ($request->hasFile('file_bukti')) {
            $fileBukti = time().'_'.$request->file_bukti->getClientOriginalName();
            $request->file_bukti->storeAs('public/dokumen-izin', $fileBukti);
        }

        LeaveRequest::create([
            'employee_id'   => $employee->id,
            'jenis'         => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
            'jumlah_hari'   => $jumlahHari,
            'keterangan'    => $request->keterangan,
            'file_bukti'    => $fileBukti,
            'status'        => 'pending',
        ]);

        return back()->with('success', 'Pengajuan izin berhasil dikirim. Menunggu persetujuan admin.');
    }

    public function show(int $id)
    {
        $employee = auth()->user()->employee;
        $leave    = LeaveRequest::where('employee_id', $employee->id)->findOrFail($id);
        return view('karyawan.leave.show', compact('leave'));
    }
}
