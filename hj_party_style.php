<style>
    :root {
        --bg-color: #06020f;
        --card-bg: rgba(255, 255, 255, 0.03);
        --neon-pink: #ff007f;
        --neon-cyan: #00f0ff;
        --neon-purple: #9d00ff;
        --neon-yellow: #fffb00;
        --border-color: #201335;
    }
    
    body { font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; background-color: var(--bg-color); color: #ffffff; display: flex; justify-content: center; min-height: 100vh; overflow-x: hidden; }
    .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #140526 0%, #06020f 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 50px rgba(157,0,255,0.25); text-align: center; position: relative; }
    
    /* Disco Neon Glitter Logo */
    .logo { font-size: 42px; font-weight: 950; background: linear-gradient(90deg, var(--neon-pink), var(--neon-cyan), var(--neon-purple), var(--neon-pink)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; letter-spacing: 3px; animation: discoShine 3s linear infinite; background-size: 200% auto; text-shadow: 0 0 15px rgba(255,0,127,0.3); }
    @keyframes discoShine { to { background-position: 200% center; } }
    .subtitle { color: var(--neon-cyan); font-size: 11px; text-transform: uppercase; letter-spacing: 5px; margin-top: 5px; font-weight: 900; text-shadow: 0 0 8px rgba(0,240,255,0.5); }
    
    /* Scherm Overgangen */
    .game-screen { display: none; }
    .game-screen.active { display: block; animation: clubFade 0.4s cubic-bezier(0.19, 1, 0.22, 1) forwards; }
    @keyframes clubFade { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    /* Professionele Disco Inputs */
    .input-field { width: 100%; padding: 16px; border-radius: 14px; border: 2px solid var(--border-color); background: #110722; color: white; font-size: 16px; box-shadow: inset 0 0 15px rgba(0,0,0,0.6); }
    .input-field:focus { border-color: var(--neon-cyan); box-shadow: 0 0 15px rgba(0,240,255,0.3); outline: none; }
    
    /* Speler Badges met Discogloed */
    .player-badge { background: #130826; border: 2px solid var(--border-color); padding: 14px 20px; border-radius: 16px; margin-bottom: 10px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 5px 15px rgba(0,0,0,0.4); }
    
    /* Knoppen met Premium Verlopen */
    .btn { width: 100%; padding: 18px; border-radius: 16px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; transition: all 0.2s ease; text-transform: uppercase; letter-spacing: 1px; }
    .btn:active { transform: scale(0.95); }
    .btn-primary { background: linear-gradient(135deg, var(--neon-pink), var(--neon-purple)); color: white; box-shadow: 0 6px 20px rgba(255, 0, 127, 0.35); text-shadow: 0 2px 4px rgba(0,0,0,0.4); }
    
    /* Grote 'DANS EN LUISTER' Knop */
    .btn-start-track { background: linear-gradient(135deg, var(--neon-cyan), #0044ff); color: #ffffff; font-size: 20px; font-weight: 900; box-shadow: 0 0 30px rgba(0, 240, 255, 0.4); padding: 25px; text-shadow: 0 2px 5px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); }
    .btn-jaar { padding: 22px 10px; border-radius: 18px; font-size: 26px; font-weight: 900; border: 2px solid var(--border-color); background: linear-gradient(180deg, #1b0f2e 0%, #10061e 100%); color: white; box-shadow: 0 5px 12px rgba(0,0,0,0.5); }

    /* 📊 DANSENDE VU-METER / EQUALIZER */
    .vu-container { display: flex; justify-content: center; align-items: flex-end; gap: 6px; height: 75px; width: 180px; margin: 20px auto; padding: 10px; background: rgba(0,0,0,0.3); border-radius: 16px; border: 2px solid var(--border-color); }
    .vu-bar { width: 12px; height: 100%; background: linear-gradient(to top, var(--neon-cyan), var(--neon-purple), var(--neon-pink)); border-radius: 6px; transform-origin: bottom; height: 10%; }
    
    /* Dynamische dans-klasse via JS geactiveerd */
    .vu-dancing .vu-bar:nth-child(1) { animation: dance 0.6s infinite alternate ease-in-out; }
    .vu-dancing .vu-bar:nth-child(2) { animation: dance 0.4s infinite alternate ease-in-out 0.1s; }
    .vu-dancing .vu-bar:nth-child(3) { animation: dance 0.75s infinite alternate ease-in-out 0.2s; }
    .vu-dancing .vu-bar:nth-child(4) { animation: dance 0.5s infinite alternate ease-in-out 0.05s; }
    .vu-dancing .vu-bar:nth-child(5) { animation: dance 0.7s infinite alternate ease-in-out 0.15s; }
    .vu-dancing .vu-bar:nth-child(6) { animation: dance 0.55s infinite alternate ease-in-out 0.25s; }
    
    @keyframes dance { 
        0% { transform: scaleY(0.1); } 
        100% { transform: scaleY(1); filter: drop-shadow(0 0 5px var(--neon-pink)); } 
    }

    /* Infokaart Design */
    .song-info-card { background: linear-gradient(180deg, rgba(255,255,255,0.04) 0%, rgba(0,0,0,0) 100%); padding: 30px 20px; border-radius: 28px; border: 2px solid var(--border-color); margin: 20px 0; box-shadow: 0 15px 35px rgba(0,0,0,0.6); }
    .info-year { font-size: 64px; font-weight: 950; color: var(--neon-yellow); text-shadow: 0 0 20px rgba(255,251,0,0.4); margin-bottom: 5px; }
    
    /* Confetti/Gouden Regen Effect */
    .coin { position: absolute; width: 8px; height: 8px; background: var(--neon-cyan); border-radius: 50%; animation: discoFall 1.2s linear infinite; top: -10px; pointer-events: none; }
    @keyframes discoFall { to { transform: translateY(500px) translateX(20px); opacity: 0; } }
</style>
