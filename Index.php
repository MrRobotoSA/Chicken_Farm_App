<?php
require_once "config/database.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chicken Farm App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            text-align: center;
            padding-top: 100px;
        }
        .box {
            background: white;
            width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
        }
        .success {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>🐔 Chicken Farm Management System</h1>

    <?php
        if ($pdo) {
            echo "<p class='success'>Database Connected Successfully ✅</p>";
        }
    ?>

    <p>System initialized and ready.</p>
</div>
<form action="chooseYourFarmPage.php" method="get">
    <button type="submit" style="
        background-color: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
    ">
        Go to Choose Your Farm
    </button>
</form>
</body>
</html>
