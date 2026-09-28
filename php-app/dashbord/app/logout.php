<?php
require_once __DIR__ . '/../../lib/bootstrap.php';

if (is_post()) {
    csrf_verify_or_die();
    admin_logout();
}

redirect('/dashbord/app/login.php');
