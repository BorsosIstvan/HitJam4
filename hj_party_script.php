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
    
    const icons = ["🎰", "🎲", "🃏", "💎", "👑", "🔥", "🚀"];
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
                <span style="color:var(--neon-pink); cursor:pointer; font-size:24px; font-weight:900;" onclick="verwijderSpeler(${idx})">×</span>
            </div>`;
    });
    btn.style.display = spelers.length >= 1 ? "block" : "none";
}

function startHet Spel() {
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
        txtSpeler.innerHTML = `
            <div style="font-size:20px; color:var(--neon-gold); margin-bottom:10px;">RONDE ${huidigeRonde} / ${maxRondes}</div>
            <div style="font-size:42px; font-weight:900; color:#fff; text-shadow:0 0 20px var(--neon-cyan);">${spelers[huidigeSpelerIndex].naam}</div>
        `;
    }
}

function activeerQuizSectie() {
    wisselScherm('schermQuiz');
    const txtQuizSpeler = document.getElementById('quizSpelerNaam');
    if(txtQuizSpeler && spelers[huidigeSpelerIndex]) {
        let scoreOverzicht = '<div style="margin-bottom:20px; font-size:13px; background:#0b0c10; padding:12px; border-radius:14px; display:flex; justify-content:center; gap:12px; flex-wrap:wrap; border:2px solid var(--border-color);">';
        spelers.forEach(s => {
            let isActief = s.naam === spelers[huidigeSpelerIndex].naam ? 'border:1px solid var(--neon-cyan); background:rgba(0,255,204,0.05); padding:4px 10px; border-radius:8px; font-weight:900; color:#fff;' : 'color:#555; padding:4px;';
            scoreOverzicht += `<span style="${isActief}">${s.naam}: ${s.score}</span>`;
        });
        scoreOverzicht += '</div>';

        txtQuizSpeler.innerHTML = `${scoreOverzicht}<div style="color:var(--neon-cyan); font-size:20px; font-weight:900; letter-spacing:1px; text-transform:uppercase;">🎰 JOUW BEURT!</div>`;
    }
    
    const slotNum = document.getElementById('slotCijfer');
    if(slotNum) slotNum.classList.add('slot-rolling');

    const audio = document.getElementById('partyAudioEngine');
    if (audio) audio.play().catch(e => console.log("Klik vereist"));
}

// FIX: Spatie gecorrigeerd in plaats van schuine streep!
function controleerJaar(knopElement, gekozenJaar, correctJaar) {
    document.querySelectorAll('.btn-jaar').forEach(btn => btn.disabled = true);
    
    const slotNum = document.getElementById('slotCijfer');
    if(slotNum) {
        slotNum.classList.remove('slot-rolling');
        slotNum.innerText = correctJaar;
        slotNum.style.color = "var(--neon-cyan)";
    }

    const feedbackText = document.getElementById('quizFeedbackText');
    const card = document.getElementById('partyInfoCard');
    
    if (gekozenJaar === correctJaar) {
        knopElement.style.borderColor = "var(--neon-cyan)";
        knopElement.style.background = "linear-gradient(180deg, rgba(0,255,204,0.2) 0%, rgba(0,0,0,0) 100%)";
        if(feedbackText) feedbackText.innerHTML = "<span style='color: var(--neon-cyan); text-shadow:0 0 15px var(--neon-cyan);'>🎰 BIG WIN! (+100p)</span>";
        spelers[huidigeSpelerIndex].score += 100;
        if(card) { card.style.borderColor = "var(--neon-cyan)"; card.style.boxShadow = "0 0 30px rgba(0,255,204,0.25)"; }
        
        besprenkelMunten();
    } else {
        knopElement.style.borderColor = "var(--neon-pink)";
        knopElement.style.background = "linear-gradient(180deg, rgba(255,0,85,0.2) 0%, rgba(0,0,0,0) 100%)";
        if(feedbackText) feedbackText.innerHTML = "<span style='color: var(--neon-pink); text-shadow:0 0 15px var(--neon-pink);'>❌ GEEN PRIJS!</span>";
        if(card) { card.style.borderColor = "var(--neon-pink)"; card.style.boxShadow = "0 0 30px rgba(255,0,85,0.15)"; }
        
        document.querySelectorAll('.btn-jaar').forEach(btn => {
            if (parseInt(btn.innerText) === correctJaar) {
                btn.style.borderColor = "var(--neon-cyan)";
                btn.style.boxShadow = "0 0 15px rgba(0,255,204,0.4)";
            }
        });
    }
    
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    const fbSectie = document.getElementById('feedbackSectie');
    if(fbSectie) fbSectie.style.display = "block";
}

function besprenkelMunten() {
    const container = document.querySelector('.app-container');
    if(!container) return;
    for (let i = 0; i < 30; i++) {
        setTimeout(() => {
            const coin = document.createElement('div');
            coin.classList.add('coin');
            coin.style.left = Math.random() * 90 + '%';
            coin.style.animationDuration = (Math.random() * 0.7 + 0.8) + 's';
            container.appendChild(coin);
            setTimeout(() => coin.remove(), 1500);
        }, i * 40);
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
    let hoogsteScore = gerangschikt[0] ? gerangschikt[0].score : 0;
    
    let winnaars = gerangschikt.filter(s => s.score === hoogsteScore).map(s => s.naam);
    let winnaarTekst = winnaars.length > 1 ? `🤝 CO-WINNAARS: ${winnaars.join(' & ')}` : `👑 CASINO KING: ${winnaars}`;
    
    box.innerHTML += `
        <div style="background:linear-gradient(135deg, #2c1a04 0%, #110b02 100%); border:3px solid var(--neon-gold); padding:25px 20px; border-radius:22px; margin-bottom:25px; font-size:22px; font-weight:900; color:var(--neon-gold); text-shadow:0 0 15px var(--neon-gold); box-shadow:0 0 30px rgba(255,170,0,0.25);">
            ${winnaarTekst}<br>
            <span style="font-size:15px; color:#fff; font-weight:normal; text-shadow:none; display:inline-block; margin-top:5px;">Met een score van <b>${hoogsteScore}</b> punten! 🔥</span>
        </div>`;
    
    gerangschikt.forEach((speler, idx) => {
        let randStijl = idx === 0 ? 'border-color: var(--neon-gold); background:rgba(255,170,0,0.04);' : 'border-color: var(--border-color);';
        box.innerHTML += `
            <div class="player-badge" style="${randStijl} padding:18px;">
                <span style="font-size:18px; ${idx === 0 ? 'color:var(--neon-gold);' : ''}">#${idx+1} ${speler.naam}</span>
                <span style="color: var(--neon-cyan); font-size:20px; font-weight:900;">${speler.score} Pts</span>
            </div>`;
    });
    
    setInterval(besprenkelMunten, 2000);
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
