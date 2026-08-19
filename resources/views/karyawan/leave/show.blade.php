@extends('layouts.app')
@section('title', 'Detail Izin')
@section('page-title', 'Detail Pengajuan Izin')

@section('content')
<div class="row justify-content-center">
<div class="col-md-6">
<div class="page-card card p-4">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <span class="badge bg-info me-1">{{ ucfirst($leave->jenis) }}</span>
            {!! $leave->status_badge !!}
        </div>
        <span class="text-muted small">{{ $leave->created_at->translatedFormat('d F Y') }}</span>
    </div>
    @foreach(['Tanggal Mulai'=>$leave->tanggal_mulai->format('d/m/Y'),'Tanggal Akhir'=>$leave->tanggal_akhir->format('d/m/Y'),'Jumlah Hari'=>$leave->jumlah_hari.' hari','Keterangan'=>$leave->keterangan,'Catatan Admin'=>$leave->catatan_admin??'-'] as $lbl=>$val)
    <div class="d-flex justify-content-between py-2 border-bottom">
        <span class="text-muted small">{{ $lbl }}</span>
        <span class="small fw-semibold">{{ $val }}</span>
    </div>
    @endforeach
    @if($leave->file_bukti)
    <div class="mt-3">
        <a href="{{ asset('storage/dokumen-izin/'.$leave->file_bukti) }}" target="_blank" class="btn btn-outline-info btn-sm">
            <i class="bi bi-file-earmark-fill me-1"></i>Lihat Bukti
        </a>
    </div>
    @endif
    <div class="mt-3">
        <a href="{{ route('karyawan.leave.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>
</div>
</div>
@endsection
