<?php
function getDB() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    
    // PHP akan otomatis mengambil password dari database yang terhubung di tab Variables
    $host = $_SERVER['MYSQLHOST'] ?? $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST');
    $port = $_SERVER['MYSQLPORT'] ?? $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT');
    $db   = $_SERVER['MYSQLDATABASE'] ?? $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE');
    $user = $_SERVER['MYSQLUSER'] ?? $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER');
    $pass = $_SERVER['MYSQLPASSWORD'] ?? $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD');

    // Mencegah PHP jalan kalau variabelnya kosong
    if (!$host || !$pass) {
        echo json_encode(["success" => false, "message" => "Variabel Database kosong! Pastikan sudah Add Reference di Railway."]);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        // Otomatis bikin tabel
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            username VARCHAR(50) PRIMARY KEY,
            password VARCHAR(100),
            email VARCHAR(100),
            level INT DEFAULT 1,
            total_xp INT DEFAULT 0,
            best_time INT DEFAULT 0,
            icon VARCHAR(50) DEFAULT 'fa-user'
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50),
            score INT,
            avg_time INT,
            best_time INT,
            xp_earned INT,
            mode VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS rooms (
            room_id INT AUTO_INCREMENT PRIMARY KEY,
            player1 VARCHAR(50) NOT NULL,
            player2 VARCHAR(50) DEFAULT NULL,
            p1_ready INT DEFAULT 0,
            p2_ready INT DEFAULT 0,
            status VARCHAR(20) DEFAULT 'waiting',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS chats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50),
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        return $pdo;

    } catch (\PDOException $e) {
        echo json_encode(["success" => false, "message" => "DB Error Detail: " . $e->getMessage()]);
        exit;
    }
}
?>