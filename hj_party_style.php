<style>
    :root {
        --bg-color: #050508;
        --card-bg: rgba(255, 255, 255, 0.03);
        --neon-pink: #ff0055;
        --neon-cyan: #00ffcc;
        --neon-gold: #ffaa00;
        --border-color: #22232b;
    }
    
    body { font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; background-color: var(--bg-color); color: #ffffff; display: flex; justify-content: center; min-height: 100vh; overflow-x: hidden; }
    .app-container { width: 100%; max-width: 450px; background: radial-gradient(circle at top, #1c0624 0%, #050508 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 50px rgba(0,0,0,0.9); text-align: center; position: relative; }
    
    /* Casino Logo Glinstering */
    .logo { font-size: 42px; font-weight: 900; background: linear-gradient(90deg, var(--neon-pink), var(--neon-gold), var(--neon-cyan)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; letter-spacing: 2px; animation: textShine 4s linear infinite; background-size: 200% auto; text-shadow: 0 0 20px rgba(255,0,85,0.2); }
    @keyframes textShine { to { background-position: 200% center; } }
    .subtitle { color: var(--neon-gold); font-size: 11px; text-transform: uppercase; letter-spacing: 4px; margin-top: 5px; font-weight: bold; text-shadow: 0 0 10px rgba(255,170,0,0.4); }
    
    /* Scherm Overgangen */
    .game-screen { display: none; }
    .game-screen.active { display: block; animation: casinoPop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    @keyframes casinoPop { from { opacity: 0; transform: scale(0.9) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }

    /* Inputs & Badges met neon-randen */
    .input-field { width: 100%; padding: 16px; border-radius: 14px; border: 2px solid var(--border-color); background: #0f1015; color: white; font-size: 16px; box-shadow: inset 0 0 10px rgba(0,0,0,0.5); }
    .input-field:focus { border-color: var(--neon-pink); box-shadow: 0 0 15px rgba(255,0,85,0.3); outline: none; }
    
    .player-badge { background: #0e0f14; border: 2px solid var(--border-color); padding: 14px 20px; border-radius: 16px; margin-bottom: 10px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 5px 15px rgba(0,0,0,0.3); transition: all 0.3s; }
    .player-badge.active-turn { border-color: var(--neon-cyan); box-shadow: 0 0 20px rgba(0,255,204,0.25); animation: pulseGlow 1.5s infinite alternate; }
    @keyframes pulseGlow { from { transform: scale(1); } to { transform: scale(1.02); } }

    /* Casino Knoppen */
    .btn { width: 100%; padding: 18px; border-radius: 16px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); text-transform: uppercase; letter-spacing: 1px; }
    .btn:active { transform: scale(0.93); }
    .btn-primary { background: linear-gradient(135deg, var(--neon-pink), #b3003b); color: white; box-shadow: 0 6px 20px rgba(255, 0, 85, 0.4); text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
    
    /* Gokkasteffect START-knop */
    .btn-start-track { background: linear-gradient(135deg, #00ffaa, #00b3ff); color: #050508; font-size: 22px; font-weight: 900; box-shadow: 0 0 30px rgba(0, 255, 170, 0.4); padding: 25px; border: 3px solid #ffffff; animation: goldGlimmer 2s infinite; }
    @keyframes goldGlimmer { 0%, 100% { box-shadow: 0 0 25px rgba(0, 255, 170, 0.4); } 50% { box-shadow: 0 0 45px rgba(0, 179, 255, 0.7); } }
    
    .btn-jaar { padding: 22px 10px; border-radius: 18px; font-size: 26px; font-weight: 900; border: 2px solid var(--border-color); background: linear-gradient(180deg, #1f2029 0%, #13141a 100%); color: white; box-shadow: 0 5px 10px rgba(0,0,0,0.4); }

    /* De Rollende Slot Machine / Gokkast Cijfers */
    .slot-wrapper { display: inline-flex; background: #000; border: 4px solid var(--neon-gold); padding: 10px 25px; border-radius: 20px; font-size: 54px; font-weight: 900; color: var(--neon-gold); box-shadow: 0 0 30px rgba(255,170,0,0.3); overflow: hidden; height: 75px; align-items: center; justify-content: center; margin: 15px 0; }
    .slot-rolling { animation: slotSpin 0.6s linear infinite; }
    @keyframes slotSpin { 0% { transform: translateY(-50px); opacity: 0.3; } 50% { opacity: 1; } 100% { transform: translateY(50px); opacity: 0.3; } }

    /* Grote Geanimeerde Feedback Kaart */
    .song-info-card { background: linear-gradient(180deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%); padding: 30px 20px; border-radius: 28px; border: 2px solid var(--border-color); margin: 20px 0; position: relative; box-shadow: 0 15px 35px rgba(0,0,0,0.5); }
    .info-title { font-size: 24px; font-weight: 800; margin-bottom: 5px; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
    
    /* Vallende Muntjes Effect */
    .coin { position: absolute; width: 20px; height: 20px; background: radial-gradient(circle, #ffe57f, #ffaa00); border-radius: 50%; border: 1px solid #fff; animation: fall 1.5s linear infinite; top: -20px; pointer-events: none; box-shadow: 0 2px 5px rgba(0,0,0,0.3); }
    @keyframes fall { to { transform: translateY(450px) rotate(360deg); opacity: 0; } }
    
    .footer { font-size: 9px; color: #333; letter-spacing: 2px; font-weight: bold; }
</style>
