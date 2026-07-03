<?php
// We halen het echte jaar uit de gekozen song
$echt_jaar = (int)$song['year'];

// Genereer 3 nep-jaartallen in de buurt van het echte jaar (maximaal 7 jaar verschil)
$jaren_lijst = [$echt_jaar];
while (count($jaren_lijst) < 4) {
    $nep_jaar = $echt_jaar + rand(-7, 7);
    if (!in_array($nep_jaar, $jaren_lijst) && $nep_jaar <= 2026) {
        $jaren_lijst[] = $nep_jaar;
    }
}
// Sorteer de jaartallen willekeurig
sort($jaren_lijst);
?>

<!-- HTML Structuur voor de Quiz Knoppen -->
<div id="quizSectie" style="margin: 20px 0;">
    <p id="quizInstructie" style="color:#aaa; font-size:14px; margin-bottom: 10px;">Kies het juiste uitgavejaar:</p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <?php foreach ($jaren_lijst as $jaar): ?>
            <button class="btn btn-jaar-keuze" style="padding: 20px 10px; border-radius: 16px; font-size: 22px; font-weight: 900; border: 2px solid #33343f; background: #1f2026; color: white; cursor: pointer; transition: all 0.1s;" onclick="controleerJaar(this, <?= $jaar ?>, <?= $echt_jaar ?>)">
                <?= $jaar ?>
            </button>
        <?php endforeach; ?>
    </div>
    
    <!-- Vakje voor Directe Goed/Fout Feedback op het scherm -->
    <div id="quizFeedback" style="font-size: 24px; font-weight: 900; margin-top: 15px; display: none; text-transform: uppercase;"></div>
</div>

<script>
// 🔥 CRUCIALE FIX: Spatie wegehaald bij 'knopElement' zodat de functie correct start!
function controleerJaar(knopElement, gekozenJaar, correctJaar) {
    // 1. Schakel direct alle jaarknoppen uit
    const alleKnoppen = document.querySelectorAll('.btn-jaar-keuze');
    alleKnoppen.forEach(btn => btn.disabled = true);

    // ❌ VERWIJDERD: De code die de audio stopte is hier weggehaald! De muziek blijft doorspelen.

    const feedbackDiv = document.getElementById('quizFeedback');
    if (feedbackDiv) feedbackDiv.style.display = 'block';

    // 2. Geef visuele feedback en bereken de score
    const scoreEl = document.getElementById('localScore');
    const streakEl = document.getElementById('localStreak');
    
    let huidigeStreak = streakEl ? parseInt(streakEl.innerText) || 0 : 0;
    let huidigeScore = scoreEl ? parseInt(scoreEl.innerText) || 0 : 0;

    if (gekozenJaar === correctJaar) {
        knopElement.style.borderColor = '#00ffcc';
        knopElement.style.background = 'rgba(0, 255, 204, 0.1)';
        
        huidigeStreak += 1;
        let puntenWinst = 50 + (huidigeStreak * 10);
        huidigeScore += puntenWinst;
        
        if (scoreEl) scoreEl.innerText = huidigeScore;
        if (streakEl) streakEl.innerText = huidigeStreak;
        if (feedbackDiv) feedbackDiv.innerHTML = `<span style='color:#00ffcc;'>🎉 Goed! (+${puntenWinst} Pnt)</span>`;
        
        if (typeof updateDatabaseScore === "function") {
            updateDatabaseScore(puntenWinst, huidigeStreak);
        }
    } else {
        knopElement.style.borderColor = '#ff2d55';
        knopElement.style.background = 'rgba(255, 45, 85, 0.1)';
        if (feedbackDiv) feedbackDiv.innerHTML = "<span style='color:#ff2d55;'>❌ Helaas Fout!</span>";
        
        if (streakEl) streakEl.innerText = 0;
        
        if (typeof updateDatabaseScore === "function") {
            updateDatabaseScore(0, 0);
        }
        
        alleKnoppen.forEach(btn => {
            if (parseInt(btn.innerText) === correctJaar) { 
                btn.style.borderColor = '#00ffcc'; 
                btn.style.background = 'rgba(0, 255, 204, 0.05)';
            }
        });
    }

    // 3. Activeer direct de onthullingskaart
    const infoCard = document.getElementById('infoCard') || document.querySelector('.song-info-card');
    if (infoCard) infoCard.style.display = 'block';
}

</script>