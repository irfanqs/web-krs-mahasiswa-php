@extends('layouts.app')
@section('title', 'Academic Profile')

@push('styles')
<style>
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 40px;
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
        color: #4B5563;
        font-weight: 500;
        margin: 0;
    }
    .btn-print {
        background: #E5E7EB;
        color: #1B3679;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-print:hover { background: #D1D5DB; }

    /* Top Grid */
    .top-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
        margin-bottom: 48px;
    }

    /* Main Info Card */
    .main-info-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        display: flex;
        gap: 32px;
        position: relative;
        overflow: hidden;
    }
    .main-info-card::before {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 150px; height: 150px;
        background: #F3F4F6;
        border-radius: 50%;
        transform: translate(30%, -30%);
        z-index: 0;
    }
    .profile-avatar-wrapper {
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        background: #EEF2FF;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 800;
        color: #1B3679;
        box-shadow: 0 10px 25px rgba(27,54,121,0.1);
        overflow: hidden;
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    
    .profile-details {
        position: relative;
        z-index: 1;
        flex: 1;
    }
    .student-name { font-size: 24px; font-weight: 800; color: #1B3679; margin: 0 0 4px 0; }
    .student-nim { font-size: 14px; font-weight: 600; color: #6B7280; margin-bottom: 24px; }
    
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px 16px;
    }
    .detail-item { display: flex; flex-direction: column; gap: 4px; }
    .d-label { font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; }
    .d-value { font-size: 14px; font-weight: 800; color: #111827; }

    /* Academic Standing & Status */
    .right-col-cards {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .standing-card {
        background: linear-gradient(135deg, #1B3679 0%, #11235A 100%);
        border-radius: 24px;
        padding: 32px;
        color: white;
        box-shadow: 0 10px 30px rgba(27,54,121,0.15);
        position: relative;
        overflow: hidden;
    }
    .standing-header { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.7); margin-bottom: 12px; }
    .gpa-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; }
    .gpa-value { font-size: 48px; font-weight: 800; line-height: 1; margin-bottom: 4px; }
    .gpa-sub { font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.7); }
    .chart-icon { width: 48px; height: 48px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    
    .standing-footer { display: flex; justify-content: space-between; }
    .sf-item { display: flex; flex-direction: column; gap: 4px; }
    .sf-val { font-size: 20px; font-weight: 800; }
    .sf-lbl { font-size: 10px; font-weight: 800; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 1px; }

    .status-card {
        background: white;
        border-radius: 20px;
        padding: 24px 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .status-header { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
    .status-icon { width: 32px; height: 32px; background: #ECFDF5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid #A7F3D0; }
    .st-lbl { font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
    .st-val { font-size: 14px; font-weight: 800; color: #111827; }
    .status-bar { height: 6px; background: #E5E7EB; border-radius: 99px; margin-bottom: 8px; overflow: hidden; }
    .status-bar-fill { width: 85%; height: 100%; background: #059669; border-radius: 99px; }
    .status-sub { font-size: 11px; color: #6B7280; font-weight: 500; }

    /* Section Headings */
    .section-heading {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 18px;
        font-weight: 800;
        color: #1B3679;
        margin-bottom: 24px;
    }
    .section-heading::before {
        content: '';
        width: 32px;
        height: 4px;
        background: #1B3679;
        border-radius: 2px;
    }

    /* Personal Details Grid */
    .personal-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 48px;
    }
    .pd-card {
        background: #F8FAFC;
        border-radius: 20px;
        padding: 24px 32px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .pd-header { font-size: 12px; font-weight: 800; color: #1B3679; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 8px; }
    .pd-header i { font-size: 16px; }

    /* Performance Table */
    .table-wrapper {
        background: #F8FAFC;
        border-radius: 20px;
        overflow: hidden;
    }
    table { width: 100%; border-collapse: collapse; }
    th {
        padding: 20px 24px;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #E5E7EB;
    }
    td {
        padding: 20px 24px;
        font-size: 14px;
        border-bottom: 1px solid #E5E7EB;
        background: white;
    }
    tr:last-child td { border-bottom: none; }
    
    .sem-name { font-weight: 800; color: #1B3679; }
    .sem-period { color: #6B7280; font-weight: 500; }
    .sem-sks { font-weight: 800; color: #111827; }
    .sem-ips { font-weight: 800; color: #111827; }
    
    .badge-pass {
        display: inline-block;
        background: #ECFDF5;
        color: #059669;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    @media(max-width: 900px) {
        .top-grid, .personal-grid { grid-template-columns: 1fr; }
        .main-info-card { flex-direction: column; align-items: center; text-align: center; }
        .details-grid { text-align: left; }
    }
</style>
@endpush

@section('content')
@php 
    $currentPage = 'profil'; 
    $initials = strtoupper(substr($mahasiswa->nama,0,1));
@endphp

<div class="profile-container">
    <div class="header-section print-hide">
        <div>
            <h1 class="page-title">Academic Profile</h1>
            <p class="page-subtitle">Manage and view your official university credentials.</p>
        </div>
        <button onclick="window.print()" class="btn-print">
            <i class="bi bi-download"></i> Print Transcript
        </button>
    </div>

    <div class="top-grid">
        <div class="main-info-card">
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar">{{ $initials }}</div>
            </div>
            <div class="profile-details">
                <h2 class="student-name">{{ $mahasiswa->nama }}</h2>
                <div class="student-nim">NIM: {{ $mahasiswa->nim }}</div>
                
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="d-label">MAJOR</span>
                        <span class="d-value">{{ $mahasiswa->program_studi }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="d-label">DEGREE PROGRAM</span>
                        <span class="d-value">Bachelor's Degree (S1)</span>
                    </div>
                    <div class="detail-item">
                        <span class="d-label">ENTRY YEAR</span>
                        <span class="d-value">{{ $mahasiswa->angkatan }} (Enrolled)</span>
                    </div>
                    <div class="detail-item">
                        <span class="d-label">CURRENT SEMESTER</span>
                        <span class="d-value">{{ $semesterAktif ? $semesterAktif->tahun_ajaran : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-col-cards">
            <div class="standing-card">
                <div class="standing-header">ACADEMIC STANDING</div>
                <div class="gpa-row">
                    <div>
                        <div class="gpa-value">{{ number_format($ipk, 2) }}</div>
                        <div class="gpa-sub">Cumulative GPA (IPK)</div>
                    </div>
                    <div class="chart-icon"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
                <div class="standing-footer">
                    <div class="sf-item">
                        <span class="sf-val">{{ $sksTempuh }}</span>
                        <span class="sf-lbl">SKS EARNED</span>
                    </div>
                    <div class="sf-item" style="text-align: right;">
                        <span class="sf-val">{{ $predikat }}</span>
                        <span class="sf-lbl">PREDICATES</span>
                    </div>
                </div>
            </div>

            <div class="status-card">
                <div class="status-header">
                    <div class="status-icon"><i class="bi bi-check"></i></div>
                    <div>
                        <div class="st-lbl">ENROLLMENT STATUS</div>
                        <div class="st-val">{{ ucfirst($mahasiswa->status) }} ({{ $semesterAktif ? $semesterAktif->tahun_ajaran : '-' }})</div>
                    </div>
                </div>
                <div class="status-bar"><div class="status-bar-fill"></div></div>
                <div class="status-sub">Study Plan Approved by Supervisor</div>
            </div>
        </div>
    </div>

    <div class="section-heading">Personal Details</div>
    <div class="personal-grid">
        <div class="pd-card">
            <div class="pd-header"><i class="bi bi-envelope"></i> CONTACT INFO</div>
            <div class="detail-item">
                <span class="d-label">UNIVERSITY EMAIL</span>
                <span class="d-value">{{ $mahasiswa->email ?? strtolower(str_replace(' ','',$mahasiswa->nama)).'@student.university.ac.id' }}</span>
            </div>
            <div class="detail-item">
                <span class="d-label">PHONE NUMBER</span>
                <span class="d-value">{{ $mahasiswa->telepon ?? '+62 812-3456-7890' }}</span>
            </div>
        </div>

        <div class="pd-card">
            <div class="pd-header"><i class="bi bi-geo-alt"></i> ADDRESS</div>
            <div class="detail-item">
                <span class="d-label">HOME ADDRESS</span>
                <span class="d-value" style="line-height:1.5;">{{ $mahasiswa->alamat ?? 'Jl. Kebon Jeruk No. 12, Jakarta Barat, DKI Jakarta, 11530' }}</span>
            </div>
        </div>

        <div class="pd-card">
            <div class="pd-header"><i class="bi bi-person-vcard"></i> BIOGRAPHICAL</div>
            <div class="detail-item">
                <span class="d-label">PLACE & DATE OF BIRTH</span>
                <span class="d-value">{{ $mahasiswa->tempat_lahir ?? 'Surabaya' }}, {{ $mahasiswa->tanggal_lahir ?? '14 August 2003' }}</span>
            </div>
            <div class="detail-item">
                <span class="d-label">RELIGION</span>
                <span class="d-value">{{ $mahasiswa->agama ?? 'Islam' }}</span>
            </div>
        </div>
    </div>

    <div class="section-heading">Semester Performance</div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>SEMESTER</th>
                    <th>PERIOD</th>
                    <th style="text-align:center;">SKS TAKEN</th>
                    <th style="text-align:center;">SEMESTER GPA (IPS)</th>
                    <th style="text-align:right;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($semesterPerformance as $index => $sp)
                    <tr>
                        <td class="sem-name">Semester {{ count($semesterPerformance) - $index }}</td>
                        <td class="sem-period">{{ $sp->tahun_ajaran }} {{ ucfirst($sp->tingkatan_semester) }}</td>
                        <td class="sem-sks" style="text-align:center;">{{ $sp->total_sks }}</td>
                        <td class="sem-ips" style="text-align:center;">{{ number_format($sp->ips, 2) }}</td>
                        <td style="text-align:right;">
                            <span class="badge-pass">PASS</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:32px;color:#6B7489;">Belum ada riwayat performa semester.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
