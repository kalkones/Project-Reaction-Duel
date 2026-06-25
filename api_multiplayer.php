<?php
header('Content-Type: application/json');
require 'db.php';
$pdo = getDB();
$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? '';
$user = $data['username'] ?? '';
$roomId = $data['room_id'] ?? null;

if ($action === 'find_match') {
    $pdo->query("DELETE FROM rooms WHERE player1 = '$user' OR player2 = '$user'");
    $stmt = $pdo->query("SELECT * FROM rooms WHERE status = 'waiting' LIMIT 1");
    $room = $stmt->fetch();
    if ($room) {
        $pdo->query("UPDATE rooms SET player2 = '$user', status = 'playing' WHERE room_id = " . $room['room_id']);
        echo json_encode(["success" => true, "room_id" => $room['room_id'], "is_host" => false, "opponent" => $room['player1']]);
    } else {
        $pdo->query("INSERT INTO rooms (player1) VALUES ('$user')");
        echo json_encode(["success" => true, "room_id" => $pdo->lastInsertId(), "is_host" => true, "opponent" => null]);
    }
} 
elseif ($action === 'check_room') {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->execute([$roomId]);
    echo json_encode(["success" => true, "room" => $stmt->fetch()]);
} 
elseif ($action === 'set_ready') {
    $col = $data['is_host'] ? 'p1_ready' : 'p2_ready';
    $pdo->query("UPDATE rooms SET $col = 1 WHERE room_id = $roomId");
    echo json_encode(["success" => true]);
} 
elseif ($action === 'leave_room') {
    $pdo->query("DELETE FROM rooms WHERE room_id = $roomId");
    echo json_encode(["success" => true]);
}
?>