<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>IULI Portal</title>
<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url()?>assets/img/iuli-owl.png">

<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 29px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

p {
	margin: 12px 15px 12px 15px;
}

article {
	display: block;
	text-align: left;
	width: 650px;
	/* padding-left: 50px; */
	margin: 0 auto;
	/* background-image: url('https://portal.iuli.ac.id/assets/img/IULIclipart.png');
	background-repeat: no-repeat;
	background-attachment: fixed;
	background-position: left; 
	background-size: auto; */
}
article img {
	float: left;
	width: 120px;
    margin-right: 20px;
}
a.links { color: #dc8100; text-decoration: none; }
a.links:hover { color: #333; text-decoration: none; }

</style>
</head>
<body>
	<?php
	$backlink = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
	$page = (isset($page_error)) ? $page_error : '';
	?>
	<article>
		<img src="https://portal.iuli.ac.id/assets/img/owl-police2.png" alt="">
		<h1><?php echo $heading; ?></h1>
		<div>
			<p><?php echo $message; ?></p>
			<p>
				<a href="mailto:employee@company.ac.id" class="links">&mdash; IULI Dev Team</a>
				<?php
				if ($backlink) {
				?>
				&emsp;&emsp;|<a href="<?=$backlink;?>" style="margin-left: 50px;" class="links">Go Back..</a>
				<?php
				}
				?>
			</p>
		</div>
	</article>
</body>
</html>