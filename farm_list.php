<?php
require_once "config/database.php";
include "layouts/header.php";

$stmt = $pdo->query("SELECT * FROM Farm ORDER BY FarmID DESC");
$farms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Farm List</h2>

<a href="farm_create.php">➕ Add New Farm</a>

<table border="1" cellpadding="10" cellspacing="0" width="100%">
    <tr>
        <th>ID</th>
        <th>Farm Name</th>
    </tr>

    <?php foreach ($farms as $farm): ?>
        <tr>
            <td><?php echo $farm["FarmID"]; ?></td>
            <td><?php echo htmlspecialchars($farm["FarmName"]); ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include "layouts/footer.php"; ?>