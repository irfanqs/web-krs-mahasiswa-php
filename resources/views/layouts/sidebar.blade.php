@php
    $currentPage = $currentPage ?? '';
    if (auth('mahasiswa')->check()) {
        $role = 'mahasiswa';
        $menuItems = [
            ['icon'=>'bi-grid-1x2','label'=>'Dashboard','route'=>'mahasiswa.dashboard','page'=>'dashboard'],
            ['icon'=>'bi-person','label'=>'Academic Profile','route'=>'mahasiswa.profil','page'=>'profil'],
            ['icon'=>'bi-calendar-check','label'=>'Course Enrollment','route'=>'mahasiswa.krs','page'=>'krs'],
            ['icon'=>'bi-star','label'=>'Study Results','route'=>'mahasiswa.khs','page'=>'khs'],
            ['icon'=>'bi-calendar3','label'=>'Schedule','route'=>'mahasiswa.jadwal','page'=>'jadwal'],
        ];
    } elseif (auth('dosen')->check()) {
        $role = 'dosen';
        $menuItems = [
            ['icon'=>'bi-grid-1x2','label'=>'Dashboard','route'=>'dosen.dashboard','page'=>'dashboard'],
            ['icon'=>'bi-people','label'=>'Student List','route'=>'dosen.daftar_mahasiswa','page'=>'daftar_mahasiswa'],
            ['icon'=>'bi-pencil','label'=>'Input Grades','route'=>'dosen.input_nilai','page'=>'input_nilai'],
            ['icon'=>'bi-calendar3','label'=>'Schedule','route'=>'dosen.jadwal','page'=>'jadwal'],
        ];
    } else {
        $role = 'admin';
        $menuItems = [
            ['icon'=>'bi-grid-1x2','label'=>'Dashboard','route'=>'admin.dashboard','page'=>'dashboard'],
            ['section'=>'Master Data','items'=>[
                ['icon'=>'bi-person-badge','label'=>'Manajemen Mahasiswa','route'=>'admin.mahasiswa.index','page'=>'mahasiswa'],
                ['icon'=>'bi-person-video3','label'=>'Manajemen Dosen','route'=>'admin.dosen.index','page'=>'dosen'],
                ['icon'=>'bi-book-half','label'=>'Manajemen Mata Kuliah','route'=>'admin.matkul.index','page'=>'matkul'],
                ['icon'=>'bi-calendar2-check','label'=>'Manajemen Semester','route'=>'admin.semester.index','page'=>'semester'],
                ['icon'=>'bi-calendar3','label'=>'Manajemen Jadwal','route'=>'admin.jadwal.index','page'=>'jadwal'],
            ]],
        ];
    }

    $logoutRoute = match($role) {
        'mahasiswa' => route('mahasiswa.logout'),
        'dosen'     => route('dosen.logout'),
        default     => route('admin.logout'),
    };
@endphp
<style>
    :root { --sidebar-width: 260px; }
    .sidebar { width:var(--sidebar-width); background:#F8F9FA; border-right:1px solid #E5E7EB; display:flex; flex-direction:column; position:fixed; left:0; top:0; height:100vh; z-index:100; overflow-y:auto; }
    .sidebar-header { padding:32px 24px; display:flex; align-items:center; gap:12px; }
    .sidebar-logo-icon { width:36px; height:36px; background:#1B3679; color:white; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:20px; }
    .sidebar-logo-title { font-size:16px; font-weight:800; color:#1B3679; letter-spacing:-0.5px; }
    .sidebar-logo-subtitle { font-size:10px; font-weight:700; color:#6B7280; letter-spacing:1px; }
    .sidebar-content { flex:1; padding-top:10px; }
    .sidebar-menu { list-style:none; padding:0; margin:0; }
    .sidebar-section-title { padding:16px 24px 8px; font-size:10px; font-weight:800; color:#9CA3AF; text-transform:uppercase; letter-spacing:1.5px; }
    .sidebar-menu a { display:flex; align-items:center; gap:16px; padding:12px 24px; color:#4B5563; text-decoration:none; font-size:14px; font-weight:600; transition:all 0.2s ease; border-left:3px solid transparent; margin-right:16px; border-radius:0 12px 12px 0; }
    .sidebar-menu a:hover { background:rgba(0,0,0,0.02); color:#111827; }
    .sidebar-menu a.active { background:#EEF2FF; color:#1B3679; border-left:4px solid #2A4A9E; }
    .sidebar-menu i { font-size:18px; }
    .sidebar-footer { padding:24px; display:flex; flex-direction:column; gap:16px; }
    .sidebar-secondary-links { display:flex; flex-direction:column; gap:12px; }
    .sidebar-secondary-links a { display:flex; align-items:center; gap:12px; font-size:14px; font-weight:600; color:#4B5563; text-decoration:none; }
    .sidebar-secondary-links a:hover { color:#111827; }
    .sidebar-secondary-links a.logout { color:#DC2626; }
    .sidebar-action-btn { width:100%; padding:12px; background:#1B3679; color:white; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; text-transform:uppercase; letter-spacing:0.5px; }
    .page-layout { margin-left:var(--sidebar-width); display:flex; flex-direction:column; min-height:100vh; background:#FFFFFF; width:calc(100% - var(--sidebar-width)); }
    .page-main { flex:1; overflow-y:auto; overflow-x:hidden; }
    .page-content { padding:32px 48px; max-width:1200px; margin:0 auto; width:100%; }
    * { box-sizing: border-box; }
</style>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-icon"><i class="bi bi-bank"></i></div>
        <div class="sidebar-logo-text">
            <div class="sidebar-logo-title">The Gallery</div>
            <div class="sidebar-logo-subtitle">ACADEMIC PORTAL</div>
        </div>
    </div>
    <div class="sidebar-content">
        <div class="sidebar-section-title">MAIN MENU</div>
        <ul class="sidebar-menu">
            @foreach($menuItems as $item)
                @if(isset($item['section']))
                    <div class="sidebar-section-title" style="margin-top:16px;">{{ $item['section'] }}</div>
                    @foreach($item['items'] as $sub)
                        <li>
                            <a href="{{ route($sub['route']) }}" class="{{ $currentPage === $sub['page'] ? 'active' : '' }}">
                                <i class="bi {{ $sub['icon'] }}"></i>
                                <span>{{ $sub['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @else
                    <li>
                        <a href="{{ route($item['route']) }}" class="{{ $currentPage === $item['page'] ? 'active' : '' }}">
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
    <div class="sidebar-footer">
        <div class="sidebar-secondary-links">
            <a href="#"><i class="bi bi-question-circle"></i> Help Center</a>
            <a href="{{ $logoutRoute }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="{{ $logoutRoute }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
        <button class="sidebar-action-btn">DIGITAL ID</button>
    </div>
</aside>
