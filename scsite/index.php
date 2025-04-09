<?php
require_once 'servers/auth_utils.php';
require_once 'online_users.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survivalcraft Multiplayer -  Servers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --main-font: 'Alegreya Sans SC', sans-serif;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--main-font) !important;
        }
        
        body, h1, h2, h3, h4, h5, h6, p, span, a, button, input, textarea, select, option, label, div {
            font-family: var(--main-font) !important;
        }

        .online-bar {
            background-color: #2a2a2a;
            color: white;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .online-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #4CAF50;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: #1a1a1a;
            color: #ffffff;
            padding-top: 35px; /* Aggiungo padding per il contatore online */
        }

        .header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://play-lh.googleusercontent.com/N1YtAVF3Qrn5CVVySOwAcb2x3p9K4o3D1NsoWJVPPjWYjnaHrUkwOq6TviQuic2XXyI=w526-h296-rw');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
            position: relative;
        }

        .auth-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }

        .auth-button {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .login-button {
            background-color: #4CAF50;
            color: white;
            border: none;
        }

        .login-button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .register-button {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .register-button:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .logout-button {
            background-color: #f44336;
            color: white;
            border: none;
        }

        .logout-button:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .header h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            animation: fadeIn 1s ease-out;
        }

        .header p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            animation: fadeIn 1s ease-out 0.3s backwards;
        }

        .cta-button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeIn 1s ease-out 0.6s backwards;
        }

        .cta-button:hover {
            background-color: #45a049;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .features {
            padding: 80px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .features h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background-color: #2a2a2a;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: scaleIn 0.5s ease-out backwards;
        }

        .feature-card:nth-child(1) { animation-delay: 0.2s; }
        .feature-card:nth-child(2) { animation-delay: 0.4s; }
        .feature-card:nth-child(3) { animation-delay: 0.6s; }

        .feature-card:hover {
            transform: translateY(-5px) scale(1.02);
            background-color: #333;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .feature-card.active {
            background-color: #4CAF50;
        }

        .feature-card.active h3,
        .feature-card.active p {
            color: white;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #4CAF50;
        }

        .feature-card p {
            line-height: 1.6;
        }

        .servers {
            background-color: #2a2a2a;
            padding: 80px 20px;
        }

        .servers h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
        }

        .server-list {
            max-width: 1200px;
            margin: 0 auto;
        }

        .server-card {
            background-color: #1a1a1a;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeIn 0.5s ease-out backwards;
            cursor: pointer;
        }

        .server-card a {
            text-decoration: none;
            color: inherit;
        }

        .server-card:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .server-info h3 {
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .server-ip {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            background-color: #333;
            padding: 10px;
            border-radius: 5px;
        }

        .server-ip span {
            font-family: monospace;
            flex-grow: 1;
        }

        .copy-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .copy-button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .copy-button.copied {
            background-color: #2196F3;
            animation: pulse 0.5s;
        }

        footer {
            background-color: #1a1a1a;
            padding: 40px 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }

            .header p {
                font-size: 1.2rem;
            }

            .server-ip {
                flex-direction: column;
                align-items: stretch;
            }

            .copy-button {
                width: 100%;
                justify-content: center;
            }
        }

        .filter-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            z-index: 1000;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s ease-out;
        }

        .filter-overlay.active {
            display: block;
            animation: fadeIn 0.3s ease-out forwards;
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .filter-header h2 {
            color: #333;
            font-size: 1.5rem;
            margin: 0;
        }

        .close-filter {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .filter-option {
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 10px;
            color: #333;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: slideIn 0.3s ease-out backwards;
        }

        .filter-option:nth-child(1) { animation-delay: 0.1s; }
        .filter-option:nth-child(2) { animation-delay: 0.2s; }
        .filter-option:nth-child(3) { animation-delay: 0.3s; }
        .filter-option:nth-child(4) { animation-delay: 0.4s; }
        .filter-option:nth-child(5) { animation-delay: 0.5s; }

        .filter-option:hover {
            background-color: #e0e0e0;
            transform: translateX(10px);
        }

        .filter-option.active {
            background-color: #4CAF50;
            color: white;
        }

        .filter-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeIn 0.5s ease-out backwards;
        }

        .filter-button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .discord-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #7289DA;
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 100;
            animation: fadeIn 0.5s ease-out backwards;
            animation-delay: 1s;
        }

        .discord-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            background-color: #677BC4;
        }

        .discord-button img {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
    <div class="online-bar">
        <span class="online-indicator"></span>
        Online: <span id="online-count"><?php echo getOnlineUsersCount(); ?></span>
    </div>
    <header class="header">
        <div class="auth-buttons">
            <?php
            if (isLoggedIn()) {
                echo '<a href="servers/logout.php" class="auth-button logout-button" style="background-color: #f44336; color: white; border: none;">Logout</a>';
            } else {
                echo '<a href="servers/register.php" class="auth-button register-button" style="background-color: transparent; color: white; border: 2px solid white;">Create Account</a>';
                echo '<a href="servers/login.php" class="auth-button login-button" style="background-color: #4CAF50; color: white; border: none;">Login</a>';
            }
            ?>
        </div>
        <h1>Survivalcraft Multiplayer</h1>
        <p>Join the official Survivalcraft servers</p>
        <button class="cta-button" onclick="scrollToServers()">Start Playing</button>
    </header>

    <section class="features">
        <h2>Features</h2>
        <div class="feature-grid">
            <div class="feature-card" onclick="filterServers('survival')">
                <h3>Advanced Survival</h3>
                <p>Realistic survival system with unique mechanics and exciting challenges.</p>
            </div>
            <div class="feature-card" onclick="filterServers('economy')">
                <h3>Dynamic Economy</h3>
                <p>Player-driven economic system with markets and player trading.</p>
            </div>
            <div class="feature-card" onclick="filterServers('events')">
                <h3>Special Events</h3>
                <p>Join weekly events with exclusive prizes and competitions.</p>
            </div>
        </div>
    </section>

    <section class="servers" id="servers">
        <h2>Official Servers</h2>
        <button class="filter-button" onclick="showFilterOverlay()">
            <span>🔍</span> Filtra Server
        </button>
        <div class="server-list">
            <div class="server-card" data-category="survival">
                <a href="servers/survival.html">
                    <div class="server-info">
                        <h3>Kakkinspro's Official Server</h3>
                        <p>Main server - Vanilla survival</p>
                        <div class="server-ip">
                            <span>survivalcraftmp.duckdns.org:28887</span>
                            <button class="copy-button" onclick="event.preventDefault(); copyIP('survivalcraftmp.duckdns.org:28887', this)">
                                <span>📋</span> Copy IP
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            <div class="server-card" data-category="creative">
                <a href="servers/creative.html">
                    <div class="server-info">
                        <h3>Creative Server</h3>
                        <p>Build freely in creative mode</p>
                        <div class="server-ip">
                            <span>survivalcraftcreative.duckdns.org</span>
                            <button class="copy-button" onclick="event.preventDefault(); copyIP('survivalcraftcreative.duckdns.org', this)">
                                <span>📋</span> Copy IP
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            <div class="server-card" data-category="economy">
                <a href="servers/economy.html">
                    <div class="server-info">
                        <h3>Economy Server</h3>
                        <p>Trade and build your empire</p>
                        <div class="server-ip">
                            <span>survivalcrafteconomy.duckdns.org</span>
                            <button class="copy-button" onclick="event.preventDefault(); copyIP('survivalcrafteconomy.duckdns.org', this)">
                                <span>📋</span> Copy IP
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            <div class="server-card" data-category="events">
                <a href="servers/events.html">
                    <div class="server-info">
                        <h3>Events Server</h3>
                        <p>Join our special events</p>
                        <div class="server-ip">
                            <span>survivalcraftevents.duckdns.org</span>
                            <button class="copy-button" onclick="event.preventDefault(); copyIP('survivalcraftevents.duckdns.org', this)">
                                <span>📋</span> Copy IP
                            </button>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <div class="filter-overlay" id="filterOverlay">
        <div class="filter-header">
            <h2>Seleziona Filtro Server</h2>
            <button class="close-filter" onclick="closeFilterOverlay()">×</button>
        </div>
        <div class="filter-options">
            <div class="filter-option" onclick="applyFilter('all')">Tutti i Server</div>
            <div class="filter-option" onclick="applyFilter('survival')">Advanced Survival</div>
            <div class="filter-option" onclick="applyFilter('creative')">Creative Servers</div>
            <div class="filter-option" onclick="applyFilter('economy')">Economy Survival Servers</div>
            <div class="filter-option" onclick="applyFilter('events')">Events</div>
        </div>
    </div>

    <a href="https://discord.gg/zZ24UQycRq" target="_blank" class="discord-button">
        <img src="https://pngimg.com/uploads/discord/discord_PNG6.png" alt="Discord">
        Join Our Discord Server
    </a>

    <footer>
        <p>&copy; 2024 Survivalcraft Multiplayer. All rights reserved.</p>
    </footer>

    <script>
        let currentFilter = 'all';

        function showFilterOverlay() {
            document.getElementById('filterOverlay').classList.add('active');
        }

        function closeFilterOverlay() {
            document.getElementById('filterOverlay').classList.remove('active');
        }

        function applyFilter(category) {
            currentFilter = category;
            const filterOptions = document.querySelectorAll('.filter-option');
            const servers = document.querySelectorAll('.server-card');
            
            // Aggiorna lo stile delle opzioni di filtro
            filterOptions.forEach(option => {
                option.classList.remove('active');
                if (option.getAttribute('onclick').includes(category)) {
                    option.classList.add('active');
                }
            });

            // Filtra i server
            servers.forEach(server => {
                if (category === 'all' || server.dataset.category === category) {
                    server.style.display = 'block';
                } else {
                    server.style.display = 'none';
                }
            });

            // Chiudi l'overlay dopo la selezione
            closeFilterOverlay();
        }

        // Mostra tutti i server all'avvio
        window.onload = function() {
            applyFilter('all');
        };

        function copyIP(ip, button) {
            // Crea un elemento textarea temporaneo
            const textarea = document.createElement('textarea');
            textarea.value = ip;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            
            // Seleziona e copia il testo
            if (navigator.userAgent.match(/ipad|iphone/i)) {
                // Gestione speciale per iOS
                const range = document.createRange();
                range.selectNodeContents(textarea);
                const selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
                textarea.setSelectionRange(0, 999999);
                textarea.contentEditable = true;
                textarea.readOnly = false;
            } else {
                textarea.select();
            }
            
            try {
                document.execCommand('copy');
                button.classList.add('copied');
                button.innerHTML = '<span>✓</span> Copiato!';
                setTimeout(() => {
                    button.classList.remove('copied');
                    button.innerHTML = '<span>📋</span> Copia IP';
                }, 2000);
            } catch (err) {
                console.error('Errore durante la copia:', err);
            }
            
            // Rimuovi l'elemento textarea temporaneo
            document.body.removeChild(textarea);
        }

        function scrollToServers() {
            document.getElementById('servers').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Funzione per aggiornare il contatore degli utenti online
        function updateOnlineCount() {
            fetch('online_users.php', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('online-count').textContent = data.count;
            })
            .catch(error => console.error('Errore nell\'aggiornamento del contatore:', error));
        }

        // Aggiorna il contatore ogni 30 secondi
        setInterval(updateOnlineCount, 30000);

        // Aggiorna il contatore all'avvio della pagina
        document.addEventListener('DOMContentLoaded', function() {
            updateOnlineCount();
        });
    </script>
</body>
</html>