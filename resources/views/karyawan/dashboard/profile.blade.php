@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="row g-3 justify-content-center">
<div class="col-md-8">
<div class="page-card card p-4">
    <div class="text-center mb-4">
        <div style="width:90px;height:90px;border-radius:50%;overflow:hidden;border:3px solid #4f46e5;margin:0 auto 12px;">
            @if($employee->foto)
                <img src="{{ asset('storage/foto-karyawan/'.$employee->foto) }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                <div style="width:100%;height:100%;background:#4f46e5;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff;font-weight:700;">
                    {{ strtoupper(substr($employee->nama_lengkap,0,1)) }}
                </div>
            @endif
        </div>
        <h5 class="fw-bold mb-1">{{ $employee->nama_lengkap }}</h5>
        <p class="text-muted small">{{ $employee->jabatan }} • {{ $employee->divisi }}</p>
        <span class="badge bg-success">{{ ucfirst($employee->status_aktif) }}</span>
    </div>
    <div class="row g-3">
        @php
        $data = [
            'ID Karyawan'   => $employee->employee_id,
            'Email'         => $employee->email ?? '-',
            'No. HP'        => $employee->no_hp ?? '-',
            'Jenis Kelamin' => $employee->jenis_kelamin ?? '-',
            'Tanggal Lahir' => $employee->tanggal_lahir?->translatedFormat('d F Y') ?? '-',
            'Tanggal Masuk' => $employee->tanggal_masuk->translatedFormat('d F Y'),
            'Status Kerja'  => $employee->status_kerja,
            'Shift Kerja'   => $employee->shift->nama_shift ?? '-',
            'Alamat'        => $employee->alamat ?? '-',
        ];
        @endphp
        @foreach($data as $lbl => $val)
        <div class="col-md-6">
            <div class="text-muted small">{{ $lbl }}</div>
            <div class="fw-semibold small">{{ $val }}</div>
        </div>
        @endforeach
    </div>
</div>
</div>
</div>
@endsection
