<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Meta tags, title and CSS -->
    <meta charset="UTF-8">
    <title>AProject - Home</title>
	<link rel="icon" type="image/jpeg" href="logo.jpeg">
    <link rel="stylesheet" href="style.css">
    <style>
		/* Styling for project tables */
		.project-table {
			width: 100%;
			border-collapse: collapse;
			padding: 25px;
		}

		/* Styling for general table */
		table{
			margin-top: 0px;
		}

		/* Styling for project table headrer and cells */
		.project-table th,
		.project-table td {
			max-width: 100px;
			overflow: hidden;
			white-space: nowrap;
			text-overflow: ellipsis;
		}

		/* Styling for main area  */
        .main-container {
            display: inline-flex;
            align-items: flex-start;
        }

		/* Styling for project box */
		.projects-box {
			width: 450px;
			height: fit-content;
			min-height: 65vh;
			overflow: hidden;
		}

		/* Styling for projects list section */
		.projects{
			max-height: 230px;
			overflow: hidden;
			overflow-y: auto;
		}

		/* Styling for form box */
		.form-box{
            width: 350px;
			height: 65vh;
			transform: translate(-100%, 0);
			margin:-150px;
        }
    </style>
</head>
<body>
	<!-- Main area -->
	<div class="main-container">
		<!-- Container for projects and login/register box -->
		<div class="container">
			<!-- Projects  box -->
			<div class="projects-box">
				<h1>Welcome to AProject</h1>
				<h2>Project List</h2>
				<form class="form-row" action="index.php" method="get" style="padding: 10px; display: inline-flex;">
					<input type="text" name="search" class="input-field" style="width: 250px;" placeholder="Search by title or start date">
					<button type="submit" class="submit-btn" style="margin: 10px; padding: 10px 0;">Search</button>
				</form>

				<div class="projects">
					<?php
					// Include database connection
					require_once('connectdb.php');

					try {
						// Form Validation: Check if the search form has been submitted
						if(isset($_GET['search'])) {
							$search = $_GET['search'];
							$searchParam = "%$search%";

							// Query database to search for projects
							$query = "SELECT * FROM projects WHERE title LIKE :search OR start_date LIKE :search";
							$stmt = $db->prepare($query);
							$stmt->bindParam(':search', $searchParam);
							$stmt->execute();
						} else {
							// Fetch all projects if not search or empty text
							$query = "SELECT * FROM projects";
							$stmt = $db->query($query);
						}

						// Start table
						echo "<table class='project-table'>";
						echo "<tr>";
						echo "<th>Title</th>";
						echo "<th>Start Date</th>";
						echo '<th class="description">Description</th>';
						echo "<th>View</th>";
						echo "</tr>";

						// Fetch and display projects
						if ($stmt && $stmt->rowCount() > 0) {
							while ($row = $stmt->fetch()) {
								echo "<tr>";
								echo "<td>" . $row['title'] . "</td>";
								echo "<td>" . $row['start_date'] . "</td>";
								echo "<td>" . $row['description'] . "</td>";
								echo "<td><a href='project_details.php?id=" . $row['pid'] . "'>View Details</a></td>";
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='4' style='color:red; text-align: center;'>No projects found</td></tr>";
						}

						// End table
						echo "</table>";
					} catch (PDOException $ex) {
						echo "<p>Failed to retrieve projects: " . $ex->getMessage() . "</p>";
					}
					?>
				</div>		
			</div>
			<!-- Sign In/Sign up headers -->
			<div class="form-box">
				<div id="headers">
					<h1 id="sign-in">Log in</h1>
					<h1 id="sign-up">Register</h1>
				</div>   
				<div id="button-box">
					<div id="btn"></div>
						<button type="button" class="toggle-btn" onclick="login()">Log In</button>
						<button type="button" class="toggle-btn" onclick="register()">Register </button>
				</div> 

				<!-- Login and register container -->
				<div id="form-inner">
					<!-- Login form and php section -->
					<form id="login" class="input-group" action="index.php" method="post">
						<input type="text" class="input-field" name="username" placeholder="Username" required>
						<input type="password" class="input-field" name="password" placeholder="Password" required><br>
						<button type="submit" class="submit-btn" value="Login">Log in</button>
						<input type="hidden" name="login" value="true"/>
						<p id="success-message" style="color: green;"></p>

						<?php
						// Form Validation: Check if the login form has been submitted
						if (isset($_POST['login'])){
							if ( !isset($_POST['username'], $_POST['password']) ) {
							// Could not get the data that should have been sent.
							exit('Please fill both the username and password fields!');
							}
							// connect DB
							require_once ("connectdb.php");
							try {
							    // Query DB to find the matching username/password
							    // Using prepare/bindparameter to prevent SQL injection.
								$stat = $db->prepare('SELECT password FROM users WHERE username = ?');
								$stat->execute(array($_POST['username']));
								
								// fetch the result row and check 
								if ($stat->rowCount()>0){  // matching username
									$row=$stat->fetch();

									//Authentication: Check if password entered and hashed password match
									if (password_verify($_POST['password'], $row['password'])){ 
										
										// Authorisation: Start a session for the user and redirect to the logged-in page
										session_start();
										$_SESSION["username"]=$_POST['username'];
										header("Location:projects.php");
										exit();
									
									} else {
									echo "<p style='color:red'>Username or password is incorrect </p>";
									}
								} else {
								//else display an error
								echo "<p style='color:red'>Username or password is incorrect </p>";
								}
							}
							catch(PDOException $ex) {
								echo("Failed to connect to the database.<br>");
								echo($ex->getMessage());
								exit;
							}
						}
						?>
					</form>

					<!-- Register form and php section -->
					<form id="register" class="input-group" action="index.php" method="post">
						<input type="text" class="input-field" name="username" placeholder="Username" required>
						<input type="password" class="input-field" name="password" placeholder="Password" required><br>
						<input type="password" class="input-field" name="confirmpassword" placeholder="Confirm Password" required><br>
						<input type="email" class="input-field" name="email" placeholder="Email" required>
						<button type="submit" class="submit-btn" value="Register">Register</button>
						<input type="hidden" name="register" value="true"/>

						<!-- PHP code to check if the registration form has been submitted -->
						<?php 
							$registerSubmitted = isset($_POST['register']);
						?>

						<?php if ($registerSubmitted): ?>
							<?php if (isset($_POST['register'])): // Form Validation: Check if the register form has been submitted?>
								<?php
								// Connect to the database
								require_once('connectdb.php');
								
								// Prepare the form input
								$username = isset($_POST['username']) ? $_POST['username'] : false;
								$password = isset($_POST['password']) ? $_POST['password'] : false;
								$confirmpassword = isset($_POST['confirmpassword']) ? $_POST['confirmpassword'] : false;
								$email = isset($_POST['email']) ? $_POST['email'] : false;

								// Check if username already exists
								$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
								$stmt->execute([$username]);
								
								// Form Validation: check all fields have bene inputed
								if (!$username || !$password ||!$confirmpassword || !$email) {
									echo "Please fill in all the fields.";
								}
								// Form validation: Check if the username already exists in the database
								else if ($stmt->rowCount() > 0) {
									$confirmpassword = '';
									echo "<p style='color:red; transform:translateY(-10px);'>Username already exists </p>";
								}
								else {
									// Form Validation: Check if passwords do not match
									if ($password !== $confirmpassword) {
										echo "<p style='color:red; transform:translateY(-10px);'>Passwords do not match </p>";	
									}else{
										try {
											// Hash the password and register the user by inserting their info into the users table
											$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
											
											// Register user by inserting the user info into the users table
											$stmt = $db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
											$stmt->execute([$username, $hashedPassword, $email]);
											
											// Check if the registration was successful before switching to login tab
											if ($stmt->rowCount() > 0) {
												echo "<script>window.onload = function() { setSuccess(); }</script>";
											} 
										} catch (PDOException $ex) {
											echo "Sorry, a database error occurred! <br>";
											echo "Error details: <em>" . $ex->getMessage() . "</em>";
										}
									}
								}
								?>
							<?php endif; ?>
						<?php endif; ?>

					</form>
				</div>

				<!-- JavaScript to handle tab switching and display registration success message -->
				<script>
					var a = document.getElementById("sign-in");
					var b = document.getElementById("sign-up");
					var c = document.getElementById("btn");
					var d = document.getElementById("login");
					var e = document.getElementById("register");
					var successMessage = document.getElementById("success-message");

					function register() {
						a.style.left = "-300px";
						b.style.left = "-50px";
						c.style.left = "110px";
						d.style.left = "-300px";
						e.style.left = "40px";
						
					}

					function login() {
						a.style.left = "50px";
						b.style.left = "450px";
						c.style.left = "0px";
						d.style.left = "40px";
						e.style.left = "450px";
						successMessage.innerText = ""; // Clear success message when switching
					}

					// Set success message
					function setSuccess(){
						successMessage.innerText = "Your account has been created successfully, You can now login!"
					}

					// Check if passwords do not match and trigger the register tab
					<?php if ($registerSubmitted && $password !== $confirmpassword): ?>
						register();
					<?php endif; ?>
				</script>
			</div>
		</div>
	</div>
</body>
</html>



