<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Queue Board | Nigist Elin Hospital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root { 
            --board-bg: #f8fafc; 
            --card-bg: #ffffff; 
            --accent: #0284c7; 
            --text-main: #0f172a;
            --text-muted: #64748b;
            --secondary-soft: rgba(16, 185, 129, 0.1);
        }
        body { 
            background: var(--board-bg); 
            color: var(--text-main); 
            height: 100vh; 
            margin: 0;
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            font-family: 'Outfit', sans-serif; 
        }
        
        header { 
            background: white; 
            padding: 1rem 2.5rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 4px solid #0ea5e9;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        
        :root { 
            --board-bg: #f8fafc; 
            --card-bg: #ffffff; 
            --accent: #0284c7; 
            --text-main: #0f172a;
            --text-muted: #64748b;
        }
        body { 
            background: var(--board-bg); 
            color: var(--text-main); 
            height: 100vh; 
            margin: 0;
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            font-family: 'Outfit', sans-serif; 
        }
        header { 
            background: white; 
            padding: 1rem 2.5rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 4px solid #0ea5e9;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .layout { 
            flex: 1; 
            display: grid; 
            grid-template-columns: 40% 60%; 
            overflow: hidden; 
        }
        .queue-list { 
            background: #f1f5f9; 
            padding: 1.5rem; 
            display: flex; 
            flex-direction: column; 
            gap: 15px; 
            border-right: 2px solid #e2e8f0;
            overflow-y: auto;
            scrollbar-width: none;
            scroll-behavior: smooth;
        }
        .queue-list::-webkit-scrollbar { display: none; }
        .list-item { 
            background: white; 
            border-radius: 20px; 
            padding: 1.2rem 2.5rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            border: 1px solid #e2e8f0;
            border-left: 8px solid var(--accent); 
            transition: all 0.4s ease;
        }
        .list-item.active-row {
            background: #e0f2fe;
            border-color: #7dd3fc;
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.1);
        }
        .list-item .dept { font-size: 1.5rem; font-weight: 800; color: #0c4a6e; }
        .list-item .token { font-size: 3.5rem; font-weight: 900; color: var(--accent); }
        .list-item .win-label { font-size: 1rem; color: var(--text-muted); font-weight: 700; }
        .hero-call { 
            background: white;
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            text-align: center; 
            padding: 2rem;
        }
        .hero-label { 
            font-size: 2rem; 
            color: #10b981; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 5px; 
            margin-bottom: 1rem;
        }
        .hero-token { 
            font-size: 15rem; 
            font-weight: 950; 
            line-height:1; 
            color: #0c4a6e; 
            letter-spacing: -5px;
        }
        .hero-target { 
            font-size: 3.5rem; 
            color: #0369a1; 
            font-weight: 800; 
            margin-top: 2rem;
            padding: 15px 60px;
            background: #f0f9ff;
            border-radius: 100px;
            border: 2px solid #bae6fd;
        }
        .footer-ticker { 
            background: #0ea5e9; 
            height: 65px; 
            display: flex; 
            align-items: center; 
            overflow: hidden;
            position: relative;
        }
        .ticker-content {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: ticker-scroll 40s linear infinite;
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
        }
        @keyframes ticker-scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-area" style="display: flex; align-items:center;">
            <div style="background:#0ea5e9; width:45px; height:45px; display:flex; align-items:center; justify-content:center; border-radius:10px; margin-right:12px;">
                <i class="fas fa-hospital" style="font-size:1.5rem; color:white;"></i>
            </div>
            <div><h1 style="font-size: 1.6rem; margin:0; letter-spacing:0.5px; color:#0c4a6e;">NIGIST ELIN HOSPITAL</h1><span style="color:#0ea5e9; font-weight:700; font-size:0.8rem;">PUBLIC DISPLAY BOARD</span></div>
        </div>
        <div style="text-align:right"><div id="clock" style="font-size: 2rem; font-weight: 900; color:#0c4a6e;">00:00:00</div><div id="date" style="color: #64748b; font-weight: 600;">April 26, 2026</div></div>
    </header>
    <div class="layout">
        <div class="queue-list" id="queueItems"></div>
        <div class="hero-call">
            <div class="hero-label">Now Calling</div>
            <div class="hero-token" id="heroToken">---</div>
            <div class="hero-target" id="heroDept">Please Wait</div>
        </div>
    </div>

    <div class="footer-ticker">
        <div class="ticker-content">
            <span class="ticker-highlight">WELCOME TO NIGIST ELIN HOSPITAL | እንኳን ወደ ንግሥተ ይህሊን ሆስፒታል በደህና መጡ!</span> 
            • Compassionate Care for Every Patient. 
            • <span class="ticker-highlight">ጤናዎ ለእኛ ትልቅ ዋጋ አለው።</span> 
            • Please wait until your token number is called. 
            • <span class="ticker-highlight">ለሰላማዊ አገልግሎት ትብብርዎ እናመሰግናለን።</span>
            • Quality Healthcare for All.
        </div>
    </div>

    <audio id="bell" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script>
        let windowsData = [];
        let lastTokens = {};
        let currentIndex = 0;

        async function sync() {
            try {
                const res = await fetch('php/api/get_status.php');
                const data = await res.json();
                if (data.success) {
                    const filtered = data.windows.filter(win => !win.counter_name.toLowerCase().includes('reception'));
                    
                    filtered.forEach((win, idx) => {
                        if (lastTokens[win.window_number] && lastTokens[win.window_number] !== win.current_token && win.current_token !== '---') {
                            document.getElementById('bell').play().catch(() => {});
                            currentIndex = idx;
                            updateHero();
                        }
                        lastTokens[win.window_number] = win.current_token;
                    });

                    windowsData = filtered;
                    renderList();
                }
            } catch (e) {}
        }

        function renderList() {
            const list = document.getElementById('queueItems');
            list.innerHTML = '';
            windowsData.forEach((win, index) => {
                let icon = "fa-user-md";
                if(win.counter_name.toLowerCase().includes('lab')) icon = "fa-microscope";
                else if(win.counter_name.toLowerCase().includes('pharmacy')) icon = "fa-pills";
                else if(win.counter_name.toLowerCase().includes('triage')) icon = "fa-heartbeat";
                else if(win.counter_name.toLowerCase().includes('pediatrics')) icon = "fa-child";

                list.innerHTML += `
                    <div class="list-item ${index === currentIndex ? 'active-row' : ''}" id="win-${win.window_number}">
                        <div style="display:flex; align-items:center;">
                            <i class="fas ${icon} dept-icon" style="font-size:1.8rem; margin-right:15px; color:#0ea5e9;"></i>
                            <div>
                                <div class="dept">${win.counter_name}</div>
                                <div style="display:flex; align-items:center; gap:10px; margin-top:5px;">
                                    <div class="win-label">Window ${win.window_number}</div>
                                    <div style="background:#f0fdf4; color:#16a34a; padding:2px 10px; border-radius:100px; font-size:0.75rem; font-weight:800; border:1px solid #dcfce7;">
                                        Waiting: ${win.waiting_count}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="token">${win.current_token || '---'}</div>
                    </div>
                `;
            });
        }

        function updateHero() {
            if (windowsData.length === 0) return;
            const win = windowsData[currentIndex];
            document.getElementById('heroToken').textContent = win.current_token || '---';
            document.getElementById('heroDept').textContent = win.counter_name;
            renderList();

            setTimeout(() => {
                const activeEl = document.querySelector('.active-row');
                if (activeEl) {
                    activeEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        }

        function cycle() {
            if (windowsData.length > 0) {
                currentIndex = (currentIndex + 1) % windowsData.length;
                updateHero();
            }
        }

        setInterval(sync, 1500);
        setInterval(cycle, 6000); 
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString();
            document.getElementById('date').textContent = now.toDateString();
        }, 1000);

        sync().then(() => updateHero());
        document.body.onclick = () => document.getElementById('bell').play().then(() => document.getElementById('bell').pause());
    </script>
</body>
</html>
