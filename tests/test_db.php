<?php
try {
    $pdo = new PDO('mysql:host=db;dbname=mvc_blank', 'mvc_user', 'mvc_password');
    $stmt = $pdo->query('DESCRIBE posts');
    echo "Posts table structure:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
