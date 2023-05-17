<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>TAB Global</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/fontawesome/css/all.css" rel="stylesheet"/>
    <link href="css/font.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid p-4">
	<div class="row justify-content-center">
		<div class="col-md-6 text-center">
            <img src="img/logo_sm.png" width="80">
		</div>
	</div>
	<div class="row justify-content-center">
		<div class="col-md-5 col-lg-4">
			<?php if(isset($_GET['failed']) && $_GET['failed'] == 1){ ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Login failed!</strong><br> You have entered an invalid email or password
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
			<?php } ?>
			<div class="login-wrap p-4 p-md-5 bg-light">
				<div class="icon d-flex align-items-center justify-content-center">
					<span><i class="fa-solid fa-user"></i></span>
				</div>
				<form method="post" action="core/login.php">
					<div class="mb-3">
						<label for="email" class="form-label">Email address</label>
						<input type="email" class="form-control" id="email" name="email">
						<!-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> -->
					</div>
					<div class="mb-3">
						<label for="password" class="form-label">Password</label>
						<input type="password" class="form-control" id="password" name="password">
					</div>
					<div class="mb-3 form-check">
						<input type="checkbox" class="form-check-input" id="forgotpassword">
						<label class="form-check-label" for="forgotpassword">Remember me</label>
					</div>
					<button type="submit" class="btn btn-primary float-end" name="submit" value="1">Submit</button>
				</form>
			</div>
		</div>
	</div>	
</div>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>