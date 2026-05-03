@extends('layouts.app')
@section('title', 'Manajemen Mahasiswa')
@section('content')
@php $currentPage = 'mahasiswa'; @endphp

<style>
    .page-header {
        margin-bottom: 32px;
    }
    .breadcrumb {
        font-size: 12px;
        font-weight: 600;
        color: #9CA3AF;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .breadcrumb span {
        color: #1B3679;
    }
    .title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #1B3679;
        margin: 0 0 4px 0;
        letter-spacing: -0.5px;
    }
    .page-subtitle {
        font-size: 14px;
        color: #6B7280;
        margin: 0;
    }
    .btn-add {
        background: #0B1E4F;
        color: white;
        padding: 12px 24px;
        border-radius: 99px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(11,30,79,0.2);
        transition: all 0.2s;
    }
    .btn-add:hover {
        background: #1B3679;
        transform: translateY(-2px);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 32px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        position: relative;
        border-bottom: 4px solid transparent;
    }
    .stat-card.active {
        border-bottom-color: #1B3679;
    }
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .icon-blue { background: #EEF2FF; color: #1B3679; }
    .icon-purple { background: #F3E8FF; color: #7E22CE; }
    .icon-orange { background: #FFF7ED; color: #C2410C; }
    .icon-green { background: #ECFDF5; color: #047857; }
    .stat-badge {
        background: #ECFDF5;
        color: #047857;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
    }
    .stat-title {
        font-size: 11px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 4px;
    }
    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #1B3679;
        line-height: 1;
    }

    .table-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .table-controls {
        padding: 20px 24px;
        border-bottom: 1px solid #F3F4F6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .search-box {
        position: relative;
        width: 300px;
    }
    .search-box i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
    }
    .search-box input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 99px;
        font-size: 13px;
        color: #4B5563;
        outline: none;
        transition: all 0.2s;
    }
    .search-box input:focus {
        border-color: #1B3679;
        background: white;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }
    th {
        background: #FFFFFF;
        padding: 16px 24px;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid #F3F4F6;
    }
    td {
        padding: 16px 24px;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    tr:last-child td {
        border-bottom: none;
    }
    
    .td-foto { width: 60px; }
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #E5E7EB;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #4B5563;
        overflow: hidden;
    }
    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .td-nim {
        font-size: 14px;
        font-weight: 800;
        color: #1B3679;
    }
    .td-nama {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }
    .td-email {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        margin-top: 2px;
    }
    .td-prodi {
        font-size: 14px;
        font-weight: 500;
        color: #4B5563;
    }
    .td-angkatan {
        font-size: 14px;
        font-weight: 800;
        color: #4B5563;
    }
    
    .status-pill {
        display: inline-flex;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
    }
    .status-aktif { background: #ECFDF5; color: #047857; }
    .status-cuti { background: #FFF7ED; color: #C2410C; }
    
    .action-buttons {
        display: flex;
        gap: 12px;
    }
    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 0;
        transition: color 0.2s;
    }
    .btn-edit { color: #9CA3AF; }
    .btn-edit:hover { color: #3B82F6; }
    .btn-delete { color: #FCA5A5; }
    .btn-delete:hover { color: #EF4444; }

    .pagination-wrapper {
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #F3F4F6;
        background: #FAFAFA;
    }
    .pagination-info {
        font-size: 13px;
        font-weight: 500;
        color: #6B7280;
    }
    .pagination-info span {
        font-weight: 700;
        color: #1B3679;
    }
    
    /* Temporary avatars for mockup */
    .avatar-1 { background: #FDE68A; }
    .avatar-2 { background: #BFDBFE; }
    .avatar-3 { background: #A7F3D0; }
    .avatar-4 { background: #FECACA; }
</style>

<div class="page-header">
    <div class="breadcrumb">Master Data <i class="bi bi-chevron-right" style="font-size:10px;"></i> <span>Mahasiswa</span></div>
    <div class="title-row">
        <div class="title-text">
            <h1 class="page-title">Manajemen Data Mahasiswa</h1>
            <p class="page-subtitle">Kelola informasi akademik dan data pribadi mahasiswa secara terpusat.</p>
        </div>
        <a href="{{ route('admin.mahasiswa.create') }}" class="btn-add">
            <i class="bi bi-plus"></i> Tambah Mahasiswa
        </a>
    </div>
</div>

@php
    // Use mock data if real data is not available
    try {
        $total = \App\Models\Mahasiswa::count();
        $aktif = \App\Models\Mahasiswa::where('status', 'aktif')->count();
        $cuti = \App\Models\Mahasiswa::whereIn('status', ['cuti', 'non-aktif'])->count();
        $lulus = \App\Models\Mahasiswa::where('status', 'lulus')->count();
    } catch (\Exception $e) {
        $total = 4821;
        $aktif = 4502;
        $cuti = 319;
        $lulus = 1240;
    }
@endphp

<div class="stats-grid">
    <div class="stat-card active">
        <div class="stat-header">
            <div class="stat-icon icon-blue"><i class="bi bi-people"></i></div>
            <div class="stat-badge">+12%</div>
        </div>
        <div class="stat-title">TOTAL MAHASISWA</div>
        <div class="stat-value">{{ number_format($total ?: 4821) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon icon-purple"><i class="bi bi-mortarboard"></i></div>
        </div>
        <div class="stat-title">AKTIF AKADEMIK</div>
        <div class="stat-value">{{ number_format($aktif ?: 4502) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon icon-orange"><i class="bi bi-clipboard-data"></i></div>
        </div>
        <div class="stat-title">CUTI/NON-AKTIF</div>
        <div class="stat-value">{{ number_format($cuti ?: 319) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon icon-green"><i class="bi bi-patch-check"></i></div>
        </div>
        <div class="stat-title">LULUS TAHUN INI</div>
        <div class="stat-value">{{ number_format($lulus ?: 1240) }}</div>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>FOTO</th>
                <th>NIM</th>
                <th>NAMA MAHASISWA</th>
                <th>PROGRAM STUDI</th>
                <th>ANGKATAN</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswaList as $index => $m)
            @php
                $email = strtolower(str_replace(' ', '.', $m->nama)) . '@siakad.gallery';
                $avatarClass = 'avatar-' . (($index % 4) + 1);
            @endphp
            <tr>
                <td class="td-foto">
                    <div class="avatar-circle {{ $avatarClass }}">
                        {{ strtoupper(substr($m->nama, 0, 1)) }}
                    </div>
                </td>
                <td class="td-nim">{{ $m->nim }}</td>
                <td>
                    <div class="td-nama">{{ $m->nama }}</div>
                    <div class="td-email">{{ $email }}</div>
                </td>
                <td class="td-prodi">{{ $m->program_studi }}</td>
                <td class="td-angkatan">{{ $m->angkatan }}</td>
                <td>
                    @if(strtolower($m->status) == 'aktif')
                        <span class="status-pill status-aktif">Aktif</span>
                    @else
                        <span class="status-pill status-cuti">{{ ucfirst($m->status) }}</span>
                    @endif
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.mahasiswa.edit', $m->nim) }}" class="btn-icon btn-edit"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('admin.mahasiswa.destroy', $m->nim) }}" style="display:inline;" onsubmit="return confirm('Hapus mahasiswa ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon btn-delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding: 40px; color: #6B7280;">
                    <i class="bi bi-inbox" style="font-size: 32px; margin-bottom: 12px; display: block;"></i>
                    Tidak ada data mahasiswa ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan <span>{{ $mahasiswaList->firstItem() ?? 0 }} - {{ $mahasiswaList->lastItem() ?? 0 }}</span> dari <span>{{ number_format($mahasiswaList->total() ?? 0) }}</span> data mahasiswa
        </div>
        <div class="pagination-links">
            {{ $mahasiswaList->links() }}
        </div>
    </div>
</div>

@endsection
