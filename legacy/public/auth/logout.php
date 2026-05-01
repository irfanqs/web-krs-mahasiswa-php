<?php
/**
 * Logout - SIAKAD Gallery
 */

require_once '../../includes/config.php';
require_once '../../includes/auth.php';

do_logout();
flash('success', 'Anda telah logout. Sampai jumpa!');
redirect(APP_URL . '/auth/login.php');
