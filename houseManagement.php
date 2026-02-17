<?php
require_once "config/database.php";

// Get HouseID from URL
if (!isset($_GET['HouseID']) || empty($_GET['HouseID'])) {
    die("No House ID provided.");
}

$houseID = intval($_GET['HouseID']);

// Fetch house info (including farm info if needed)
try {
    $stmtHouse = $pdo->prepare("
        SELECT h.HouseID, h.MeterSquaredArea, f.FarmName
        FROM Houses h
        JOIN Farm f ON h.FarmID = f.FarmID
        WHERE h.HouseID = ?
    ");
    $stmtHouse->execute([$houseID]);
    $house = $stmtHouse->fetch(PDO::FETCH_ASSOC);

    if (!$house) {
        die("House not found.");
    }

    // Fetch crop numbers for this house
    $stmtCrops = $pdo->prepare("
        SELECT CropNumberID, TotalDayOldChickenPlacement, StartPlaceDateAsAge0
        FROM CropNumber
        WHERE HouseID = ?
        ORDER BY StartPlaceDateAsAge0 DESC
    ");
    $stmtCrops->execute([$houseID]);
    $crops = $stmtCrops->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>House <?php echo $house['HouseID']; ?> Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            text-align: center;
            padding-top: 50px;
        }
        .box {
            background: white;
            width: 700px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1, h2 {
            color: #2c3e50;
        }
        .crop-list {
            list-style: none;
            padding: 0;
        }
        .crop-item {
            margin: 15px 0;
            padding: 10px;
            background-color: #ecf0f1;
            border-radius: 5px;
        }
        .house-button {
            display: inline-block;
            background-color: #2980b9;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .house-button:hover {
            background-color: #1f6391;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>🏠 House <?php echo $house['HouseID']; ?> Management</h1>
    <h2>Farm: <?php echo htmlspecialchars($house['FarmName']); ?></h2>
    <p>Area: <?php echo $house['MeterSquaredArea']; ?> m²</p>

    <?php if (!empty($crops)) : ?>
        <h2>Crop Numbers</h2>
        <ul class="crop-list">
            <?php foreach ($crops as $crop) : ?>
                <li class="crop-item">
                    CropNumber ID: <?php echo $crop['CropNumberID']; ?><br>
                    Total Day-Old Chickens Placed: <?php echo $crop['TotalDayOldChickenPlacement']; ?><br>
                    Start Date (Age 0): <?php echo $crop['StartPlaceDateAsAge0']; ?><br>
            
            
            <!--Coment 1) THe code below is the button used to take the user to the information needed for day 0 information input
            Notes to change code here below later:
            1)THIS SHOULD BE PLACED INTO AN IF STAEMENT LATER BECAUSE IT SHOULD ONLY APPEAR IF THIS IS DAY 0, a new crop just sterted
            2)A different button should be used for the other days, and it should take the user to a different page that is used for inputting information for the other days 
            -->
                <a class="house-button" href="cropDay0InputInfo.php?CropNumberID=<?php echo $crop['CropNumberID']; ?>">
                Enter info for day 0 for this crop
                </a>
            <!--End of code talking about in the above comment 1) -->

                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
    <p>No crop numbers found for this house.</p>
    <a class="house-button" href="startAcropInAhouse.php?HouseID=<?php echo $house['HouseID']; ?>">
        Place Crop in this house
    </a>
    <?php endif; ?>

</div>

</body>
</html>