<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Error</title>
<link rel="icon" type="image/png" sizes="16x16" href=".assets/img/iuli-owl.png">

<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {
	background-color: #e4e5e6;
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
	font-size: 19px;
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

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}

article { display: block; text-align: left; width: 650px; margin: 0 auto; }
a.links { color: #dc8100; text-decoration: none; }
a.links:hover { color: #333; text-decoration: none; }

</style>
</head>
<body>
	<?php
	$page = (isset($page_error)) ? $page_error : '';
	?>
	<article>
		<h1><?php echo $heading; ?></h1>
		<div>
			<p>Sorry for the inconvenience, an error has occurred on the page you are visiting.</p>
        	<p>Please contact the IT team to immediately fix the error on the <?=$page;?> page</p>
			<p><?php echo $message; ?></p>
			<p><a href="" class="links">&mdash; IULI Dev Team</a></p>
		</div>
	</article>
</body>
</html>