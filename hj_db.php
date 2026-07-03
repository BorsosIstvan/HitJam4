<?php
try {
    // 🔥 NIEUW EN ONAFHANKELIJK DATABASESCHIP
    $db_path = '/var/www/html/HitData/hitjam2.db';
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Database verbindingsfout: " . $e->getMessage());
}
?>