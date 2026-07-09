<?php
// Zorg dat fouten netjes in beeld komen mocht er iets misgaan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
echo "<h1>HitJam3 Database Builder</h1>";

$db_file = '/var/www/html/HitData/hitjam3.db';
$old_db_file = '/var/www/html/HitData/hitjam2.db';

try {
    // 1. Maak of open de nieuwe HitJam3 SQLite database
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Verbonden met/aanmaken van <strong>$db_file</strong>...<br>";

    // 2. Tabellen aanmaken
    echo "⚙️ Bezig met tabellen aanmaken...<br>";

    // Tabel: users
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'speler',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // Tabel: game_songs (Nu met preview_url cache!)
    $db->exec("CREATE TABLE IF NOT EXISTS game_songs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        artist TEXT NOT NULL,
        title TEXT NOT NULL,
        year INTEGER NOT NULL,
        theme TEXT NOT NULL,
        preview_url TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // Tabel: game_status
    $db->exec("CREATE TABLE IF NOT EXISTS game_status (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        game_code TEXT NOT NULL UNIQUE,
        status TEXT DEFAULT 'lobby',
        current_song_id INTEGER DEFAULT 0,
        active_player_id INTEGER DEFAULT 0,
        music_started INTEGER DEFAULT 0,
        start_time REAL DEFAULT 0,
        gestart_door TEXT DEFAULT ''
    );");

    // Tabel: game_players
    $db->exec("CREATE TABLE IF NOT EXISTS game_players (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        tokens INTEGER DEFAULT 3,
        is_host INTEGER DEFAULT 0,
        is_active INTEGER DEFAULT 1
    );");

    // Tabel: player_timelines
    $db->exec("CREATE TABLE IF NOT EXISTS player_timelines (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        player_id INTEGER NOT NULL,
        song_id INTEGER NOT NULL,
        placed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (player_id) REFERENCES game_players(id) ON DELETE CASCADE,
        FOREIGN KEY (song_id) REFERENCES game_songs(id) ON DELETE CASCADE
    );");

    echo "✅ Alle HitJam3 tabellen zijn succesvol aangemaakt!<br><br>";

    // 3. Automatische data-migratie vanuit HitJam2 (indien aanwezig)
    if (file_exists($old_db_file)) {
        echo "📂 Oude <strong>$old_db_file</strong> gedetecteerd! Liedjes overzetten...<br>";
        
        // Controleer of de tabel leeg is voordat we importeren om duplicaten te voorkomen
        $checkCount = $db->query("SELECT COUNT(*) FROM game_songs")->fetchColumn();
        
        if ($checkCount == 0) {
            // Koppel de oude database tijdelijk aan de nieuwe sessie
            $db->exec("ATTACH '$old_db_file' AS hj2");
            
            // Kopieer alle muziekgegevens over
            $db->exec("INSERT INTO game_songs (id, artist, title, year, theme, created_at) 
                       SELECT id, artist, title, year, theme, created_at FROM hj2.game_songs");
            
            // Kopieer ook de bestaande gebruikers over
            $db->exec("INSERT OR IGNORE INTO users (id, username, password, role, created_at) 
                       SELECT id, username, password, role, created_at FROM hj2.users");
            
            echo "🚀 <strong>Succes!</strong> Je bestaande muziekbibliotheek en accounts zijn overgezet naar HitJam3!<br>";
        } else {
            echo "⚠️ Er stonden al liedjes in de HitJam3 database. De import is overgeslagen om dubbele nummers te voorkomen.<br>";
        }
    } else {
        echo "ℹ️ Geen oude <strong>$old_db_file</strong> gevonden in deze map. Je begint met een lege muziekbibliotheek.<br>";
    }

    echo "<br>🎉 <strong>Database is helemaal klaar voor HitJam3!</strong>";

} catch (PDOException $e) {
    echo "<br>❌ <strong>Fout opgetreden:</strong> " . $e->getMessage();
}
?>