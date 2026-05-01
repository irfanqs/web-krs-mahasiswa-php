@php
    $userName = 'User';
    $roleLabel = 'User';
    if (auth('mahasiswa')->check()) {
        $userName  = auth('mahasiswa')->user()->nama;
        $roleLabel = 'Student';
    } elseif (auth('dosen')->check()) {
        $userName  = auth('dosen')->user()->nama;
        $roleLabel = 'Lecturer';
    } elseif (auth('admin')->check()) {
        $userName  = auth('admin')->user()->nama;
        $roleLabel = 'Admin';
    }
@endphp
<style>
  .topbar {
    background-color: #FFFFFF;
    border-bottom: 1px solid #E5E7EB;
    padding: 16px 48px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 90;
  }
  .topbar-actions { display: flex; align-items: center; gap: 24px; }
  .role-switcher-btn { background: #EEF2FF; color: #1B3679; border: none; padding: 8px 16px; border-radius: 99px; font-size: 12px; font-weight: 700; cursor: pointer; }
  .topbar-icon { font-size: 18px; color: #4B5563; cursor: pointer; background: none; border: none; padding: 0; }
  .topbar-icon:hover { color: #1B3679; }
  .topbar-profile { display: flex; align-items: center; gap: 12px; padding-left: 24px; border-left: 1px solid #E5E7EB; }
  .topbar-profile-info { text-align: right; }
  .topbar-profile-name { font-size: 14px; font-weight: 700; color: #111827; }
  .topbar-profile-role { font-size: 10px; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; }
  .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; background: #1B3679; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; }
  .alert-flash { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
  .alert-flash-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
  .alert-flash-error { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
</style>
<div class="topbar">
  <div class="topbar-left"></div>
  <div class="topbar-actions">
    <button class="role-switcher-btn">{{ $roleLabel }}</button>
    <button class="topbar-icon"><i class="bi bi-bell"></i></button>
    <button class="topbar-icon"><i class="bi bi-gear"></i></button>
    <div class="topbar-profile">
      <div class="topbar-profile-info">
        <div class="topbar-profile-name">Hi, {{ $userName }}</div>
        <div class="topbar-profile-role">{{ strtoupper($roleLabel) }} &bull; SIAKAD</div>
      </div>
      <div class="topbar-avatar">{{ strtoupper(substr($userName, 0, 1)) }}</div>
    </div>
  </div>
</div>
