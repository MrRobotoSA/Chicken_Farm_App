<?php
require_once "config/database.php";

$sql = "SELECT h.HouseID, h.MeterSquaredArea, f.FarmName
        FROM Houses h
        JOIN Farm f ON h.FarmID = f.FarmID
        ORDER BY h.HouseID DESC";

$stmt = $pdo->query($sql);
$houses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>House List</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f6f9;
            padding: 50px;
        }
        table {
            width: 70%;
            margin: auto;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        a {
            display: block;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<a href="house_create.php">➕ Add New House</a>

<table>
    <tr>
        <th>ID</th>
        <th>Farm</th>
        <th>Area (m²)</th>
    </tr>

    <?php foreach ($houses as $house): ?>
        <tr>
            <td><?php echo $house["HouseID"]; ?></td>
            <td><?php echo htmlspecialchars($house["FarmName"]); ?></td>
            <td><?php echo $house["MeterSquaredArea"]; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>