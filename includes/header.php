<?php
/**
 * SIAKAD Gallery — Header Include (Topbar)
 * Outputs the topbar for authenticated pages
 * Usage: <?php require_once 'includes/header.php'; ?>
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Get current user info from session
// Auth menyimpan nama di $_SESSION['nama'] (bukan 'user_name')
$user_name = $_SESSION['nama'] ?? ($_SESSION['user_name'] ?? 'User');
$user_role = $_SESSION['role'] ?? 'guest';

// Map role to display name
$role_display = [
  'mahasiswa' => 'Student',
  'dosen' => 'Lecturer',
  'admin' => 'Admin'
];
$role_label = $role_display[$user_role] ?? 'User';
?>

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

  .topbar-search-wrapper {
    position: relative;
    width: 320px;
  }

  .topbar-search-wrapper i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
  }

  .topbar-search-input {
    width: 100%;
    background: #F3F4F6;
    border: none;
    padding: 12px 16px 12px 40px;
    border-radius: 99px;
    font-size: 13px;
    color: #374151;
    font-weight: 500;
  }

  .topbar-search-input:focus {
    outline: none;
    box-shadow: 0 0 0 2px #E0E7FF;
  }

  .topbar-actions {
    display: flex;
    align-items: center;
    gap: 24px;
  }

  .role-switcher-btn {
    background: #EEF2FF;
    color: #1B3679;
    border: none;
    padding: 8px 16px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
  }

  .topbar-icon {
    font-size: 18px;
    color: #4B5563;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
  }

  .topbar-icon:hover {
    color: #1B3679;
  }

  .topbar-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-left: 24px;
    border-left: 1px solid #E5E7EB;
  }

  .topbar-profile-info {
    text-align: right;
  }

  .topbar-profile-name {
    font-size: 14px;
    font-weight: 700;
    color: #111827;
  }

  .topbar-profile-role {
    font-size: 10px;
    font-weight: 700;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .topbar-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #1B3679;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
  }

  @media (max-width: 768px) {
    .topbar {
      padding: 16px 24px;
    }
    .topbar-search-wrapper {
      display: none;
    }
    .role-switcher-btn {
      display: none;
    }
  }
</style>

<div class="topbar">
  <div class="topbar-left">
    <div class="topbar-search-wrapper">
      <i class="bi bi-search"></i>
      <input type="text" class="topbar-search-input" placeholder="Search courses, news, or grades..." />
    </div>
  </div>

  <div class="topbar-actions">
    <button class="role-switcher-btn">Role Switcher</button>
    <button class="topbar-icon"><i class="bi bi-bell"></i></button>
    <button class="topbar-icon"><i class="bi bi-gear"></i></button>
    
    <div class="topbar-profile">
      <div class="topbar-profile-info">
        <div class="topbar-profile-name">Hi, <?= htmlspecialchars($user_name) ?></div>
        <div class="topbar-profile-role"><?= $role_label ?> &bull; ROLE SWITCHER</div>
      </div>
      <div class="topbar-avatar">
        <?= strtoupper(substr($user_name, 0, 1)) ?>
      </div>
    </div>
  </div>
</div>
