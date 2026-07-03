<script>
let spelers = JSON.parse(sessionStorage.getItem('hjPartySpelers')) || [];
let huidigeSpelerIndex = parseInt(sessionStorage.getItem('hjPartyIndex')) || 0;
let huidigeRonde = parseInt(sessionStorage.getItem('hjPartyRonde')) || 1;
const maxRondes = 5;

document.addEventListener("DOMContentLoaded", function() {
    if (spelers.length > 0) {
        const setup = document.getElementById('schermSetup');
        if(setup) setup.classList.remove('active');
        if (huidigeRonde > maxRondes) { toonEindstand(); } else { laadBeurtScherm(); }
    }
});

function voegSpelerToe() {
    const input = document.getElementById('spelerNaamInput');
    if(!input) return;
    const naam = input.value.trim();
    if (naam === "") return;
    spelers.push({ naam: naam, score: 0 });
    input.value = "";
    updateSpelerLijstUI();
}

function verwijderSpeler(index) {
    spelers.splice(index, 1);
    updateSpelerLijstUI();
}

function updateSpelerLijstUI() {
    const box = document.getElementById('spelerLijstBox');
    const btn = document.getElementById('btnStartGame');
    if(!box || !btn) return;
    box.innerHTML = "";
    spelers.forEach((speler, idx) => {
        box.innerHTML += `<div class="player-badge"><span>👤 ${speler.naam}</span><span style="color:var(--accent-color); cursor:pointer; font-weight:900;" onclick="verwijderSpeler(${idx})">×</span></div>`;
    });
    btn.style.display = spelers.length >= 1 ? "block" : "none";
}

function startHetSpel() {
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    sessionStorage.setItem('hjPartyIndex', '0');
    sessionStorage.setItem('hjPartyRonde', '1');
    huidigeSpelerIndex = 0;
    huidigeRonde = 1;
    laadBeurtScherm();
}

function laadBeurtScherm() {
    wisselScherm('schermBeurt');
    const txt = document.getElementById('txtHuidigeSpeler');
    if(txt && spelers[huidigeSpelerIndex]) {
        txt.innerHTML = `${spelers[huidigeSpelerIndex].naam}<br><span style="font-size:14px; color:#666; font-weight:normal;">Ronde ${huidigeRonde} van ${maxRondes}</span>`;
    }
}

function activeerQuizSectie() {
    wisselScherm('schermQuiz');
    
    // 🪝 EFFECT HAAKJE: Muziek start met spelen. Activeer hier eventueel een effect.
    if (typeof startPartyRegen === "function") {
         startPartyRegen('neon', '.app-container'); 
    }

    const txtQuiz = document.getElementById('quizSpelerNaam');
    if(txtQuiz && spelers[huidigeSpelerIndex]) {
        txtQuiz.innerText = `Beurt van: ${spelers[huidigeSpelerIndex].naam}`;
    }
    const audio = document.getElementById('partyAudioEngine');
    if (audio) audio.play().catch(e => console.log("Klik vereist"));
}

function controleerJaar(knopElement, gekozenJaar, correctJaar) {
    document.querySelectorAll('.btn-jaar').forEach(btn => btn.disabled = true);
    
    const fbText = document.getElementById('quizFeedbackText');
    if (gekozenJaar === correctJaar) {
        knopElement.style.borderColor = "var(--success-color)";
        if(fbText) fbText.innerHTML = "<span style='color: var(--success-color);'>🎉 CORRECT ANTWOORD! (+100p)</span>";
        spelers[huidigeSpelerIndex].score += 100;
        
        // 🪝 EFFECT HAAKJE: Schakel hier de modulaire party_effects.js in bij een win!
        if (typeof startPartyRegen === "function") {
            startPartyRegen('goud', '.app-container');
            // 🔥 NIEUW: Stop de regen automatisch na exact 3 seconden!
            setTimeout(function() {
                if (typeof stopPartyRegen === "function") {
                    stopPartyRegen(); // Stopt het aanmaken van nieuwe deeltjes
                    
                    // Ruim de overgebleven vallende deeltjes direct netjes op
                    document.querySelectorAll('.party-drop').forEach(drop => drop.remove());
                }
            }, 2000);
        }
    } else {
        knopElement.style.borderColor = "var(--accent-color)";
        if(fbText) fbText.innerHTML = "<span style='color: var(--accent-color);'>❌ HELAAS FOUT!</span>";
        document.querySelectorAll('.btn-jaar').forEach(btn => {
            if (parseInt(btn.innerText) === correctJaar) { btn.style.borderColor = "var(--success-color)"; }
        });
    }
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    document.getElementById('feedbackSectie').style.display = "block";
}

function volgendeBeurt() {
    const audio = document.getElementById('partyAudioEngine');
    if (audio) audio.pause();
    
    // 🪝 EFFECT HAAKJE: Schakel elk effect uit voor de beurt overgaat
    if (typeof stopPartyRegen === "function") {
        stopPartyRegen();
    }

    huidigeSpelerIndex++;
    if (huidigeSpelerIndex >= spelers.length) {
        huidigeSpelerIndex = 0;
        huidigeRonde++;
        sessionStorage.setItem('hjPartyRonde', huidigeRonde.toString());
    }
    sessionStorage.setItem('hjPartyIndex', huidigeSpelerIndex.toString());
    if (huidigeRonde > maxRondes) { toonEindstand(); } else { window.location.href = 'speel.php'; }
}

function toonEindstand() {
    wisselScherm('schermEind');
    const box = document.getElementById('eindklassementBox');
    if(!box) return;
    box.innerHTML = "";
    
    let gerangschikt = [...spelers].sort((a, b) => b.score - a.score);
    gerangschikt.forEach((speler, idx) => {
        box.innerHTML += `<div class="player-badge"><span>#${idx+1} ${speler.naam}</span><strong>${speler.score} Pnt</strong></div>`;
    });
    
    // 🪝 EFFECT HAAKJE: Oneindige gouden regen op het eindscherm
    if (typeof startPartyRegen === "function") {
        //startPartyRegen('goud');
    }
}

function opnieuwSpelen() {
    if (typeof stopPartyRegen === "function") { stopPartyRegen(); }
    sessionStorage.clear();
    window.location.href = 'speel.php';
}

function wisselScherm(schermId) {
    document.querySelectorAll('.game-screen').forEach(s => s.classList.remove('active'));
    const doel = document.getElementById(schermId);
    if(doel) { doel.classList.add('active'); }
}
</script>
