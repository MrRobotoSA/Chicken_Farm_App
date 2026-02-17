<?php
require_once "config/database.php";

// Validate HouseID from GET (since you said you're carrying it through)
if (!isset($_GET['HouseID']) || !is_numeric($_GET['HouseID'])) {
    die("Invalid House ID.");
}

$houseID = (int) $_GET['HouseID'];

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $totalPlacement = $_POST["TotalDayOldChickenPlacement"] ?? null;
    $startDate      = $_POST["StartPlaceDateAsAge0"] ?? null;

    if (!empty($totalPlacement) && !empty($startDate)) {

        $stmt = $pdo->prepare("
            INSERT INTO CropNumber 
            (HouseID, TotalDayOldChickenPlacement, StartPlaceDateAsAge0)
            VALUES (:houseID, :totalPlacement, :startDate)
        ");

        $stmt->execute([
            ":houseID"        => $houseID,
            ":totalPlacement" => $totalPlacement,
            ":startDate"      => $startDate
        ]);
        //The lines below take the user automatically back to the house management page aftr starting a crop in the house.
        header("Location: houseManagement.php?HouseID=" . $houseID);
        exit();
        
        $success = "Crop successfully started in this house.";
    } else {
        $error = "All fields are required.";
    }
}
?>

<div class="container">
    <h1>Start a Crop in House <?php echo $houseID; ?></h1>

    <p><strong>House ID:</strong> <?php echo $houseID; ?></p>

    <?php if (!empty($success)) : ?>
        <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)) : ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">

        <!-- Hidden House ID -->
        <input type="hidden" name="HouseID" value="<?php echo $houseID; ?>">

        <div class="form-group">
            <label>Total Day Old Chicken Placement</label>
            <input type="number" 
                   name="TotalDayOldChickenPlacement" 
                   min="1" 
                   required>
        </div>

        <div class="form-group">
            <label>Start Place Date (Age 0)</label>
            <input type="date" 
                   name="StartPlaceDateAsAge0" 
                   required>
        </div>

        <button type="submit" class="btn">Start Crop</button>
    </form>
</div>
