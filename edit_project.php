<?php

    // Start the session
    session_start();

    // Include database connection
    require_once('connectdb.php');

    // Authorisation: Check if the user is logged in else redirect to home page
    // Cross-site request forgery prevention
    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit();
    }

    // Fetch project details from the database
    try {
        // Check if the project ID is provided
        if (!isset($_GET['id'])) {
            header("Location: projects.php");
            exit();
        }
        
        $projectId = $_GET['id'];
        $query = "SELECT * FROM projects WHERE pid = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$projectId]);
        $project = $stmt->fetch();

        // Check if the project exists
        if (!$project) {
            echo "Project not found.";
            exit();
        }

        // Fetch users from the database
        $query_users = "SELECT * FROM users";
        $stmt_users = $db->prepare($query_users);
        $stmt_users->execute();
        $users = $stmt_users->fetchAll();

    } catch (PDOException $ex) {
        echo "Database error: " . $ex->getMessage();
        exit();
    }

    // Form Validation: Check if the form data is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Extract form data
        $pid = $_POST['project_id']; 
        $title = $_POST['title'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $phase = $_POST['phase'];
        $description = $_POST['description'];
        $selected_user = $_POST['selected_user']; 

        // Form Validation: Check if the end date is before start date
        if ($end_date <= $start_date) {
            $error_message = "End date cannot be before start date Please enter correct dates";
        } else {
            // Update project details in the database
            try {
                $query = "UPDATE projects SET title = ?, start_date = ?, end_date = ?, phase = ?, description = ?, uid = ? WHERE pid = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$title, $start_date, $end_date, $phase, $description, $selected_user, $pid]);

                // Redirect to projects page after update
                header("Location: projects.php");
                exit();
            } catch (PDOException $ex) {
                $error_message = "Database error: " . $ex->getMessage();
            }
        }   
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title and CSS -->
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <link rel="icon" type="image/jpeg" href="logo.jpeg">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styling for form-box */
        .form-box{
            width: 400px;
        }

        /* Styling for form-rows */
        .form-row{
            display: inline-flex;
            gap: 20px;
            align-items: center;
        }

        /* Styling for input field  */
        .input-field{
            width: 83%;
        }

        /* Styling for date and selct fields */
        .date-field,
        .select-field{
            width: 145px;
            padding: 10px 0; 
            margin: 5px 0;
            border-left: 0;
            border-top: 0;
            border-right: 0;
            border-bottom: 2px solid #ccc;
            outline: none;
            background: transparent;
        }

        /* Styling for labels */
        label{
            width: 112px;
        }
    </style>
</head>
<body>
    <!-- Main area -->
    <div class="main-container">
        <!-- Form box -->
        <div class="form-box" style="width: 350px;">
            <h1>Edit Project</h1>
            <!-- Form container -->
            <div class="form-inner">
                <form action="edit_project.php?id=<?php echo $projectId; ?>" method="post">
                    <!-- Title -->
                    <input type="text" class="input-field" name="title" required placeholder="Title" value="<?php echo $project['title']; ?>"><br>
                    
                    <!-- Start date -->
                    <div class="form-row">
                        <label>Start Date:</label>
                        <input type="date" class="date-field" name="start_date" required value="<?php echo $project['start_date']; ?>"><br>
                    </div><br>
                    
                    <!-- End date -->
                    <div class="form-row">
                        <label>End Date:</label>
                        <input type="date" class="date-field" name="end_date" required value="<?php echo $project['end_date']; ?>"><br>
                    </div><br>

                    <!-- Phase selction -->
                    <div class="form-row">
                        <label>Select Phase:</label>
                        <select class="select-field" name="phase">
                            <option value="design" <?php if ($project['phase'] == 'design') echo 'selected'; ?>>Design</option>
                            <option value="development" <?php if ($project['phase'] == 'development') echo 'selected'; ?>>Development</option>
                            <option value="testing" <?php if ($project['phase'] == 'testing') echo 'selected'; ?>>Testing</option>
                            <option value="deployment" <?php if ($project['phase'] == 'deployment') echo 'selected'; ?>>Deployment</option>
                            <option value="complete" <?php if ($project['phase'] == 'complete') echo 'selected'; ?>>Complete</option>
                        </select>
                    </div><br>
                    
                    <!-- Description -->
                    <textarea class="input-field" name="description" required placeholder="Description"><?php echo $project['description']; ?></textarea><br>
                    
                    <!-- User selection -->
                    <div class="form-row">
                        <label>Select User:</label>
                        <select name="selected_user" class="select-field" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['uid']; ?>"><?php echo $user['username']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div><br>

                    <!-- Error Message-->
                    <?php if(!empty($error_message)): ?>
                    <br><p style="color:red"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    
                    <!-- Submit button -->
                    <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
                    <button type="submit" class="submit-btn">Update Project</button>
                    <input type="hidden" name="update" value="true"/>
                </form>
                <!-- Return option -->
                <p>Would you like to go back? <a href="projects.php">Go back</a></p>
            </div>
        </div>
    </div>
</body>
</html>
