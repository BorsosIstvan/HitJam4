<script>
let spelers = JSON.parse(sessionStorage.getItem('hjPartySpelers')) || [];
let huidigeSpelerIndex = parseInt(sessionStorage.getItem('hjPartyIndex')) || 0;
let huidigeRonde = parseInt(sessionStorage.getItem('hjPartyRonde')) || 1;
const maxRondes = 5;

document.addEventListener("DOMContentLoaded", function() {
    if (spelers.length > 0) {
        if(document.getElementById('schermSetup')) document.getElementById('schermSetup').classList.remove('active');
        if (huidigeRonde > maxRondes) { toonEindstand(); } else { laadBeurtScherm(); }
    }
});

function voegSpelerToe() {
    const name = document.getElementById('spelerNaamInput').value.trim();
    if (name === "") return;
    spelers.push({ naam: name, score: 0 });
    document.getElementById('spelerNaamInput').value = "";
    updateSpelerLijstUI();
}

function verwijderSpeler(index) { spelers.splice(index, 1); updateSpelerLijstUI(); }

function updateSpelerLijstUI() {
    const box = document.getElementById('spelerLijstBox');
    box.innerHTML = "";
    spelers.forEach((s, i) => { box.innerHTML += `<div class="player-badge"><span>👤 ${s.naam}</span><span style="color:#ff2d55; cursor:pointer;" onclick="verwijderSpeler(${i})">×</span></div>`; });
    document.getElementById('btnStartGame').style.display = spelers.length >= 1 ? "block" : "none";
}

function startHetSpel() {
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    sessionStorage.setItem('hjPartyIndex', '0'); sessionStorage.setItem('hjPartyRonde', '1');
    huidigeSpelerIndex = 0; huidigeRonde = 1; laadBeurtScherm();
}

function laadBeurtScherm() {
    wisselScherm('schermBeurt');
    document.getElementById('txtHuidigeSpeler').innerText = spelers[huidigeSpelerIndex].naam;
}

function activeerQuizSectie() {
    wisselScherm('schermQuiz');
    
    // 🪝 HAAKJE: DISCO ACHTERGROND EFFECT
    if (typeof toggleDiscoAchtergrond === "function") { toggleDiscoAchtergrond(true); }

    document.getElementById('quizSpelerNaam').innerText = `Beurt van: ${spelers[huidigeSpelerIndex].naam}`;
    document.getElementById('partyAudioEngine').play().catch(e => console.log("Klik vereist"));
}

function controleerJaar(knop, gekozen, correct) {
    document.querySelectorAll('.btn-jaar').forEach(b => b.disabled = true);
    if (typeof toggleDiscoAchtergrond === "function") { toggleDiscoAchtergrond(false); }

    const txt = document.getElementById('quizFeedbackText');
    if (gekozen === correct) {
        knop.style.borderColor = "#00ffcc"; txt.innerHTML = "<span style='color:#00ffcc;'>🎉 GOED! (+100p)</span>";
        spelers[huidigeSpelerIndex].score += 100;
        
        // 🪝 HAAKJE: GOED ANTWOORD EFFECTEN
        if (typeof startPartyRegen === "function") { startPartyRegen('goud'); }
        if (typeof startHitExplosie === "function") { startHitExplosie(); }
    } else {
        knop.style.borderColor = "#ff2d55"; txt.innerHTML = "<span style='color:#ff2d55;'>❌ FOUT!</span>";
        document.querySelectorAll('.btn-jaar').forEach(b => { if(parseInt(b.innerText)===correct) b.style.borderColor="#00ffcc"; });
        
        // 🪝 HAAKJE: FOUT ANTWOORD EFFECT
        if (typeof startFoutStroboscoop === "function") { startFoutStroboscoop(); }
    }
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    document.getElementById('feedbackSefctie', document.getElementById('feedbackSectie').style.display = "block");
}

function volgendeBeurt() {
    document.getElementById('partyAudioEngine').pause();
    
    // 🪝 HAAKJE: EFFECTEN OPHEFFEN EN RESETTEN
    if (typeof stopPartyRegen === "function") { stopPartyRegen(); }

    huidigeSpelerIndex++;
    if (huidigeSpelerIndex >= spelers.length) { huidigeSpelerIndex = 0; huidigeRonde++; sessionStorage.setItem('hjPartyRonde', huidigeRonde.toString()); }
    sessionStorage.setItem('hjPartyIndex', huidigeSpelerIndex.toString());
    if (huidigeRonde > maxRondes) { toonEindstand(); } else { window.location.href = 'speel.php'; }
}

function toonEindstand() {
    wisselScherm('schermEind');
    const box = document.getElementById('eindklassementBox'); box.innerHTML = "";
    let gerangschikt = [...spelers].sort((a, b) => b.score - a.score);
    gerangschikt.forEach((s, i) => { box.innerHTML += `<div class="player-badge"><span>#${i+1} ${s.naam}</span><strong>${s.score} Pnt</strong></div>`; });
    
    // 🪝 HAAKJE: EINDWINNAAR EFFECT
    if (typeof startPartyRegen === "function") { startPartyRegen('goud'); }
}

function opnieuwSpelen() { sessionStorage.clear(); window.location.href = 'speel.php'; }
function wisselScherm(id) { document.querySelectorAll('.game-screen').forEach(s => s.classList.remove('active')); if(document.getElementById(id)) document.getElementById(id).classList.add('active'); }
</script>
