<?php
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['loggedin'])) { 
    header("Location: login.php"); 
    exit; 
}

require_once('hj2_db.php');

// 1. HAAL EEN WILLEKEURIG LIEDJE UIT SQLITE
try {
    $stmt = $db->query("SELECT id, artist, title, year FROM game_songs ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$song) { 
        die("<p style='color:red; text-align:center; margin-top:50px;'>Fout: Geen liedjes gevonden in de database.</p>"); 
    }
} catch (Exception $e) { 
    die("Database fout: " . $e->getMessage()); 
}

// 2. INTERNET / ITUNES API INTEGRATIE (30 SEC PREVIEW)
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
    if (isset($json['results'][0]['previewUrl'])) {
        $preview_url = $json['results'][0]['previewUrl'];
    }
}

// Fallback als iTunes het nummer niet kent
if (empty($preview_url)) {
    header("Location: speel.php");
    exit;
}

// 3. GENEREER DE 4 MEERKEUZE JAREN
$echt_jaar = (int)$song['year'];
$jaren_lijst = [$echt_jaar];
$pogingen = 0;

while (count($jaren_lijst) < 4 && $pogingen < 100) {
    $pogingen++;
    $max_afwijking = min(7, 2026 - $echt_jaar);
    $min_afwijking = -7;
    $nep_jaar = $echt_jaar + rand($min_afwijking, $max_afwijking);
    
    if (!in_array($nep_jaar, $jaren_lijst)) {
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
    <title>HitJam Party!</title>
    <style>
        :root {
            --bg-color: #0b0c10;
            --card-bg: rgba(255, 255, 255, 0.04);
            --neon-pink: #ff2d55;
            --neon-cyan: #00ffcc;
            --neon-gold: #ff9500;
            --border-color: #33343f;
        }
        body { font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; background-color: var(--bg-color); color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
        .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #120917 0%, #0b0c10 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 40px rgba(0,0,0,0.8); text-align: center; }
        
        .logo-area { margin-bottom: 10px; }
        .logo { font-size: 36px; font-weight: 900; background: linear-gradient(45deg, var(--neon-pink), var(--neon-gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        .subtitle { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin-top: 5px; }
        
        /* Schermen */
        .game-screen { display: none; animation: screenFade 0.4s ease-in-out forwards; }
        .game-screen.active { display: block; }
        @keyframes screenFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Inputs & Lijsten */
        .player-input-row { display: flex; gap: 10px; margin-bottom: 15px; }
        .input-field { flex: 1; padding: 15px; border-radius: 12px; border: 2px solid var(--border-color); background: #1f2026; color: white; font-size: 16px; font-weight: bold; }
        .input-field:focus { border-color: var(--neon-pink); outline: none; }
        .player-badge { background: var(--card-bg); border: 1px solid var(--border-color); padding: 12px; border-radius: 10px; margin-bottom: 8px; text-align: left; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }

        /* Knoppen */
        .btn { width: 100%; padding: 18px; border-radius: 16px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; transition: all 0.2s; text-transform: uppercase; letter-spacing: 1px; }
        .btn:active { transform: scale(0.96); }
        .btn-primary { background: linear-gradient(90deg, var(--neon-pink), #e01b43); color: white; box-shadow: 0 6px 20px rgba(255, 45, 85, 0.3); }
        .btn-start-track { background: linear-gradient(135deg, var(--neon-cyan), #00b3ff); color: #0b0c10; font-size: 20px; font-weight: 900; box-shadow: 0 6px 20px rgba(0, 255, 204, 0.3); padding: 25px; }
        .btn-jaar { padding: 22px 10px; border-radius: 16px; font-size: 24px; font-weight: 900; border: 2px solid var(--border-color); background: #1f2026; color: white; }

        /* Game UI Elementen */
        .turn-announcement { font-size: 14px; color: #aaa; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px; }
        .current-player-name { font-size: 38px; font-weight: 900; color: var(--neon-cyan); margin: 0 0 25px 0; text-shadow: 0 0 15px rgba(0,255,204,0.3); }
        
        .song-info-card { background: var(--card-bg); padding: 25px 20px; border-radius: 24px; border: 2px solid var(--border-color); margin: 20px 0; text-align: center; }
        .info-year { font-size: 64px; font-weight: 900; color: var(--neon-gold); margin-bottom: 5px; }
        .info-title { font-size: 22px; font-weight: 800; margin-bottom: 5px; line-height: 1.2; }
        .info-artist { color: #b3b3b3; font-size: 16px; }

        .footer { font-size: 10px; color: #444; letter-spacing: 1px; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="app-container">
        
        <!-- Header -->
        <div class="logo-area">
            <h1 class="logo">HitJam</h1>
            <div class="subtitle">🎉 Party Edition</div>
        </div>

        <!-- 🎥 SCHERM 1: SPELERS INVOEREN -->
        <div id="schermSetup" class="game-screen active">
            <p style="color: #ccc; margin-bottom: 20px;">Voeg je vrienden toe om te starten:</p>
            <div class="player-input-row">
                <input type="text" id="spelerNaamInput" class="input-field" placeholder="Naam van vriend(in)..." onkeypress="if(event.key==='Enter') voegSpelerToe()">
                <button class="btn btn-primary" style="width: auto; padding: 15px 25px;" onclick="voegSpelerToe()">+</button>
            </div>
            <div id="spelerLijstBox" style="margin: 20px 0; max-height: 250px; overflow-y: auto;">
                <!-- Dynamische spelers komen hier -->
            </div>
            <button class="btn btn-primary" id="btnStartGame" style="margin-top: 15px; display: none;" onclick="startHetSpel()">🚀 START HET FEEST</button>
        </div>

        <!-- 🎥 SCHERM 2: DE BEURT / READY -->
        <div id="schermBeurt" class="game-screen">
            <div class="turn-announcement">Nu aan de beurt:</div>
            <h2 class="current-player-name" id="txtHuidigeSpeler">Speler</h2>
            
            <div style="margin: 40px 0;">
                <button class="btn btn-start-track" onclick="activeerQuizSectie()">🎵 HOOR HET LIED</button>
            </div>
            <p style="color: #666; font-size: 13px;">Geef de telefoon aan de speler hierboven!</p>
        </div>

        <!-- 🎥 SCHERM 3: DE QUIZ & JAARKNOPPEN -->
        <div id="schermQuiz" class="game-screen">
            <div id="quizSpelerNaam" style="font-size: 16px; font-weight: bold; color: var(--neon-cyan); text-transform: uppercase; margin-bottom: 15px;"></div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 25px 0;">
                <?php foreach ($jaren_lijst as $jaar): ?>
                    <button class="btn btn-jaar" onclick="controleerJaar(this, <?= $jaar ?>, <?= $echt_jaar ?>)">
                        <?= $jaar ?>
                    </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Directe Feedback & Infokaart (Standaard verborgen, klapt open in ditzelfde scherm!) -->
            <div id="feedbackSectie" style="display: none; margin-top: 20px;">
                <div id="quizFeedbackText" style="font-size: 26px; font-weight: 900; margin-bottom: 15px; text-transform: uppercase;"></div>
                
                <div class="song-info-card" id="partyInfoCard">
                    <div class="info-year"><?= $echt_jaar ?></div>
                    <div class="info-title"><?= htmlspecialchars($song['title'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="info-artist"><?= htmlspecialchars($song['artist'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <button class="btn" style="background: linear-gradient(90deg, #007bff, var(--neon-cyan)); color: white;" onclick="volgendeBeurt()">
                    🔄 VOLGENDE SPELER
                </button>
            </div>
        </div>

        <!-- 🎥 SCHERM 4: EINDKLASSEMENT -->
        <div id="schermEind" class="game-screen">
            <h2 style="font-size: 32px; font-weight: 900; color: var(--neon-gold); margin-bottom: 5px;">🏆 SPEL AFGELOPEN</h2>
            <p style="color: #aaa; margin-bottom: 25px;">Hier is de eindstand:</p>
            <div id="eindklassementBox" style="margin: 20px 0;"></div>
            <button class="btn btn-primary" onclick="opnieuwSpelen()">🔄 NIEUW SPEL STARTEN</button>
        </div>

        <!-- Onzichtbare Audio Motor -->
        <audio id="partyAudioEngine" src="<?= htmlspecialchars($preview_url, ENT_QUOTES, 'UTF-8') ?>" preload="auto"></audio>

        <!-- Footer -->
        <div class="footer">
            HITJAM PARTY ENGINE • POWERED BY ITUNES API
        </div>

    </div>

    <script>
// Wij bewaren alle spelersdata veilig in het browsergeheugen (SessionStorage)
let spelers = JSON.parse(sessionStorage.getItem('hjPartySpelers')) || [];
let huidigeSpelerIndex = parseInt(sessionStorage.getItem('hjPartyIndex')) || 0;
let huidigeRonde = parseInt(sessionStorage.getItem('hjPartyRonde')) || 1;
const maxRondes = 5; 
// Verhoog dit als je een langer spel wilt!
// Synchroniseer interface bij herladen van de pagina
document.addEventListener("DOMContentLoaded", function() {if (spelers.length > 0) {
    // Als er al spelers zijn ingevoerd, gaan we direct door naar het speelscherm
    document.getElementById('schermSetup').classList.remove('active');
    if (huidigeRonde > maxRondes) {toonEindstand();

    } else {laadBeurtScherm();
        
    }}});