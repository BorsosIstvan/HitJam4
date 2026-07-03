<script>
let spelers = JSON.parse(sessionStorage.getItem('hjPartySpelers')) || [];
let huidigeSpelerIndex = parseInt(sessionStorage.getItem('hjPartyIndex')) || 0;
let huidigeRonde = parseInt(sessionStorage.getItem('hjPartyRonde')) || 1;
const maxRondes = 5;

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
    
    const icons = ["🎸", "⚡", "👑", "🔥", "🎤", "🎵", "💥"];
    const randomIcon = icons[spelers.length % icons.length];
    
    spelers.push({ naam: randomIcon + " " + naam, score: 0 });
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
                <span style="font-size:18px;">${speler.naam}</span>
                <span style="color:var(--gh-red); cursor:pointer; font-size:24px; font-weight:900;" onclick="verwijderSpeler(${idx})">×</span>
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
    document.body.classList.remove('gh-track-active');
    wisselScherm('schermBeurt');
    const txtSpeler = document.getElementById('txtHuidigeSpeler');
    if(txtSpeler && spelers[huidigeSpelerIndex]) {
        txtSpeler.innerHTML = `
            <div style="font-size:18px; color:var(--gh-yellow); font-weight:900; letter-spacing:2px; margin-bottom:10px;">TRACK ${huidigeRonde} / ${maxRondes}</div>
            <div style="font-size:42px; font-weight:950; color:#fff; text-shadow:0 0 20px var(--gh-orange);">${spelers[huidigeSpelerIndex].naam}</div>
        `;
    }
}

function activeerQuizSectie() {
    wisselScherm('schermQuiz');
    document.body.classList.add('gh-track-active');
    startGuitarNotes();

    const txtQuizSpeler = document.getElementById('quizSpelerNaam');
    if(txtQuizSpeler && spelers[huidigeSpelerIndex]) {
        let scoreOverzicht = '<div style="margin-bottom:25px; font-size:13px; background:#0c0418; padding:12px; border-radius:14px; display:flex; justify-content:center; gap:12px; flex-wrap:wrap; border:2px solid var(--border-color);">';
        spelers.forEach(s => {
            let isActief = s.naam === spelers[huidigeSpelerIndex].naam ? 'border:2px solid var(--gh-green); background:rgba(0,255,34,0.08); padding:4px 10px; border-radius:8px; font-weight:900; color:#fff;' : 'color:#5d4c75; padding:4px;';
            scoreOverzicht += `<span style="${isActief}">${s.naam}: ${s.score}</span>`;
        });
        scoreOverzicht += '</div>';
        txtQuizSpeler.innerHTML = `${scoreOverzicht}<div style="color:var(--gh-green); font-size:18px; font-weight:900; letter-spacing:2px; text-shadow:0 0 10px var(--gh-green)">🎸 READY TO ROCK! 🎸</div>`;
    }
    const audio = document.getElementById('partyAudioEngine');
    if (audio) audio.play().catch(e => console.log("Klik vereist"));
}

let noteInterval = null;
function startGuitarNotes() {
    if(noteInterval) clearInterval(noteInterval);
    const lanes = document.querySelectorAll('.gh-lane');
    const colors = ["var(--gh-green)", "var(--gh-red)", "var(--gh-yellow)", "var(--gh-blue)", "var(--gh-orange)"];
    
    noteInterval = setInterval(() => {
        if(!document.body.classList.contains('gh-track-active')) {
            clearInterval(noteInterval);
            return;
        }
        const randomLaneIdx = Math.floor(Math.random() * lanes.length);
        const note = document.createElement('div');
        note.classList.add('gh-note');
        note.style.color = colors[randomLaneIdx];
        note.style.background = colors[randomLaneIdx];
        note.style.animationDuration = (Math.random() * 0.4 + 1.1) + 's';
        if(lanes[randomLaneIdx]) lanes[randomLaneIdx].appendChild(note);
        setTimeout(() => note.remove(), 1500);
    }, 180);
}

