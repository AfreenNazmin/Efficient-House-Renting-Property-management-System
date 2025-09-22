<?php
session_start();
if(!isset($_SESSION['user_id'])) exit;
include 'config.php';
$uid = $_SESSION['user_id'];
$conv = intval($_GET['conversation_id']);

// check participant
$stmt = $conn->prepare("SELECT 1 FROM conversation_participants WHERE conversation_id=? AND user_id=?");
$stmt->bind_param("ii", $conv, $uid);
$stmt->execute();
if($stmt->get_result()->num_rows === 0){ http_response_code(403); exit; }

$stmt = $conn->prepare("SELECT m.id, m.sender_id, m.body, m.attachment, m.created_at FROM messages m WHERE conversation_id=? ORDER BY created_at ASC");
$stmt->bind_param("i", $conv);
$stmt->execute();
$res = $stmt->get_result();
$messages = $res->fetch_all(MYSQLI_ASSOC);

// mark messages as read where sender != me
$stmt = $conn->prepare("UPDATE messages SET is_read=1 WHERE conversation_id=? AND sender_id!=?");
$stmt->bind_param("ii", $conv, $uid);
$stmt->execute();

echo json_encode($messages);
?>
