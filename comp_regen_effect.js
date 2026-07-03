let partyEffectInterval = null;
(function() {
    const s = document.createElement('style');
    s.innerHTML = `.party-drop { position: absolute; width: 8px; height: 8px; border-radius: 50%; top: -20px; pointer-events: none; z-index: 999; animation: pFall 1.2s linear forwards; } @keyframes pFall { 0% { transform: translateY(0); opacity: 0; } 15% { opacity: 1; } 100% { transform: translateY(100vh); opacity: 0; } }`;
    document.head.appendChild(s);
})();
function startPartyRegen(t = 'goud') {
    if (partyEffectInterval) clearInterval(partyEffectInterval);
    const set = (t === 'goud') ? ["#ffe57f", "#ffaa00", "#ffd700"] : ["#ff007f", "#00f0ff", "#9d00ff"];
    partyEffectInterval = setInterval(() => {
        const d = document.createElement('div'); d.classList.add('party-drop'); d.style.left = Math.random() * 95 + '%';
        const k = set[Math.floor(Math.random() * set.length)]; d.style.background = k; d.style.boxShadow = `0 0 8px ${k}`;
        d.style.animationDuration = (Math.random() * 0.5 + 0.7) + 's';
        const c = document.getElementById('hitjamApp'); if(c) c.appendChild(d); setTimeout(() => d.remove(), 1400);
    }, 40);
}
function stopPartyRegen() { if(partyEffectInterval) { clearInterval(partyEffectInterval); partyEffectInterval = null; } document.querySelectorAll('.party-drop').forEach(d => d.remove()); }
