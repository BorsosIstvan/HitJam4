<!-- Bouwsteen: Interactieve Infokaart & Spelknop in één -->
<div class="song-info-card" id="infoCard" onclick="verwerkKaartKlik()" style="display: block; background: rgba(255, 255, 255, 0.04); padding: 25px 20px; border-radius: 24px; border: 2px solid #ff2d55; margin: 25px 0; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 8px 25px rgba(255, 45, 85, 0.1);">
    
    <!-- STANDAARD TEKST (Als de gegevens nog geheim zijn) -->
    <div id="infoGeheimTxt" style="font-size: 18px; font-weight: bold; color: #ff2d55; text-transform: uppercase; letter-spacing: 1px; padding: 20px 0; text-align: center;">
    Welke jaar is dit lied?
    </div>

    <!-- DE GEGEVENS (Standaard verborgen) -->
    <div id="infoDataSectie" style="display: none; animation: fadeInHJ2 0.4s ease; text-align: center;">
        <div class="info-year" style="font-size: 56px; font-weight: 900; color: #ff9500; margin-bottom: 5px;"><?= (int)$song['year'] ?></div>
        <div class="info-title" style="font-size: 22px; font-weight: 800; margin-bottom: 5px;"><?= htmlspecialchars($song['title'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="info-artist" style="color: #b3b3b3; font-size: 16px; margin-bottom: 15px;"><?= htmlspecialchars($song['artist'], ENT_QUOTES, 'UTF-8') ?></div>
        
        <div style="font-size: 11px; color: #00ffcc; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; background: rgba(0,255,204,0.05); padding: 8px; border-radius: 8px; display: inline-block;">
            🔄 Klik hier voor de volgende hit!
        </div>
    </div>
</div>

<style>
@keyframes fadeInHJ2 { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
let kaartStaatOpen = false;

function verwerkKaartKlik() {
    const audio = document.getElementById('soloAudio');
    const playBtn = document.getElementById('playBtn');
    const kaart = document.getElementById('infoCard');

    if (!kaartStaatOpen) {
        kaartStaatOpen = true;

        // Stop muziek direct
        if (audio) audio.pause();
        if (playBtn) { playBtn.innerHTML = "▶️"; playBtn.classList.remove('playing'); }

        // Schakel weergave om
        document.getElementById('infoGeheimTxt').style.display = 'none';
        document.getElementById('infoDataSectie').style.display = 'block';

        // Update kaartstijl naar open-modus
        kaart.style.borderColor = "#00ffcc";
        kaart.style.boxShadow = "0 8px 25px rgba(0, 255, 204, 0.2)";

        // ANTI-CHEAT: Schakel de quiz-knoppen uit als men direct spiekt
        const alleKnoppen = document.querySelectorAll('.btn-jaar-keuze');
        alleKnoppen.forEach(btn => btn.disabled = true);
        
        const feedbackDiv = document.getElementById('quizFeedback');
        if (feedbackDiv && feedbackDiv.style.display !== 'block') {
            feedbackDiv.style.display = 'block';
            feedbackDiv.innerHTML = "<span style='color:#ffaa00;'>👁️ Antwoord bekeken</span>";
        }

    } else {
        // Volgende liedje laden (Klik nummer 2)
        // Controleer of we in de multiplayer modus zitten
        if (typeof mpRondeId !== "undefined" || window.location.search.includes('multiplayer=1')) {
            window.location.href = 'speel.php?multiplayer=1';
        } else {
            window.location.href = 'speel.php';
        }
    }
}

// Wacht tot alle bouwstenen zijn ingeladen voordat we de quiz-functie overschrijven
document.addEventListener("DOMContentLoaded", function() {
    if (typeof controleerJaar === "function") {
        const origineleControleerJaar = controleerJaar;
        
        controleerJaar = function(knopElement, gekozenJaar, correctJaar) {
            // 1. Voer de normale goed/fout-score logica van comp_quiz.php uit
            origineleControleerJaar(knopElement, gekozenJaar, correctJaar);
            
            // 2. Zet de kaart direct op open-modus zonder de kaartKlik logica te tripperen
            kaartStaatOpen = true;
            document.getElementById('infoGeheimTxt').style.display = 'none';
            document.getElementById('infoDataSectie').style.display = 'block';
            
            const kaart = document.getElementById('infoCard');
            if (kaart) {
                kaart.style.borderColor = "#00ffcc";
                kaart.style.boxShadow = "0 8px 25px rgba(0, 255, 204, 0.2)";
            }
        };
    }
});
</script>