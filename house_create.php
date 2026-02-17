<?php
require_once "config/database.php";

$message = "";

/* ==========================
   Fetch Farms for Dropdown
   ========================== */
$stmt = $pdo->query("SELECT FarmID, FarmName FROM Farm ORDER BY FarmName ASC");
$farms = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================
   Handle Form Submission
   ========================== */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $farmID = $_POST["FarmID"];
    $area = trim($_POST["MeterSquaredArea"]);

    if (!empty($farmID) && !empty($area)) {

        $sql = "INSERT INTO Houses (FarmID, MeterSquaredArea) 
                VALUES (:farmID, :area)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":farmID", $farmID);
        $stmt->bindParam(":area", $area);

        if ($stmt->execute()) {
            $message = "House added successfully ✅";
        } else {
            $message = "Error adding house ❌";
        }

    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create House</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f6f9;
            padding-top: 60px;
            text-align: center;
        }
        .container {
            width: 450px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        select, input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            padding: 10px 20px;
            background: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
        }
        a {
            display: block;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🏠 Create New House</h2>

    <form method="POST">

        <label>Select Farm</label>
        <select name="FarmID" required>
            <option value="">-- Choose Farm --</option>
            <?php foreach ($farms as $farm): ?>
                <option value="<?php echo $farm["FarmID"]; ?>">
                    <?php echo htmlspecialchars($farm["FarmName"]); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Meter Squared Area</label>
        <input type="number" step="0.01" name="MeterSquaredArea" placeholder="Enter Area (m²)" required>

        <button type="submit">Save House</button>
    </form>

    <?php if (!empty($message)) : ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <a href="house_list.php">View All Houses</a>
    <a href="farm_create.php">Add Farm</a>

</div>

</body>
</html>