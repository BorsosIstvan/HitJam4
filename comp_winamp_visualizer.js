/**
 * HitJam Modulair Component: Winamp Pro Multi-Visualizer Engine
 */

let audioContext = null;
let analyser = null;
let dataArray = null;
let visualizerCanvas = null;
let canvasCtx = null;
let animationFrameId = null;

// Dit onthoudt welke modus er deze beurt actief is (willekeurig gekozen)
let actieveWinampModus = 1; 

(function injecteerWinampProCSS() {
    const style = document.createElement('style');
    style.innerHTML = `
        .winamp-canvas {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0; pointer-events: none;
            opacity: 0; transition: opacity 0.4s ease;
        }
        .winamp-active .winamp-canvas {
            opacity: 0.35; /* Iets duidelijker aanwezig voor de balken */
        }
    `;
    document.head.appendChild(style);
})();

function initWinampAudioEngine() {
    const audioElement = document.getElementById('partyAudioEngine');
    const app = document.getElementById('hitjamApp');
    if (!audioElement || !app || audioContext) return;

    try {
        visualizerCanvas = document.createElement('canvas');
        visualizerCanvas.id = 'winampCanvas';
        visualizerCanvas.classList.add('winamp-canvas');
        app.insertBefore(visualizerCanvas, app.firstChild);
        canvasCtx = visualizerCanvas.getContext('2d');

        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        analyser = audioContext.createAnalyser();
        
        // fftSize op 256 zorgt voor een perfecte balans tussen vloeiende balken en snelheid
        analyser.fftSize = 256; 

        const source = audioContext.createMediaElementSource(audioElement);
        source.connect(analyser);
        analyser.connect(audioContext.destination);

        const bufferLength = analyser.frequencyBinCount;
        dataArray = new Uint8Array(bufferLength);
        
        visualizerCanvas.width = app.clientWidth;
        visualizerCanvas.height = app.clientHeight;
    } catch (e) {
        console.log("Winamp Engine initialisatie mislukt:", e);
    }
}

/**
 * DE CENTRALE DRAW-LOOP (60 frames per seconde)
 */
function renderWinampVisuals() {
    if (!analyser || !canvasCtx || !visualizerCanvas) return;

    animationFrameId = requestAnimationFrame(renderWinampVisuals);
    const width = visualizerCanvas.width;
    const height = visualizerCanvas.height;

    // Maak het canvas leeg voor het nieuwe frame
    canvasCtx.clearRect(0, 0, width, height);

    // =============================================================
    // MODUS 1: RETRO NEON OSCILLOSCOPE (De vertrouwde golflijn)
    // =============================================================
    if (actieveWinampModus === 1) {
        analyser.getByteTimeDomainData(dataArray);
        canvasCtx.lineWidth = 3;
        canvasCtx.strokeStyle = '#00ffcc';
        canvasCtx.shadowBlur = 15;
        canvasCtx.shadowColor = '#00ffcc';
        canvasCtx.beginPath();

        const sliceWidth = width * 1.0 / analyser.frequencyBinCount;
        let x = 0;

        for (let i = 0; i < analyser.frequencyBinCount; i++) {
            const v = dataArray[i] / 128.0;
            const y = v * height / 2;
            if (i === 0) canvasCtx.moveTo(x, y); else canvasCtx.lineTo(x, y);
            x += sliceWidth;
        }
        canvasCtx.lineTo(width, height / 2);
        canvasCtx.stroke();
    }
    
    // =============================================================
    // MODUS 2: RETRO SPECTRUM BARS (De klassieke Winamp VU-balkjes)
    // =============================================================
    else if (actieveWinampModus === 2) {
        analyser.getByteFrequencyData(dataArray);
        canvasCtx.shadowBlur = 0; // Geen zware blur voor strakke retro-look

        const barWidth = (width / analyser.frequencyBinCount) * 1.5;
        let barHeight;
        let x = 0;

        for (let i = 0; i < analyser.frequencyBinCount; i++) {
            barHeight = dataArray[i] * 1.2; // Vermenigvuldig voor extra uitslag

            // Maak een klassiek kleurverloop per balk: onder cyaan, midden paars, boven roze
            const gradient = canvasCtx.createLinearGradient(0, height, 0, height - barHeight);
            gradient.addColorStop(0, '#00f0ff');
            gradient.addColorStop(0.5, '#9d00ff');
            gradient.addColorStop(1, '#ff007f');

            canvasCtx.fillStyle = gradient;
            // Teken de balk vanaf de bodem van de app-container omhoog
            canvasCtx.fillRect(x, height - barHeight, barWidth - 4, barHeight);

            x += barWidth;
        }
    }
    
    // =============================================================
    // MODUS 3: MUSICAL PLASMA GLOW (De pulserende bass-cirkel)
    // =============================================================
    else if (actieveWinampModus === 3) {
        analyser.getByteFrequencyData(dataArray);

        // Bereken het gemiddelde volume van de bas (de eerste paar frequenties)
        let basVolume = 0;
        const basFrequenties = 10;
        for (let i = 0; i < basFrequenties; i++) {
            basVolume += dataArray[i];
        }
        basVolume = basVolume / basFrequenties; // Gemiddelde bas-waarde tussen 0 en 255

        // Bepaal de grootte van de cirkel op basis van de bas-dreun
        const basisStraal = 70;
        const actueleStraal = basisStraal + (basVolume * 0.5);

        // Teken een gloeiende cirkel exact in het midden van je telefoonscherm
        canvasCtx.beginPath();
        canvasCtx.arc(width / 2, height / 2, actueleStraal, 0, 2 * Math.PI);
        
        canvasCtx.shadowBlur = 30;
        canvasCtx.shadowColor = '#9d00ff';
        
        const radialGradient = canvasCtx.createRadialGradient(width/2, height/2, 10, width/2, height/2, actueleStraal);
        radialGradient.addColorStop(0, 'rgba(255, 0, 127, 0.4)');
        radialGradient.addColorStop(0.6, 'rgba(157, 0, 255, 0.2)');
        radialGradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

        canvasCtx.fillStyle = radialGradient;
        canvasCtx.fill();
    }
}

/**
 * HOOFDFUNCTIE
 */
function toggleWinampVisualizer(status = true) {
    const app = document.getElementById('hitjamApp');
    if (!app) return;

    if (status) {
        initWinampAudioEngine();
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
        
        // 🔥 SLIMME CASINO/DISCO TRICK: Kies elke beurt een willekeurige Winamp modus (1, 2 of 3)
        actieveWinampModus = Math.floor(Math.random() * 3) + 1;
        
        app.classList.add('winamp-active');
        if(!animationFrameId) renderWinampVisuals();
    } else {
        app.classList.remove('winamp-active');
        if(animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        if(canvasCtx && visualizerCanvas) {
            canvasCtx.clearRect(0, 0, visualizerCanvas.width, visualizerCanvas.height);
        }
    }
}
