<?php
require_once "config/database.php"; // your database connection

// ===============================
// Validate CropNumberID
// ===============================
if (!isset($_GET['CropNumberID']) || !is_numeric($_GET['CropNumberID'])) {
    die("Invalid Crop Number ID.");
}

$cropNumberID = (int)$_GET['CropNumberID'];

// ===============================
// Fetch Required Data From DB
// ===============================
$sql = "
    SELECT 
        cn.StartPlaceDateAsAge0,
        cn.TotalDayOldChickenPlacement,
        cn.HouseID,
        h.MeterSquaredArea
    FROM CropNumber cn
    JOIN Houses h ON cn.HouseID = h.HouseID
    WHERE cn.CropNumberID = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$cropNumberID]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("Crop not found.");
}


$data = $result;

$startDate = $data['StartPlaceDateAsAge0'];
$totalBirds = $data['TotalDayOldChickenPlacement'];
$meterSquared = $data['MeterSquaredArea'];
$houseID = $data['HouseID'];


// ===============================
// Handle Form Submission
// ===============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $bodyWeight = (float)$_POST['BodyWeight_g'];

    // ===============================
    // Calculations
    // ===============================

    $birdsPerMeterSquared = $totalBirds / $meterSquared;

    $kgPerMeterSquared = ($bodyWeight * $birdsPerMeterSquared) / 1000;

    // ===============================
    // Insert Into CycleDays
    // ===============================

    $insert = "
        INSERT INTO CycleDays (
            CropNumberID,
            DateOfCycleDay,
            BirdAge,
            BroilerBirds,
            DailyMortalitiesTotal,
            CummMortality_ACTpercent,
            CummMortality_STDpercent,
            BirdsSold,
            SoldWeight,
            BodyWeight_g,
            VarToStandardPercent,
            AverageDailyGain_ACT,
            AverageDailyGain_STD,
            BirdsPerMeterSquared,
            KgPerMeterSquared,
            FeedIntake_ACTUAL,
            FeedIntake_STD,
            VarToSTD_IntakePercent,
            CumFeedIntake_ACTUAL,
            CumFeed_STD,
            VarToSTD_CumFeedIntakePercent,
            FCR_ACT,
            FCR_STD,
            DailyWaterIntake_ACTUAL,
            DailyWaterIntake_STD,
            DailyWaterIntakeVarToSTDPercent,
            WaterFeedRatio,
            CumWaterIntake_ACTUAL,
            CumWaterIntake_STD,
            CumWaterIntakeVarToSTDPercent,
            CumWaterFeedRatio
        )
        VALUES (
            ?, ?, 0, ?, 
            0, 0, 0, 0, 0,
            ?, 0, 0, 0,
            ?, ?, 
            0, 0, 0,
            0, 0, 0,
            0, 0,
            0, 0, 0,
            0,
            0, 0, 0,
            0
        )
    ";

    $stmtInsert = $pdo->prepare($insert);

$stmtInsert->bindParam(1, $cropNumberID);
$stmtInsert->bindParam(2, $startDate);
$stmtInsert->bindParam(3, $totalBirds);
$stmtInsert->bindParam(4, $bodyWeight);
$stmtInsert->bindParam(5, $birdsPerMeterSquared);
$stmtInsert->bindParam(6, $kgPerMeterSquared);

$stmtInsert->execute();

   // $stmtInsert->execute();

    // Redirect after success
    header("Location: houseManagement.php?HouseID=" . $houseID);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Day 0 Input</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h2>Day 0 Input</h2>

    <form method="POST">
        <div class="form-group">
            <label>Average Body Weight of 1 Bird (grams)</label>
            <input type="number" step="0.01" name="BodyWeight_g" required>
        </div>

        <button type="submit" class="btn-primary">
            Save Day 0 Data
        </button>
    </form>

</div>

</body>
</html>
