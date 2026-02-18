<?php
//NOTE TO SELF ABOUT THIS PAGE:
//This page needs HTML structuring and updats still at the bottom for the html form


require_once "config/database.php"; // your database connection
// ===============================
// Validate Cycle and 
// ===============================
if (!isset($_GET['CropNumberID']) || !is_numeric($_GET['CropNumberID'])) {
    die("Invalid Crop Number ID.");
}

$cropNumberID = (int)$_GET['CropNumberID'];

// ===============================
// Fetch Required Data From DB, we need the CycleDaysID, CropNumberID that mathces where the BirdAge = 0 
// This is so we can isolate the first day to adjust the water and feed intake for the first day of the cycle in the database for this crop only
// ===============================
$sql = "
    SELECT 
        CycleDaysID,
        CropNumberID,
        BirdAge,
        DateOfCycleDay
    FROM CycleDays
    WHERE CropNumberID = ? AND BirdAge = 0
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$cropNumberID]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("Crop not found.");
}


$data = $result;

$CycleDaysID = $data['CycleDaysID'];
$BirdAge = $data['BirdAge'];
$DateOfCycleDay = $data['DateOfCycleDay'];



//-------------------------------------------------------------------------------
//Testing the data variables below
// ------ Remove this later ------
echo "This cycles crop number " . $cropNumberID . "<br>";
echo "This cycles CycleDaysID is " . $CycleDaysID . "<br>";
echo "This cycles BirdAge is " . $BirdAge . "<br>";
echo "This cycles DateOfCycleDay is " . $DateOfCycleDay . "<br>";
// ------ Remove this later ------
//---------------------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $waterReadingATM = (float)$_POST['WaterReadingATM'];
    $BinClosingStockReading = (float)$_POST['BinClosingStockReading'];
try {
    //This is the sql code to enter info into just the waterreadingstable for day 0 of this specific cycle day
    $insertWater = "
        INSERT INTO WaterReadingsTable (
            CycleDaysID,
            WaterReadingATM,
            WaterUsage
        )
        VALUES (
            ?, ?, 0
        )
    ";

    $stmtWater = $pdo->prepare($insertWater);
    $stmtWater->execute([
        $CycleDaysID,
        $waterReadingATM
    ]);

    echo "Water reading saved successfully.";

    //this code below is the sql code to insert just the feed reconinfo table for day 0 of this specific cycle day

    $insertFood = "
        INSERT INTO FeedReconInfo (
            CycleDaysID,
            BinClosingStockReading,
            TotalUsageBins
        )
        VALUES (
            ?, ?, 0
        )
    ";

    $stmtWater = $pdo->prepare($insertFood);
    $stmtWater->execute([
        $CycleDaysID,
        $BinClosingStockReading
    ]);

    echo "food saved successfully.";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
}

?>
<form method="POST">
    <label>Water Reading at Start of cycle</label>
    <input type="number" step="0.01" name="WaterReadingATM" required>
    <br>
    <br>
    <label>Feed Bin Stock Reading at Start of cycle</label>
    <input type="number" step="0.01" name="BinClosingStockReading" required>
    <br>
    <br>
    <button type="submit">Save Water and Feed Readings</button>
</form>