<?php
// Include the firewall API handler FIRST to ensure function availability
include 'fortinet_firewall.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fortigate DNS Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Main Header -->
    <div class="header">Fortigate DNS Management</div>

    <!-- Main Content Container -->
    <div class="container">

        <!-- Left Section (Main Content) -->
        <div class="left-section">
            <div class="section-header">DNS Records</div>
            <?php include 'content-left.php'; ?>
        </div>

        <!-- Right Section (Four Boxes) -->
        <div class="right-section">
            <div class="box">
                <div class="section-header">Add A Record</div>
                <?php include 'content-box1.php'; ?>
            </div>
            <div class="box">
                <div class="section-header">Add CNAME</div>
                <?php include 'content-box2.php'; ?>
            </div>
            <div class="box">
                <div class="section-header">Backup - Restore</div>
                <?php include 'content-box3.php'; ?>
            </div>
            <div class="box">
                <?php include 'content-box4.php'; ?>
            </div>
        </div>

    </div>

</body>
</html>
