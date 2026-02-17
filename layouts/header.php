<!DOCTYPE html>
<html>
<head>
    <title>Chicken Farm Management System</title>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        .navbar {
            background-color: #2c3e50;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            color: white;
            margin: 0;
        }

        .nav-links a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .content {
            padding: 40px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>🐔 Chicken Farm App</h2>
    <div class="nav-links">
        <a href="index.php">Dashboard</a>
        <a href="farm_list.php">Farms</a>
        <a href="house_list.php">Houses</a>
        <a href="#">Crops</a>
        <a href="#">Cycle Days</a>
    </div>
</div>

<div class="content">
<div class="container">