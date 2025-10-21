<?php
include 'config.php';
$result = $conn->query("SELECT * FROM rental_requests ORDER BY request_date DESC");
?>

<h2>Rental Requests</h2>
<table border="1" cellpadding="8">
<tr><th>Property</th><th>Tenant</th><th>Document</th><th>Status</th><th>Action</th></tr>

<?php while($r = $result->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($r['property_name']) ?></td>
  <td><?= htmlspecialchars($r['tenant_name']) ?></td>
  <td>
    <?php if($r['document_path']): ?>
      <a href="<?= $r['document_path'] ?>" target="_blank">📄 View PDF</a>
    <?php else: ?>
      No File
    <?php endif; ?>
  </td>
  <td><?= $r['status'] ?></td>
  <td>
    <a href="update_request.php?id=<?= $r['id'] ?>&status=approved">✅ Approve</a> |
    <a href="update_request.php?id=<?= $r['id'] ?>&status=rejected">❌ Reject</a>
  </td>
</tr>
<?php endwhile; ?>
</table>
