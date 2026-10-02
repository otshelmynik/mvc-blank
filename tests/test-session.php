<?php
require '/var/www/html/app/Config.php';
Config::init();
session_start();
echo "Session path: " . session_save_path() . "\n";
echo "Session ID: " . session_id() . "\n";
$_SESSION['flash']['test'] = 'flash works';
echo "Session saved: " . (isset($_SESSION['flash']['test']) ? 'yes' : 'no');
echo "\nSession file: " . session_save_path() . "/sess_" . session_id();
