<?php
	$backlink = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="./">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="IULI Information">
		<meta name="author" content="IULI IS&T">
		<title>IULI <?=(isset($title_site)) ? $title_site : '';?></title>
		<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url()?>assets/img/iuli-owl.png">
		<link href="<?=base_url()?>assets/css/style.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/css/iuli.css" rel="stylesheet">
	</head>
	<body>
        <div class="container">
            <div class="card mb-3 p-3 mt-5 shadow mb-5 rounded" style="max-width: 740px; margin: 0px auto;">
                <div class="row justify-content-md-center">
                    <div class="col-md-4">
                        <img src="https://portal.iuli.ac.id/assets/img/icon/owl-info.png" class="img-fluid" alt="">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $heading; ?></h3>
                            <p class="card-text"><?php echo $message; ?></p>
                            <a href="mailto:employee@company.ac.id">&mdash; IULI Dev Team</a>
                            <?php
                            if ($backlink) {
                            ?>
                            &emsp;&emsp;<a href="<?=$backlink;?>">Go Back..</a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</body>
</html>
