(function() {
    const s = document.createElement('style');
    s.innerHTML = `@keyframes dBg { 0%, 100% { background: #111216; } 50% { background: #18032b; } }`;
    document.head.appendChild(s);
})();
function toggleDiscoAchtergrond(status) {
    const c = document.getElementById('hitjamApp'); if(!c) return;
    c.style.animation = status ? "dBg 2s infinite alternate ease-in-out" : "none";
}
