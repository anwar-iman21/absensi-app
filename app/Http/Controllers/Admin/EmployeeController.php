<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\User;
use App\Models\Payroll;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user','shift']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_lengkap','like','%'.$request->search.'%')
                  ->orWhere('employee_id','like','%'.$request->search.'%')
                  ->orWhere('jabatan','like','%'.$request->search.'%')
                  ->orWhere('divisi','like','%'.$request->search.'%');
            });
        }

        if ($request->status) {
            $query->where('status_aktif', $request->status);
        }

        $employees = $query->latest()->paginate(15)->withQueryString();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $shifts = Shift::all();
        return view('admin.employees.create', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'jabatan'       => 'required|string|max:100',
            'divisi'        => 'required|string|max:100',
            'tanggal_masuk' => 'required|date',
            'status_kerja'  => 'required|in:Tetap,Kontrak,Magang,Freelance',
            'gaji_pokok'    => 'required|numeric|min:0',
            'shift_id'      => 'required|exists:shifts,id',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Buat user login
        $user = User::create([
            'name'     => $request->nama_lengkap,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'karyawan',
        ]);

        // Generate employee ID unik
        $lastEmp   = Employee::orderByDesc('id')->first();
        $nextNum   = $lastEmp ? (intval(substr($lastEmp->employee_id, 3)) + 1) : 1;
        $empId     = 'EMP' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        // Generate barcode unik
        $barcode = $empId . '-' . strtoupper(Str::random(6));

        // Upload foto
        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = $empId . '_' . time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public/foto-karyawan', $fotoName);
        }

        $employee = Employee::create([
            'user_id'       => $user->id,
            'employee_id'   => $empId,
            'nama_lengkap'  => $request->nama_lengkap,
            'foto'          => $fotoName,
            'jabatan'       => $request->jabatan,
            'divisi'        => $request->divisi,
            'no_hp'         => $request->no_hp,
            'email'         => $request->email,
            'alamat'        => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_masuk' => $request->tanggal_masuk,
            'status_kerja'  => $request->status_kerja,
            'gaji_pokok'    => $request->gaji_pokok,
            'shift_id'      => $request->shift_id,
            'barcode'       => $barcode,
            'status_aktif'  => 'aktif',
        ]);

        // Buat payroll awal
        Payroll::create([
            'employee_id'       => $employee->id,
            'periode_mulai'     => $request->tanggal_masuk,
            'estimasi_gajian'   => Carbon::parse($request->tanggal_masuk)->addDays(30),
            'target_hari_kerja' => 30,
            'gaji_pokok'        => $request->gaji_pokok,
            'total_gaji'        => $request->gaji_pokok,
            'status'            => 'proses',
        ]);

        ActivityLog::catat('Tambah Karyawan', "Karyawan {$employee->nama_lengkap} ditambahkan", 'Employee', $employee->id);

        return redirect()->route('admin.employees.index')
            ->with('success', "Karyawan {$employee->nama_lengkap} berhasil ditambahkan. ID: {$empId}");
    }

    public function show(Employee $employee)
    {
        $employee->load(['user','shift','attendances' => function($q){
            $q->latest()->take(30);
        },'payrolls','leaveRequests']);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $shifts = Shift::all();
        return view('admin.employees.edit', compact('employee','shifts'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'jabatan'       => 'required|string|max:100',
            'divisi'        => 'required|string|max:100',
            'gaji_pokok'    => 'required|numeric|min:0',
            'shift_id'      => 'required|exists:shifts,id',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status_aktif'  => 'required|in:aktif,nonaktif',
        ]);

        $fotoName = $employee->foto;
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($fotoName) Storage::delete('public/foto-karyawan/' . $fotoName);
            $fotoName = $employee->employee_id . '_' . time() . '.' . $request->foto->extension();
            $request->foto->storeAs('public/foto-karyawan', $fotoName);
        }

        $employee->update([
            'nama_lengkap'  => $request->nama_lengkap,
            'foto'          => $fotoName,
            'jabatan'       => $request->jabatan,
            'divisi'        => $request->divisi,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'status_kerja'  => $request->status_kerja,
            'gaji_pokok'    => $request->gaji_pokok,
            'shift_id'      => $request->shift_id,
            'status_aktif'  => $request->status_aktif,
        ]);

        // Update nama di user
        $employee->user->update(['name' => $request->nama_lengkap]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $employee->user->update(['password' => Hash::make($request->password)]);
        }

        ActivityLog::catat('Edit Karyawan', "Data karyawan {$employee->nama_lengkap} diperbarui", 'Employee', $employee->id);

        return redirect()->route('admin.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $nama = $employee->nama_lengkap;
        if ($employee->foto) Storage::delete('public/foto-karyawan/' . $employee->foto);
        $employee->user->delete(); // cascade delete employee
        ActivityLog::catat('Hapus Karyawan', "Karyawan {$nama} dihapus");
        return redirect()->route('admin.employees.index')
            ->with('success', "Karyawan {$nama} berhasil dihapus.");
    }

    public function cetakKartu(int $id)
    {
        $employee = Employee::with('shift')->findOrFail($id);
        return view('admin.employees.kartu', compact('employee'));
    }

    public function downloadQr(int $id)
    {
        $employee = Employee::findOrFail($id);
        return response()->json(['barcode' => $employee->barcode]);
    }
}
