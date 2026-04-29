<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | Nigist Elin Hospital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: var(--bg-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 3.5rem;
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            text-align: center;
            box-shadow: var(--shadow-premium);
        }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; margin-bottom: 8px; color: var(--text-muted); font-size: 0.9rem; font-weight: 600; }
        .input-group input { width: 100%; padding: 1rem; border-radius: 12px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-icon" style="margin: 0 auto 2rem;"><i class="fas fa-lock"></i></div>
        <h1 style="font-family: 'Outfit'; color: #0c4a6e;">Staff Portal</h1>
        <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Access Management System</p>
        <form id="loginForm">
            <div class="input-group"><label>Username</label><input type="text" id="username" placeholder="e.g. admin" required></div>
            <div class="input-group" style="margin-bottom: 3rem;"><label>Password</label><input type="password" id="password" placeholder="••••••••" required></div>
            <button type="submit" class="btn-premium btn-primary" style="width: 100%; justify-content: center;">LOGIN <i class="fas fa-arrow-right"></i></button>
        </form>
        <p id="errorMsg" style="color: var(--danger); margin-top: 1.5rem; display: none;"></p>
    </div>
    <script>
        document.getElementById('loginForm').onsubmit = async (e) => {
            e.preventDefault();
            const user = document.getElementById('username').value;
            const pass = document.getElementById('password').value;
            const error = document.getElementById('errorMsg');
            try {
                const response = await fetch('php/api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: user, password: pass })
                });
                const result = await response.json();
                if (result.success) { window.location.href = 'staff.php'; } 
                else { error.textContent = result.message; error.style.display = 'block'; }
            } catch (err) { error.textContent = "Server connection error."; error.style.display = 'block'; }
        };
    </script>
</body>
</html>
