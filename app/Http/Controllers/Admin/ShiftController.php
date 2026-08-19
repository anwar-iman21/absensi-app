<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::withCount('employees')->get();
        return view('admin.shift.index', compact('shifts'));
    }

    public function create() { return view('admin.shift.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'nama_shift'      => 'required|string|max:100',
            'jam_masuk'       => 'required|date_format:H:i',
            'jam_pulang'      => 'required|date_format:H:i',
            'toleransi_menit' => 'required|integer|min:0|max:120',
        ]);
        Shift::create($request->all());
        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    public function edit(Shift $shift) { return view('admin.shift.edit', compact('shift')); }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'nama_shift'      => 'required|string|max:100',
            'jam_masuk'       => 'required|date_format:H:i',
            'jam_pulang'      => 'required|date_format:H:i',
            'toleransi_menit' => 'required|integer|min:0|max:120',
        ]);
        $shift->update($request->all());
        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        if ($shift->employees()->count() > 0) {
            return back()->with('error', 'Shift tidak bisa dihapus karena masih digunakan karyawan.');
        }
        $shift->delete();
        return back()->with('success', 'Shift berhasil dihapus.');
    }
}
