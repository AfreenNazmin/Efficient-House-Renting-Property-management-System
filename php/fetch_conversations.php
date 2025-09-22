<?php
session_start();
if(!isset($_SESSION['user_id'])) exit;
include 'config.php';
$uid = $_SESSION['user_id'];

$sql = "
SELECT c.id AS conversation_id, c.subject,
       m.body AS last_message, m.created_at AS last_time,
       SUM(CASE WHEN (messages.is_read = 0 AND messages.sender_id != ?) THEN 1 ELSE 0 END) AS unread_count
FROM conversations c
JOIN messages m ON m.id = (
    SELECT id FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1
)
JOIN conversation_participants cp ON cp.conversation_id = c.id
LEFT JOIN messages ON messages.conversation_id = c.id
WHERE cp.user_id = ?
GROUP BY c.id
ORDER BY last_time DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $uid, $uid);
$stmt->execute();
$res = $stmt->get_result();
$out = $res->fetch_all(MYSQLI_ASSOC);
echo json_encode($out);
?>
