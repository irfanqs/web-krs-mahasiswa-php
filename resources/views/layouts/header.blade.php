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
        $userName  = auth('admin')->user()->nama ?? 'Admin Administrator';
        $roleLabel = 'Master Admin';
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
  .topbar-left {
    display: flex;
    align-items: center;
  }
  .topbar-brand {
    font-size: 20px;
    font-weight: 800;
    color: #1B3679;
    letter-spacing: -0.5px;
  }
  .topbar-center {
    flex: 1;
    display: flex;
    justify-content: center;
    padding: 0 40px;
  }
  .topbar-search {
    background: #F3F4F6;
    border-radius: 99px;
    display: flex;
    align-items: center;
    padding: 10px 20px;
    width: 100%;
    max-width: 400px;
  }
  .topbar-search i {
    color: #9CA3AF;
    font-size: 16px;
    margin-right: 12px;
  }
  .topbar-search input {
    background: transparent;
    border: none;
    outline: none;
    width: 100%;
    font-size: 14px;
    color: #4B5563;
  }
  .topbar-search input::placeholder {
    color: #9CA3AF;
  }
  .topbar-actions { display: flex; align-items: center; gap: 20px; }
  .role-switcher-btn { background: transparent; color: #1B3679; border: 1px solid #D1D5DB; padding: 8px 16px; border-radius: 99px; font-size: 12px; font-weight: 700; cursor: pointer; text-transform: uppercase; }
  .topbar-icon { font-size: 20px; color: #4B5563; cursor: pointer; background: none; border: none; padding: 0; position: relative; }
  .topbar-icon:hover { color: #1B3679; }
  .notification-dot { position: absolute; top: 0; right: 2px; width: 8px; height: 8px; background: #EF4444; border-radius: 50%; border: 2px solid white; }
  .topbar-profile { display: flex; align-items: center; gap: 12px; }
  .topbar-profile-info { text-align: right; }
  .topbar-profile-name { font-size: 14px; font-weight: 700; color: #1B3679; }
  .topbar-profile-role { font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; }
  .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; background: #1B3679; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; overflow: hidden; }
  .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .alert-flash { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: flex; align-items: center; gap: 10px; }
  .alert-flash-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
  .alert-flash-error { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
</style>
<div class="topbar">
  <div class="topbar-left">
    <div class="topbar-brand">SIAKAD Gallery</div>
  </div>

  <div class="topbar-actions">

    <div class="topbar-profile">
      <div class="topbar-profile-info">
        <div class="topbar-profile-name">{{ $userName }}</div>
        <div class="topbar-profile-role">{{ $roleLabel }}</div>
      </div>
      <div class="topbar-avatar">
        {{-- For demo purposes using initials if no image --}}
        {{ strtoupper(substr($userName, 0, 1)) }}
      </div>
    </div>
    <button class="topbar-icon">
        <i class="bi bi-bell"></i>
        <div class="notification-dot"></div>
    </button>
    <button class="topbar-icon"><i class="bi bi-gear"></i></button>
  </div>
</div>
