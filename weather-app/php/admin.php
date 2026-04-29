<?php
require_once 'db.php';

// --- CUSTOM SMTP MAILER (No PHPMailer Required) ---
function send_smtp_email($to, $subject, $message, $from_email, $password) {
    $host = 'ssl://smtp.gmail.com';
    $port = 465;
    
    $socket = @fsockopen($host, $port, $errno, $errstr, 15);
    if (!$socket) { return false; }
    
    $send = function($cmd) use ($socket) {
        if ($cmd !== '') { fwrite($socket, $cmd . "\r\n"); }
        $res = '';
        while ($str = fgets($socket, 515)) {
            $res .= $str;
            if (substr($str, 3, 1) == ' ') break;
        }
        return $res;
    };
    
    $send(''); // Read greeting
    $send('EHLO localhost');
    $send('AUTH LOGIN');
    $send(base64_encode($from_email));
    $send(base64_encode($password));
    $send("MAIL FROM: <$from_email>");
    $send("RCPT TO: <$to>");
    $send("DATA");
    
    // Build Headers
    $headers = "From: SkyCast Weather <$from_email>\r\n";
    $headers .= "To: <$to>\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // HTML Wrapper for better styling
    $htmlMessage = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;'>
            <div style='background: #3b82f6; color: white; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>SkyCast Weather Update ☀️🌧️</h2>
            </div>
            <div style='padding: 30px; color: #1e293b; line-height: 1.6; font-size: 16px;'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            <div style='background: #f8fafc; color: #64748b; padding: 15px; text-align: center; font-size: 12px;'>
                You received this email because you subscribed to SkyCast updates.<br>
                &copy; " . date("Y") . " SkyCast. All rights reserved.
            </div>
        </div>
    ";

    $payload = $headers . "\r\n" . $htmlMessage . "\r\n.";
    $res = $send($payload);
    $send('QUIT');
    fclose($socket);
    
    return (strpos($res, '250') !== false);
}
// ---------------------------------------------------

// --- OBSERVER PATTERN IMPLEMENTATION ---
interface Observer {
    public function update($subject);
}

interface Subject {
    public function attach(Observer $observer);
    public function detach(Observer $observer);
    public function notify();
}

class WeatherNotifier implements Subject {
    private $observers = [];
    public $message = "";
    public $subjectLine = "";
    public $successCount = 0;
    public $failCount = 0;

    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    public function detach(Observer $observer) {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function sendAlert($subjectLine, $message) {
        $this->subjectLine = $subjectLine;
        $this->message = $message;
        $this->notify();
    }
}

class EmailSubscriber implements Observer {
    private $email;
    private $admin_email = 'fekemark6@gmail.com';
    private $app_password = 'zedupswrsssbmokc';

    public function __construct($email) {
        $this->email = $email;
    }

    public function update($subject) {
        // Send email via Gmail SMTP
        $success = send_smtp_email($this->email, $subject->subjectLine, $subject->message, $this->admin_email, $this->app_password);
        
        if ($success) {
            $subject->successCount++;
        } else {
            $subject->failCount++;
        }
    }
}
// --- END OBSERVER PATTERN ---

$notificationSent = false;
$sentCount = 0;
$failedCount = 0;

// Handle Sending Notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $subjectLine = trim($_POST['subject']);
    $messageBody = trim($_POST['message']);

    $notifier = new WeatherNotifier();

    // Fetch all subscribers
    $res = $conn->query("SELECT email FROM subscribers");
    while ($row = $res->fetch_assoc()) {
        $subscriber = new EmailSubscriber($row['email']);
        $notifier->attach($subscriber);
    }

    // Trigger Notification
    $notifier->sendAlert($subjectLine, $messageBody);
    
    $notificationSent = true;
    $sentCount = $notifier->successCount;
    $failedCount = $notifier->failCount;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM subscribers WHERE id = $id");
    header("Location: admin.php");
    exit;
}

