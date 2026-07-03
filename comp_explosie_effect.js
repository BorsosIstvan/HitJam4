(function() {
    const s = document.createElement('style');
    s.innerHTML = `.blast-p { position: absolute; width: 10px; height: 10px; border-radius: 50%; top: 50%; left: 50%; pointer-events: none; z-index: 999; animation: rBlast 0.7s cubic-bezier(0.1, 0.8, 0.3, 1) forwards; } @keyframes rBlast { 0% { transform: translate(-50%, -50%) scale(1); opacity: 1; } 100% { transform: translate(var(--x), var(--y)) scale(0.2); opacity: 0; } }`;
    document.head.appendChild(s);
})();
function startHitExplosie() {
    const c = document.getElementById('hitjamApp'); if(!c) return;
    const kl = ["#00ffcc", "#ff007f", "#fffb00"];
    for (let i = 0; i < 35; i++) {
        const p = document.createElement('div'); p.classList.add('blast-p');
        const k = kl[Math.floor(Math.random() * kl.length)]; p.style.background = k; p.style.boxShadow = `0 0 10px ${k}`;
        const h = Math.random() * Math.PI * 2, a = Math.random() * 150 + 50;
        p.style.setProperty('--x', Math.cos(h) * a + 'px'); p.style.setProperty('--y', Math.sin(h) * a + 'px');
        c.appendChild(p); setTimeout(() => p.remove(), 800);
    }
}
