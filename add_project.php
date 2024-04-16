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

    // Retrieve existing users from the database
    $users_query = "SELECT uid, username FROM users";
    $users_stmt = $db->prepare($users_query);
    $users_stmt->execute();
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialise variables to hold form field values
    $title = $phase = $description = $selected_user = '';

    // Form Validation: Check if the form data is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Extract and clean the form data
        $title = htmlspecialchars($_POST['title']);
        $phase = htmlspecialchars($_POST['phase']);
        $description = htmlspecialchars($_POST['description']);
        $selected_user = $_POST['selected_user'];

        // Form Validation: Check if the selected user exists
        $user_exists = false;
        foreach ($users as $user) {
            if ($user['uid'] == $selected_user) {
                $user_exists = true;
                break;
            }
        }

        if ($user_exists) {
            // Form Validation: Check if the end date is before start date
            if ($_POST['end_date'] <= $_POST['start_date']) {
                $error_message = "End date cannot be before start date Please enter correct dates";
            } else {
                // Insert new project into the database
                try {
                    $query = "INSERT INTO projects (title, start_date, end_date, phase, description, uid) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$title, $_POST['start_date'], $_POST['end_date'], $phase, $description, $selected_user]);

                    // Redirect to projects page after adding the project
                    header("Location: projects.php");
                    exit();
                } catch (PDOException $ex) {
                    // Log the error or display a user-friendly message
                    error_log("Database error: " . $ex->getMessage());
                    // Redirect to an error page or display an error message
                    header("Location: error.php");
                    exit();
                }
            }
        } else {
            // User does not exist, handle accordingly
            $error_message = "Selected user does not exist.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title and CSS -->
    <meta charset="UTF-8">
    <title>AProject - Add Project</title>
    <link rel="icon" type="image/jpeg" href="logo.jpeg">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styling for form-box */
        .form-box{
            width: 350px;
        }

        /* Styling for form-row */
        .form-row{
            display: inline-flex;
            gap: 20px;
            align-items: center;
        }

        /* Styling for form rows phase and user */
        .form-row#phase,
        .form-row#user{
            gap: 0px;
        }

        /* Styling for input fields */
        .input-field{
            width: 250px;
        }

        /* Styling for date and select fields */
        .date-field,
        .select-field{
            width: 140px;
            padding: 10px 0; 
            margin: 5px 0;
            border-left: 0;
            border-top: 0;
            border-right: 0;
            border-bottom: 2px solid #ccc;
            outline: none;
            background: transparent;
        }

        /* Styling for label */
        label{
            width: 112px;
        }
    </style>
</head>
<body>
    <!-- Main area -->
    <div class="main-container">
        <!-- Form box -->
        <div class="form-box">
            <h1>Welcome to AProject</h1>
            <h2>Add Project</h2>
            <!-- Form container -->
            <div class="form-inner">
                <form action="add_project.php" method="post">
                    <!-- Title -->
                    <input type="text" class="input-field" name="title" required placeholder="Title" value="<?php echo $title; ?>"><br>
                    <!-- Start date  -->
                    <div class="form-row">
                        <label>Start Date:</label>
                        <input type="date" class="date-field" name="start_date" required value="<?php echo $start_date; ?>"><br>
                    </div><br>
                    
                    <!-- End date -->
                    <div class="form-row">
                        <label>End Date:</label>
                        <input type="date" class="date-field" name="end_date" required value="<?php echo $end_date; ?>"><br>
                    </div><br>

                    <!-- Phase selection  -->
                    <div id="phase" class="form-row">
                        <label id="phase">Select Phase:</label>
                        <select id="phase" class="select-field" name="phase">
                            <option value="design" <?php if($phase == 'design') echo 'selected'; ?>>Design</option>
                            <option value="development" <?php if($phase == 'development') echo 'selected'; ?>>Development</option>
                            <option value="testing" <?php if($phase == 'testing') echo 'selected'; ?>>Testing</option>
                            <option value="deployment" <?php if($phase == 'deployment') echo 'selected'; ?>>Deployment</option>
                            <option value="complete" <?php if($phase == 'complete') echo 'selected'; ?>>Complete</option>
                        </select>
                    </div><br>
                    
                    <!-- Desxription -->
                    <textarea class="input-field" name="description" required placeholder="Description"><?php echo $description; ?></textarea><br>
                    
                    <!-- User selection -->
                    <div id="user" class="form-row">
                        <label id="user">Select User:</label>
                        <select id="user" name="selected_user" class="select-field" required>
                            <option value="">Select User</option>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['uid']; ?>" <?php if($selected_user == $user['uid']) echo 'selected'; ?>><?php echo $user['username']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div><br>
                    <!-- Error message -->
                    <?php if(isset($error_message)): ?>
                    <br><p style="color:red"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                     <!-- Submit button -->
                    <button type="submit" class="submit-btn" >Add Project</button>
                </form>
            </div>
            <!-- Return option -->
            <div class="register-link">
                <p>Would you like to go back? <a href="projects.php">Go back</a></p>
            </div>
        </div>
    </div>
</body>
</html>
