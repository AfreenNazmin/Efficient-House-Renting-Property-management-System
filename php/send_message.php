<?php
session_start();
if(!isset($_SESSION['user_id'])) { http_response_code(401); exit; }
include 'config.php';

$user_id = $_SESSION['user_id'];
$conversation_id = isset($_POST['conversation_id']) ? intval($_POST['conversation_id']) : null;
$subject = trim($_POST['subject'] ?? '');
$body = trim($_POST['body'] ?? '');
$other_user_id = isset($_POST['other_user_id']) ? intval($_POST['other_user_id']) : null; // if creating new

if($conversation_id === null && $other_user_id){
    // create conversation
    $stmt = $conn->prepare("INSERT INTO conversations (subject) VALUES (?)");
    $stmt->bind_param("s", $subject);
    $stmt->execute();
    $conversation_id = $stmt->insert_id;
    $stmt->close();

    // add participants
    $stmt = $conn->prepare("INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?), (?, ?)");
    $stmt->bind_param("iiii", $conversation_id, $user_id, $conversation_id, $other_user_id);
    $stmt->execute();
    $stmt->close();
}

// handle optional attachment upload here (validate type & size), set $attachment_path or NULL

$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, body, attachment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $conversation_id, $user_id, $body, $attachment_path);
$stmt->execute();
$stmt->close();

// optionally update participants.last_read for sender
$stmt = $conn->prepare("UPDATE conversation_participants SET last_read = NOW() WHERE conversation_id = ? AND user_id = ?");
$stmt->bind_param("ii", $conversation_id, $user_id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'conversation_id' => $conversation_id]);
?>
<input type="file" name="attachment" accept="image/*,application/pdf">
