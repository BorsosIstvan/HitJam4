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
        txtSpeler.innerText = spelers[huidigeSpelerIndex].naam;
    }
}

function activeerQuizSectie() {
    wisselScherm('schermQuiz');
    const txtQuizSpeler = document.getElementById('quizSpelerNaam');
    if(txtQuizSpeler && spelers[huidigeSpelerIndex]) {
        txtQuizSpeler.innerText = `Beurt van: ${spelers[huidigeSpelerIndex].naam}`;
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
        if(feedbackText) feedbackText.innerHTML = "<span style='color: var(--neon-cyan);'>🎉 GOED GERADEN!</span>";
        spelers[huidigeSpelerIndex].score += 100;
    } else {
        knopElement.style.borderColor = "var(--neon-pink)";
        knopElement.style.background = "rgba(255, 45, 85, 0.1)";
        if(feedbackText) feedbackText.innerHTML = "<span style='color: var(--neon-pink);'>❌ HELAAS FOUT!</span>";
        document.querySelectorAll('.btn-jaar').forEach(btn => {
            if (parseInt(btn.innerText) === correctJaar) {
                btn.style.borderColor = "var(--neon-cyan)";
            }
        });
    }
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    const fbSectie = document.getElementById('feedbackSectie');
    if(fbSectie) fbSectie.style.display = "block";
}

// 🔥 FIX: Typefout hersteld en herlaad-logica beveiligd tegen lussen
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
    gerangschikt.forEach((speler, idx) => {
        let kroon = idx === 0 ? "👑 " : `🥇 `;
        box.innerHTML += `
            <div class="player-badge" style="border-color: ${idx === 0 ? 'var(--neon-gold)' : 'var(--border-color)'};">
                <span>${kroon} ${speler.naam}</span>
                <span style="color: var(--neon-cyan);">${speler.score} Pnt</span>
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
