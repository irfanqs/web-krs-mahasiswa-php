<?php
/**
 * Authentication Helper Functions
 * SIAKAD Gallery - Sistem Informasi KRS Mahasiswa
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

/**
 * Generate CSRF Token
 */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function csrf_verify($token)
{
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token ?? '');
}

/**
 * Check if user is logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user data from session
 */
function current_user()
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'user_id' => $_SESSION['user_id'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'nama' => $_SESSION['nama'] ?? null,
        'email' => $_SESSION['email'] ?? null,
    ];
}

/**
 * Require specific role(s)
 * Redirects to login if not authenticated or wrong role
 */
function require_role($roles = [])
{
    if (!is_logged_in()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect(APP_URL . '/auth/login.php');
        exit;
    }

    $current_role = $_SESSION['role'] ?? null;

    if (!in_array($current_role, (array)$roles)) {
        flash('error', 'Anda tidak memiliki akses ke halaman ini.');
        redirect(APP_URL . '/auth/login.php');
        exit;
    }
}

/**
 * Set flash message
 */
function flash($key, $message = null)
{
    if ($message === null) {
        // Get flash message
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    } else {
        // Set flash message
        $_SESSION['flash'][$key] = $message;
    }
}

/**
 * Login as Mahasiswa
 */
function login_mahasiswa($nim, $password)
{
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("SELECT nim, nama, email, password, program_studi, status FROM mahasiswa WHERE nim = ?");
        $stmt->execute([$nim]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password']) && $user['status'] === 'aktif') {
            // Session regenerate untuk security
            session_regenerate_id(true);

            // Set session data
            $_SESSION['user_id'] = $user['nim'];
            $_SESSION['role'] = 'mahasiswa';
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['program_studi'] = $user['program_studi'];
            $_SESSION['login_time'] = time();

            return true;
        }

        return false;
    } catch (Exception $e) {
        error_log("Login Mahasiswa Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Login as Dosen
 */
function login_dosen($nidn, $password)
{
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("SELECT nidn, nama, email, password, jurusan FROM dosen WHERE nidn = ?");
        $stmt->execute([$nidn]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Session regenerate
            session_regenerate_id(true);

            // Set session data
            $_SESSION['user_id'] = $user['nidn'];
            $_SESSION['role'] = 'dosen';
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['jurusan'] = $user['jurusan'];
            $_SESSION['login_time'] = time();

            return true;
        }

        return false;
    } catch (Exception $e) {
        error_log("Login Dosen Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Login as Admin
 */
function login_admin($username, $password)
{
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("SELECT id, username, password, nama FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Session regenerate
            session_regenerate_id(true);

            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();

            return true;
        }

        return false;
    } catch (Exception $e) {
        error_log("Login Admin Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logout user
 */
function do_logout()
{
    // Clear all session data
    $_SESSION = [];

    // Destroy session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

/**
 * Redirect function
 */
function redirect($url)
{
    header("Location: " . $url);
    exit;
}
