<?php
// Include database connection
require_once('connectdb.php');

// Check if project id is provided in the URL
if(isset($_GET['id'])) {
    $project_id = $_GET['id'];

// Query database to retrieve project details + user email
$query = "SELECT * FROM projects AS p INNER JOIN users AS u ON p.uid = u.uid WHERE p.pid = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $project_id);
$stmt->execute();

// Fetch project details
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if($project) {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, title and CSS -->
    <meta charset="UTF-8">
    <title>Project Details</title>
    <link rel="icon" type="image/jpeg" href="logo.jpeg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
     <!-- Main area -->
     <div class="main-container">
        <!-- Form boxes -->
        <div class="form-box">
             <!-- Project details -->
            <div class="project-details">
                <h1>Project Details</h1>
                 <!-- Display tables and data-->
                <table class="project-table">
                    <tr>
                        <td><b>Title:</b></td>
                        <td><?php echo $project['title']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Start Date:</b></td>
                        <td><?php echo $project['start_date']; ?></td>
                    </tr>
                    <tr>
                        <td><b>End Date:</b></td>
                        <td><?php echo $project['end_date']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Description:</b></td>
                        <td><?php echo $project['description']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Phase:</b></td>
                        <td><?php echo $project['phase']; ?></td>
                    </tr>
                    <tr>
                        <td><b>User email:</b></td>
                        <td><?php echo $project['email']; ?></td>
                    </tr>
                </table> 
                <!-- Return option -->
                <br><p>Would you like to go back? <a href="index.php">Go back</a></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
    } else {
        echo "Project not found.";
    }
} else {
    echo "Project ID not provided.";
}
?>




