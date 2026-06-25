<?php
header('Content-Type: application/json');
require 'db.php';
$pdo = getDB();

// OTOMATIS MEMBUAT TABEL CHAT JIKA BELUM ADA
$pdo->exec("CREATE TABLE IF NOT EXISTS chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';

if ($action === 'register') {
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$data['username'], $data['email'], $data['password']]);
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Username/Email sudah terdaftar!"]);
    }
} 
elseif ($action === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND password = ?");
    $stmt->execute([$data['id'], $data['id'], $data['password']]);
    $user = $stmt->fetch();
    if ($user) {
        echo json_encode(["success" => true, "user" => [
            "username" => $user['username'], "level" => $user['level'], 
            "totalXP" => $user['total_xp'], "bestTime" => $user['best_time'], "icon" => $user['icon']
        ]]);
    } else {
        echo json_encode(["success" => false, "message" => "Akun salah!"]);
    }
}
elseif ($action === 'save_session') {
    try {
        // PAKSA JADI ANGKA (INT) AGAR MYSQL TIDAK ERROR
        $score = (int)($data['score'] ?? 0);
        $avgTime = (int)($data['avgTime'] ?? 0);
        $bestTime = (int)($data['bestTime'] ?? 0);
        $xp = (int)($data['xp'] ?? 0);
        $newLevel = (int)($data['newLevel'] ?? 1);
        $mode = $data['mode'] ?? 'Unknown';

        $stmt = $pdo->prepare("INSERT INTO history (username, score, avg_time, best_time, xp_earned, mode) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['username'], $score, $avgTime, $bestTime, $xp, $mode]);
        
        $stmt2 = $pdo->prepare("UPDATE users SET total_xp = total_xp + ?, level = ?, best_time = CASE WHEN best_time = 0 OR ? < best_time THEN ? ELSE best_time END WHERE username = ?");
        $stmt2->execute([$xp, $newLevel, $bestTime, $bestTime, $data['username']]);
        
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
elseif ($action === 'get_dashboard') {
    $lb = $pdo->query("SELECT username, total_xp as score, best_time as bestTime FROM users ORDER BY total_xp DESC LIMIT 10")->fetchAll();
    $hist = $pdo->query("SELECT * FROM history ORDER BY created_at DESC LIMIT 20")->fetchAll();
    echo json_encode(["success" => true, "leaderboard" => $lb, "history" => $hist]);
}
// FITUR CHAT GLOBAL REAL-TIME
elseif ($action === 'send_chat') {
    $stmt = $pdo->prepare("INSERT INTO chats (username, message) VALUES (?, ?)");
    $stmt->execute([$data['username'], $data['message']]);
    echo json_encode(["success" => true]);
}
elseif ($action === 'get_chats') {
    // Ambil 30 chat terbaru
    $stmt = $pdo->query("SELECT * FROM (SELECT * FROM chats ORDER BY id DESC LIMIT 30) sub ORDER BY id ASC");
    echo json_encode(["success" => true, "chats" => $stmt->fetchAll()]);
}
?>