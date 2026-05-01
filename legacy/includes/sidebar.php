<?php
/**
 * SIAKAD Gallery — Sidebar Include
 * Navigation sidebar for all authenticated pages
 * Usage: <?php include_once 'includes/sidebar.php'; ?>
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Get user info from session
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'mahasiswa';
$current_page = isset($current_page) ? $current_page : '';

// Determine menu items based on role
$menu_items = [];

if ($user_role === 'mahasiswa') {
    $menu_items = [
        [
            'icon' => 'bi-grid-1x2',
            'label' => 'Dashboard',
            'url' => '/mahasiswa/dashboard.php',
            'page' => 'dashboard'
        ],
        [
            'icon' => 'bi-person',
            'label' => 'Academic Profile',
            'url' => '/mahasiswa/profil.php',
            'page' => 'profil'
        ],
        [
            'icon' => 'bi-calendar-check',
            'label' => 'Course Enrollment',
            'url' => '/mahasiswa/krs.php',
            'page' => 'krs'
        ],
        [
            'icon' => 'bi-star',
            'label' => 'Study Results',
            'url' => '/mahasiswa/khs.php',
            'page' => 'khs'
        ],
        [
            'icon' => 'bi-calendar3',
            'label' => 'Schedule',
            'url' => '/mahasiswa/jadwal.php',
            'page' => 'jadwal'
        ],
        [
            'icon' => 'bi-wallet2',
            'label' => 'Finance',
            'url' => '#',
            'page' => 'finance'
        ],
    ];
} elseif ($user_role === 'dosen') {
    $menu_items = [
        [
            'icon' => 'bi-grid-1x2',
            'label' => 'Dashboard',
            'url' => '/dosen/dashboard.php',
            'page' => 'dashboard'
        ],
        [
            'icon' => 'bi-person',
            'label' => 'Academic Profile',
            'url' => '#',
            'page' => 'profil'
        ],
        [
            'icon' => 'bi-calendar-check',
            'label' => 'Course Management',
            'url' => '/dosen/matkul.php',
            'page' => 'matkul'
        ],
        [
            'icon' => 'bi-people',
            'label' => 'Student List',
            'url' => '/dosen/daftar_mahasiswa.php',
            'page' => 'daftar_mahasiswa'
        ],
        [
            'icon' => 'bi-pencil',
            'label' => 'Input Grades',
            'url' => '/dosen/input_nilai.php',
            'page' => 'input_nilai'
        ],
        [
            'icon' => 'bi-calendar3',
            'label' => 'Schedule',
            'url' => '/dosen/jadwal.php',
            'page' => 'jadwal'
        ],
    ];
} elseif ($user_role === 'admin') {
    $menu_items = [
        [
            'icon' => 'bi-grid-1x2',
            'label' => 'Dashboard',
            'url' => '/admin/dashboard.php',
            'page' => 'dashboard'
        ],
        [
            'section' => 'Master Data',
            'items' => [
                [
                    'icon' => 'bi-person-badge',
                    'label' => 'Manajemen Mahasiswa',
                    'url' => '/admin/mahasiswa/index.php',
                    'page' => 'mahasiswa'
                ],
                [
                    'icon' => 'bi-person-video3',
                    'label' => 'Manajemen Dosen',
                    'url' => '/admin/dosen/index.php',
                    'page' => 'dosen'
                ],
                [
                    'icon' => 'bi-book-half',
                    'label' => 'Manajemen Mata Kuliah',
                    'url' => '/admin/matkul/index.php',
                    'page' => 'matkul'
                ],
                [
                    'icon' => 'bi-calendar2-check',
                    'label' => 'Manajemen Semester',
                    'url' => '/admin/semester/index.php',
                    'page' => 'semester'
                ],
                [
                    'icon' => 'bi-calendar3',
                    'label' => 'Manajemen Jadwal',
                    'url' => '/admin/jadwal/index.php',
                    'page' => 'jadwal'
                ],
            ]
        ],

    ];
}
?>
<style>
    :root {
        --sidebar-width: 260px;
        --sidebar-bg: #F8F9FA;
        --sidebar-active-bg: #EEF2FF;
        --sidebar-active-border: #2A4A9E;
        --sidebar-text: #4B5563;
        --sidebar-text-active: #1B3679;
    }

    .sidebar {
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        border-right: 1px solid #E5E7EB;
        display: flex;
        flex-direction: column;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 100;
        overflow-y: auto;
    }

    .sidebar-header {
        padding: 32px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-logo-icon {
        width: 36px;
        height: 36px;
        background: #1B3679;
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .sidebar-logo-text {
        display: flex;
        flex-direction: column;
    }

    .sidebar-logo-title {
        font-size: 16px;
        font-weight: 800;
        color: #1B3679;
        letter-spacing: -0.5px;
    }

    .sidebar-logo-subtitle {
        font-size: 10px;
        font-weight: 700;
        color: #6B7280;
        letter-spacing: 1px;
    }

    .sidebar-content {
        flex: 1;
        padding-top: 10px;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-section-title {
        padding: 16px 24px 8px;
        font-size: 10px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 24px;
        color: var(--sidebar-text);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        margin-right: 16px;
        border-radius: 0 12px 12px 0;
    }

    .sidebar-menu a:hover {
        background: rgba(0,0,0,0.02);
        color: #111827;
    }

    .sidebar-menu a.active {
        background: var(--sidebar-active-bg);
        color: var(--sidebar-text-active);
        border-left: 4px solid var(--sidebar-active-border);
    }

    .sidebar-menu i {
        font-size: 18px;
    }

    .sidebar-footer {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sidebar-action-btn {
        width: 100%;
        padding: 12px;
        background: #1B3679;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sidebar-action-btn:hover {
        background: #162C66;
    }

    .sidebar-secondary-links {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 8px;
    }

    .sidebar-secondary-links a {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #4B5563;
        text-decoration: none;
    }

    .sidebar-secondary-links a:hover {
        color: #111827;
    }

    .sidebar-secondary-links a.logout {
        color: #DC2626;
    }

    * {
        box-sizing: border-box;
    }

    .page-layout {
        margin-left: var(--sidebar-width);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background: #FFFFFF;
        width: calc(100% - var(--sidebar-width));
    }

    .page-main {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .page-content {
        padding: 32px 48px;
        max-width: 1200px; /* Reduced from 1400px to look cleaner */
        margin: 0 auto;
        width: 100%;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
            border-right: none;
            border-bottom: 1px solid #E5E7EB;
        }

        .page-layout {
            margin-left: 0;
        }
        
        .page-content {
            padding: 24px;
        }
    }
