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
echo "<br>";
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

    $stmtFood = $pdo->prepare($insertFood);
    $stmtFood->execute([
        $CycleDaysID,
        $BinClosingStockReading
    ]);

    echo "food saved successfully.";

// After saving both water and feed readings, redirect back to the house management page for this crop
//The below SQL code is to get the corredct HouseID for the redirect after saving the water and feed readings, we need this to redirect back to the correct house management page for this crop after saving the water and feed readings for day 0
    $sqlHouseID = "
        SELECT HouseID
        FROM CropNumber
        WHERE CropNumberID = ?
    ";

    $stmtHouseID = $pdo->prepare($sqlHouseID);
    $stmtHouseID->execute([$cropNumberID]);
    $houseResult = $stmtHouseID->fetch(PDO::FETCH_ASSOC);

    if (!$houseResult) {
        die("House not found.");
    }

    $houseID = $houseResult['HouseID'];


    //IF all is good then this one line will redirect back to the house management page
 header("Location: houseManagement.php?HouseID=" . $houseID . "&CropNumberID=" . $cropNumberID);
    exit();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
}

?>
<form method="POST">
    <label>Water Meter Reading at Start of cycle (L)</label>
    <input type="number" step="0.01" name="WaterReadingATM" required>
    <br>
    <br>
    <label>Feed Bin Stock Reading at Start of cycle (Kg)</label>
    <input type="number" step="0.01" name="BinClosingStockReading" required>
    <br>
    <br>
    <button type="submit">Save Water and Feed Readings</button>
</form>