<script>
// We halen de spelersdata en voortgang veilig op uit het browsergeheugen
let spelers = JSON.parse(sessionStorage.getItem('hjPartySpelers')) || [];
let huidigeSpelerIndex = parseInt(sessionStorage.getItem('hjPartyIndex')) || 0;
let huidigeRonde = parseInt(sessionStorage.getItem('hjPartyRonde')) || 1;
const maxRondes = 5; // Het spel stopt automatisch na 5 rondes

document.addEventListener("DOMContentLoaded", function() {
    if (spelers.length > 0) {
        const setupScherm = document.getElementById('schermSetup');
        if(setupScherm) setupScherm.classList.remove('active');
        
        if (huidigeRonde > maxRondes) {
            toonEindstand();
        } else {
            laadBeurtScherm();
        }
    }
});

function voegSpelerToe() {
    const input = document.getElementById('spelerNaamInput');
    if(!input) return;
    const naam = input.value.trim();
    if (naam === "") return;
    
    // Elke nieuwe speler begint uiteraard op 0 punten
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
        box.innerHTML += `
            <div class="player-badge">
                <span>👤 ${speler.naam}</span>
                <span style="color:#ff2d55; cursor:pointer; font-size:20px;" onclick="verwijderSpeler(${idx})">×</span>
            </div>`;
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
    const txtSpeler = document.getElementById('txtHuidigeSpeler');
    if(txtSpeler && spelers[huidigeSpelerIndex]) {
        // Toon de naam van de actieve speler en de huidige spelronde
        txtSpeler.innerHTML = `${spelers[huidigeSpelerIndex].naam}<br><span style="font-size:16px; color:#888; font-weight:normal;">Ronde ${huidigeRonde} van ${maxRondes}</span>`;
    }
}

function activeerQuizSectie() {
    wisselScherm('schermQuiz');
    const txtQuizSpeler = document.getElementById('quizSpelerNaam');
    if(txtQuizSpeler && spelers[huidigeSpelerIndex]) {
        // Bouw een LIVE TUSSENSTAND-balkje boven de jaarknoppen
        let scoreOverzicht = '<div style="margin-bottom:15px; font-size:12px; background:rgba(255,255,255,0.02); padding:10px; border-radius:10px; display:flex; justify-content:center; gap:15px; flex-wrap:wrap; border:1px solid #222;">';
        spelers.forEach(s => {
            let isActief = s.naam === spelers[huidigeSpelerIndex].naam ? 'border-bottom:2px solid var(--neon-cyan); padding-bottom:2px; font-weight:900;' : 'color:#888;';
            scoreOverzicht += `<span style="${isActief}">👤 ${s.naam}: ${s.score}p</span>`;
        });
        scoreOverzicht += '</div>';

        txtQuizSpeler.innerHTML = `${scoreOverzicht}<div style="color:var(--neon-cyan); letter-spacing:1px; font-size:18px; font-weight:900;">🎯 ${spelers[huidigeSpelerIndex].naam}, KIES HET JAAR:</div>`;
    }
    const audio = document.getElementById('partyAudioEngine');
    if (audio) audio.play().catch(e => console.log("Klik vereist om af te spelen"));
}

function controleerJaar(knopElement, gekozenJaar, correctJaar) {
    document.querySelectorAll('.btn-jaar').forEach(btn => btn.disabled = true);
    const feedbackText = document.getElementById('quizFeedbackText');
    const card = document.getElementById('partyInfoCard');
    
    if (gekozenJaar === correctJaar) {
        knopElement.style.borderColor = "var(--neon-cyan)";
        knopElement.style.background = "rgba(0, 255, 204, 0.1)";
        if(feedbackText) feedbackText.innerHTML = "<span style='color: var(--neon-cyan);'>🎉 GOED GERADEN! (+100p)</span>";
        
        // Voeg 100 punten toe aan de score van de huidige speler
        spelers[huidigeSpelerIndex].score += 100;
    } else {
        knopElement.style.borderColor = "var(--neon-pink)";
        knopElement.style.background = "rgba(255, 45, 85, 0.1)";
        if(feedbackText) feedbackText.innerHTML = "<span style='color: var(--neon-pink);'>❌ HELAAS FOUT!</span>";
        document.querySelectorAll('.btn-jaar').forEach(btn => {
            if (parseInt(btn.innerText) === correctJaar) {
                btn.style.borderColor = "var(--neon-cyan)";
                btn.style.background = "rgba(0, 255, 204, 0.05)";
            }
        });
    }
    
    // Sla de bijgewerkte score direct op in de browser-sessie
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    
    const fbSectie = document.getElementById('feedbackSectie');
    if(fbSectie) fbSectie.style.display = "block";
}

