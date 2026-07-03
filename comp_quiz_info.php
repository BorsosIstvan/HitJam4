<?php
// Haal het echte jaar uit de gekozen song
$echt_jaar = (int)$song['year'];

// Genereer 3 nep-jaartallen in de buurt van het echte jaar (maximaal 7 jaar verschil)
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

<!-- ========================================== -->
<!-- DYNAMISCHE QUIZBOX (ALLES IN ÉÉN BEELD)   -->
<!-- ========================================== -->
<div id="hitjamQuizBox" style="margin: 25px 0; min-height: 220px; display: flex; flex-direction: column; justify-content: center;">

    <!-- FASE 1: DE STARTKNOP -->
    <div id="faseStart">
        <p style="color:#aaa; font-size:16px; margin-bottom: 20px;">Klaar voor de volgende Hit?</p>
        <button class="btn" style="background: linear-gradient(135deg, #ff2d55, #ff9500); color: white; padding: 25px; font-size: 24px; font-weight: 900; border-radius: 20px; box-shadow: 0 8px 25px rgba(255, 45, 85, 0.4);" onclick="startLiedje()">
            🎵 START TRACK
        </button>
    </div>

    <!-- FASE 2: DE JAARKNOPPEN (Standaard verborgen) -->
    <div id="faseSpelen" style="display: none;">
        <p style="color:#aaa; font-size:14px; margin-bottom: 15px;">Uit welk jaar komt deze hit?</p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <?php foreach ($jaren_lijst as $jaar): ?>
                <button class="btn btn-jaar-keuze" style="padding: 20px 10px; border-radius: 16px; font-size: 22px; font-weight: 900; border: 2px solid #33343f; background: #1f2026; color: white; cursor: pointer; transition: all 0.1s;" onclick="controleerJaar(this, <?= $jaar ?>, <?= $echt_jaar ?>)">
                    <?= $jaar ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- FASE 3: DE INFOKAART (Standaard verborgen) -->
    <div id="faseResultaat" style="display: none; animation: gecombineerdFadeIn 0.4s ease;">
        
        <!-- Feedback melding bovenin de kaart -->
        <div id="quizFeedback" style="font-size: 22px; font-weight: 900; margin-bottom: 15px; text-transform: uppercase;"></div>

        <!-- Track details -->
        <div class="song-info-card" style="background: rgba(255, 255, 255, 0.04); padding: 25px 20px; border-radius: 24px; border: 2px solid #00ffcc; box-shadow: 0 8px 25px rgba(0, 255, 204, 0.15); text-align: center;">
            <div class="info-year" style="font-size: 56px; font-weight: 900; color: #ff9500; margin-bottom: 5px;"><?= $echt_jaar ?></div>
            <div class="info-title" style="font-size: 22px; font-weight: 800; margin-bottom: 5px;"><?= htmlspecialchars($song['title'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="info-artist" style="color: #b3b3b3; font-size: 16px; margin-bottom: 25px;"><?= htmlspecialchars($song['artist'], ENT_QUOTES, 'UTF-8') ?></div>
            
            <!-- Volgende hit knop IN de infokaart -->
            <button class="btn" style="background: linear-gradient(90deg, #007bff, #00ffcc); color: white; font-size: 16px;" onclick="volgendeTrack()">
                🔄 VOLGENDE HIT!
            </button>
        </div>
    </div>

</div>

<style>
@keyframes gecombineerdFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>

<script>
// 1. START HET LIEDJE EN TOON DE KNOPPEN
function startLiedje() {
    // Wissel van fase-scherm
    document.getElementById('faseStart').style.display = 'none';
    document.getElementById('faseSpelen').style.display = 'block';

    // Start direct de audio via comp_audio.php logica
    const audio = document.getElementById('soloAudio');
    const playBtn = document.getElementById('playBtn');
    
    if (audio) {
        audio.play()
            .then(() => {
                if (playBtn) {
                    playBtn.innerHTML = "⏸️";
                    playBtn.classList.add('playing');
                }
            })
            .catch(err => console.log("Autoplay geblokkeerd, gebruiker start handmatig via audio-button."));
    }
}

// 2. CONTROLEER HET JAAR EN WISSEL NAAR INFOKAART
function controleerJaar(knopElement, gekozenJaar, correctJaar) {
    // Stop de muziek onmiddellijk
    const audio = document.getElementById('soloAudio');
    if (audio) audio.pause();
    const playBtn = document.getElementById('playBtn');
    if (playBtn) { playBtn.innerHTML = "▶️"; playBtn.classList.remove('playing'); }

    // Bereid feedback voor
    const feedbackDiv = document.getElementById('quizFeedback');
    const scoreEl = document.getElementById('localScore');
    const streakEl = document.getElementById('localStreak');
    
    let huidigeStreak = streakEl ? parseInt(streakEl.innerText) || 0 : 0;
    let huidigeScore = scoreEl ? parseInt(scoreEl.innerText) || 0 : 0;

    if (gekozenJaar === correctJaar) {
        huidigeStreak += 1;
        let puntenWinst = 50 + (huidigeStreak * 10);
        huidigeScore += puntenWinst;
        
        if (scoreEl) scoreEl.innerText = huidigeScore;
        if (streakEl) streakEl.innerText = huidigeStreak;
        feedbackDiv.innerHTML = `<span style='color:#00ffcc;'>🎉 GOED! (+${puntenWinst} Pnt)</span>`;
        
        if (typeof updateDatabaseScore === "function") {
            updateDatabaseScore(puntenWinst, huidigeStreak);
        }
    } else {
        feedbackDiv.innerHTML = "<span style='color:#ff2d55;'>❌ HELAAS FOUT!</span>";
        if (streakEl) streakEl.innerText = 0;
        
        if (typeof updateDatabaseScore === "function") {
            updateDatabaseScore(0, 0);
        }
    }

    // Wissel direct naar de infokaart in plaats van de knoppen
    document.getElementById('faseSpelen').style.display = 'none';
    document.getElementById('faseResultaat').style.display = 'block';
}

// 3. LAAD DE VOLGENDE TRACK
function volgendeTrack() {
    if (window.location.search.includes('multiplayer=1')) {
        window.location.href = 'speel.php?multiplayer=1';
    } else {
        window.location.href = 'speel.php';
    }
}
</script>
