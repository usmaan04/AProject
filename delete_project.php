<?php
    // Start the session
    session_start();

    // Check if the user is logged in else redirect to homepage
    // Cross-site request forgery security
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit();
    }

    // Check if the project ID is provided
    if (!isset($_GET['id'])) {
        header("Location: projects.php");
        exit();
    }

    // Include database connection
    require_once('connectdb.php');

    // Delete the project from the database
    try {
        $projectId = $_GET['id'];
        if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
            $query = "DELETE FROM projects WHERE pid = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$projectId]);

            // Redirect to projects page after deletion
            header("Location: projects.php");
            exit();
        } else {
            // Display confirmation popup
            echo "<script>
                    var confirmed = confirm('Are you sure you want to delete this project?');
                    if (confirmed) {
                        window.location.href = 'delete_project.php?id=$projectId&confirm=yes';
                    } else {
                        window.location.href = 'projects.php';
                    }
                  </script>";
        }
    } catch (PDOException $ex) {
        echo "Database error: " . $ex->getMessage();
        exit();
    }

