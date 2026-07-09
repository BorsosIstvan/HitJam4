<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Docker & SQLite Rechten Debugger</h2>";

// 1. Controleer de actuele PHP-gebruiker
if (function_exists('posix_getpwuid')) {
    $user_info = posix_getpwuid(posix_geteuid());
    echo "<b>PHP Gebruiker (whoami):</b> " . $user_info['name'] . " (UID: " . $user_info['uid'] . ")<br>";
} else {
    echo "<b>PHP Gebruiker (whoami):</b> " . exec('whoami') . "<br>";
}

// Het pad dat je probeert te gebruiken
$target_dir = '/var/www/html/HitData';
$parent_dir = '/var/www/html';

echo "<hr>";

// 2. Controleer de bovenliggende map (/var/www/html)
echo "<h3>Bovenliggende map: $parent_dir</h3>";
echo "Bestaat: " . (is_dir($parent_dir) ? "✅ Ja" : "❌ Nee") . "<br>";
echo "Leesbaar: " . (is_readable($parent_dir) ? "✅ Ja" : "❌ Nee") . "<br>";
echo "Schrijfbaar: " . (is_writable($parent_dir) ? "✅ Ja" : "❌ Nee") . "<br>";
if (is_dir($parent_dir)) {
    echo "Echte Linux Rechten: " . substr(sprintf('%o', fileperms($parent_dir)), -4) . "<br>";
}

echo "<hr>";

// 3. Controleer de doelmap (/var/www/html/HitData)
echo "<h3>Doelmap: $target_dir</h3>";
echo "Bestaat: " . (is_dir($target_dir) ? "✅ Ja" : "❌ Nee") . "<br>";
echo "Leesbaar: " . (is_readable($target_dir) ? "✅ Ja" : "❌ Nee") . "<br>";
echo "Schrijfbaar: " . (is_writable($target_dir) ? "✅ Ja" : "❌ Nee") . "<br>";
if (is_dir($target_dir)) {
    echo "Echte Linux Rechten: " . substr(sprintf('%o', fileperms($target_dir)), -4) . "<br>";
}

echo "<hr>";

// 4. Probeer handmatig een testbestand aan te maken in HitData
echo "<h3>Schrijf-test in HitData</h3>";
$test_file = $target_dir . '/test_permissions.txt';
@$write_result = file_put_contents($test_file, 'test');

if ($write_result !== false) {
    echo "✅ <b>Succes!</b> PHP kan bestanden aanmaken in HitData.<br>";
    unlink($test_file); // Ruim testbestand op
} else {
    $last_error = error_get_last();
    echo "❌ <b>Fout!</b> PHP mag niet schrijven in HitData.<br>";
    echo "Reden: " . ($last_error ? $last_error['message'] : "Onbekende rechtenfout") . "<br>";
}
?>
