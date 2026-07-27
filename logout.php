<?php
/**
 * Log the current user out.
 */
require_once __DIR__ . '/includes/functions.php';

logoutUser();
redirect('index.php');
