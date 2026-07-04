<style>
    /* De JBL PartyBox LED Ring achtergrond */
    .jbl-partybox-bg {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 380px;
        height: 380px;
        border-radius: 50%;
        background: conic-gradient(from 0deg, #ff007f, #9d00ff, #00f0ff, #fffb00, #ff007f);
        filter: blur(60px);
        opacity: 0.08; /* Standaard zacht aanwezig in de rustmodus */
        z-index: 0;
        pointer-events: none;
        transition: opacity 0.5s ease, filter 0.5s ease;
    }

    /* 🔊 DE RAVE-MODUS: Als de muziek start, gaat de JBL ring draaien en flitsen! */
    .jbl-rave-active .jbl-partybox-bg {
        opacity: 0.35; /* Felle neon party glow */
        filter: blur(40px);
        animation: jblSpin 3s linear infinite, jblPulse 0.6s infinite alternate ease-in-out;
    }

    /* Animaties voor het draaien van de LED-ring */
    @keyframes jblSpin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Animatie voor de pulserende bas-glowing */
    @keyframes jblPulse {
        0% { transform: translate(-50%, -50%) rotate(360deg) scale(0.95); opacity: 0.25; }
        100% { transform: translate(-50%, -50%) rotate(0deg) scale(1.1); opacity: 0.45; }
    }
</style>
