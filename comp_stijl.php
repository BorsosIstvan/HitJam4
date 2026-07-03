<style>
    :root { --bg-color: #0b0c10; --card-bg: rgba(255, 255, 255, 0.04); --border-color: #33343f; }
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: var(--bg-color); color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
    .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #120917 0%, #0b0c10 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; text-align: center; position: relative; overflow: hidden; }
    .logo { font-size: 36px; font-weight: 900; background: linear-gradient(45deg, #ff2d55, #ff9500); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; }
    .subtitle { color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; margin-top: 5px; }
    .game-screen { display: none; } .game-screen.active { display: block; }
    .player-input-row { display: flex; gap: 10px; margin-bottom: 15px; }
    .input-field { flex: 1; padding: 15px; border-radius: 12px; border: 2px solid var(--border-color); background: #1f2026; color: white; font-size: 16px; }
    .player-badge { background: var(--card-bg); border: 1px solid var(--border-color); padding: 12px; border-radius: 10px; margin-bottom: 8px; text-align: left; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
    .btn { width: 100%; padding: 18px; border-radius: 16px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; text-transform: uppercase; }
    .btn-primary { background: linear-gradient(90deg, #ff2d55, #e01b43); color: white; }
    .btn-start-track { background: linear-gradient(135deg, #00ffcc, #00b3ff); color: #0b0c10; font-size: 20px; font-weight: 900; padding: 25px; }
    .btn-jaar { padding: 22px 10px; border-radius: 16px; font-size: 24px; font-weight: 900; border: 2px solid var(--border-color); background: #1f2026; color: white; }
    .song-info-card { background: var(--card-bg); padding: 25px 20px; border-radius: 24px; border: 2px solid var(--border-color); margin: 20px 0; }
    .info-year { font-size: 64px; font-weight: 900; color: #ff9500; margin-bottom: 5px; }
    .info-title { font-size: 22px; font-weight: 800; margin-bottom: 5px; }
    .info-artist { color: #b3b3b3; font-size: 16px; }
    .footer { font-size: 10px; color: #444; margin-top: 20px; }
</style>