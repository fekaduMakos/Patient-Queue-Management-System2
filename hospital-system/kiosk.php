<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration | Nigist Elin Hospital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #f8fafc; color: var(--text-main); }
        .reg-card {
            max-width: 800px; margin: 4rem auto; background: white; border: 1px solid var(--border);
            border-top: 8px solid var(--primary); padding: 3rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-premium);
        }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 2rem; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-weight: 700; color: var(--text-muted); font-size: 0.9rem; }
        .form-group input, .form-group select { padding: 14px; border: 1px solid #e2e8f0; border-radius: 12px; }
        .btn-full { width: 100%; margin-top: 2.5rem; justify-content: center; padding: 1.5rem; font-size: 1.2rem; box-shadow: 0 10px 30px rgba(2, 132, 199, 0.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="reg-card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div class="logo-icon" style="margin: 0 auto 1.5rem;"><i class="fas fa-id-card"></i></div>
                <h1 style="font-family: 'Outfit'; color: #0c4a6e;">Patient Registration</h1>
                <p style="color: var(--primary); font-weight: 600;">NIGIST ELIN HOSPITAL | ንግሥተ ይህሊን ሆስፒታል</p>
            </div>
            <form id="regForm">
                <div class="form-grid">
                    <div class="form-group"><label>Full Name / ሙሉ ስም</label><input type="text" id="patient_name" required></div>
                    <div class="form-group"><label>Phone Number / ስልክ ቁጥር</label><input type="tel" id="phone" pattern="[0-9]{10}" minlength="10" maxlength="10" title="Please enter exactly 10 digits" required></div>
                    <div class="form-group"><label>Age / እድሜ</label><input type="number" id="age" required></div>
                    <div class="form-group"><label>Gender / ጾታ</label><select id="gender"><option value="Male">Male</option><option value="Female">Female</option></select></div>
                    <div class="form-group"><label>Patient ID (Optional)</label><input type="text" id="patient_id"></div>
                    <div class="form-group"><label>Department / ክፍል</label>
                        <select id="department">
                            <option value="Triage">Triage / ትሪያጅ</option>
                            <option value="OPD">OPD / የዶክተር ክፍል</option>
                            <option value="Pediatrics">Pediatrics / የህጻናት ክፍል</option>
                            <option value="Laboratory">Laboratory / ላብራቶሪ</option>
                            <option value="Pharmacy">Pharmacy / ፋርማሲ</option>
                            <option value="Radiology">Radiology / ራጅ</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Urgency</label><select id="urgency"><option value="Normal">Normal</option><option value="Urgent">Urgent</option><option value="Emergency">Emergency</option></select></div>
                    <div class="form-group"><label>Payment Status</label><select id="payment_status"><option value="Pending">Pending</option><option value="Paid">Paid</option></select></div>
                    <div class="form-group" style="grid-column: span 2;"><label>Create Portal Password</label><input type="password" id="pat_password" required></div>
                </div>
                <button type="submit" class="btn-premium btn-primary btn-full">REGISTER & GET TOKEN <i class="fas fa-ticket-alt"></i></button>
            </form>
        </div>
    </div>

    <!-- Ticket Modal -->
    <div id="ticketModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 2000; align-items: center; justify-content: center;">
        <div class="glass-card" style="background: white; padding: 4rem; text-align: center; width: 450px;">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--secondary);"></i>
            <h1 id="modalToken" style="font-size: 6rem; font-weight: 800; color: var(--primary); margin: 1rem 0;">A-101</h1>
            <p id="modalName" style="font-weight: 700; font-size: 1.2rem;"></p>
            <div style="background: var(--primary-light); padding: 10px; border-radius: 10px; margin-top: 15px; font-size: 0.85rem; color: var(--primary);">
                <i class="fas fa-info-circle"></i> Portal access is <strong>Pending</strong>. Admin will approve shortly.<br>
                የፖርታል አካውንትዎ ገና አልጸደቀም። አስተዳዳሪው በቅርቡ ያጸድቁታል።
            </div>
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 1.5rem;">
                <button onclick="window.location.reload()" class="btn-premium" style="background: #f1f5f9; box-shadow: none; flex: 1;">BACK TO HOME</button>
                <a href="patient_login.php" class="btn-premium" style="background: var(--secondary); color: white; text-decoration: none; flex: 1; display: flex; align-items: center; justify-content: center;">LOGIN PORTAL <i class="fas fa-sign-in-alt" style="margin-left: 8px;"></i></a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('regForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = {
                patient_name: document.getElementById('patient_name').value,
                phone: document.getElementById('phone').value,
                age: document.getElementById('age').value,
                gender: document.getElementById('gender').value,
                patient_id: document.getElementById('patient_id').value,
                department: document.getElementById('department').value,
                urgency: document.getElementById('urgency').value,
                payment_status: document.getElementById('payment_status').value,
                password: document.getElementById('pat_password').value,
                prefix: document.getElementById('department').value.charAt(0)
            };
            try {
                const response = await fetch('php/api/add_to_queue.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(formData) });
                const result = await response.json();
                if (result.success) { document.getElementById('modalToken').textContent = result.token; document.getElementById('modalName').textContent = result.name; document.getElementById('ticketModal').style.display = 'flex'; }
            } catch (err) { alert("Failed to register."); }
        };
    </script>
</body>
</html>
