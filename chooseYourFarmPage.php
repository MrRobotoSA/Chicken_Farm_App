<?php
require_once "config/database.php";

// Fetch farms from database
try {
    $stmt = $pdo->query("SELECT FarmID, FarmName FROM farm");
    $farms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching farms: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Choose Your Farm</title>
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
        .farm-list {
            list-style: none;
            padding: 0;
        }
        .farm-item {
            margin: 15px 0;
        }
        .farm-button {
            display: inline-block;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .farm-button:hover {
            background-color: #1e8449;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>🐔 Choose Your Farm</h1>

    <?php if (!empty($farms)) : ?>
        <ul class="farm-list">
            <?php foreach ($farms as $farm) : ?>
                <li class="farm-item">
                    <strong><?php echo htmlspecialchars($farm['FarmName']); ?></strong>
                    <a class="farm-button" href="farmDashboard.php?FarmID=<?php echo $farm['FarmID']; ?>">
                        Manage Farm
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No farms available. Please add a farm first.</p>
    <?php endif; ?>
</div>