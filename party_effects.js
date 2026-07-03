/**
 * HitJam Live Party Effects Engine
 * Modulaire code voor vallende neon- en gouden deeltjes
 */

let partyEffectInterval = null;

// Voeg direct bij het laden de benodigde CSS-animaties toe aan de pagina
(function injecteerPartyCSS() {
    const style = document.createElement('style');
    style.innerHTML = `
        .party-drop {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            top: -20px;
            pointer-events: none;
            z-index: 9999;
            animation: partyFallDown 1.2s linear forwards;
        }
        @keyframes partyFallDown {
            0% {
                transform: translateY(0) rotate(0deg) scale(0.6);
                opacity: 0;
            }
            15% {
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(360deg) scale(1.4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
})();

/**
 * START HET EFFECT
 * @param {string} type - Kies 'goud' voor gouden regen, of 'neon' voor discokleuren.
 * @param {string} selector - (Optioneel) De HTML-id of class waar het in moet vallen. Standaard is body.
 */
function startPartyRegen(type = 'goud', selector = 'body') {
    // Voorkom dubbele loops als het al aan staat
    if (partyEffectInterval) clearInterval(partyEffectInterval);

    const container = document.querySelector(selector);
    if (!container) return;

    // Kleurenpaletten bepalen
    const goudKleuren = ["#ffe57f", "#ffaa00", "#ffd700", "#ffcc00"];
    const neonKleuren = ["#ff007f", "#00f0ff", "#9d00ff", "#fffb00"];
    const gekozenSet = (type === 'goud') ? goudKleuren : neonKleuren;

    // Start de loop die continu deeltjes aanmaakt
    partyEffectInterval = setInterval(() => {
        const drop = document.createElement('div');
        drop.classList.add('party-drop');
        
        // Willekeurige positie en snelheid voor een natuurlijk effect
        drop.style.left = Math.random() * 96 + '%';
        
        const randomKleur = gekozenSet[Math.floor(Math.random() * gekozenSet.length)];
        drop.style.background = randomKleur;
        drop.style.boxShadow = `0 0 10px ${randomKleur}, 0 0 20px ${randomKleur}`;
        
        // Variatie in valtijd (tussen 0.8s en 1.5s)
        drop.style.animationDuration = (Math.random() * 0.7 + 0.8) + 's';

        container.appendChild(drop);

        // Ruim het element netjes op uit de computer zodra de animatie klaar is
        setTimeout(() => drop.remove(), 1600);
    }, 40); // Hoe lager dit getal, hoe intenser de storm
}

/**
 * STOP HET EFFECT DIRECT
 */
function stopPartyRegen() {
    if (partyEffectInterval) {
        clearInterval(partyEffectInterval);
        partyEffectInterval = null;
    }
}
