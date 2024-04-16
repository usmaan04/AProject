<?php

    // Start the session
    session_start();	

    // Authorisation: Check if the user is logged in else redirect to home page
    // Cross-site request forgery prevention
    if (!isset($_SESSION['username'])){
        header("Location: index.php");
        exit();
    }
    $username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title and CSS -->
    <meta charset="UTF-8">
    <title>AProject - Projects</title>
    <link rel="icon" type="image/jpeg" href="logo.jpeg">
    <link rel="stylesheet" href="style.css">
    <style>
        #projects{
			max-height: 300px;
			overflow-y: auto;
		}
    </style>
</head>
<body>
    <!-- Main page -->
    <div class="main-container">
        <!-- Form boxes -->
        <div class="form-box">
            <h1>Welcome to AProject</h1>
            <h2>Projects</h2>  
            <!-- Project list section -->
            <section id="projects">
                <?php
                    // Step 2: Include the connectdb.php to connect to the database, the PDO object is called $db and run the query to get all the project information 
                    require_once('connectdb.php');  
                    try {
                        // Select all columns from the projects table and run query
                        $query = "SELECT * FROM projects"; 
                        $rows = $db->query($query);
                        
                        // Step 3: Display the projects in a table 	
                        if ($rows && $rows->rowCount() > 0) {
                            ?>	
                            <table cellspacing="0" cellpadding="5" id="myTable">
                                <tr>
                                    <th align='left'><b>Project ID</b></th>
                                    <th align='left'><b>Title</b></th>
                                    <th align='left'><b>Start Date</b></th>
                                    <th align='left'><b>End Date</b></th>
                                    <th align='left'><b>Phase</b></th>
                                    <th align='left'><b>Description</b></th>
                                    <th align='left'><b>User</b></th>
                                    <th align='left'><b>Edit</b></th>
                                </tr>
                                <?php
                                // Fetch and print all the records.
                                while ($row = $rows->fetch()) {
                                    // Fetch username associated with user ID
                                    $userId = $row['uid'];
                                    $userQuery = $db->prepare("SELECT username FROM users WHERE uid = :uid");
                                    $userQuery->execute(array(':uid' => $userId));
                                    $userRow = $userQuery->fetch();
                                    echo "<tr>";
                                    echo "<td align='left'>" . $row['pid'] . "</td>";
                                    echo "<td align='left'>" . $row['title'] . "</td>";
                                    echo "<td align='left'>" . $row['start_date'] . "</td>";
                                    echo "<td align='left'>" . $row['end_date'] . "</td>";
                                    echo "<td align='left'>" . $row['phase'] . "</td>";
                                    echo "<td align='left'>" . $row['description'] . "</td>";
                                    echo "<td align='left'>" . $userRow['username'] . "</td>";
                                    // Edit button
                                    echo "<td align='left'><a href='edit_project.php?id=" . $row['pid'] . "'>Edit</a></td>";
                                }
                                echo  '</table><br>';
                        } else {
                            echo "<p>There are no projects</p>\n"; 
                            
                        }
                    }
                    catch (PDOException $ex){
                        echo "Sorry, a database error occurred! <br>";
                        echo "Error details: <em>" . $ex->getMessage() . "</em>";
                    }     
                ?> 
            </section><br>  
                
            <!-- Add project button -->
            <a href="add_project.php" class="submit-btn" style="width: fit-content; margin: auto;">Add Project</a>
            <!-- step 4: display the logout choice -->
            <div class="register-link" style=" margin-top: 20px;">
                <p>Would you like to log out? <a href="logout.php">Log out</a></p>
            </div>
        </div>
    </div>
</body>
</html>
