<style>
    :root {
        --bg-color: #0b0c10;
        --card-bg: rgba(255, 255, 255, 0.04);
        --neon-pink: #ff2d55;
        --neon-cyan: #00ffcc;
        --neon-gold: #ff9500;
        --border-color: #33343f;
    }
    body { font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; background-color: var(--bg-color); color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
    .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #120917 0%, #0b0c10 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 40px rgba(0,0,0,0.8); text-align: center; }
    
    .logo-area { margin-bottom: 10px; }
    .logo { font-size: 36px; font-weight: 900; background: linear-gradient(45deg, var(--neon-pink), var(--neon-gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
    .subtitle { color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin-top: 5px; }
    
    .game-screen { display: none; animation: screenFade 0.4s ease-in-out forwards; }
    .game-screen.active { display: block; }
    @keyframes screenFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .player-input-row { display: flex; gap: 10px; margin-bottom: 15px; }
    .input-field { flex: 1; padding: 15px; border-radius: 12px; border: 2px solid var(--border-color); background: #1f2026; color: white; font-size: 16px; font-weight: bold; }
    .input-field:focus { border-color: var(--neon-pink); outline: none; }
    .player-badge { background: var(--card-bg); border: 1px solid var(--border-color); padding: 12px; border-radius: 10px; margin-bottom: 8px; text-align: left; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }

    .btn { width: 100%; padding: 18px; border-radius: 16px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; transition: all 0.2s; text-transform: uppercase; letter-spacing: 1px; }
    .btn:active { transform: scale(0.96); }
    .btn-primary { background: linear-gradient(90deg, var(--neon-pink), #e01b43); color: white; box-shadow: 0 6px 20px rgba(255, 45, 85, 0.3); }
    .btn-start-track { background: linear-gradient(135deg, var(--neon-cyan), #00b3ff); color: #0b0c10; font-size: 20px; font-weight: 900; box-shadow: 0 6px 20px rgba(0, 255, 204, 0.3); padding: 25px; }
    .btn-jaar { padding: 22px 10px; border-radius: 16px; font-size: 24px; font-weight: 900; border: 2px solid var(--border-color); background: #1f2026; color: white; }

    .turn-announcement { font-size: 14px; color: #aaa; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px; }
    .current-player-name { font-size: 38px; font-weight: 900; color: var(--neon-cyan); margin: 0 0 25px 0; text-shadow: 0 0 15px rgba(0,255,204,0.3); }
    
    .song-info-card { background: var(--card-bg); padding: 25px 20px; border-radius: 24px; border: 2px solid var(--border-color); margin: 20px 0; text-align: center; }
    .info-year { font-size: 64px; font-weight: 900; color: var(--neon-gold); margin-bottom: 5px; }
    .info-title { font-size: 22px; font-weight: 800; margin-bottom: 5px; line-height: 1.2; }
    .info-artist { color: #b3b3b3; font-size: 16px; }

    .footer { font-size: 10px; color: #444; letter-spacing: 1px; margin-top: 20px; }
</style>
