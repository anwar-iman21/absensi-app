<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\SalaryLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with('employee');

        if ($request->status) $query->where('status', $request->status);
        if ($request->bulan)  $query->whereMonth('estimasi_gajian', $request->bulan);
        if ($request->tahun)  $query->whereYear('estimasi_gajian', $request->tahun);

        $payrolls = $query->latest()->paginate(15)->withQueryString();
        return view('admin.payroll.index', compact('payrolls'));
    }

    public function show(int $id)
    {
        $payroll = Payroll::with(['employee.shift','salaryLogs'])->findOrFail($id);
        // Hitung ulang otomatis
        $payroll->hitungGaji();
        return view('admin.payroll.show', compact('payroll'));
    }

    public function generate(Request $request)
    {
        $request->validate(['employee_id' => 'required|exists:employees,id']);

        $employee = Employee::findOrFail($request->employee_id);

        // Cek apakah payroll aktif sudah ada
        $existing = Payroll::where('employee_id', $employee->id)->where('status','proses')->first();
        if ($existing) {
            return back()->with('error', 'Karyawan ini sudah memiliki periode gaji aktif.');
        }

        $payroll = Payroll::create([
            'employee_id'       => $employee->id,
            'periode_mulai'     => Carbon::today(),
            'estimasi_gajian'   => Carbon::today()->addDays(30),
            'target_hari_kerja' => 30,
            'gaji_pokok'        => $employee->gaji_pokok,
            'total_gaji'        => $employee->gaji_pokok,
            'status'            => 'proses',
        ]);

        return back()->with('success', "Periode gaji baru untuk {$employee->nama_lengkap} berhasil dibuat.");
    }

    public function tandaiSelesai(int $id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        $payroll->hitungGaji();
        $payroll->update([
            'status'       => 'selesai',
            'periode_akhir'=> Carbon::today(),
        ]);

        SalaryLog::create([
            'payroll_id'    => $payroll->id,
            'employee_id'   => $payroll->employee_id,
            'tanggal_bayar' => Carbon::today(),
            'jumlah'        => $payroll->total_gaji,
            'metode'        => 'Transfer Bank',
            'keterangan'    => "Pembayaran gaji periode {$payroll->periode_mulai->format('d/m/Y')}",
            'dibuat_oleh'   => auth()->id(),
        ]);

        ActivityLog::catat('Bayar Gaji', "Gaji {$payroll->employee->nama_lengkap} ditandai selesai", 'Payroll', $payroll->id);

        return back()->with('success', 'Gaji berhasil ditandai selesai dan log pembayaran disimpan.');
    }

    public function cetakSlip(int $id)
    {
        $payroll = Payroll::with('employee.shift')->findOrFail($id);
        $payroll->hitungGaji();
        $pdf = Pdf::loadView('admin.payroll.slip-pdf', compact('payroll'));
        return $pdf->download("slip-gaji-{$payroll->employee->employee_id}-{$payroll->id}.pdf");
    }
}
