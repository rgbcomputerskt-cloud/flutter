<?php
require_once __DIR__ . '/../includes/functions.php';
unset($_SESSION['admin']);
session_regenerate_id(true);
flash('You have been logged out.');
redirect('admin/login.php');
