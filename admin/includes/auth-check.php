<?php
/**
 * Guard included by every admin page (except login/logout).
 */
require_once dirname(__DIR__, 2) . '/includes/functions.php';

setLanguage();

if (!isLoggedIn()) {
    setFlash(t('msg_admin_required'), 'error');
    redirect('admin/login.php');
}

if (!isAdmin()) {
    setFlash(t('msg_admin_required'), 'error');
    redirect('index.php');
}

$adminUser = currentUser();
