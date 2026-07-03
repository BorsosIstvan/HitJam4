(function() {
    const s = document.createElement('style');
    s.innerHTML = `.strobe-flash { animation: rFlash 0.4s ease-out 2, sShake 0.3s linear; } @keyframes rFlash { 0%, 100% { background: transparent; } 50% { background: rgba(255, 0, 85, 0.3); } } @keyframes sShake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-6px); } 75% { transform: translateX(6px); } }`;
    document.head.appendChild(s);
})();
function startFoutStroboscoop() {
    const c = document.getElementById('hitjamApp'); if(!c) return;
    c.classList.add('strobe-flash'); setTimeout(() => c.classList.remove('strobe-flash'), 700);
}
