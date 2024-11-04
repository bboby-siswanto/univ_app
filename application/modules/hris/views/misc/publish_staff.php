<?php
// if ($this->session->userdata('name') != 'BUDI SISWANTO') {
// 	redirect(site_url('auth/logout'));
// }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="./">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="IULI Portal">
		<meta name="author" content="IULI IS&T">
		<title>IULI Portal</title>
		<link rel="icon" type="image/png" sizes="16x16" href="<?= base_url()?>assets/img/iuli-owl.png">
		<!-- Icons-->
		<link href="<?=base_url()?>assets/vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/fontawesome/css/all.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
		<!-- Main styles for this application-->
		<link href="<?=base_url()?>assets/css/style.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/pace-progress/css/pace.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/css/iuli.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.css" rel="stylesheet">
	    
	    <!-- CoreUI and necessary plugins-->
		<script src="<?=base_url()?>assets/vendors/jquery/js/jquery.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.js"></script>
	    <script src="<?=base_url()?>assets/vendors/popper.js/js/popper.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/bootstrap/js/bootstrap.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/@coreui/coreui/js/coreui.min.js"></script>
	    <script>
		    
			function objectify_form(form_array){
				var return_array = {};
				for (var i = 0; i < form_array.length; i++){
					return_array[form_array[i]['name']] = form_array[i]['value'];
				}
				return return_array;
			}
		</script>
	</head>
	<body>
        <div class="row">
            <div class="col-sm-10 offset-sm-1 mt-5">
                <div class="row">
                    <div class="col-sm-4">
                        <img src="<?=$url_img;?>" class="mr-3 img-fluid w-100" alt="MY_PHOTO">
                    </div>
                    <div class="col-sm-8">
                        <h1 class="display-4"><?=$user_name;?></h1>
                        <h3></h3>
                        <p class="lead"><?=$userdata->employee_id_number;?> - <?=$userdata->employee_job_title;?> - <?=$userdata->department_name;?></p>
                        <hr class="my-4">
                        <p>Is one of the <?=$employee_type;?> of the International University Liaison Indonesia.</p>
                        <a href="<?=$userdata->employee_email?>"><?=$userdata->employee_email?></a>
                    </div>
                </div>
            </div>
        </div>
	</body>
</html>
