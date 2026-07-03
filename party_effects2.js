/**
 * HitJam Live Party Effects Engine v5.0
 * Modulaire effecten voor Disco, Fouten en Hits
 */

let partyEffectInterval = null;

// Injecteer automatisch alle benodigde CSS-animaties bij het laden
(function injecteerDiscoCSS() {
    const style = document.createElement('style');
    style.innerHTML = `
        /* 1. REGENEFFECT */
        .party-drop {
            position: absolute; width: 8px; height: 8px; border-radius: 50%; top: -20px;
            pointer-events: none; z-index: 9999; animation: partyFallDown 1.2s linear forwards;
        }
        @keyframes partyFallDown {
            0% { transform: translateY(0) scale(0.6); opacity: 0; }
            15% { opacity: 1; }
            100% { transform: translateY(100vh) scale(1.4); opacity: 0; }
        }

        /* 2. STROBOSCOOP / SHAKE EFFECT (FOUT) */
        .strobe-flash {
            animation: redFlashAlarm 0.4s ease-out 2, screenShakeHJ 0.3s linear;
        }
        @keyframes redFlashAlarm {
            0%, 100% { background-color: transparent; }
            50% { background-color: rgba(255, 0, 85, 0.4); }
        }
        @keyframes screenShakeHJ {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }

        /* 3. EXPLOSIE EFFECT (GOED) */
        .blast-particle {
            position: absolute; width: 10px; height: 10px; border-radius: 50%;
            top: 50%; left: 50%; pointer-events: none; z-index: 9999;
            animation: radialBlastHJ 0.8s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
        }
        @keyframes radialBlastHJ {
            0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
            100% { transform: translate(var(--x), var(--y)) scale(0.2); opacity: 0; }
        }

        /* 4. DISCO BACKGROUND PULSE */
        @keyframes discoBgPulse {
            0%, 100% { background-color: #111216; box-shadow: inset 0 0 20px rgba(0,0,0,0.8); }
            33% { background-color: #1a052e; box-shadow: inset 0 0 40px rgba(157,0,255,0.2); }
            66% { background-color: #031b24; box-shadow: inset 0 0 40px rgba(0,240,255,0.15); }
        }
    `;
    document.head.appendChild(style);
})();

/**
 * EFFECT 1: GOUDEN OF NEON REGEN
 */
function startPartyRegen(type = 'goud', selector = '.app-container') {
    if (partyEffectInterval) clearInterval(partyEffectInterval);
    const container = document.querySelector(selector);
    if (!container) return;

    const goudKleuren = ["#ffe57f", "#ffaa00", "#ffd700", "#ffcc00"];
    const neonKleuren = ["#ff007f", "#00f0ff", "#9d00ff", "#fffb00"];
    const gekozenSet = (type === 'goud') ? goudKleuren : neonKleuren;

    partyEffectInterval = setInterval(() => {
        const drop = document.createElement('div');
        drop.classList.add('party-drop');
        drop.style.left = Math.random() * 96 + '%';
        const randomKleur = gekozenSet[Math.floor(Math.random() * gekozenSet.length)];
        drop.style.background = randomKleur;
        drop.style.boxShadow = `0 0 10px ${randomKleur}`;
        drop.style.animationDuration = (Math.random() * 0.5 + 0.7) + 's';
        container.appendChild(drop);
        setTimeout(() => drop.remove(), 1300);
    }, 35);
}

function stopPartyRegen() {
    if (partyEffectInterval) { clearInterval(partyEffectInterval); partyEffectInterval = null; }
    document.querySelectorAll('.party-drop').forEach(drop => drop.remove());
}

/**
 * EFFECT 2: FOUT ANTWOORD STROBOSCOOP (ALARM FLITS & SHAKE)
 */
function startFoutStroboscoop(selector = '.app-container') {
    const container = document.querySelector(selector);
    if (!container) return;

    // Voeg de flits- en schudklasse toe
    container.classList.add('strobe-flash');

    // Verwijder de klasse na de animatie zodat hij de volgende keer weer werkt
    setTimeout(() => {
        container.classList.remove('strobe-flash');
    }, 800);
}

/**
 * EFFECT 3: RADIALE HIT-EXPLOSIE (BOOM!)
 */
function startHitExplosie(selector = '.app-container') {
    const container = document.querySelector(selector);
    if (!container) return;

    const kleuren = ["#00ffcc", "#ff007f", "#fffb00", "#9d00ff", "#ff9500"];

    for (let i = 0; i < 40; i++) {
        const particle = document.createElement('div');
        particle.classList.add('blast-particle');
        
        const randomKleur = kleuren[Math.floor(Math.random() * kleuren.length)];
        particle.style.background = randomKleur;
        particle.style.boxShadow = `0 0 12px ${randomKleur}, 0 0 20px ${randomKleur}`;

        // Bereken een willekeurige 360-graden hoek en afstand voor de explosie
        const hoek = Math.random() * Math.PI * 2;
        const afstand = Math.random() * 160 + 60; // Straal van de knal
        
        particle.style.setProperty('--x', Math.cos(hoek) * afstand + 'px');
        particle.style.setProperty('--y', Math.sin(hoek) * afstand + 'px');
        particle.style.animationDuration = (Math.random() * 0.3 + 0.6) + 's';

        container.appendChild(particle);
        setTimeout(() => particle.remove(), 1000);
    }
}

/**
 * EFFECT 4: DISCO PULSE ACHTERGROND (AAN / UIT)
 */
function toggleDiscoAchtergrond(status = true, selector = '.app-container') {
    const container = document.querySelector(selector);
    if (!container) return;

    if (status) {
        container.style.animation = "discoBgPulse 2.5s infinite alternate ease-in-out";
    } else {
        container.style.animation = "none";
    }
}
