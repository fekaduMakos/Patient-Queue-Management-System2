<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal | Nigist Elin Hospital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background-color: var(--bg-main); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
        .portal-card { background: white; padding: 3rem; border-radius: 30px; width: 100%; max-width: 400px; box-shadow: var(--shadow-premium); text-align: center; }
    </style>
</head>
<body>
    <div class="portal-card">
        <div class="logo-icon" style="margin: 0 auto 1.5rem; background: var(--secondary);"><i class="fas fa-user-injured"></i></div>
        <h1 style="font-family: 'Outfit'; margin-bottom: 0.5rem; color: #0c4a6e;">Patient Portal</h1>
        <form id="patientLoginForm">
            <div style="text-align: left; margin-bottom: 20px;"><label style="font-weight: 700; font-size: 0.9rem; color: var(--text-muted);">Phone Number</label><input type="tel" id="patPhone" style="width: 100%; mt-8px" placeholder="09..." required></div>
            <div style="text-align: left; margin-bottom: 25px;"><label style="font-weight: 700; font-size: 0.9rem; color: var(--text-muted);">Password</label><input type="password" id="patPass" style="width: 100%; mt-8px" placeholder="••••••" required></div>
            <button type="submit" class="btn-premium btn-primary" style="width: 100%; justify-content: center; background: var(--secondary);">ACCESS PORTAL <i class="fas fa-sign-in-alt"></i></button>
        </form>
        <p id="errorTxt" style="color: var(--danger); margin-top: 1.5rem; display: none;"></p>
        <div style="margin-top: 2rem; border-top: 1px solid #f1f5f9; pt-2rem;"><p style="font-size: 0.9rem;">Register at <a href="kiosk.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Kiosk</a></p></div>
    </div>
    <script>
        document.getElementById('patientLoginForm').onsubmit = async (e) => {
            e.preventDefault();
            const phone = document.getElementById('patPhone').value; const pass = document.getElementById('patPass').value;
            try {
                const response = await fetch('php/api/patient_login.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ phone: phone, password: pass }) });
                const result = await response.json();
                if (result.success) { window.location.href = 'patient_dashboard.php'; }
                else { const err = document.getElementById('errorTxt'); err.textContent = result.message; err.style.display = 'block'; }
            } catch (err) { alert("Login failed."); }
        };
    </script>
</body>
</html>
