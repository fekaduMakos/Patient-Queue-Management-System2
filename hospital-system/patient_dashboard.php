<?php
session_start();
if(!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard | Nigist Elin Hospital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary-soft: #e0f2fe;
            --accent-blue: #0ea5e9;
            --medical-green: #10b981;
        }
        body { background: #f1f5f9; font-family: 'Outfit', sans-serif; margin: 0; min-height: 100vh; color: #1e293b; }
        
        .header-nav { 
            background: white; padding: 1rem 5%; display: flex; 
            justify-content: space-between; align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .main-grid { padding: 2rem 5%; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; }

        /* ID Identity Cards */
        .identity-card {
            background: white; padding: 1.5rem; border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex; flex-direction: column; align-items: center;
            position: relative; overflow: hidden; border: 1px solid #e2e8f0;
        }
        .id-label { font-size: 0.85rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 10px; }
        .id-value { font-size: 2.2rem; font-weight: 950; color: #0f172a; margin: 0; }
        .token-card { background: linear-gradient(135deg, #0ea5e9, #0369a1); color: white; border: none; }
        .token-card .id-label { color: rgba(255,255,255,0.8); }
        .token-card .id-value { color: white; font-size: 3.5rem; }

        /* Status Section */
        .status-container { 
            grid-column: span 3; background: white; border-radius: 25px; 
            padding: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;
            text-align: center; border: 1px solid #e2e8f0;
        }
        .status-box { padding: 1rem; }
        .status-box .val { font-size: 2.5rem; font-weight: 900; color: var(--medical-green); }
        .status-box .txt { color: #64748b; font-weight: 700; font-size: 0.95rem; }

        /* Info Board */
        .info-section { grid-column: span 2; }
        .dept-sidebar { grid-column: span 1; background: white; padding: 1.5rem; border-radius: 25px; border: 1px solid #e2e8f0; }

        .patient-header { 
            grid-column: span 3; background: #0f172a; color: white; 
            padding: 2rem 3rem; border-radius: 25px; display: flex; 
            justify-content: space-between; align-items: center;
        }

        .btn-refresh { background: var(--medical-green); color: white; padding: 10px 20px; border-radius: 10px; border: none; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>

    <nav class="header-nav">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-hospital" style="color:var(--accent-blue); font-size:1.8rem;"></i>
            <h2 style="margin:0; font-size:1.4rem;">Nigist Elin Portal</h2>
        </div>
        <a href="php/api/logout.php" style="color:#ef4444; text-decoration:none; font-weight:700;">Sign Out</a>
    </nav>

    <div class="main-grid">
        <div class="patient-header">
            <div>
                <h1 style="margin:0; font-size:2.2rem;">Welcome, <span id="pNameDisp"><?php echo $_SESSION['patient_name']; ?></span></h1>
                <p style="margin:5px 0 0 0; opacity:0.8;">Registered Phone: <span id="pPhoneDisp" style="font-weight:700;">...</span> | Age: <span id="pAgeDisp">...</span> | Gender: <span id="pGenderDisp">...</span></p>
            </div>
            <button class="btn-refresh" onclick="updateStatus()"><i class="fas fa-sync"></i> Refresh</button>
        </div>

        <!-- Identity Cards -->
        <div class="identity-card" title="Your permanent Hospital ID">
            <div class="id-label">Your Patient ID</div>
            <div class="id-value" id="dispPatientId"><?php echo $_SESSION['patient_id']; ?></div>
            <i class="fas fa-id-badge" style="position:absolute; right:-10px; bottom:-10px; font-size:5rem; opacity:0.05;"></i>
        </div>

        <div class="identity-card token-card" title="Your Token number for today">
            <div class="id-label">Your Queue Token</div>
            <div class="id-value" id="dispToken">...</div>
            <i class="fas fa-ticket-alt" style="position:absolute; right:-10px; bottom:-10px; font-size:6rem; opacity:0.1;"></i>
        </div>

        <div class="identity-card">
            <div class="id-label">Assigned Department</div>
            <div class="id-value" id="dispDept" style="font-size:1.8rem; color:var(--accent-blue);">...</div>
            <i class="fas fa-door-open" style="position:absolute; right:-10px; bottom:-10px; font-size:5rem; opacity:0.05;"></i>
        </div>

        <!-- Real-time Stats -->
        <div class="status-container" style="grid-template-columns: 1fr 1fr 1fr;">
            <div class="status-box">
                <div class="val" id="dispCurrent">...</div>
                <div class="txt">Currently Serving</div>
            </div>
            <div class="status-box" style="border-left: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9;">
                <div class="val" id="dispAhead" style="color:#f59e0b;">...</div>
                <div class="txt">People Ahead of You / የእርስዎ ተራ ቁጥር</div>
            </div>
            <div class="status-box">
                <div class="val" id="dispTotalRem" style="color:var(--secondary);">...</div>
                <div class="txt">Total Remaining / የቀሩ ታካሚዎች</div>
            </div>
        </div>

        <!-- Hospital Hub -->
        <div class="info-section">
            <div style="background:white; padding:2rem; border-radius:25px; border:1px solid #e2e8f0;">
                <h3 style="margin-top:0;"><i class="fas fa-info-circle"></i> Instructions / መመሪያዎች</h3>
                <ul style="line-height:2; color:#475569;">
                    <li>Please remain in the waiting area for <strong><span id="deptNameBold">...</span></strong>.</li>
                    <li>When your number <strong><span id="tokenBold"></span></strong> is called, proceed to the counter.</li>
                    <li>If you miss your turn, please contact the registration desk.</li>
                </ul>
            </div>
        </div>

        <div class="dept-sidebar">
            <h3 style="margin-top:0; font-size:1.1rem; border-bottom:2px solid #f1f5f9; padding-bottom:10px;">Ongoing Sessions</h3>
            <div id="sidebarList">
                <!-- Injected -->
            </div>
        </div>
    </div>

    <script>
        async function updateStatus() {
            try {
                const pId = "<?php echo $_SESSION['patient_id']; ?>";
                const res = await fetch(`php/api/get_patient_status.php?id=${pId}`);
                const data = await res.json();
                
                if (data.success) {
                    document.getElementById('pPhoneDisp').textContent = data.patient_phone;
                    document.getElementById('pAgeDisp').textContent = data.patient_age;
                    document.getElementById('pGenderDisp').textContent = data.patient_gender;
                    
                    document.getElementById('dispToken').textContent = data.token;
                    document.getElementById('tokenBold').textContent = data.token;
                    document.getElementById('dispDept').textContent = data.department;
                    document.getElementById('deptNameBold').textContent = data.department;
                    document.getElementById('dispCurrent').textContent = "#" + data.current_serving;
                    document.getElementById('dispAhead').textContent = data.people_ahead;
                    document.getElementById('dispTotalRem').textContent = data.total_remaining;
                    
                    const list = document.getElementById('sidebarList');
                    list.innerHTML = '';
                    data.all_depts.forEach(d => {
                        list.innerHTML += `
                            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px dashed #f1f5f9;">
                                <span style="font-size:0.9rem; font-weight:600;">${d.name}</span>
                                <span style="color:var(--accent-blue); font-weight:800;">#${d.current || '---'}</span>
                            </div>
                        `;
                    });
                }
            } catch (e) { console.error(e); }
        }

        setInterval(updateStatus, 5000);
        updateStatus();
    </script>
</body>
</html>
