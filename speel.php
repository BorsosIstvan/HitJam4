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
curl_setopt($ch, CURLOPT_USERAGENT, 'HitJamDisco/4.0');
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

// 4. JAARKEUZE GENEREREN
$echt_jaar = (int)$song['year'];
$jaren_lijst = [$echt_jaar];
$pogingen = 0;
while (count($jaren_lijst) < 4 && $pogingen < 50) {
    $pogingen++;
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
    <title>HitJam Disco Club!</title>
    <?php require_once('hj_party_style.php'); ?>
</head>
<body>
    <div class="app-container">
        <div class="logo-area"><h1 class="logo">HitJam</h1><div class="subtitle">🪩 Disco Edition</div></div>

        <!-- VEILIGHEIDSVENTIEL: GEEN REDIRECTS MEER, MAAR EEN NET DISCO FOUTSCHERM -->
        <?php if (empty($preview_url)): ?>
            <div class="game-screen active" style="margin: 40px 0;">
                <p style="color: var(--neon-pink); font-weight: bold; font-size: 18px; text-shadow:0 0 10px var(--neon-pink);">🪩 DJ WISSELT VAN PLAAT</p>
                <p style="color: #aaa; font-size: 14px; margin-bottom: 25px;">iTunes kon de 30s preview voor dit specifieke nummer niet laden.</p>
                <button class="btn btn-primary" onclick="window.location.reload();">🔄 VOLGENDE PLAAT MIXEN</button>
            </div>
        <?php else: ?>

            <!-- SCHERM 1: ENTREÉ -->
            <div id="schermSetup" class="game-screen active">
                <p style="color: #aaa; font-size:13px; letter-spacing:2px; font-weight:900;">VOER DE PARTY CREW IN:</p>
                <div class="player-input-row">
                    <input type="text" id="spelerNaamInput" class="input-field" placeholder="Naam van vriend(in)..." onkeypress="if(event.key==='Enter') voegSpelerToe()">
                    <button class="btn btn-primary" style="width: auto; padding: 15px 25px;" onclick="voegSpelerToe()">+</button>
                </div>
                <div id="spelerLijstBox" style="margin: 20px 0; max-height: 220px; overflow-y: auto;"></div>
                <button class="btn btn-primary" id="btnStartGame" style="display: none; font-size:16px; letter-spacing:2px;" onclick="startHetSpel()">🪩 OPEN DE DANSVLOER</button>
            </div>

            <!-- SCHERM 2: READY -->
            <div id="schermBeurt" class="game-screen">
                <div id="txtHuidigeSpeler" style="margin: 40px 0;"></div>
                <button class="btn btn-start-track" onclick="activeerQuizSectie()">🎵 DROP DE HIT</button>
            </div>

            <!-- SCHERM 3: DE QUIZ MET DANSENDE VU-METERS -->
            <div id="schermQuiz" class="game-screen">
                <div id="quizSpelerNaam"></div>
                
                <div class="vu-container" id="equalizerBox">
                    <div class="vu-bar"></div>
                    <div class="vu-bar"></div>
                    <div class="vu-bar"></div>
                    <div class="vu-bar"></div>
                    <div class="vu-bar"></div>
                    <div class="vu-bar"></div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px;">
                    <?php foreach ($jaren_lijst as $jaar): ?>
                        <button class="btn btn-jaar" onclick="controleerJaar(this, <?= $jaar ?>, <?= $echt_jaar ?>)"><?= $jaar ?></button>
                    <?php endforeach; ?>
                </div>
                
                <div id="feedbackSectie" style="display: none; margin-top: 15px;">
                    <div id="quizFeedbackText" style="font-size: 24px; font-weight: 900; margin-bottom: 10px;"></div>
                    <div class="song-info-card" id="partyInfoCard" style="border: 2px solid var(--border-color);">
                        <div class="info-year"><?= $echt_jaar ?></div>
                        <div class="info-title"><?= htmlspecialchars($song['title'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="info-artist"><?= htmlspecialchars($song['artist'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <button class="btn" style="background: linear-gradient(90deg, #110722, var(--neon-cyan)); border:2px solid var(--neon-cyan); color: white;" onclick="volgendeBeurt()">🔄 VOLGENDE DJ</button>
                </div>
            </div>

            <!-- SCHERM 4: END -->
            <div id="schermEind" class="game-screen">
                <div id="eindklassementBox" style="margin: 10px 0;"></div>
                <button class="btn btn-primary" onclick="opnieuwSpelen()">🔄 REMIX / NIEUW SPEL</button>
            </div>

            <audio id="partyAudioEngine" src="<?= htmlspecialchars($preview_url, ENT_QUOTES, 'UTF-8') ?>" preload="auto"></audio>
        <?php endif; ?>

        <div class="footer">HITJAM DISCO-TRACK ENGINE</div>
    </div>
    <?php require_once('hj_party_script.php'); ?>
</body>
</html>
