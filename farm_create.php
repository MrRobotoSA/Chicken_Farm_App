<?php
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $farmName = trim($_POST["FarmName"]);

    if (!empty($farmName)) {

        $sql = "INSERT INTO Farm (FarmName) VALUES (:farmName)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":farmName", $farmName);

        if ($stmt->execute()) {
            $message = "Farm added successfully ✅";
        } else {
            $message = "Error adding farm ❌";
        }

    } else {
        $message = "Farm name cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Farm</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f6f9;
            padding-top: 80px;
            text-align: center;
        }
        .container {
            width: 400px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        input[type=text] {
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
    <h2>🐔 Create New Farm</h2>

    <form method="POST">
        <input type="text" name="FarmName" placeholder="Enter Farm Name" required>
        <button type="submit">Save Farm</button>
    </form>

    <?php if (!empty($message)) : ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <a href="farm_list.php">View All Farms</a>
</div>

</body>
</html>