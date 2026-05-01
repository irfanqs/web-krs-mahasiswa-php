<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIAKAD Gallery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --navy-900: #1B3679; --navy-800: #162C66; --navy-500: #2A4A9E; --navy-100: #D8E0FE;
            --white: #FFFFFF; --bg-page: #F4F6F9; --border: #E5E7EB; --text-900: #111827;
            --text-700: #374151; --text-500: #6B7280; --input-bg: #F9FAFB;
            --success: #10B981; --danger: #EF4444;
            --font-family: 'Inter', system-ui, -apple-system, sans-serif;
            --transition: all 0.2s ease-in-out;
        }
        * { box-sizing: border-box; }
        body { margin:0; font-family:var(--font-family); background:var(--bg-page); display:flex; flex-direction:column; min-height:100vh; align-items:center; justify-content:center; padding:20px; background-image:radial-gradient(circle at top right,rgba(216,224,254,.4),transparent 40%),radial-gradient(circle at bottom left,rgba(216,224,254,.3),transparent 40%); }
        .login-card { background:var(--white); width:100%; max-width:1000px; border-radius:16px; box-shadow:0 10px 25px -5px rgba(0,0,0,.05); display:flex; overflow:hidden; min-height:600px; margin-bottom:24px; }
        .login-panel-left { flex:1; background:var(--navy-900); color:var(--white); padding:48px; display:flex; flex-direction:column; position:relative; overflow:hidden; max-width:480px; }
        .logo-area { display:flex; align-items:center; gap:12px; margin-bottom:64px; position:relative; z-index:2; }
        .logo-text { font-size:20px; font-weight:700; letter-spacing:-0.5px; }
        .tagline { font-size:38px; font-weight:700; line-height:1.2; margin-bottom:24px; position:relative; z-index:2; letter-spacing:-1px; }
        .tagline-desc { font-size:15px; opacity:.8; line-height:1.6; margin-bottom:40px; position:relative; z-index:2; }
        .hero-img-container { flex:1; position:relative; border-radius:12px; overflow:hidden; z-index:2; margin-top:auto; min-height:200px; box-shadow:0 20px 40px rgba(0,0,0,.2); }
        .hero-img { width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0; opacity:.8; mix-blend-mode:overlay; filter:grayscale(20%) contrast(1.2); }
        .hero-img-gradient { position:absolute; top:0; left:0; right:0; bottom:0; background:linear-gradient(to top,rgba(27,54,121,.9) 0%,rgba(27,54,121,.2) 100%); z-index:1; }
        .login-panel-right { flex:1; padding:48px 64px; display:flex; flex-direction:column; justify-content:center; }
        .login-title { font-size:28px; font-weight:700; color:var(--navy-900); margin:0 0 8px 0; letter-spacing:-0.5px; }
        .login-subtitle { font-size:15px; color:var(--text-500); margin:0 0 32px 0; }
        .alert { padding:14px 16px; border-radius:8px; margin-bottom:24px; font-size:14px; display:flex; align-items:center; gap:10px; }
        .alert-error { background:#FEF2F2; color:var(--danger); border:1px solid #FECACA; }
        .alert-success { background:#ECFDF5; color:var(--success); border:1px solid #A7F3D0; }
        .form-section-label { font-size:11px; font-weight:700; color:var(--text-500); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px; display:block; }
        .role-selector { display:flex; gap:16px; margin-bottom:28px; }
        .role-btn { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; padding:16px 12px; background:var(--input-bg); border:2px solid transparent; border-radius:12px; cursor:pointer; transition:var(--transition); }
        .role-btn .role-icon { font-size:20px; color:var(--text-500); transition:var(--transition); }
        .role-btn .role-text { font-size:11px; font-weight:700; color:var(--text-700); text-transform:uppercase; letter-spacing:.5px; }
        .role-btn:hover { background:var(--border); }
        .role-btn.active { background:var(--navy-100); border-color:var(--navy-500); }
        .role-btn.active .role-icon,.role-btn.active .role-text { color:var(--navy-900); }
        .form-group { margin-bottom:20px; position:relative; }
        .form-group-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
        .form-label { font-size:11px; font-weight:700; color:var(--text-500); text-transform:uppercase; letter-spacing:1px; }
        .forgot-link { font-size:12px; font-weight:600; color:var(--navy-900); text-decoration:none; }
        .input-wrapper { position:relative; display:flex; align-items:center; }
        .input-icon { position:absolute; left:16px; color:var(--text-500); font-size:18px; }
        .form-input { width:100%; padding:14px 16px 14px 44px; background:var(--input-bg); border:1px solid transparent; border-radius:10px; font-family:var(--font-family); font-size:14px; color:var(--text-900); transition:var(--transition); }
        .form-input:focus { outline:none; background:var(--white); border-color:var(--navy-500); box-shadow:0 0 0 4px var(--navy-100); }
        .form-input::placeholder { color:#9CA3AF; }
        .password-toggle { position:absolute; right:16px; background:none; border:none; cursor:pointer; color:var(--text-500); font-size:18px; padding:0; display:flex; align-items:center; }
        .form-checkbox { display:flex; align-items:center; gap:10px; margin-bottom:28px; margin-top:24px; }
        .form-checkbox input { width:16px; height:16px; border-radius:4px; accent-color:var(--navy-900); cursor:pointer; }
        .form-checkbox label { font-size:14px; color:var(--text-700); cursor:pointer; user-select:none; }
        .btn-login { width:100%; padding:14px 24px; background:var(--navy-900); color:var(--white); border:none; border-radius:10px; font-size:15px; font-weight:600; cursor:pointer; transition:var(--transition); display:flex; align-items:center; justify-content:center; gap:8px; }
        .btn-login:hover { background:var(--navy-800); transform:translateY(-1px); }
        .panel-footer { margin-top:auto; padding-top:40px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); margin-top:48px; }
        .manual-link { display:flex; align-items:center; gap:8px; color:var(--navy-900); font-size:13px; font-weight:700; text-decoration:none; }
        .copyright-text { font-size:11px; color:var(--text-500); font-weight:600; letter-spacing:.5px; text-transform:uppercase; }
        .global-footer { display:flex; margin-top:20px; gap:32px; font-size:12px; font-weight:700; color:var(--text-500); letter-spacing:1px; text-transform:uppercase; }
        .global-footer a { color:var(--text-500); text-decoration:none; }
        .global-footer a:hover { color:var(--navy-900); }
        @media(max-width:900px){.login-card{flex-direction:column;}.login-panel-left{max-width:none;padding:32px;}.hero-img-container{display:none;}.logo-area{margin-bottom:24px;}.login-panel-right{padding:32px;}}
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-panel-left">
            <div class="logo-area">
                <i class="bi bi-images" style="font-size:24px;"></i>
                <div class="logo-text">SIAKAD Gallery</div>
            </div>
            <div class="tagline">Elevating your academic journey.</div>
            <div class="tagline-desc">Experience a curated educational portal designed for excellence, clarity, and institutional prestige.</div>
            <div class="hero-img-container">
                <img src="{{ asset('assets/img/corridor_blue.png') }}" alt="University Corridor" class="hero-img"
                    onerror="this.src='https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                <div class="hero-img-gradient"></div>
            </div>
        </div>

        <div class="login-panel-right">
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Please enter your credentials to proceed.</p>

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <span class="form-section-label">SELECT YOUR ROLE</span>
                <div class="role-selector">
                    <div class="role-btn {{ old('role', 'mahasiswa') === 'mahasiswa' ? 'active' : '' }}" data-role="mahasiswa">
                        <i class="bi bi-mortarboard role-icon"></i>
                        <span class="role-text">MAHASISWA</span>
                    </div>
                    <div class="role-btn {{ old('role') === 'dosen' ? 'active' : '' }}" data-role="dosen">
                        <i class="bi bi-person-badge role-icon"></i>
                        <span class="role-text">DOSEN</span>
                    </div>
                    <div class="role-btn {{ old('role') === 'admin' ? 'active' : '' }}" data-role="admin">
                        <i class="bi bi-shield-lock role-icon"></i>
                        <span class="role-text">ADMIN</span>
                    </div>
                </div>
                <input type="hidden" id="role_input" name="role" value="{{ old('role', 'mahasiswa') }}">

                <div class="form-group">
                    <div class="form-group-header">
                        <label class="form-label" id="username_label">USERNAME / NIM</label>
                    </div>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" id="username_input" name="username" class="form-input"
                            placeholder="e.g. 21010023" value="{{ old('username') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-group-header">
                        <label class="form-label">PASSWORD</label>
                        <a href="#" class="forgot-link">Lupa Password?</a>
                    </div>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" id="password_input" name="password" class="form-input"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
                        <button type="button" class="password-toggle" id="password_toggle">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-checkbox">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Remember this session</label>
                </div>

                <button type="submit" class="btn-login">
                    Masuk <i class="bi bi-box-arrow-in-right"></i>
                </button>
            </form>

            <div class="panel-footer">
                <a href="#" class="manual-link"><i class="bi bi-book"></i> Panduan Sistem</a>
                <span class="copyright-text">SIAKAD GALLERY &copy; {{ date('Y') }} &bull; ACADEMIC EXCELLENCE</span>
            </div>
        </div>
    </div>

    <div class="global-footer">
        <a href="#">SUPPORT CENTER</a>
        <a href="#">PRIVACY POLICY</a>
        <a href="#">TERMS OF SERVICE</a>
    </div>

    <script>
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const role = this.getAttribute('data-role');
                document.getElementById('role_input').value = role;
                const labels = { mahasiswa:'USERNAME / NIM', dosen:'USERNAME / NIDN', admin:'USERNAME' };
                const placeholders = { mahasiswa:'e.g. 21010023', dosen:'e.g. 198504122010', admin:'e.g. admin' };
                document.getElementById('username_label').textContent = labels[role];
                document.getElementById('username_input').placeholder = placeholders[role];
            });
        });
        document.getElementById('password_toggle').addEventListener('click', function () {
            const input = document.getElementById('password_input');
            const icon = this.querySelector('i');
            if (input.type === 'password') { input.type='text'; icon.className='bi bi-eye-slash'; }
            else { input.type='password'; icon.className='bi bi-eye'; }
        });
    </script>
</body>
</html>
