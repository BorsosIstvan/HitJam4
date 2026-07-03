<style>
    :root {
        --bg-color: #05020c;
        --card-bg: rgba(255, 255, 255, 0.03);
        --gh-green: #00ff22;
        --gh-red: #ff003c;
        --gh-yellow: #ffea00;
        --gh-blue: #0044ff;
        --gh-orange: #ff7700;
        --border-color: #1e1330;
    }
    
    body { font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; background-color: var(--bg-color); color: #ffffff; display: flex; justify-content: center; min-height: 100vh; overflow-x: hidden; position: relative; }
    
    /* Het bewegende Guitar Hero Fretboard op de achtergrond */
    .gh-background { position: absolute; top: 0; left: 50%; transform: translateX(-50%) perspective(300px) rotateX(45deg); width: 100%; max-width: 450px; height: 100%; z-index: 1; pointer-events: none; opacity: 0.15; transition: opacity 0.5s; display: flex; justify-content: space-around; border-left: 3px solid #331b52; border-right: 3px solid #331b52; background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(32,13,59,0.3) 100%); }
    .gh-track-active .gh-background { opacity: 0.45; } /* Licht intenser op tijdens het spelen */
    
    .gh-lane { width: 2px; height: 100%; background: linear-gradient(to bottom, rgba(255,255,255,0.05), rgba(157,0,255,0.4)); position: relative; }

    /* De sjezende Guitar Hero Neon Notes */
    .gh-note { position: absolute; width: 14px; height: 14px; border-radius: 50%; top: -20px; transform: translateX(-50%); filter: drop-shadow(0 0 10px currentColor); animation: ghStream 1.4s linear infinite; }
    @keyframes ghStream { 
        0% { top: -20px; opacity: 0; transform: translateX(-50%) scale(0.4); }
        15% { opacity: 1; }
        100% { top: 100%; opacity: 0; transform: translateX(-50%) scale(1.6); } 
    }

    .app-container { width: 100%; max-width: 450px; background: radial-gradient(circle at center, #130424 0%, #05020c 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 50px rgba(157,0,255,0.3); text-align: center; position: relative; z-index: 2; }
    
    /* Rock Logo Glinstering */
    .logo { font-size: 42px; font-weight: 950; background: linear-gradient(90deg, var(--gh-red), var(--gh-orange), var(--gh-yellow), var(--gh-red)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; letter-spacing: 2px; animation: rockShine 3s linear infinite; background-size: 200% auto; text-shadow: 0 0 15px rgba(255,0,60,0.4); }
    @keyframes rockShine { to { background-position: 200% center; } }
    .subtitle { color: var(--gh-yellow); font-size: 11px; text-transform: uppercase; letter-spacing: 4px; margin-top: 5px; font-weight: 900; text-shadow: 0 0 10px rgba(255,234,0,0.5); }
    
    .game-screen { display: none; }
    .game-screen.active { display: block; animation: ghPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    @keyframes ghPop { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

    .input-field { width: 100%; padding: 16px; border-radius: 14px; border: 2px solid var(--border-color); background: #0c0418; color: white; font-size: 16px; box-shadow: inset 0 0 15px rgba(0,0,0,0.7); }
    .input-field:focus { border-color: var(--gh-orange); box-shadow: 0 0 15px rgba(255,119,0,0.3); outline: none; }
    
    .player-badge { background: #120624; border: 2px solid var(--border-color); padding: 14px 20px; border-radius: 16px; margin-bottom: 10px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 5px 15px rgba(0,0,0,0.5); }
    
    .btn { width: 100%; padding: 18px; border-radius: 16px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; transition: all 0.2s ease; text-transform: uppercase; letter-spacing: 1px; }
    .btn:active { transform: scale(0.95); }
    .btn-primary { background: linear-gradient(135deg, var(--gh-red), #990024); color: white; box-shadow: 0 6px 20px rgba(255,0,60,0.4); text-shadow: 0 2px 4px rgba(0,0,0,0.4); }
    
    .btn-start-track { background: linear-gradient(135deg, var(--gh-green), #009914); color: #ffffff; font-size: 20px; font-weight: 950; box-shadow: 0 0 35px rgba(0, 255, 34, 0.4); padding: 25px; text-shadow: 0 2px 5px rgba(0,0,0,0.5); border: 2px solid rgba(255,255,255,0.2); }
    
    /* Gekleurde Jaarknoppen in stijl van de Guitar Hero Fret Buttons */
    .btn-jaar { padding: 22px 10px; border-radius: 18px; font-size: 26px; font-weight: 900; border: 3px solid var(--border-color); background: linear-gradient(180deg, #1b0c30 0%, #0d041a 100%); color: white; box-shadow: 0 6px 12px rgba(0,0,0,0.6); }

    .song-info-card { background: linear-gradient(180deg, rgba(255,255,255,0.04) 0%, rgba(0,0,0,0) 100%); padding: 30px 20px; border-radius: 28px; border: 2px solid var(--border-color); margin: 20px 0; box-shadow: 0 15px 35px rgba(0,0,0,0.6); }
    .info-year { font-size: 64px; font-weight: 950; color: var(--gh-yellow); text-shadow: 0 0 25px rgba(255,234,0,0.4); margin-bottom: 5px; }
    
    /* Hit Explosie Confetti */
    .coin { position: absolute; width: 10px; height: 10px; background: var(--gh-green); border-radius: 50%; animation: ghBlast 1.0s ease-out infinite; top: 50%; left: 50%; pointer-events: none; }
    @keyframes ghBlast { 
        0% { transform: translate(-50%, -50__) scale(1); opacity: 1; }
        100% { transform: translate(var(--x), var(--y)) scale(0.1); opacity: 0; }
    }
</style>