</style>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-icon">
            <i class="bi bi-bank"></i>
        </div>
        <div class="sidebar-logo-text">
            <span class="sidebar-logo-title">The Gallery</span>
            <span class="sidebar-logo-subtitle">ACADEMIC PORTAL</span>
        </div>
    </div>

    <div class="sidebar-content">
        <div class="sidebar-section-title">MAIN MENU</div>
        <ul class="sidebar-menu">
            <?php foreach ($menu_items as $item): ?>
                <?php if (isset($item['section'])): ?>
                    <div class="sidebar-section-title" style="margin-top: 16px;"><?= h($item['section']) ?></div>
                    <?php foreach ($item['items'] as $subitem): ?>
                        <li>
                            <a href="<?= APP_URL . h($subitem['url']) ?>" class="<?= $current_page === $subitem['page'] ? 'active' : '' ?>">
                                <i class="bi <?= h($subitem['icon']) ?>"></i>
                                <span><?= h($subitem['label']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>
                        <a href="<?= APP_URL . h($item['url']) ?>" class="<?= $current_page === $item['page'] ? 'active' : '' ?>">
                            <i class="bi <?= h($item['icon']) ?>"></i>
                            <span><?= h($item['label']) ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-secondary-links">
            <a href="#"><i class="bi bi-question-circle"></i> Help Center</a>
            <a href="<?= APP_URL ?>/auth/logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
        <button class="sidebar-action-btn">DIGITAL ID</button>
    </div>
</aside>
