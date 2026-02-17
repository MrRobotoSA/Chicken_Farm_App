<?php
require_once "config/database.php";

// Get the farm ID from the URL
if (!isset($_GET['FarmID']) || empty($_GET['FarmID'])) {
    die("No farm ID provided.");
}

$farmID = intval($_GET['FarmID']);

// Fetch farm info
try {
    $stmtFarm = $pdo->prepare("SELECT FarmName FROM Farm WHERE FarmID = ?");
    $stmtFarm->execute([$farmID]);
    $farm = $stmtFarm->fetch(PDO::FETCH_ASSOC);

    if (!$farm) {
        die("Farm not found.");
    }

    // Fetch houses for this farm
    $stmtHouses = $pdo->prepare("SELECT HouseID, MeterSquaredArea FROM Houses WHERE FarmID = ?");
    $stmtHouses->execute([$farmID]);
    $houses = $stmtHouses->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($farm['FarmName']); ?> Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            text-align: center;
            padding-top: 50px;
        }
        .box {
            background: white;
            width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
        }
        .house-list {
            list-style: none;
            padding: 0;
        }
        .house-item {
            margin: 15px 0;
        }
        .house-button {
            display: inline-block;
            background-color: #e67e22;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .house-button:hover {
            background-color: #d35400;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>🐔 <?php echo htmlspecialchars($farm['FarmName']); ?> Dashboard</h1>

    <?php if (!empty($houses)) : ?>
        <ul class="house-list">
            <?php foreach ($houses as $house) : ?>
                <li class="house-item">
                    House ID: <?php echo $house['HouseID']; ?> |
                    Area: <?php echo $house['MeterSquaredArea']; ?> m²
                    <!-- Example button for future house management -->
                    <a class="house-button" href="houseManagement.php?HouseID=<?php echo $house['HouseID']; ?>">
                        Manage House
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No houses found for this farm.</p>
    <?php endif; ?>
</div>

</body>
</html>