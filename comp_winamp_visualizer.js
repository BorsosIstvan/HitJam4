/**
 * HitJam Modulair Component: Winamp Retro Audio Visualizer
 */

let audioContext = null;
let analyser = null;
let dataArray = null;
let visualizerCanvas = null;
let canvasCtx = null;
let animationFrameId = null;

// CSS direct injecteren voor het visualizer-scherm op de achtergrond
(function injecteerWinampCSS() {
    const style = document.createElement('style');
    style.innerHTML = `
        .winamp-canvas {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0; pointer-events: none;
            opacity: 0; transition: opacity 0.5s ease;
        }
        .winamp-active .winamp-canvas {
            opacity: 0.25; /* Subtiel aanwezig achter de knoppen */
        }
    `;
    document.head.appendChild(style);
})();

/**
 * Initialiseert de Web Audio API (Gebeurt éénmalig bij de eerste klik)
 */
function initWinampAudioEngine() {
    const audioElement = document.getElementById('partyAudioEngine');
    const app = document.getElementById('hitjamApp');
    if (!audioElement || !app || audioContext) return;

    try {
        // Bouw het canvas element op de achtergrond van de telefoon
        visualizerCanvas = document.createElement('canvas');
        visualizerCanvas.id = 'winampCanvas';
        visualizerCanvas.classList.add('winamp-canvas');
        app.insertBefore(visualizerCanvas, app.firstChild);
        canvasCtx = visualizerCanvas.getContext('2d');

        // Start de browser Audio Context
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
        analyser = audioContext.createAnalyser();
        analyser.fftSize = 256; // Bepaalt de scherpte van de golf

        // Koppel de iTunes speler aan de analyser
        const source = audioContext.createMediaElementSource(audioElement);
        source.connect(analyser);
        analyser.connect(audioContext.destination);

        const bufferLength = analyser.frequencyBinCount;
        dataArray = new Uint8Array(bufferLength);
        
        // Zorg dat het canvas de juiste grootte aanneemt
        visualizerCanvas.width = app.clientWidth;
        visualizerCanvas.height = app.clientHeight;
    } catch (e) {
        console.log("Web Audio API kon niet starten (CORS of interactie-beperking):", e);
    }
}

/**
 * De RUN CYCLE (Game Loop) van de visualizer: Tekent 60fps de dansende Winamp-lijn
 */
function drawWinampWaves() {
    if (!analyser || !canvasCtx || !visualizerCanvas) return;

    animationFrameId = requestAnimationFrame(drawWinampWaves);
    analyser.getByteTimeDomainData(dataArray);

    const width = visualizerCanvas.width;
    const height = visualizerCanvas.height;

    // Maak het scherm leeg voor het volgende frame
    canvasCtx.clearRect(0, 0, width, height);

    // Teken de neon-groene/cyaan Winamp golflijn
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

        if (i === 0) {
            canvasCtx.moveTo(x, y);
        } else {
            canvasCtx.lineTo(x, y);
        }
        x += sliceWidth;
    }

    canvasCtx.lineTo(width, height / 2);
    canvasCtx.stroke();
}

/**
 * HOOFDFUNCTIE: Start of stop de Winamp visualisatie
 */
function toggleWinampVisualizer(status = true) {
    const app = document.getElementById('hitjamApp');
    if (!app) return;

    if (status) {
        // Activeer audio context indien nog niet gedaan
        initWinampAudioEngine();
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
        
        app.classList.add('winamp-active');
        // Start de teken-loop
        if(!animationFrameId) drawWinampWaves();
    } else {
        app.classList.remove('winamp-active');
        // Stop de teken-loop om stroom/batterij te besparen
        if(animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        // Maak het canvas leeg
        if(canvasCtx && visualizerCanvas) {
            canvasCtx.clearRect(0, 0, visualizerCanvas.width, visualizerCanvas.height);
        }
    }
}
