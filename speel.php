<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit; }
require_once('hj2_db.php');

try {
    $stmt = $db->query("SELECT id, artist, title, year FROM game_songs ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$song) { die("Database is leeg."); }
} catch (Exception $e) { die("Database fout: " . $e->getMessage()); }

$schone_artiest = str_replace('&', ' ', $song['artist']);
$zoekterm = urlencode($schone_artiest . " " . $song['title']);
$api_url = "https://apple.com" . $zoekterm . "&limit=1&entity=song";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'HitJamParty/4.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 4);
$response = curl_exec($ch);
curl_close($ch);

$preview_url = "";
if ($response) {
    $json = json_decode($response, true);
    if (isset($json['results'][0]['previewUrl'])) { $preview_url = $json['results'][0]['previewUrl']; }
}
if (empty($preview_url)) { header("Location: speel.php"); exit; }

$echt_jaar = (int)$song['year'];
$jaren_lijst = [$echt_jaar];
while (count($jaren_lijst) < 4) {
    $nep_jaar = $echt_jaar + rand(-7, 7);
    if (!in_array($nep_jaar, $jaren_lijst) && $nep_jaar <= 2026) { $jaren_lijst[] = $nep_jaar; }
}
sort($jaren_lijst);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam Party!</title>
    <?php require_once('hj_party_style.php'); ?>
</head>
<body>
    <div class="app-container">
        <div class="logo-area"><h1 class="logo">HitJam</h1><div class="subtitle">🎉 Party Edition</div></div>

        <!-- SCHERM 1 -->
        <div id="schermSetup" class="game-screen active">
            <p style="color: #ccc;">Voeg je vrienden toe:</p>
            <div class="player-input-row">
                <input type="text" id="spelerNaamInput" class="input-field" placeholder="Naam..." onkeypress="if(event.key==='Enter') voegSpelerToe()">
                <button class="btn btn-primary" style="width: auto; padding: 15px 25px;" onclick="voegSpelerToe()">+</button>
            </div>
            <div id="spelerLijstBox" style="margin: 20px 0; max-height: 200px; overflow-y: auto;"></div>
            <button class="btn btn-primary" id="btnStartGame" style="display: none;" onclick="startHetSpel()">🚀 REIS STARTEN</button>
        </div>

        <!-- SCHERM 2 -->
        <div id="schermBeurt" class="game-screen">
            <div class="turn-announcement">Nu aan de beurt:</div>
            <h2 class="current-player-name" id="txtHuidigeSpeler">Speler</h2>
            <button class="btn btn-start-track" onclick="activeerQuizSectie()">🎵 HOOR HET LIED</button>
        </div>

        <!-- SCHERM 3 -->
        <div id="schermQuiz" class="game-screen">
            <div id="quizSpelerNaam" style="font-size: 16px; font-weight: bold; color: var(--neon-cyan); margin-bottom: 15px;"></div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <?php foreach ($jaren_lijst as $jaar): ?>
                    <button class="btn btn-jaar" onclick="controleerJaar(this, <?= $jaar ?>, <?= $echt_jaar ?>)"><?= $jaar ?></button>
                <?php endforeach; ?>
            </div>
            <div id="feedbackSectie" style="display: none; margin-top: 20px;">
                <div id="quizFeedbackText" style="font-size: 24px; font-weight: 900; margin-bottom: 15px;"></div>
                <div class="song-info-card">
                    <div class="info-year"><?= $echt_jaar ?></div>
                    <div class="info-title"><?= htmlspecialchars($song['title']) ?></div>
                    <div class="info-artist"><?= htmlspecialchars($song['artist']) ?></div>
                </div>
                <button class="btn" style="background: linear-gradient(90deg, #007bff, var(--neon-cyan)); color: white;" onclick="volgendeBeurt()">🔄 VOLGENDE</button>
            </div>
        </div>

        <!-- SCHERM 4 -->
        <div id="schermEind" class="game-screen">
            <h2 style="color: var(--neon-gold);">🏆 EINDKLASSEMENT</h2>
            <div id="eindklassementBox" style="margin: 20px 0;"></div>
            <button class="btn btn-primary" onclick="opnieuwSpelen()">🔄 OPRAAP NIEUW SPEL</button>
        </div>

        <audio id="partyAudioEngine" src="<?= htmlspecialchars($preview_url) ?>" preload="auto"></audio>
        <div class="footer">HITJAM PARTY ENGINE</div>
    </div>
    <?php require_once('hj_party_script.php'); ?>
</body>
</html>