function controleerJaar(knopElement, gekozenJaar, correctJaar) {
    document.querySelectorAll('.btn-jaar').forEach(btn => btn.disabled = true);
    document.body.classList.remove('gh-track-active');

    const feedbackText = document.getElementById('quizFeedbackText');
    const card = document.getElementById('partyInfoCard');
    
    if (gekozenJaar === correctJaar) {
        knopElement.style.borderColor = "var(--gh-green)";
        knopElement.style.background = "linear-gradient(180deg, rgba(0,255,34,0.25) 0%, rgba(0,0,0,0) 100%)";
        knopElement.style.boxShadow = "0 0 25px var(--gh-green)";
        if(feedbackText) feedbackText.innerHTML = "<span style='color: var(--gh-green); text-shadow:0 0 15px var(--gh-green);'>🎸 ROCKSTAR HIT! (+100p)</span>";
        spelers[huidigeSpelerIndex].score += 100;
        if(card) { card.style.borderColor = "var(--gh-green)"; card.style.boxShadow = "0 0 35px rgba(0,255,34,0.2)"; }
        ghExplosie();
    } else {
        knopElement.style.borderColor = "var(--gh-red)";
        knopElement.style.background = "linear-gradient(180deg, rgba(255,0,60,0.2) 0%, rgba(0,0,0,0) 100%)";
        if(feedbackText) feedbackText.innerHTML = "<span style='color: var(--gh-red); text-shadow:0 0 15px var(--gh-red);'>❌ STAGE DIVE FAIL!</span>";
        if(card) { card.style.borderColor = "var(--gh-red)"; card.style.boxShadow = "0 0 30px rgba(255,0,60,0.15)"; }
        document.querySelectorAll('.btn-jaar').forEach(btn => {
            if (parseInt(btn.innerText) === correctJaar) {
                btn.style.borderColor = "var(--gh-green)";
                btn.style.boxShadow = "0 0 20px rgba(0,255,34,0.6)";
            }
        });
    }
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    const fbSectie = document.getElementById('feedbackSectie');
    if(fbSectie) fbSectie.style.display = "block";
}

function ghExplosie() {
    const container = document.querySelector('.app-container');
    if(!container) return;
    const colors = ["var(--gh-green)", "var(--gh-yellow)", "var(--gh-orange)"];
    
    for (let i = 0; i < 45; i++) {
        const coin = document.createElement('div');
        coin.classList.add('coin');
        coin.style.background = colors[Math.floor(Math.random() * colors.length)];
        coin.style.boxShadow = `0 0 10px ${coin.style.background}`;
        
        const hoek = Math.random() * Math.PI * 2;
        const afstand = Math.random() * 180 + 70;
        coin.style.setProperty('--x', Math.cos(hoek) * afstand + 'px');
        coin.style.setProperty('--y', Math.sin(hoek) * afstand + 'px');
        coin.style.animationDuration = (Math.random() * 0.4 + 0.6) + 's';
        
        container.appendChild(coin);
        setTimeout(() => coin.remove(), 1000);
    }
}

function volgendeBeurt() {
    const audio = document.getElementById('partyAudioEngine');
    if (audio) audio.pause();
    huidigeSpelerIndex++;
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
    
    let gerangschikt = [...spelers].sort((a, b) => b.score - a.score);
    let hoogsteScore = gerangschikt.length > 0 ? gerangschikt[0].score : 0;
    
    let winnaars = gerangschikt.filter(s => s.score === hoogsteScore).map(s => s.naam);
    let winnaarTekst = winnaars.length > 1 ? `🤝 BAND LEGENDS: ${winnaars.join(' & ')}` : `👑 GUITAR GOD: ${winnaars}`;
    
    box.innerHTML += `
        <div style="background:linear-gradient(135deg, #2b0411 0%, #0d0206 100%); border:3px solid var(--gh-red); padding:25px 20px; border-radius:22px; margin-bottom:25px; font-size:22px; font-weight:950; color:#fff; text-shadow:0 0 15px var(--gh-red); box-shadow:0 0 35px rgba(255,0,60,0.35);">
            ${winnaarTekst}<br>
            <span style="font-size:15px; color(--gh-yellow); font-weight:bold; display:inline-block; margin-top:5px;">Rockt de tent plat met <b>${hoogsteScore}</b> punten! ⚡</span>
        </div>`;
    
    gerangschikt.forEach((speler, idx) => {
        let randStijl = idx === 0 ? 'border-color: var(--gh-yellow); background:rgba(255,234,0,0.05);' : 'border-color: var(--border-color);';
        box.innerHTML += `
            <div class="player-badge" style="${randStijl} padding:18px;">
                <span style="font-size:18px; ${idx === 0 ? 'color:var(--gh-yellow); text-shadow:0 0 5px var(--gh-yellow);' : ''}">#${idx+1} ${speler.naam}</span>
                <span style="color: var(--gh-green); font-size:20px; font-weight:900; text-shadow:0 0 5px var(--gh-green);">${speler.score} Pts</span>
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
  