<?php
require_once "config/database.php";

//The below is to set the time zone to south africa for the date function used later in this page. 
date_default_timezone_set('Africa/Johannesburg'); // change to yours

// Get HouseID from URL
if (!isset($_GET['CropNumberID']) || empty($_GET['CropNumberID'])) {
    die("No Crop Number ID provided.");
}

// Get CropNumberID from URL
$CropNumberID = intval($_GET['CropNumberID']);

//This line below is just for debugging to see if we get the correct CropNumberID, 
//------------REMOVE THIS LATER----------------
echo "CropNumberID: " . $CropNumberID  . "<br>";
//------------REMOVE THIS ABOVE LATER----------

try {

//The below is the important sql statement that gets all the info from the previous days cycledays table row.
    $stmtCycleDayInfo = $pdo->prepare("
        SELECT CycleDaysID, CropNumberID, DateOfCycleDay, BirdAge, BroilerBirds, DailyMortalitiesTotal
        FROM cycledays
        WHERE CropNumberID = ?
        ORDER BY CycleDaysID DESC
        LIMIT 1
    ");
    $stmtCycleDayInfo->execute([$CropNumberID]);
    $cycleDayInfo = $stmtCycleDayInfo->fetch(PDO::FETCH_ASSOC);

    if (!$cycleDayInfo) {
        die("Cycle Day Info not found.");
    }


//The below SQL is to get the Total DAy Old Chicken Placement from the cropnumber table (the starting amount of stock for this crop)
    $stmtCropNumberInfo = $pdo->prepare("
        SELECT TotalDayOldChickenPlacement
        FROM cropnumber
        WHERE CropNumberID = ?
    ");
    $stmtCropNumberInfo->execute([$CropNumberID]);
    $cropNumberInfo = $stmtCropNumberInfo->fetch(PDO::FETCH_ASSOC);

    if (!$cropNumberInfo) {
        die("Crop Number Info not found.");
    }

//The below is to get the total birds sold for this crop number from the cycledays table, we will use this in the CummMortality_ACTpercent calculation later
$sqlTotalBirdsSold = "
    SELECT SUM(BirdsSold) AS TotalBirdsSold
    FROM CycleDays
    WHERE CropNumberID = ?
";

$stmtTotalBirdsSold = $pdo->prepare($sqlTotalBirdsSold);
$stmtTotalBirdsSold->execute([$CropNumberID]);

$TotalBirdsSoldData = $stmtTotalBirdsSold->fetch(PDO::FETCH_ASSOC);

$totalBirdsSold = $TotalBirdsSoldData['TotalBirdsSold'];

    
    } catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
    }

    //THE BELOW LINES ARE FOR DEBUGGING PURPOSES TO SEE IF WE ARE GETTING THE CORRECT INFO FROM THE DATABASE 
//----------------------REMOVE LATER------------------------------
echo "CycleDaysID: " . $cycleDayInfo['CycleDaysID'] . "<br>";
echo "CropNumberID: " . $cycleDayInfo['CropNumberID'] . "<br>";
echo "DateOfCycleDay: " . $cycleDayInfo['DateOfCycleDay'] . "<br>";
echo "BirdAge: " . $cycleDayInfo['BirdAge'] . "<br>";
//----------------------REMOVE LATER-------------------------------
echo "<br>";
echo "<br>";
echo "<br>";
echo "<h1>Below is debugging info to see if all variables are correct</h1>";
//----------------------REMOVE LATER-------------------------------
// THe below are all the variables that should be echoes to the page for debugging, if all is good REMOVE THIS then
//USE THE BELOW VARIABLES TO INSERT THE ROW INTO THE cycledays table next row





//VAR 1 is the CycleDaysID (this shoul auto be inserted)
$CropNumberID = $cycleDayInfo['CropNumberID'];
$DateOfCycleDay = date('Y-m-d');
$BirdAge = $cycleDayInfo['BirdAge'] + 1;
$BroilerBirds = $cycleDayInfo['BroilerBirds'] - $cycleDayInfo['DailyMortalitiesTotal']; // this is to take the previos days mortalities off the total amount of birds
//THE DAILY MORTALITIES NEED TO BE CAPTURED BY THE FORM HERE 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
$DailyMortalitiesTotal = (float)$_POST['DailyMortalitiesTotal'];
$BirdsSold = (float)$_POST['BirdsSold'];
}
//THE BELOW IS THE ACTUAL CUMM MORTALITY PERCENTAGE CALCULATION, IT TAKES THE TOTAL DAY OLD CHICKEN PLACEMENT AND TAKES OFF THE CURRENT AMOUNT OF BIRDS LEFT (WHICH IS THE PREVIOUS DAYS BIRDS MINUS THE DAILY MORTALITIES AND MINUS THE BIRDS SOLD) AND THEN DIVIDES THAT BY THE TOTAL DAY OLD CHICKEN PLACEMENT TO GET THE PERCENTAGE.
$CummMortality_ACTpercent= ($cropNumberInfo["TotalDayOldChickenPlacement"] - ($BroilerBirds - $DailyMortalitiesTotal-$BirdsSold) - $totalBirdsSold) / $cropNumberInfo["TotalDayOldChickenPlacement"] * 100; // this is the actual cumulative mortality percentage calculation
$CummMortality_ACTpercent = round($CummMortality_ACTpercent, 2); // round to 2 decimal places

/*-------------------------------------------------------------------------------------------
CONTINUE HERE!!!!!!---------------------------------------------------------------------------
----------------------------------------------------------------------------------------------
TO DO:
1) Above is data you have working
2) carry on here going forward from CummMortality_STDpercent column in the cycledays table 
3) Rememver bellow is just the variables as above for debuging purposes. 
4) Remember you have the Birds Sold totals.
5) IDEA MAKE THE ROSS STANDARDS ITS OWN TABLE that uses the birds age to calculate the standard for that days bird age ACT with the ROSS STANDARD
6) MAIN PLAN HERE --> ONCE ALL THESE VARIABLES ARE WORKING CORRECTLY THEN ADD THEM TO AN INSERT STATEMENT TO ENTER THE INFO IN THE DATABASE FOR that days cycle ID
-----------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------*/

//Echoed all the values from above for debugging purposes according to the variables above
echo "CropNumberID: " . $CropNumberID . "<br>";
echo "Date Of Cycle Day: " . $DateOfCycleDay . "<br>";
echo "Bird Age: " . $BirdAge . "<br>";
echo "Broiler Birds: " . $BroilerBirds . "<br>";
echo "Daily Mortalities Total: " . $DailyMortalitiesTotal . "<br>";
echo "Cumm Mortality ACT percent: " . $CummMortality_ACTpercent . "<br>";

echo "Birds Sold: " . $BirdsSold . "<br>";

//take this out after calculation
echo $cropNumberInfo["TotalDayOldChickenPlacement"]. "<br>";
echo $totalBirdsSold;

//---------------------------------------------------------------------------------------------
//-------------DEBUGGING INFO ENDS HERE-------------------------------------------------------


?>
<br>
<br>
<!-- The below is the form for daily inputs -->
<div class="container">
    <h2>Daily Input Form</h2>

    <form method="POST">
        <div class="form-group">
            <label>Daily Mortalities Total</label>
            <input type="number" step="1" min="0" name="DailyMortalitiesTotal" required>
        </div>

        <div class="form-group">
            <label>Birds Sold</label>
            <input type="number" step="1" min="0" name="BirdsSold" required>
        </div>

        <button type="submit" class="btn-primary">
            Save Daily Input Data
        </button>
    </form>

</div>
<br>