function volgendeBeurt() {
    const audio = document.getElementById('partyAudioEngine');
    if (audio) audio.pause();
    
    huidigeSpelerIndex++;
    
    // Als iedereen in de groep aan de beurt is geweest, schuiven we op naar de volgende ronde
    if (huidigeSpelerIndex >= spelers.length) {
        huidigeSpelerIndex = 0;
        huidigeRonde++;
        sessionStorage.setItem('hjPartyRonde', huidigeRonde.toString());
    }
    
    sessionStorage.setItem('hjPartyIndex', huidigeSpelerIndex.toString());
    
    if (huidigeRonde > maxRondes) {
        toonEindstand();
    } else {
        window.location.href = 'speel.php';
    }
}

function toonEindstand() {
    wisselScherm('schermEind');
    const box = document.getElementById('eindklassementBox');
    if(!box) return;
    box.innerHTML = "";
    
    // Sorteer de spelerslijst zodat de hoogste score bovenaan staat
    let gerangschikt = [...spelers].sort((a, b) => b.score - a.score);
    let hoogsteScore = gerangschikt[0] ? gerangschikt[0].score : 0;
    
    // Toon de absolute winnaar(s) groot boven de scorelijst
    let winnaars = gerangschikt.filter(s => s.score === hoogsteScore).map(s => s.naam);
    let winnaarTekst = winnaars.length > 1 ? `🤝 GELIJKSPEL TUSSEN: ${winnaars.join(' & ')}!` : `👑 DE WINNAAR IS: ${winnaars[0]}!`;
    
    box.innerHTML += `
        <div style="background:linear-gradient(135deg, rgba(255,149,0,0.1), rgba(255,45,85,0.1)); border:2px dashed var(--neon-gold); padding:20px; border-radius:18px; margin-bottom:25px; font-size:20px; font-weight:900; color:var(--neon-gold); text-transform:uppercase; letter-spacing:1px; animation:gecombineerdFadeIn 0.6s ease;">
            ${winnaarTekst}<br><span style="font-size:14px; color:#fff; font-weight:normal; text-transform:none;">Met een monsterscore van <b>${hoogsteScore}</b> punten! ✨</span>
        </div>
        <div style="text-align:left; color:#888; font-size:12px; margin-bottom:10px; text-transform:uppercase; letter-spacing:1px; padding-left:5px;">Volledig Klassement:</div>`;
    
    // Bouw de ranking badges voor alle overige spelers op het scherm
    gerangschikt.forEach((speler, idx) => {
        let positieSymbool = idx === 0 ? "🏆" : `🏅`;
        let specifiekeRand = idx === 0 ? 'border-color: var(--neon-gold); background:rgba(255,149,0,0.03);' : 'border-color: var(--border-color);';
        
        box.innerHTML += `
            <div class="player-badge" style="${specifiekeRand} padding:15px 20px;">
                <span style="font-size:18px; ${idx === 0 ? 'color:var(--neon-gold); font-weight:900;' : ''}">${positieSymbool} ${speler.naam}</span>
                <span style="color: var(--neon-cyan); font-size:18px; font-weight:900;">${speler.score} Pnt</span>
            </div>`;
    });
}

function opnieuwSpelen() {
    sessionStorage.clear();
    window.location.href = 'speel.php';
}

function wisselScherm(schermId) {
    document.querySelectorAll('.game-screen').forEach(s => s.classList.remove('active'));
    const doelScherm = document.getElementById(schermId);
    if(doelScherm) doelScherm.classList.add('active');
}
</script>
