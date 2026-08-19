<?php
namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        $employee  = auth()->user()->employee;
        $absensi   = $employee->attendances()->latest()->paginate(20);
        $hariIni   = $employee->attendances()->where('tanggal', Carbon::today())->first();
        return view('karyawan.absensi.index', compact('employee','absensi','hariIni'));
    }

    public function scan(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $employee = auth()->user()->employee;

        if ($employee->barcode !== $request->barcode) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak cocok dengan akun Anda.'], 403);
        }

        $today      = Carbon::today();
        $now        = Carbon::now();
        $attendance = Attendance::where('employee_id', $employee->id)->where('tanggal', $today)->first();

        if (!$attendance) {
            $shift       = $employee->shift;
            $terlambat   = false;
            $menit       = 0;

            if ($shift) {
                $jamShift    = Carbon::parse($today->format('Y-m-d').' '.$shift->jam_masuk);
                $batasLambat = $jamShift->copy()->addMinutes($shift->toleransi_menit);
                if ($now->gt($batasLambat)) {
                    $terlambat = true;
                    $menit     = $now->diffInMinutes($jamShift);
                }
            }

            Attendance::create([
                'employee_id'     => $employee->id,
                'tanggal'         => $today,
                'jam_masuk'       => $now->format('H:i:s'),
                'status'          => 'hadir',
                'terlambat'       => $terlambat,
                'menit_terlambat' => $menit,
                'device_scan'     => 'Self Scan',
            ]);

            return response()->json([
                'success' => true, 'type' => 'masuk',
                'message' => "Absen MASUK berhasil! Jam: {$now->format('H:i')}".($terlambat ? " (Terlambat {$menit} menit)" : ''),
            ]);
        }

        if ($attendance->jam_pulang) {
            return response()->json(['success' => false, 'message' => 'Anda sudah absen masuk dan pulang hari ini.']);
        }

        $attendance->update(['jam_pulang' => $now->format('H:i:s')]);
        return response()->json(['success' => true, 'type' => 'pulang', 'message' => "Absen PULANG berhasil! Jam: {$now->format('H:i')}"]);
    }
}
