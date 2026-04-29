<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal | Nigist Elin Hospital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { display: flex; background: var(--bg-main); color: var(--text-main); min-height: 100vh; }
        .side-nav {
            width: 300px; background: #fff; border-right: 1px solid var(--border);
            padding: 2rem; display: flex; flex-direction: column; box-shadow: 10px 0 30px rgba(0,0,0,0.02);
        }
        .main-content { flex: 1; padding: 3rem; overflow-y: auto; }
        .active-nav { background: var(--primary-light) !important; color: var(--primary) !important; }
        .window-status { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
    </style>
</head>
<body>
    <aside class="side-nav">
        <div class="logo-area" style="margin-bottom: 3rem;">
            <div class="logo-icon"><i class="fas fa-stethoscope"></i></div>
            <div class="hospital-name"><h1 id="userNameDisplay" style="color: #0c4a6e;">STAFF PORTAL</h1><p id="userRoleDisplay">Loading...</p></div>
        </div>
        <nav style="display: flex; flex-direction: column; gap: 10px;">
            <a href="#" class="btn-premium active-nav"><i class="fas fa-th-large"></i> Dashboard</a>
            <div id="adminLinks" style="display: none;">
                <a href="#" class="btn-premium" style="width: 100%; color: var(--text-muted); background: transparent; box-shadow: none;"><i class="fas fa-user-shield"></i> User Mgmt</a>
                <a href="#" class="btn-premium" style="width: 100%; color: var(--text-muted); background: transparent; box-shadow: none;"><i class="fas fa-chart-line"></i> Analytics</a>
            </div>
            <a href="#" class="btn-premium" style="width: 100%; color: var(--text-muted); background: transparent; box-shadow: none;"><i class="fas fa-cog"></i> Settings</a>
            <button onclick="logout()" class="btn-premium" style="width: 100%; color: var(--danger); background: #fee2e2; border: none; margin-top: auto;"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </nav>
    </aside>
    <main class="main-content">
        <header style="background: transparent; border: none; padding: 0; margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: center;">
            <div><h1 id="pageTitle" style="font-family: 'Outfit'; font-size: 2.5rem; color: #0c4a6e;">Service Dashboard</h1><p id="subTitle" style="color: var(--text-muted);">Welcome back!</p></div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div id="adminWindowSelector" style="display: none; background: white; padding: 8px 15px; border-radius: 12px; border: 1px solid var(--border); box-shadow: var(--shadow-premium);">
                    <label style="font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-right: 10px;">OPERATING WINDOW:</label>
                    <select id="winSelect" onchange="switchWindow(this.value)" style="border: none; font-weight: 700; color: var(--primary); outline: none; cursor: pointer;">
                        <option value="2">Window 2 (Triage)</option>
                        <option value="3">Window 3 (OPD)</option>
                        <option value="4">Window 4 (Pediatrics)</option>
                        <option value="5">Window 5 (Laboratory)</option>
                        <option value="6">Window 6 (Pharmacy)</option>
                        <option value="7">Window 7 (Radiology)</option>
                    </select>
                </div>
                <div style="background: white; padding: 12px 24px; border-radius: 12px; border: 1px solid var(--border); display: flex; align-items: center; gap: 12px; box-shadow: var(--shadow-premium);"><span class="window-status" style="background: var(--secondary);"></span><span id="systemStatus" style="font-weight: 700; font-size: 0.9rem;">SYSTEM ONLINE</span></div>
            </div>
        </header>

        <div id="staffControls" style="display: grid; grid-template-columns: 1fr 400px; gap: 3rem;">
            <div>
                <!-- Queue Stats -->
                <div style="display: flex; gap: 20px; margin-bottom: 2rem;">
                    <div class="glass-card" style="flex: 1; padding: 1.5rem; border-left: 5px solid #f59e0b;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Patients Remaining / የቀሩ ታካሚዎች</span>
                        <h2 id="totalWaitingCount" style="font-size: 2.5rem; margin: 5px 0; color: #b45309;">0</h2>
                    </div>
                    <div class="glass-card" style="flex: 1; padding: 1.5rem; border-left: 5px solid var(--secondary);">
                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Served Today</span>
                        <h2 id="totalServedCount" style="font-size: 2.5rem; margin: 5px 0; color: var(--secondary);">0</h2>
                    </div>
                </div>

                <div class="glass-card" style="text-align: center; padding: 5rem 2rem; margin-bottom: 2rem;">
                    <span style="font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 4px; font-size: 0.9rem;">Currently Serving</span>
                    <h1 id="activeNumber" style="font-family: 'Outfit'; font-size: 10rem; font-weight: 900; color: var(--text-main); margin: 1rem 0;">---</h1>
                    <div style="display: flex; flex-direction: column; gap: 15px; align-items: center;">
                        <button class="btn-premium btn-primary" style="padding: 1.5rem 5rem; font-size: 1.4rem; box-shadow: 0 10px 40px rgba(2, 132, 199, 0.2);" onclick="callNext()">
                            <i class="fas fa-bullhorn"></i> CALL NEXT
                        </button>
                        <button onclick="setManualToken()" style="background:transparent; border:none; color:var(--text-muted); text-decoration:underline; font-size:0.9rem; cursor:pointer;">
                            <i class="fas fa-edit"></i> Set Manually / በራስዎ ይሙሉ
                        </button>
                    </div>
                </div>

                <!-- Transfer Patient -->
                <div id="transferContainer" class="glass-card" style="padding: 2rem; border-top: 5px solid var(--primary); display:none;">
                    <h3 style="margin-bottom: 1rem; font-family: 'Outfit';"><i class="fas fa-exchange-alt"></i> Transfer Patient / በሽተኛውን ያስተላልፉ</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Send the current patient to another department for the next step.</p>
                    <div style="display: flex; gap: 15px;">
                        <select id="transferDept" style="flex: 1; padding: 12px; border-radius: 10px; border: 1px solid var(--border); font-family: 'Outfit';">
                            <option value="Reception">Reception / ካርድ ክፍል</option>
                            <option value="Triage">Triage / ትሪያጅ</option>
                            <option value="OPD">OPD / የዶክተር ክፍል</option>
                            <option value="Pediatrics">Pediatrics / የህጻናት ክፍል</option>
                            <option value="Laboratory">Laboratory / ላብራቶሪ</option>
                            <option value="Pharmacy">Pharmacy / ፋርማሲ</option>
                            <option value="Radiology">Radiology / ራጅ</option>
                        </select>
                        <button class="btn-premium btn-secondary" onclick="transferCurrentPatient()">Transfer Now</button>
                    </div>
                </div>
            </div>
            <div class="glass-card" style="padding: 2rem;">
                <h2 style="font-family: 'Outfit'; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;"><i class="fas fa-list-ul" style="color: var(--primary);"></i> Waiting List</h2>
                <div id="waitingListContainer"><p style="color: var(--text-muted);">Fetching...</p></div>
            </div>
        </div>

        <div id="adminDashboard" style="display: none; margin-top: 3rem;">
            <!-- PENDING SECTION -->
            <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 2rem; border-radius: 25px; margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1.5rem; font-family: 'Outfit'; color: #92400e;"><i class="fas fa-user-clock"></i> Pending Approvals / የሚጠባበቁ ታካሚዎች</h2>
                <div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 20px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div style="overflow-x: auto;">
                        <table>
                            <thead style="background: #fef3c7;">
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Patient ID</th>
                                    <th>Registered At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="pendingPatientsTable">
                                <tr><td colspan="5" style="text-align:center;">No pending approvals</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ALL ACCOUNTS SECTION -->
            <h2 style="margin-bottom: 1.5rem; font-family: 'Outfit'; color: var(--primary);"><i class="fas fa-users-cog"></i> Patient Account Management / የታካሚዎች አካውንት መቆጣጠሪያ</h2>
            <div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 20px; margin-bottom: 3rem;">
                <div style="overflow-x: auto;">
                    <table>
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Age/Sex</th>
                                <th>Status</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody id="allAccountsTable"></tbody>
                    </table>
                </div>
            </div>

            <!-- NEW: STAFF ACCOUNT CREATOR -->
            <div class="glass-card" style="padding: 2rem; margin-bottom: 3rem;">
                <h2 style="margin-top: 0; font-family: 'Outfit'; color: #0c4a6e;"><i class="fas fa-user-plus"></i> Create Staff Account</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Generate separate login accounts for each department.</p>
                <form id="createStaffForm" onsubmit="createStaffAccount(event)" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group"><label>Full Name</label><input type="text" id="staffName" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width: 100%;"></div>
                    <div class="form-group"><label>Username</label><input type="text" id="staffUser" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width: 100%;"></div>
                    <div class="form-group"><label>Password</label><input type="password" id="staffPass" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width: 100%;"></div>
                    <div class="form-group">
                        <label>Role & Department</label>
                        <select id="staffRole" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width: 100%;" onchange="autoSetWindow(this.value)">
                            <option value="doctor">Doctor (OPD)</option>
                            <option value="lab_tech">Lab Technician (Laboratory)</option>
                            <option value="pharmacist">Pharmacist (Pharmacy)</option>
                            <option value="receptionist">Triage Nurse (Triage)</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Assign Window #</label>
                        <select id="staffWindow" required style="padding:10px; border:1px solid #e2e8f0; border-radius:8px; width: 100%;">
                            <option value="2">Window 2 (Triage)</option>
                            <option value="3">Window 3 (OPD)</option>
                            <option value="4">Window 4 (Pediatrics)</option>
                            <option value="5">Window 5 (Laboratory)</option>
                            <option value="6">Window 6 (Pharmacy)</option>
                            <option value="7">Window 7 (Radiology)</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <button type="submit" class="btn-premium" style="width: 100%; margin-top: 10px;">CREATE ACCOUNT</button>
                    </div>
                </form>
            </div>

            <h2 style="margin-bottom: 1.5rem; font-family: 'Outfit';"><i class="fas fa-ticket-alt"></i> Today's Queue Activity</h2>
            <div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 20px;"><div style="overflow-x: auto;"><table><thead style="background: #f8fafc;"><tr><th>Token</th><th>Patient Name</th><th>Age/Sex</th><th>Phone</th><th>Dept.</th><th>Urgency</th><th>Payment</th><th>Portal Account</th></tr></thead><tbody id="adminPatientTable"></tbody></table></div></div>
        </div>
    </main>

    <script>
        let WINDOW_NUMBER = null;
        async function checkSession() {
            try {
                const response = await fetch('php/api/get_session.php');
                const result = await response.json();
                if (!result.loggedIn) { window.location.href = 'login.php'; return; }
                const user = result.user; 
                WINDOW_NUMBER = user.window;
                document.getElementById('userNameDisplay').textContent = user.name;
                document.getElementById('userRoleDisplay').textContent = user.role.toUpperCase();

                if (user.role === 'admin') { 
                    document.getElementById('adminLinks').style.display = 'block'; 
                    document.getElementById('adminDashboard').style.display = 'block'; 
                    document.getElementById('adminWindowSelector').style.display = 'block';
                    if (!WINDOW_NUMBER) WINDOW_NUMBER = 2; // Default to Triage for Admin
                    document.getElementById('winSelect').value = WINDOW_NUMBER;
                    loadPatients(); 
                    loadPendingPatients();
                    loadAllAccounts();
                    fetchCurrentServing();
                    document.getElementById('staffControls').style.display = 'grid';
                } else {
                    // STAFF ROLE: Dedicated branding for each department
                    document.getElementById('adminLinks').style.display = 'none';
                    document.getElementById('adminDashboard').style.display = 'none';
                    document.getElementById('adminWindowSelector').style.display = 'none';
                    
                    if (!WINDOW_NUMBER) {
                        alert("No window assigned to this account. Please contact Admin.");
                        window.location.href = 'php/api/logout.php';
                        return;
                    }

                    // Set Department-Specific Branding
                    let deptTitle = "Service Dashboard";
                    let iconClass = "fa-user-md";
                    
                    if (user.role === 'lab_tech') { deptTitle = "Laboratory Control Center"; iconClass = "fa-microscope"; }
                    else if (user.role === 'pharmacist') { deptTitle = "Pharmacy Dispensary"; iconClass = "fa-pills"; }
                    else if (user.role === 'receptionist') { deptTitle = "Triage & Vital Signs"; iconClass = "fa-heartbeat"; }
                    else if (user.role === 'doctor') { deptTitle = "Medical Consultation (OPD)"; iconClass = "fa-stethoscope"; }

                    document.getElementById('pageTitle').innerHTML = `<i class="fas ${iconClass}"></i> ${deptTitle}`;
                    document.getElementById('subTitle').textContent = `Now Managing Window ${WINDOW_NUMBER} | Nigist Elin Hospital`;

                    document.getElementById('staffControls').style.display = 'grid';
                    fetchCurrentServing();
                    loadPatients();
                }
            } catch (error) { console.error("Session check failed"); }
        }

        function switchWindow(val) {
            WINDOW_NUMBER = parseInt(val);
            document.getElementById('subTitle').textContent = `Now managing Window ${WINDOW_NUMBER}`;
            fetchCurrentServing();
            loadPatients();
        }

        async function createStaffAccount(e) {
            e.preventDefault();
            const data = {
                full_name: document.getElementById('staffName').value,
                username: document.getElementById('staffUser').value,
                password: document.getElementById('staffPass').value,
                role: document.getElementById('staffRole').value,
                window: document.getElementById('staffWindow').value
            };

            try {
                const res = await fetch('php/api/create_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if (result.success) {
                    alert("Staff Account Created Successfully!");
                    document.getElementById('createStaffForm').reset();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (e) { alert("Failed to create account"); }
        }

        function autoSetWindow(role) {
            const winSelect = document.getElementById('staffWindow');
            if (role === 'lab_tech') winSelect.value = '5';
            else if (role === 'pharmacist') winSelect.value = '6';
            else if (role === 'receptionist') winSelect.value = '2';
            else if (role === 'doctor') winSelect.value = '3';
        }

        async function fetchCurrentServing() {
            if (!WINDOW_NUMBER) return;
            try {
                const res = await fetch(`php/api/get_current_serving.php?window=${WINDOW_NUMBER}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('activeNumber').textContent = data.token || '---';
                }
            } catch (e) {}
        }

        async function loadPendingPatients() {
            try {
                const response = await fetch('php/api/get_pending_patients.php');
                const data = await response.json();
                if (data.success) {
                    const tbody = document.getElementById('pendingPatientsTable');
                    tbody.innerHTML = '';
                    if (data.patients.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No pending approvals</td></tr>';
                        return;
                    }
                    data.patients.forEach(p => {
                        tbody.innerHTML += `
                            <tr>
                                <td style="font-weight:700;">${p.full_name}</td>
                                <td>${p.phone}</td>
                                <td style="color:var(--primary); font-weight:600;">${p.patient_id}</td>
                                <td>${new Date(p.created_at).toLocaleDateString()}</td>
                                <td>
                                    <button class="btn-premium" style="background: var(--secondary); color: white; border: none; padding: 5px 15px; font-size: 0.8rem;" onclick="managePatient('${p.patient_id}', 'approve')">APPROVE</button>
                                    <button class="btn-premium" style="background: var(--danger); color: white; border: none; padding: 5px 15px; font-size: 0.8rem; margin-left: 5px;" onclick="managePatient('${p.patient_id}', 'reject')">REJECT</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            } catch (e) { console.error("Load pending failed"); }
        }

        async function loadAllAccounts() {
            try {
                const response = await fetch('php/api/get_all_accounts.php');
                const data = await response.json();
                if (data.success) {
                    const tbody = document.getElementById('allAccountsTable');
                    tbody.innerHTML = '';
                    data.accounts.forEach(a => {
                        let statusColor = a.status === 'approved' ? 'var(--secondary)' : (a.status === 'rejected' ? 'var(--danger)' : '#f59e0b');
                        tbody.innerHTML += `
                            <tr>
                                <td>${a.full_name}</td>
                                <td>${a.phone}</td>
                                <td>${a.age}/${a.gender}</td>
                                <td><span style="background:${statusColor}; color:white; padding:4px 10px; border-radius:100px; font-size:0.75rem; font-weight:700; text-transform:uppercase;">${a.status}</span></td>
                                <td>
                                    <button class="btn-premium" style="background:transparent; border:1px solid #cbd5e1; color:var(--text-muted); padding:4px 10px; font-size:0.7rem;" onclick="managePatient('${a.patient_id}', '${a.status === 'approved' ? 'reject' : 'approve'}')">
                                        ${a.status === 'approved' ? 'Disable' : 'Enable'}
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
            } catch (e) { console.error("Load accounts failed"); }
        }

        async function managePatient(pid, action) {
            const verb = (action === 'approve') ? "Approve" : "Reject";
            if (!confirm(`${verb} this patient account?`)) return;
            try {
                const response = await fetch('php/api/approve_patient.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ patient_id: pid, action: action })
                });
                const result = await response.json();
                if (result.success) {
                    loadPendingPatients();
                    loadAllAccounts();
                    loadPatients();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (e) { alert("Action failed"); }
        }

        async function loadPatients() {
            try {
                const role = document.getElementById('userRoleDisplay').textContent;
                let url = 'php/api/get_patients.php';
                
                // For non-admins, strictly filter by their assigned window/department
                if (role !== 'ADMIN' && WINDOW_NUMBER) {
                    url += `?window=${WINDOW_NUMBER}`;
                } else if (role === 'ADMIN' && WINDOW_NUMBER) {
                    // Admin sees what they are currently controlling
                    url += `?window=${WINDOW_NUMBER}`;
                }
                
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    const tbody = document.getElementById('adminPatientTable'); tbody.innerHTML = '';
                    
                    let waitingCount = 0;
                    let servedCount = 0;

                    data.patients.forEach(p => {
                        if (p.status === 'waiting') waitingCount++;
                        else servedCount++;

                        let statusColor = p.account_status === 'approved' ? '#10b981' : (p.account_status === 'rejected' ? '#ef4444' : '#f59e0b');
                        let statusBg = p.account_status === 'approved' ? '#dcfce7' : (p.account_status === 'rejected' ? '#fee2e2' : '#fef3c7');
                        
                        let statusBadge = `<span style="background:${statusBg}; color:${statusColor}; padding:6px 14px; border-radius:100px; font-size:0.75rem; font-weight:800; text-transform:uppercase; display:inline-block; margin-bottom:8px;">${p.account_status}</span>`;

                        let actionBtn = p.account_status === 'approved' 
                            ? `<button class="btn-premium" style="background:#fee2e2; color:#ef4444; border:none; padding:6px 12px; font-size:0.7rem; width:100%; margin-bottom:5px;" onclick="managePatient('${p.patient_id}', 'reject')"><i class="fas fa-user-slash"></i> Reject</button>`
                            : `<button class="btn-premium" style="background:#dcfce7; color:#10b981; border:none; padding:6px 12px; font-size:0.7rem; width:100%; margin-bottom:5px;" onclick="managePatient('${p.patient_id}', 'approve')"><i class="fas fa-user-check"></i> Approve</button>`;

                        let deleteBtn = `<button class="btn-premium" style="background:#f8fafc; color:#94a3b8; border:1px solid #e2e8f0; padding:4px 12px; font-size:0.7rem; width:100%;" onclick="deletePatient('${p.patient_id}')"><i class="fas fa-trash"></i> Delete</button>`;

                        tbody.innerHTML += `<tr>
                            <td style="font-weight:700;">${p.token_number}</td>
                            <td style="font-weight:600; color:#0f172a;">${p.patient_name}</td>
                            <td>${p.age}/${p.gender}</td>
                            <td>${p.phone}</td>
                            <td>${p.department}</td>
                            <td><span style="background:#fef3c7; color:#d97706; padding:6px 12px; border-radius:100px; font-size:0.75rem; font-weight:700;">${p.urgency}</span></td>
                            <td><span style="background:#f1f5f9; color:#475569; padding:6px 12px; border-radius:100px; font-size:0.75rem; font-weight:700;">${p.payment_status}</span></td>
                            <td style="text-align:center; min-width:130px;">
                                ${statusBadge}
                                ${actionBtn}
                                ${deleteBtn}
                            </td>
                        </tr>`;
                    });

                    document.getElementById('totalWaitingCount').textContent = waitingCount;
                    document.getElementById('totalServedCount').textContent = servedCount;
                }
            } catch (e) { console.error("Load failed"); }
        }

        async function deletePatient(pid) {
            if (!confirm("Are you sure you want to PERMANENTLY DELETE this patient and all their queue history?")) return;
            try {
                const response = await fetch('php/api/delete_patient.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ patient_id: pid })
                });
                const result = await response.json();
                if (result.success) {
                    alert("Patient deleted successfully!");
                    loadPendingPatients();
                    loadAllAccounts();
                    loadPatients();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (e) { alert("Delete failed"); }
        }
        async function setManualToken() {
            let win = WINDOW_NUMBER;
            if (!win) { alert("No window selected."); return; }
            
            const newToken = prompt("Enter number:");
            if (!newToken) return;
            try {
                const response = await fetch('php/api/set_current_token.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ token: newToken, window_number: win }) });
                const result = await response.json();
                if (result.success) { 
                    document.getElementById('activeNumber').textContent = newToken;
                    document.getElementById('transferContainer').style.display = 'none';
                }
                else { alert("Error: " + result.message); }
            } catch (e) { alert("Failed."); }
        }

        async function callNext() {
            let win = WINDOW_NUMBER;
            if (!win) { alert("No window selected."); return; }
            
            try {
                const response = await fetch('php/api/call_next.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ window_number: win }) });
                const result = await response.json();
                if (result.success) { 
                    document.getElementById('activeNumber').textContent = result.token; 
                    CURRENT_PATIENT_ID = result.patient_id; 
                    document.getElementById('transferContainer').style.display = 'block';
                }
                else { 
                    alert(result.message); 
                    document.getElementById('transferContainer').style.display = 'none';
                }
            } catch (error) { console.error("Call failed"); }
        }
        function logout() { window.location.href = 'php/api/logout.php'; }
        checkSession();

        // Auto-refresh the dashboard every 5 seconds to show new patients immediately
        setInterval(() => {
            if (document.getElementById('adminDashboard').style.display === 'block' || 
                document.getElementById('staffControls').style.display !== 'none') {
                loadPatients();
                loadPendingPatients();
            }
        }, 5000);
    </script>
</body>
</html>
