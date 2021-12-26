<?php
session_start();

$conn = mysqli_connect("127.0.0.1", "root", "", "hackme");

if(!$conn){
	die("Fail connecting to database. Have you create and import hackme.sql file?");
}

$q = mysqli_query($conn, "SHOW TABLES");

$r = mysqli_num_rows($q);

if($r < 1){
	$success = "Your database has been set-up!";
	mysqli_query($conn, "CREATE TABLE users (
			id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
			name VARCHAR(255) NOT NULL,
			username VARCHAR(255) NOT NULL,
			password VARCHAR(255) NOT NULL
		);
	");
	
	mysqli_query($conn, "CREATE TABLE posts (
			id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
			title VARCHAR(255) NOT NULL,
			content TEXT NOT NULL,
			user VARCHAR(255) NOT NULL,
			timestamp INT(15) NOT NULL
		);
	");
	
	mysqli_query($conn, "INSERT INTO users (name, username, password) VALUES('Admin', '4dmin', 'P@ssw0rd!sW0r1d');");
}

if(isset($_GET["logout"])){
	session_destroy();
	
	header("Location: ./");
}

if(isset($_POST["login"])){
	if(!empty($_POST["username"]) && !empty($_POST["password"])){
		$q = mysqli_query($conn, "SELECT * FROM users WHERE username = '". $_POST["username"] ."' AND password = '". $_POST["password"] ."'");
		
		$n = mysqli_num_rows($q);
		
		if($n > 0){
			$r = mysqli_fetch_assoc($q);
			
			$_SESSION["user_login"] = $r["username"];
			
			header("Location: ./?success=" . urlencode("You have been logged in successfully!"));
		}else{
			header("Location: ./?error=" . urlencode("Username or Password is wrong!"));
		}
	}else{
		header("Location: ./?error=" . urlencode("Username & Password cannot be empty!"));
	}
}

if(isset($_POST["register"])){
	if(!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["name"])){
		$q = mysqli_query($conn, "SELECT * FROM users WHERE username = '". $_POST["username"] ."'");
		
		$n = mysqli_num_rows($q);
		
		if($n > 0){			
			header("Location: ./?error=" . urlencode("Username has been registered before!"));
		}else{
			$i = mysqli_query($conn, "INSERT INTO users (name, username, password) VALUE('". $_POST["name"] ."', '". $_POST["username"] ."', '". $_POST["password"] ."')");
			
			if($i){
				header("Location: ./?success=" . urlencode("Registration complete! You can now login!"));
			}else{
				header("Location: ./?error=" . urlencode("Theres something error with the register query!"));
			}
		}
	}else{
		header("Location: ./?error=" . urlencode("Name, Username & Password cannot be empty!"));
	}
}

if(isset($_POST["post"])){
	if(!empty($_POST["title"]) && !empty($_POST["content"])){
		$i = mysqli_query($conn, "INSERT INTO posts (title, content, user, timestamp) VALUE('". $_POST["title"] ."', '". $_POST["content"] ."', '". $_POST["user"] ."', '". time() ."')");
			
		if($i){
			header("Location: ./?success=" . urlencode("Post added successfully."));
		}else{
			header("Location: ./?error=" . urlencode("Theres something error with the add post query!"));
		}
	}else{
		header("Location: ./?error=" . urlencode("Title & Content cannot be empty!"));
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>HackMe - Web Hacking Test</title>
	<meta charset="utf-8" />
	
	<script src="./assets/jquery/jquery.min.js"></script>
	<script src="./assets/popper.min.js"></script>
	<script src="./assets/bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css"></script>
</head>

<body>
	<div class="container mt-5">
		<div class="text-center">
			<img src="./assets/H4ckM3.png" style="width: 200px;" />
		</div>
		<div class="card">
			<div class="card-header">
				Welcome <?= isset($_SESSION["user_login"]) ? $_SESSION["user_login"] . ", " : "" ?> to HackMe!
			<?php
				if(isset($_SESSION["user_login"])){
				?>
				(<a href="./?logout">
					Logout
				</a>)
				<?php
				}
			?>
			</div>
			
			<div class="card-body">
				<div class="alert alert-info">
					<strong>Notice!</strong> This HackMe website has been created for online hacking seminar participants. Visit Mr Hery YouTube for more information. You are allowed to hacking website for your skills training.
				</div>
			<?php
				if(isset($_GET["error"])){
				?>
				<div class="alert alert-danger">
					<strong>Error!</strong> <?= $_GET["error"] ?>
				</div>
				<?php
				}
				
				if(isset($_GET["success"]) || isset($success)){
				?>
				<div class="alert alert-success">
					<strong>Success!</strong> <?= isset($_GET["success"]) ? $_GET["success"] : $success ?>
				</div>
				<?php
				}
				
				if(!isset($_SESSION["user_login"])){
			?>
				<div class="row">
					<div class="col-md-6 mb-2">
						<h3>Login</h3>
						
						<form action="" method="POST">
							Username:
							<input type="text" class="form-control" placeholder="Username" name="username" /><br />
							
							Passsword:
							<input type="password" class="form-control" placeholder="Password" name="password" /><br />
							
							<input type="hidden" name="login" value="login" />
							
							<button class="btn btn-sm btn-success">
								Login
							</button>
						</form>
					</div>	
					
					<div class="col-md-6">
						<h3>Register</h3>
						
						<form action="" method="POST">
							Name:
							<input type="text" class="form-control" placeholder="Name" name="name" /><br />
							
							Username:
							<input type="text" class="form-control" placeholder="Username" name="username" /><br />
							
							Passsword:
							<input type="password" class="form-control" placeholder="Password" name="password" /><br />
							
							<input type="hidden" name="register" value="register" />
							
							<button class="btn btn-sm btn-success">
								Register
							</button>
						</form>
					</div>
				</div>
			<?php	
				}else{
					if(!isset($_GET["id"])){
					?>
						<h3>
							Discussions:
						</h3>
						
						<form action="" method="POST">
							Title:
							<input type="text" class="form-control" name="title" placeholder="Title" /><br />
							
							Content:
							<textarea class="form-control" name="content" placeholder="Content"></textarea><br />
							
							<input type="hidden" name="user" value="<?= $_SESSION["user_login"] ?>" />
							<input type="hidden" name="post" value="post" />
							
							<button class="btn btn-sm btn-success">
								Add Post
							</button>
						</form>
						<hr />
					<?php
						$q = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC LIMIT 10");
						
						while($r = mysqli_fetch_assoc($q)){
						?>
						<div class="card mb-2">
							<div class="card-body">
								<strong><u><?= $r["title"] ?></u></strong> <small><a href="./?id=<?= $r["id"] ?>">show only this</a></small><br />
								<?= $r["content"] ?><br />
								<small>by <?= $r["user"] ?></small>
							</div>
						</div>
					<?php
						}
					}else{
					?>
						<h3>
							<a href="./" class="btn btn-sm btn-primary">
								&larr; Back
							</a>
							Discussion id <?= $_GET["id"] ?>:
						</h3>
					<?php
						$q = mysqli_query($conn, "SELECT * FROM posts WHERE id = '". $_GET["id"] ."'");
						
						$r = mysqli_fetch_assoc($q);
					?>
						<strong>Title:</strong> <?= $r["title"] ?><br />
						<strong>Written by:</strong> <?= $r["user"] ?><br />
						<strong>Published:</strong> <?= date("d M Y", $r["timestamp"]) ?><br /><br />
						
						<strong>Content:</strong><br />
						<?= $r["content"] ?>
					<?php
					}
				}
			?>
				
			</div>
		</div>
	</div>
</body>
</html>