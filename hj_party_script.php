<script>
let spelers = JSON.parse(sessionStorage.getItem('hjPartySpelers')) || [];
let huidigeSpelerIndex = parseInt(sessionStorage.getItem('hjPartyIndex')) || 0;
let huidigeRonde = parseInt(sessionStorage.getItem('hjPartyRonde')) || 1;
const maxRondes = 5;

document.addEventListener("DOMContentLoaded", function() {
    if (spelers.length > 0) {
        document.getElementById('schermSetup').classList.remove('active');
        if (huidigeRonde > maxRondes) {
            toonEindstand();
        } else {
            laadBeurtScherm();
        }
    }
});

function voegSpelerToe() {
    const input = document.getElementById('spelerNaamInput');
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
    document.getElementById('txtHuidigeSpeler').innerText = spelers[huidigeSpelerIndex].naam;
}

function activeerQuizSectie() {
    wisselScherm('schermQuiz');
    document.getElementById('quizSpelerNaam').innerText = `Beurt van: ${spelers[huidigeSpelerIndex].naam}`;
    const audio = document.getElementById('partyAudioEngine');
    if (audio) audio.play().catch(e => console.log("Klik vereist"));
}

function controleerJaar(knopElement, gekozenJaar, correctJaar) {
    document.querySelectorAll('.btn-jaar').forEach(btn => btn.disabled = true);
    const feedbackText = document.getElementById('quizFeedbackText');
    const card = document.getElementById('partyInfoCard');
    
    if (gekozenJaar === correctJaar) {
        knopElement.style.borderColor = "var(--neon-cyan)";
        knopElement.style.background = "rgba(0, 255, 204, 0.1)";
        feedbackText.innerHTML = "<span style='color: var(--neon-cyan);'>🎉 GOED GERADEN!</span>";
        spelers[huidigeSpelerIndex].score += 100;
        card.style.borderColor = "var(--neon-cyan)";
    } else {
        knopElement.style.borderColor = "var(--neon-pink)";
        knopElement.style.background = "rgba(255, 45, 85, 0.1)";
        feedbackText.innerHTML = "<span style='color: var(--neon-pink);'>❌ HELAAS FOUT!</span>";
        card.style.borderColor = "var(--neon-pink)";
        document.querySelectorAll('.btn-jaar').forEach(btn => {
            if (parseInt(btn.innerText) === correctJaar) {
                btn.style.borderColor = "var(--neon-cyan)";
            }
        });
    }
    sessionStorage.setItem('hjPartySpelers', JSON.stringify(spelers));
    document.getElementById('feedbackSectie').style.display = "block";
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
    sessionStorage.setItem('hjPartyIndex', Hills = huidigeSpelerIndex.toString());
    if (huidigeRonde > maxRondes) {
        toonEindstand();
    } else {
        window.location.reload();
    }
}

function toonEindstand() {
    wisselScherm('schermEind');
    const box = document.getElementById('eindklassementBox');
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
    document.getElementById(schermId).classList.add('active');
}
</script>