// Fetch all subscribers for the table
$result = $conn->query("SELECT * FROM subscribers ORDER BY subscribed_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyCast Admin - Subscribers</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
        body { background: #f0f5ff; padding: 3rem 1rem; color: #1e293b; }
        .container { 
            max-width: 1000px; margin: auto; background: white; padding: 2.5rem; 
            border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }
        h1 { margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; color: #3b82f6; }
        .grid-layout { display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; }
        
        .left-panel { display: flex; flex-direction: column; gap: 1.5rem; }
        .stat-box { 
            background: #f8fafc; padding: 1.5rem; border-radius: 12px; 
            text-align: center; border: 1px solid #e2e8f0;
        }
        .stat-box h3 { font-size: 2.5rem; color: #3b82f6; }
        .stat-box p { color: #64748b; font-weight: 500; }
        
        .notify-box { background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; }
        .notify-box h3 { margin-bottom: 1rem; color: #1e293b; font-size: 1.2rem; display: flex; align-items: center; gap: 8px;}
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; color: #64748b; }
        .form-control { width: 100%; padding: 0.8rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-family: inherit;}
        .form-control:focus { border-color: #3b82f6; }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .btn-send { 
            width: 100%; background: #3b82f6; color: white; border: none; 
            padding: 1rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;
        }
        .btn-send:hover { background: #2563eb; }
        
        .alert-success { background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;}
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;}

        .right-panel { background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1.2rem 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;}
        tr:hover { background: #f1f5f9; }
        .email-text { font-weight: 500; color: #1e293b; }
        .date-text { color: #64748b; font-size: 0.95rem; }
        .btn-delete { 
            color: #ef4444; text-decoration: none; padding: 0.5rem 0.8rem; 
            background: #fee2e2; border-radius: 6px; font-weight: 500; 
            display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; font-size: 0.9rem;
        }
        .btn-delete:hover { background: #ef4444; color: white; }

        @media (max-width: 768px) {
            .grid-layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fa-solid fa-shield-halved"></i> SkyCast Admin Dashboard</h1>
        
        <?php if($notificationSent): ?>
            <?php if($sentCount > 0): ?>
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i> 
                Real Email successfully sent to <strong><?= $sentCount ?></strong> subscribers via Gmail SMTP!
            </div>
            <?php endif; ?>
            <?php if($failedCount > 0): ?>
            <div class="alert-error">
                <i class="fa-solid fa-circle-xmark"></i> 
                Failed to send email to <strong><?= $failedCount ?></strong> subscribers.
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="grid-layout">
            <div class="left-panel">
                <div class="stat-box">
                    <h3><?= $result->num_rows ?></h3>
                    <p>Total Subscribers</p>
                </div>

                <div class="notify-box">
                    <h3><i class="fa-solid fa-paper-plane" style="color: #3b82f6;"></i> Send Real Email Alert</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Email Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Heavy Rain Warning!" required>
                        </div>
                        <div class="form-group">
                            <label>Message Content</label>
                            <textarea name="message" class="form-control" placeholder="Type your weather update here..." required></textarea>
                        </div>
                        <button type="submit" name="send_notification" class="btn-send">
                            <i class="fa-solid fa-envelope-open-text"></i> Send Email to All
                        </button>
                    </form>
                </div>
            </div>

            <div class="right-panel">
                <table>
                    <thead>
                        <tr>
                            <th>Email Address</th>
                            <th>Subscribed Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="email-text"><i class="fa-regular fa-envelope" style="color: #94a3b8; margin-right: 8px;"></i> <?= htmlspecialchars($row['email']) ?></td>
                            <td class="date-text"><?= date("M j, Y", strtotime($row['subscribed_at'])) ?></td>
                            <td>
                                <a href="?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this subscriber?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if($result->num_rows == 0): ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding: 3rem; color: #64748b;">
                                <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i><br>
                                No subscribers yet.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
