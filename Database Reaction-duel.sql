-- 1. Tabel Users untuk menyimpan data profil dan skor tertinggi
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    username VARCHAR(50) PRIMARY KEY,
    password VARCHAR(100),
    email VARCHAR(100),
    level INT DEFAULT 1,
    total_xp INT DEFAULT 0,
    best_time INT DEFAULT 0,
    icon VARCHAR(50) DEFAULT 'fa-user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel History untuk menyimpan riwayat setiap sesi permainan
CREATE TABLE IF NOT EXISTS history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    score INT,
    avg_time INT,
    best_time INT,
    xp_earned INT,
    mode VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabel Rooms untuk sistem multiplayer
CREATE TABLE IF NOT EXISTS rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    player1 VARCHAR(50) NOT NULL,
    player2 VARCHAR(50) DEFAULT NULL,
    p1_ready INT DEFAULT 0,
    p2_ready INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tabel Chats untuk fitur Chat Global
CREATE TABLE IF NOT EXISTS chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
DROP TABLE IF EXISTS rooms;
ALTER TABLE rooms ADD COLUMN IF NOT EXISTS spectators JSON DEFAULT NULL;
ALTER TABLE rooms ADD COLUMN game_log JSON DEFAULT NULL;

ALTER TABLE rooms ADD COLUMN  password VARCHAR(50) DEFAULT NULL;
ALTER TABLE rooms ADD COLUMN IF NOT EXISTS game_log JSON DEFAULT NULL;
ALTER TABLE rooms ADD COLUMN IF NOT EXISTS spectators JSON DEFAULT NULL;

ALTER TABLE rooms ADD COLUMN password VARCHAR(50) DEFAULT NULL;
ALTER TABLE rooms ADD COLUMN game_log JSON DEFAULT NULL;
ALTER TABLE rooms ADD COLUMN spectators JSON DEFAULT NULL;

SELECT * FROM rooms LIMIT 1;

ALTER TABLE rooms ADD COLUMN password VARCHAR(50) DEFAULT NULL;
ALTER TABLE rooms ADD COLUMN game_log JSON DEFAULT NULL;
ALTER TABLE rooms ADD COLUMN spectators JSON DEFAULT NULL;
ALTER TABLE rooms ADD COLUMN started_at TIMESTAMP NULL DEFAULT NULL;

DROP TABLE IF EXISTS rooms;
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    player1 VARCHAR(50) NOT NULL,
    player2 VARCHAR(50) DEFAULT NULL,
    p1_ready INT DEFAULT 0,
    p2_ready INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'waiting',
    spectators JSON DEFAULT NULL,
    game_log JSON DEFAULT NULL,
    password VARCHAR(50) DEFAULT NULL,
    started_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;