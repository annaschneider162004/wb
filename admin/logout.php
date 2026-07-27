<?php
/**
 * Log the administrator out.
 */
require_once dirname(__DIR__) . '/includes/functions.php';

logoutUser();
redirect('admin/login.php');
