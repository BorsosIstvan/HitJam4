<?php
session_start();

// 1. SESSIECONTROLE
if (!isset($_SESSION['loggedin'])) { 
    header("Location: login.php"); 
    exit; 
}

require_once('hj2_db.php');

// 2. HAAL EEN WILLEKEURIG LIEDJE UIT SQLITE
try {
    $stmt = $db->query("SELECT id, artist, title, year FROM game_songs ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$song) { 
        die("<p style='color:red; text-align:center; margin-top:50px;'>Fout: Database is leeg.</p>"); 
    }
} catch (Exception $e) { 
    die("Database fout: " . $e->getMessage()); 
}

// 3. ITUNES API INTEGRATIE
$schone_artiest = str_replace('&', ' ', $song['artist']);
$zoekterm = urlencode($schone_artiest . " " . $song['title']);
$api_url = "https://itunes.apple.com/search?term=" . $zoekterm . "&limit=1&entity=song";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'HitJamClean/4.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 4);
$response = curl_exec($ch);
curl_close($ch);

$preview_url = "";
if ($response) {
    $json = json_decode($response, true);
    if (isset($json['results'][0]['previewUrl'])) { 
        $preview_url = $json['results'][0]['previewUrl']; 
    }
}

// 4. JAARKEUZE MEERKEUZE GENEREREN
$echt_jaar = (int)$song['year'];
$jaren_lijst = [$echt_jaar];
while (count($jaren_lijst) < 4) {
    $nep_jaar = $echt_jaar + rand(-7, 7);
    if (!in_array($nep_jaar, $jaren_lijst) && $nep_jaar <= 2026) { 
        $jaren_lijst[] = $nep_jaar; 
    }
}
sort($jaren_lijst);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam - Party Engine</title>
    <style>
        :root {
            --bg-color: #0b0c10;
            --card-bg: rgba(255, 255, 255, 0.04);
            --border-color: #33343f;
            --accent-color: #ff2d55;
            --success-color: #00ffcc;
        }
        body { font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; background-color: var(--bg-color); color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
        .app-container { width: 100%; max-width: 450px; background: #111216; padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; text-align: center; position: relative; overflow: hidden; }
        .logo-area { margin-bottom: 20px; }
        .logo { font-size: 32px; font-weight: 900; color: #fff; margin: 0; text-transform: uppercase; }
        .game-screen { display: none; }
        .game-screen.active { display: block; }
        .input-field { width: 100%; padding: 15px; border-radius: 12px; border: 2px solid var(--border-color); background: #1f2026; color: white; font-size: 16px; box-sizing: border-box; }
        .player-badge { background: var(--card-bg); border: 1px solid var(--border-color); padding: 12px; border-radius: 10px; margin-bottom: 8px; text-align: left; display: flex; justify-content: space-between; align-items: center; }
        .btn { width: 100%; padding: 18px; border-radius: 14px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; text-transform: uppercase; margin-bottom: 10px; }
        .btn-primary { background: var(--accent-color); color: white; }
        .btn-jaar { padding: 20px 10px; border: 2px solid var(--border-color); background: #1f2026; color: white; font-size: 22px; font-weight: bold; }
        .song-info-card { background: var(--card-bg); padding: 25px 20px; border-radius: 20px; border: 1px solid var(--border-color); margin: 20px 0; }
        .info-year { font-size: 54px; font-weight: 900; color: #ff9500; margin-bottom: 5px; }
        .info-title { font-size: 22px; font-weight: 800; margin-bottom: 5px; }
        .info-artist { color: #b3b3b3; font-size: 16px; }
        .footer { font-size: 11px; color: #444; margin-top: 20px; }
    </style>
    <!-- MODULAIRE EFFECTEN BESTAND KOPPELING -->
    <script src="party_effects.js"></script>
    <script src="party_effects2.js"></script>
</head>
<body>
    <div class="app-container">
        <div class="logo-area"><h1 class="logo">HitJam</h1><p style="color:#666; font-size:12px; margin:5px 0 0 0;">CLEAN GAME ENGINE</p></div>

        <?php if (empty($preview_url)): ?>
            <div class="game-screen active">
                <p style="color: var(--accent-color);">⚠️ iTunes kon dit specifieke nummer niet inladen.</p>
                <button class="btn btn-primary" onclick="window.location.reload();">🔄 VOLGENDE PLAAT</button>
            </div>
        <?php else: ?>
            <!-- SCHERM 1: SETUP -->
            <div id="schermSetup" class="game-screen active">
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <input type="text" id="spelerNaamInput" class="input-field" placeholder="Naam..." onkeypress="if(event.key==='Enter') voegSpelerToe()">
                    <button class="btn btn-primary" style="width: auto; margin:0; padding: 0 25px;" onclick="voegSpelerToe()">+</button>
                </div>
                <div id="spelerLijstBox" style="margin: 20px 0; max-height: 200px; overflow-y: auto;"></div>
                <button class="btn btn-primary" id="btnStartGame" style="display: none;" onclick="startHetSpel()">🚀 START SPEL</button>
            </div>

            <!-- SCHERM 2: READY -->
            <div id="schermBeurt" class="game-screen">
                <p style="color:#aaa;">Nu aan de beurt:</p>
                <h2 id="txtHuidigeSpeler" style="font-size: 36px; margin: 10px 0 30px 0;">Speler</h2>
                <button class="btn btn-primary" style="background:#007bff; padding:22px;" onclick="activeerQuizSectie()">🎵 HOOR HET LIED</button>
            </div>

            <!-- SCHERM 3: QUIZ -->
            <div id="schermQuiz" class="game-screen">
                <div id="quizSpelerNaam" style="font-size: 14px; color:#aaa; margin-bottom: 20px;"></div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <?php foreach ($jaren_lijst as $jaar): ?>
                        <button class="btn btn-jaar" onclick="controleerJaar(this, <?= $jaar ?>, <?= $echt_jaar ?>)"><?= $jaar ?></button>
                    <?php endforeach; ?>
                </div>
                <div id="feedbackSectie" style="display: none;">
                    <div id="quizFeedbackText" style="font-size: 22px; font-weight: bold; margin-bottom: 15px;"></div>
                    <div class="song-info-card">
                        <div class="info-year"><?= $echt_jaar ?></div>
                        <div class="info-title"><?= htmlspecialchars($song['title'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="info-artist"><?= htmlspecialchars($song['artist'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <button class="btn btn-primary" style="background: var(--success-color); color:#000;" onclick="volgendeBeurt()">🔄 VOLGENDE SPELER</button>
                </div>
            </div>

            <!-- SCHERM 4: END -->
            <div id="schermEind" class="game-screen">
                <h2>🏆 EINDKLASSEMENT</h2>
                <div id="eindklassementBox" style="margin: 20px 0;"></div>
                <button class="btn btn-primary" onclick="opnieuwSpelen()">🔄 SPEL RESETTEN</button>
            </div>
            <audio id="partyAudioEngine" src="<?= htmlspecialchars($preview_url, ENT_QUOTES, 'UTF-8') ?>" preload="auto"></audio>
        <?php endif; ?>
        <div class="footer">HITJAM LOGIC ENGINE v4.0</div>
    </div>
    <?php require_once('speel_logic.php'); ?>
</body>
</html>
