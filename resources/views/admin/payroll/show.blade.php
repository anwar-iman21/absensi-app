@extends('layouts.app')
@section('title', 'Detail Payroll')
@section('page-title', 'Detail Payroll')

@section('content')
@php $emp = $payroll->employee; @endphp
<div class="row g-3">
    <div class="col-md-5">
        <div class="page-card card p-4 mb-3">
            <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-person-fill text-primary me-2"></i>Info Karyawan</h6>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:56px;height:56px;border-radius:50%;background:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;font-weight:700;">
                    {{ strtoupper(substr($emp->nama_lengkap,0,1)) }}
                </div>
                <div>
                    <div class="fw-bold">{{ $emp->nama_lengkap }}</div>
                    <div class="text-muted small">{{ $emp->jabatan }} • {{ $emp->divisi }}</div>
                    <div class="text-muted small">{{ $emp->employee_id }}</div>
                </div>
            </div>
            <div class="row g-2 text-center">
                @foreach([['Hadir','total_hadir','success'],['Izin','total_izin','warning'],['Sakit','total_sakit','info'],['Alfa','total_alfa','danger'],['Cuti','total_cuti','secondary']] as [$lbl,$key,$color])
                <div class="col">
                    <div class="p-2 rounded" style="background:#f8fafc;">
                        <div class="fw-bold text-{{ $color }}">{{ $payroll->$key }}</div>
                        <div style="font-size:.65rem;color:#64748b;">{{ $lbl }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="page-card card p-4">
            <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-calendar-fill text-info me-2"></i>Info Periode</h6>
            @php
            $items = [
                'Periode Mulai'    => $payroll->periode_mulai->format('d/m/Y'),
                'Estimasi Gajian'  => $payroll->estimasi_gajian->format('d/m/Y'),
                'Target Hari Kerja'=> $payroll->target_hari_kerja.' hari',
                'Total Hadir'      => $payroll->total_hadir.' hari',
                'Hari Mundur'      => '+'.$payroll->hari_mundur.' hari',
                'Status'           => ucfirst($payroll->status),
            ];
            @endphp
            @foreach($items as $lbl => $val)
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">{{ $lbl }}</span>
                <span class="fw-semibold small">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-md-7">
        <div class="page-card card p-4 mb-3">
            <h6 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-cash-stack text-success me-2"></i>Rincian Gaji</h6>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span>Gaji Pokok</span>
                <span class="fw-semibold">Rp {{ number_format($payroll->gaji_pokok,0,',','.') }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom text-danger">
                <span>Potongan Ketidakhadiran ({{ $payroll->hari_mundur }} hari)</span>
                <span class="fw-semibold">- Rp {{ number_format($payroll->potongan,0,',','.') }}</span>
            </div>
            @if($payroll->bonus > 0)
            <div class="d-flex justify-content-between py-2 border-bottom text-success">
                <span>Bonus</span>
                <span class="fw-semibold">+ Rp {{ number_format($payroll->bonus,0,',','.') }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between py-3 mt-1 rounded px-2" style="background:#f0fdf4;">
                <span class="fw-bold fs-6">Total Gaji Diterima</span>
                <span class="fw-bold fs-5 text-success">Rp {{ number_format($payroll->total_gaji,0,',','.') }}</span>
            </div>

            <!-- Progress hari kerja -->
            <div class="mt-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Progress Hari Kerja</span>
                    <span class="fw-semibold">{{ $payroll->total_hadir }} / {{ $payroll->target_hari_kerja }} hari</span>
                </div>
                <div class="progress" style="height:10px;border-radius:10px;">
                    <div class="progress-bar bg-primary" style="width:{{ min(100, round($payroll->total_hadir/$payroll->target_hari_kerja*100)) }}%"></div>
                </div>
                <div class="text-muted small mt-1">
                    Sisa {{ max(0,$payroll->target_hari_kerja - $payroll->total_hadir) }} hari lagi menuju gajian
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.payroll.cetak', $payroll->id) }}" class="btn btn-outline-secondary" target="_blank">
                <i class="bi bi-file-pdf-fill me-1"></i>Cetak Slip PDF
            </a>
            @if($payroll->status=='proses')
            <form method="POST" action="{{ route('admin.payroll.selesai', $payroll->id) }}"
                  onsubmit="return confirm('Tandai gaji sudah dibayar?')">
                @csrf @method('PUT')
                <button class="btn btn-success"><i class="bi bi-check-circle-fill me-1"></i>Tandai Sudah Dibayar</button>
            </form>
            @endif
            <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-secondary ms-auto">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>
@endsection
