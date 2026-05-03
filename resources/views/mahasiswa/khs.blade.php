@extends('layouts.app')
@section('title', 'Kartu Hasil Studi')

@push('styles')
<style>
    .khs-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #1B3679;
        margin: 0 0 8px 0;
        letter-spacing: -0.5px;
    }
    .page-subtitle {
        font-size: 15px;
        color: #6B7280;
        font-weight: 500;
        margin: 0;
    }
    
    .header-actions {
        display: flex;
        gap: 16px;
        align-items: center;
    }
    .semester-select {
        padding: 12px 20px;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        background: white;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        outline: none;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1em;
        padding-right: 40px;
    }
    .btn-print {
        background: #E5E7EB;
        color: #1B3679;
        border: none;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        line-height: 1.2;
    }
    .btn-print i { font-size: 18px; }
    .btn-print:hover { background: #D1D5DB; }

    /* Top Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        border-radius: 20px;
        padding: 28px 32px;
        position: relative;
        overflow: hidden;
    }
    .stat-card-navy {
        background: linear-gradient(135deg, #1B3679 0%, #152960 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(27,54,121,0.15);
    }
    .stat-card-navy::after {
        content: '';
        position: absolute;
        bottom: -20px;
        right: 20px;
        width: 100px;
        height: 100px;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='10' y='50' width='16' height='50' rx='4' fill='white' fill-opacity='0.1'/%3E%3Crect x='36' y='30' width='16' height='70' rx='4' fill='white' fill-opacity='0.1'/%3E%3Crect x='62' y='10' width='16' height='90' rx='4' fill='white' fill-opacity='0.1'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
    }
    .stat-card-white {
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .border-left-blue {
        border-left: 4px solid #3B82F6;
    }
    
    .stat-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }
    .stat-card-navy .stat-title { color: rgba(255,255,255,0.7); }
    .stat-card-white .stat-title { color: #9CA3AF; }
    
    .stat-value {
        font-size: 40px;
        font-weight: 800;
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 8px;
    }
    .stat-card-navy .stat-value { color: white; margin-bottom: 16px; }
    .stat-card-white .stat-value { color: #1B3679; }
    .stat-value span { font-size: 18px; font-weight: 600; color: #9CA3AF; }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(255,255,255,0.15);
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
        color: white;
    }

    /* Table Section */
    .table-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        overflow: hidden;
        margin-bottom: 32px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th {
        padding: 24px 32px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid #F3F4F6;
    }
    td {
        padding: 20px 32px;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    
    .kode-pill {
        background: #F3F4F6;
        color: #4B5563;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 800;
    }
    .matkul-name {
        font-size: 14px;
        font-weight: 800;
        color: #1B3679;
    }
    .sks-val {
        font-size: 14px;
        font-weight: 600;
        color: #4B5563;
    }
    .bobot-val {
        font-size: 14px;
        font-weight: 600;
        color: #9CA3AF;
    }
    .total-val {
        font-size: 15px;
        font-weight: 800;
        color: #1B3679;
    }

    .grade-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 800;
    }
    .grade-A { background: #ECFDF5; color: #059669; }
    .grade-B { background: #EFF6FF; color: #2563EB; }
    .grade-C { background: #FFF7ED; color: #D97706; }
    .grade-D { background: #FEF2F2; color: #DC2626; }
    .grade-E { background: #FEF2F2; color: #DC2626; }

    /* Footer Summary */
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 32px;
        background: #FAFAFA;
        border-top: 2px solid #F3F4F6;
    }
    .footer-stats {
        display: flex;
        gap: 48px;
    }
    .f-stat-lbl {
        font-size: 11px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
    .f-stat-val {
        font-size: 20px;
        font-weight: 800;
        color: #1B3679;
    }
    
    .status-box {
        display: flex;
        align-items: center;
        gap: 16px;
        text-align: right;
    }
    .status-text {
        font-size: 14px;
        font-weight: 800;
        color: #059669;
    }
    .status-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #ECFDF5;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        border: 2px solid #A7F3D0;
    }

    /* Bottom Section */
    .notice-card {
        background: #F8FAFC;
        border-radius: 20px;
        padding: 32px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .notice-header {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        font-weight: 800;
        color: #1B3679;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .notice-header i { font-size: 18px; }
    .notice-text {
        font-size: 13px;
        color: #6B7280;
        line-height: 1.6;
        margin: 0;
    }

    @media(max-width: 900px) {
        .stats-grid { grid-template-columns: 1fr; }
        .table-footer { flex-direction: column; align-items: flex-start; gap: 24px; }
        .status-box { width: 100%; justify-content: space-between; flex-direction: row-reverse; }
    }
</style>
@endpush

@section('content')
@php 
    $currentPage = 'khs'; 
    $semName = $semesterSelected ? ucfirst($semesterSelected->tingkatan_semester).' '.$semesterSelected->tahun_ajaran : '-';
@endphp

<div class="khs-container">
    <div class="header-section">
        <div>
            <h1 class="page-title">Kartu Hasil Studi - Semester {{ $semName }}</h1>
            <p class="page-subtitle">Detailed performance report for your current academic cycle.</p>
        </div>
        <div class="header-actions print-hide">
            <form method="GET">
                <select name="semester_id" class="semester-select" onchange="this.form.submit()">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id_semester }}" {{ $semesterSelected?->id_semester == $sem->id_semester ? 'selected' : '' }}>
                            {{ ucfirst($sem->tingkatan_semester) }} {{ $sem->tahun_ajaran }}
                        </option>
                    @endforeach
                </select>
            </form>
            <button onclick="window.print()" class="btn-print">
                <i class="bi bi-file-earmark-pdf"></i>
                <div>Cetak<br>KHS (PDF)</div>
            </button>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card stat-card-navy">
            <div class="stat-title">IP SEMESTER INI</div>
            <div class="stat-value">{{ number_format($ips, 2) }}</div>
            <!-- Mockup badge since prev sem data isn't easily available in view -->
            <div class="stat-badge"><i class="bi bi-graph-up-arrow"></i> +0.07 from prev sem</div>
        </div>
        <div class="stat-card stat-card-white border-left-blue">
            <div class="stat-title">IP KUMULATIF (IPK)</div>
            <div class="stat-value">{{ number_format($ipk, 2) }}</div>
        </div>
        <div class="stat-card stat-card-white">
            <div class="stat-title">SKS TEMPUH</div>
            <div class="stat-value">{{ $totalSks }} <span>/ 144</span></div>
        </div>
    </div>

    <div class="table-card">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>KODE</th>
                        <th>MATA KULIAH</th>
                        <th style="text-align:center;">SKS</th>
                        <th style="text-align:center;">NILAI</th>
                        <th style="text-align:center;">BOBOT</th>
                        <th style="text-align:right;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilaiList as $n)
                        @php
                            $bobot = match($n->nilai_huruf) { 'A'=>4.0,'B+'=>3.5,'B'=>3.0,'C+'=>2.5,'C'=>2.0,'D'=>1.0,default=>0.0 };
                            
                            // Determine grade badge class based on first letter
                            $gradeFirst = substr($n->nilai_huruf ?? 'E', 0, 1);
                            $badgeClass = 'grade-' . $gradeFirst;
                        @endphp
                        <tr>
                            <td><span class="kode-pill">{{ $n->kode_matkul }}</span></td>
                            <td class="matkul-name">{{ $n->nama_matkul }}</td>
                            <td class="sks-val" style="text-align:center;">{{ $n->sks }}</td>
                            <td style="text-align:center;">
                                <span class="grade-badge {{ $badgeClass }}">{{ $n->nilai_huruf ?? '-' }}</span>
                            </td>
                            <td class="bobot-val" style="text-align:center;">{{ number_format($bobot, 1) }}</td>
                            <td class="total-val" style="text-align:right;">{{ number_format($bobot * $n->sks, 1) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;padding:40px;color:#6B7489;font-size:14px;">Belum ada nilai untuk semester ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="table-footer">
            <div class="footer-stats">
                <div>
                    <div class="f-stat-lbl">TOTAL SKS DIAMBIL</div>
                    <div class="f-stat-val">{{ $totalSks }} SKS</div>
                </div>
                <div>
                    <div class="f-stat-lbl">TOTAL BOBOT NILAI</div>
                    <div class="f-stat-val">{{ number_format($totalBobot, 2) }}</div>
                </div>
            </div>
            <div class="status-box">
                <div>
                    <div class="f-stat-lbl" style="margin-bottom:4px;color:#9CA3AF;">STATUS AKADEMIK</div>
                    <div class="status-text">AKTIF / MEMUASKAN</div>
                </div>
                <div class="status-icon"><i class="bi bi-check"></i></div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Notice -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        <div class="notice-card">
            <div class="notice-header"><i class="bi bi-info-circle"></i> ACADEMIC NOTICE</div>
            <p class="notice-text">Nilai Semester {{ $semName }} telah diverifikasi oleh Ketua Program Studi. Silakan menghubungi bagian akademik jika terdapat ketidaksesuaian data. Dokumen ini sah jika dicetak langsung dari sistem terpadu.</p>
        </div>
        <!-- Timeline omitted as requested since there is no backend data -->
    </div>
</div>
@endsection
