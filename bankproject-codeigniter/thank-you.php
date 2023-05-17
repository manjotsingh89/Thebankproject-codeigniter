<?php
session_start();
$a = isset($_SESSION['ApplicationID']) ? $_SESSION['ApplicationID'] : null;

if (!$a) {
	header('location: login.php');
}

?>


<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>TAB Global - Thank You</title>
	<link rel="icon" type="image/x-icon" href="img/favicon.ico" />
	<link href='https://fonts.googleapis.com/css?family=Lato:300,400|Montserrat:700' rel='stylesheet' type='text/css'>
	<style>
		@import url(//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css);
		@import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
	</style>
	<script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/jquery-1.9.1.min.js"></script>
	<script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/html5shiv.js"></script>
	<style type="text/css">
		@font-face {
		    font-family: "Akkurat-Regular";
		    src:url("../font/akkurat/lineto-akkurat-regular.eot");
		    src:url("../font/akkurat/lineto-akkurat-regular.eot?#iefix") format("embedded-opentype"),
		        url("../font/akkurat/lineto-akkurat-regular.woff") format("woff");
		    font-weight: normal;
		    font-style: normal;
		}

		.cf:before,
		.cf:after {
		    content: " ";
		    display: table;
		}
		.cf:after {
		    clear: both;
		}

		* {
			box-sizing: border-box;
		}

		html {
			font-size: 16px;
			background-color: #fffffe;
		}
		body {
			padding: 0 20px;
			min-width: 300px;
			font-family: 'Akkurat-Regular', sans-serif;
			background-color: #fffffe;
			color: #1a1a1a;
			text-align: center;
			word-wrap: break-word;
			-webkit-font-smoothing: antialiased
		}
		a:link,
		a:visited {
			color: #00c2a8;
		}
		a:hover,
		a:active {
			color: #03a994;
		}

		.site-header {
			margin: 0 auto;
			padding: 80px 0 0;
			max-width: 820px;
		}
		.site-header__title {
			margin: 0;
			font-family: Montserrat, sans-serif;
			font-size: 2.5rem;
			font-weight: 700;
			line-height: 1.1;
			text-transform: uppercase;
			-webkit-hyphens: auto;
			-moz-hyphens: auto;
			-ms-hyphens: auto;
			hyphens: auto;
		}

		.main-content {
			margin: 0 auto;
			max-width: 820px;
		}
		.main-content__checkmark {
			font-size: 4.0625rem;
			line-height: 1;
			color: #24b663;
		}
		.main-content__body {
			margin: 20px 0 0;
			font-size: 1rem;
			line-height: 1.4;
		}
		.site-footer {
			margin: 0 auto;
			padding: 80px 0 25px;
			padding: 0;
			max-width: 820px;
		}
		.site-footer__fineprint {
			font-size: 0.9375rem;
			line-height: 1.3;
			font-weight: 300;
		}
		@media only screen and (min-width: 40em) {
			.site-header {
				padding-top: 150px;
			}
			.site-header__title {
				font-size: 6.25rem;
			}
			.main-content__checkmark {
				font-size: 9.75rem;
			}
			.main-content__body {
				font-size: 1.25rem;
			}
			.site-footer {
				padding: 145px 0 25px;
			}
			.site-footer__fineprint {
				font-size: 1.125rem;
			}
		}
	</style>
</head>
<body>
	<header class="site-header" id="header">
		<h1 class="site-header__title" data-lead-id="site-header-title">THANK YOU!</h1>
	</header>

	<div class="main-content">
		<i class="fa fa-check main-content__checkmark" id="checkmark"></i>
		<p class="main-content__body" data-lead-id="main-content-body">Thank you for taking the time to fill in the application. Your application has been received. We will review and have feedback for you shortly.</p>
	</div>

	<footer class="site-footer" id="footer">
		<p class="site-footer__fineprint" id="fineprint">Copyright © <?= date('Y') ?> | All Rights Reserved</p>
	</footer>
</body>
</html>