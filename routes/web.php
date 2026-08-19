<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\LeaveController as AdminLeave;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboard;
use App\Http\Controllers\Karyawan\AbsensiController;
use App\Http\Controllers\Karyawan\LeaveController as KaryawanLeave;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect('/login'));

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('auth', 'role:admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Employees
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/{id}/kartu', [EmployeeController::class, 'cetakKartu'])->name('employees.kartu');
    Route::get('employees/{id}/qrcode', [EmployeeController::class, 'downloadQr'])->name('employees.qrcode');

    // Attendance
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance/manual', [AttendanceController::class, 'manualAbsen'])->name('attendance.manual');
    Route::put('attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    Route::get('attendance/scan', [AttendanceController::class, 'scanPage'])->name('attendance.scan');
    Route::post('attendance/scan', [AttendanceController::class, 'processScan'])->name('attendance.process_scan');

    // Payroll
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('payroll/{id}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
    Route::put('payroll/{id}/selesai', [PayrollController::class, 'tandaiSelesai'])->name('payroll.selesai');
    Route::get('payroll/{id}/cetak', [PayrollController::class, 'cetakSlip'])->name('payroll.cetak');

    // Leave
    Route::get('leave', [AdminLeave::class, 'index'])->name('leave.index');
    Route::put('leave/{id}/approve', [AdminLeave::class, 'approve'])->name('leave.approve');
    Route::put('leave/{id}/reject', [AdminLeave::class, 'reject'])->name('leave.reject');

    // Shifts
    Route::resource('shifts', ShiftController::class);

    // Reports
    Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('reports/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');
    Route::get('reports/leave', [ReportController::class, 'leave'])->name('reports.leave');
    Route::get('reports/attendance/export-pdf', [ReportController::class, 'exportAttendancePdf'])->name('reports.attendance.pdf');
    Route::get('reports/attendance/export-excel', [ReportController::class, 'exportAttendanceExcel'])->name('reports.attendance.excel');
    Route::get('reports/payroll/export-pdf', [ReportController::class, 'exportPayrollPdf'])->name('reports.payroll.pdf');
});

/*
|--------------------------------------------------------------------------
| KARYAWAN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('karyawan')->name('karyawan.')->middleware('auth', 'role:karyawan')->group(function () {

    // Dashboard
    Route::get('/dashboard', [KaryawanDashboard::class, 'index'])->name('dashboard');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/scan', [AbsensiController::class, 'scan'])->name('absensi.scan');

    // Leave
    Route::get('/leave', [KaryawanLeave::class, 'index'])->name('leave.index');
    Route::post('/leave', [KaryawanLeave::class, 'store'])->name('leave.store');
    Route::get('/leave/{id}', [KaryawanLeave::class, 'show'])->name('leave.show');

    // Profile & QR
    Route::get('/profile', [KaryawanDashboard::class, 'profile'])->name('profile');
    Route::get('/qrcode', [KaryawanDashboard::class, 'qrcode'])->name('qrcode');
});
