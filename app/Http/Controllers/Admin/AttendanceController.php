<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->tanggal) {
            $query->where('tanggal', $request->tanggal);
        } else {
            $query->where('tanggal', Carbon::today());
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->latest()->paginate(20)->withQueryString();
        $employees   = Employee::where('status_aktif','aktif')->get();

        return view('admin.attendance.index', compact('attendances','employees'));
    }

    public function scanPage()
    {
        return view('admin.attendance.scan');
    }

    public function processScan(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $employee = Employee::where('barcode', $request->barcode)
                            ->where('status_aktif', 'aktif')
                            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Karyawan tidak ditemukan atau tidak aktif.'], 404);
        }

        $today      = Carbon::today();
        $now        = Carbon::now();
        $attendance = Attendance::where('employee_id', $employee->id)->where('tanggal', $today)->first();

        if (!$attendance) {
            // Absen masuk
            $shift       = $employee->shift;
            $terlambat   = false;
            $menit       = 0;

            if ($shift) {
                $jamShift   = Carbon::parse($today->format('Y-m-d') . ' ' . $shift->jam_masuk);
                $batasLambat = $jamShift->copy()->addMinutes($shift->toleransi_menit);
                if ($now->gt($batasLambat)) {
                    $terlambat = true;
                    $menit     = $now->diffInMinutes($jamShift);
                }
            }

            $attendance = Attendance::create([
                'employee_id'     => $employee->id,
                'tanggal'         => $today,
                'jam_masuk'       => $now->format('H:i:s'),
                'status'          => 'hadir',
                'terlambat'       => $terlambat,
                'menit_terlambat' => $menit,
                'device_scan'     => 'QR Scanner',
            ]);

            ActivityLog::catat('Absen Masuk', "{$employee->nama_lengkap} absen masuk", 'Attendance', $attendance->id);

            return response()->json([
                'success' => true,
                'type'    => 'masuk',
                'message' => "✅ Absen MASUK berhasil!\n{$employee->nama_lengkap}\nJam: {$now->format('H:i')}".($terlambat ? "\n⚠️ Terlambat {$menit} menit" : ''),
                'data'    => ['nama' => $employee->nama_lengkap, 'jam' => $now->format('H:i'), 'terlambat' => $terlambat],
            ]);
        }

        if ($attendance->jam_pulang) {
            return response()->json([
                'success' => false,
                'message' => "⚠️ {$employee->nama_lengkap} sudah absen masuk & pulang hari ini.",
            ]);
        }

        // Absen pulang
        $attendance->update([
            'jam_pulang' => $now->format('H:i:s'),
        ]);

        ActivityLog::catat('Absen Pulang', "{$employee->nama_lengkap} absen pulang", 'Attendance', $attendance->id);

        return response()->json([
            'success' => true,
            'type'    => 'pulang',
            'message' => "✅ Absen PULANG berhasil!\n{$employee->nama_lengkap}\nJam: {$now->format('H:i')}",
            'data'    => ['nama' => $employee->nama_lengkap, 'jam' => $now->format('H:i')],
        ]);
    }

    public function manualAbsen(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal'     => 'required|date',
            'status'      => 'required|in:hadir,izin,sakit,alfa,cuti',
            'jam_masuk'   => 'nullable|date_format:H:i',
            'jam_pulang'  => 'nullable|date_format:H:i',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'tanggal' => $request->tanggal],
            [
                'jam_masuk'  => $request->jam_masuk ? $request->jam_masuk . ':00' : null,
                'jam_pulang' => $request->jam_pulang ? $request->jam_pulang . ':00' : null,
                'status'     => $request->status,
                'keterangan' => $request->keterangan,
                'device_scan'=> 'Manual Admin',
            ]
        );

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function update(Request $request, int $id)
    {
        $attendance = Attendance::findOrFail($id);
        $request->validate([
            'status'     => 'required|in:hadir,izin,sakit,alfa,cuti',
            'keterangan' => 'nullable|string|max:500',
        ]);
        $attendance->update($request->only('status','jam_masuk','jam_pulang','keterangan'));
        return back()->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Attendance::findOrFail($id)->delete();
        return back()->with('success', 'Data absensi dihapus.');
    }
}
