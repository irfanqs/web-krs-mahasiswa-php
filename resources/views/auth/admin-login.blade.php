<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SIAKAD Gallery</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0B1E4F;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 40px 48px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
        }
        .icon-box {
            width: 40px; height: 40px;
            background: #1B3679;
            color: white;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .brand { font-size: 16px; font-weight: 800; color: #1B3679; }
        .brand-sub { font-size: 10px; color: #9CA3AF; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        h1 { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 4px; }
        .subtitle { font-size: 13px; color: #6B7280; margin-bottom: 28px; }

        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex; align-items: center; gap: 8px;
            background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA;
        }

        .form-group { margin-bottom: 18px; }
        .form-label { font-size: 11px; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px; }
        .input-wrap { position: relative; display: flex; align-items: center; }
        .input-icon { position: absolute; left: 14px; color: #9CA3AF; font-size: 16px; }
        .form-input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: #111827;
            transition: all 0.2s;
        }
        .form-input:focus { outline: none; background: white; border-color: #1B3679; box-shadow: 0 0 0 3px rgba(27,54,121,0.1); }
        .toggle-pw { position: absolute; right: 12px; background: none; border: none; cursor: pointer; color: #9CA3AF; font-size: 16px; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #1B3679;
            color: white;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
        .btn-login:hover { background: #11235A; }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #6B7280;
            text-decoration: none;
        }
        .back-link:hover { color: #1B3679; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="icon-box"><i class="bi bi-shield-lock"></i></div>
            <div>
                <div class="brand">SIAKAD Gallery</div>
                <div class="brand-sub">Administrator</div>
            </div>
        </div>

        <h1>Login Admin</h1>
        <p class="subtitle">Akses terbatas untuk administrator sistem.</p>

        @if(session('error'))
            <div class="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">USERNAME</label>
                <div class="input-wrap">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" name="username" class="form-input"
                        placeholder="Username admin" value="{{ old('username') }}" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">PASSWORD</label>
                <div class="input-wrap">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" id="pw" name="password" class="form-input"
                        placeholder="••••••••" required>
                    <button type="button" class="toggle-pw" onclick="
                        const i=document.getElementById('pw');
                        const ic=this.querySelector('i');
                        if(i.type==='password'){i.type='text';ic.className='bi bi-eye-slash';}
                        else{i.type='password';ic.className='bi bi-eye';}
                    "><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Kembali ke halaman login mahasiswa/dosen
        </a>
    </div>
</body>
</html>
