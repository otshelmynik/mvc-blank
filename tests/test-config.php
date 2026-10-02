<?php
require '/var/www/html/app/Config.php';
Config::init();
echo "db.host = " . Config::get('db.host') . "\n";
echo "db.port = " . Config::get('db.port') . "\n";
echo "db.name = " . Config::get('db.name') . "\n";
echo "db.user = " . Config::get('db.user') . "\n";
echo "db.password = " . Config::get('db.password') . "\n";
