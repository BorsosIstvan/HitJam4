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

/**
 * 🔥 NIEUWE MODULAIRE HULPFUNCTIE: Bouwt een live scoreoverzicht voor de schermen
 */
function bouwLiveScorebordUI() {
    let html = '<div style="margin-bottom:20px; font-size:13px; background:rgba(255,255,255,0.03); padding:10px; border-radius:12px; display:flex; justify-content:center; gap:12px; flex-wrap:wrap; border:1px solid var(--border-color);">';
    spelers.forEach(s => {
        // Als de speler nu aan de beurt is, lichten we zijn score subtiel op in het groen
        let isActief = s.naam === spelers[huidigeSpelerIndex].naam ? 'border-bottom:2px solid var(--success-color); padding-bottom:2px; font-weight:900; color:#fff;' : 'color:#888;';
        html += `<span style="${isActief}">👤 ${s.naam}: ${s.score}p</span>`;
    });
    html += '</div>';
    return html;
}

function laadBeurtScherm() {
    wisselScherm('schermBeurt');
    const txt = document.getElementById('txtHuidigeSpeler');
    if(txt && spelers[huidigeSpelerIndex]) {
        // 🔥 PAS DIT AAN: Plak de live scorebalk direct boven de naam van de speler
        const liveScorebord = bouwLiveScorebordUI();
        txt.innerHTML = `${liveScorebord}<div style="font-size:14px; color:#aaa; text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Ronde ${huidigeRonde} van ${maxRondes}</div><div style="font-size:32px; font-weight:900; color:var(--success-color);">${spelers[huidigeSpelerIndex].naam}</div>`;
    }
}

function activeerQuizSectie() {
    wisselScherm('schermQuiz');
    
    if (typeof toggleDiscoAchtergrond === "function") { toggleWinampVisualizer(true); }

    const txtQuiz = document.getElementById('quizSpelerNaam');
    if(txtQuiz && spelers[huidigeSpelerIndex]) {
        // 🔥 PAS DIT AAN: Toon ook tijdens de jaarknoppen de live scores bovenin beeld
        const liveScorebord = bouwLiveScorebordUI();
        txtQuiz.innerHTML = `${liveScorebord}<div style="font-weight:bold; font-size:16px; margin-top:10px; text-transform:uppercase; letter-spacing:1px;">🎯 Kies het jaar, ${spelers[huidigeSpelerIndex].naam}:</div>`;
    }
    document.getElementById('partyAudioEngine').play().catch(e => console.log("Klik vereist"));
}

function controleerJaar(knop, gekozen, correct) {
    document.querySelectorAll('.btn-jaar').forEach(b => b.disabled = true);
    if (typeof toggleDiscoAchtergrond === "function") { toggleWinampVisualizer(false); }

    const txt = document.getElementById('quizFeedbackText');
    if (gekozen === correct) {
        knop.style.borderColor = "#00ffcc"; txt.innerHTML = "<span style='color:#00ffcc;'>🎉 GOED! (+100p)</span>";
        spelers[huidigeSpelerIndex].score += 100;
        
        if (typeof startPartyRegen === "function") { startPartyRegen('goud'); }
        if (typeof startHitExplosie === "function") { startHitExplosie(); }
    } else {
        knop.style.borderColor = "#ff2d55"; txt.innerHTML = "<span style='color:#ff2d55;'>❌ FOUT!</span>";
        document.querySelectorAll('.btn-jaar').forEach(b => { if(parseInt(b.innerText)===correct) b.style.borderColor="#00ffcc"; });
        
        if (typeof startFoutStroboscoop === "function") { startFoutStroboscoop(); }
    }
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    document.getElementById('feedbackSectie').style.display = "block";
}

function volgendeBeurt() {
    document.getElementById('partyAudioEngine').pause();
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
    
    if (typeof startPartyRegen === "function") { startPartyRegen('goud'); }
}

function opnieuwSpelen() { sessionStorage.clear(); window.location.href = 'speel.php'; }
function wisselScherm(id) { document.querySelectorAll('.game-screen').forEach(s => s.classList.remove('active')); if(document.getElementById(id)) document.getElementById(id).classList.add('active'); }
</script>
