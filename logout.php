<?php
    session_start();
    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title and CSS -->
    <meta charset="UTF-8">
    <title>AProject - Logged Out</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-inner p {
            margin-top: 15px;
        }
    </style>
    <script>
        window.onload = function() {
            // Redirect to index.php
            window.location.href = "index.php";

            // Display popup message after a short delay
            setTimeout(function() {
                alert("You have been signed out.");
            }, 500); 
        };
    </script>
</head>
<body>
    <!-- Main area -->
    <div class="main-container">
        <!-- Form boxes -->
        <div class="form-box">
            <h1>AProject</h1>
            <div class="form-inner">
            </div>
        </div>
    </div>
</body>
</html>
