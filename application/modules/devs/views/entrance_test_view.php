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
		<!-- <link href="<?=base_url()?>assets/vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet"> -->
		<!-- <link href="<?=base_url()?>assets/vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet"> -->
		<link href="<?=base_url()?>assets/vendors/fontawesome/css/all.min.css" rel="stylesheet">
		<!-- <link href="<?=base_url()?>assets/vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet"> -->
		<!-- Main styles for this application-->
		<link href="<?=base_url()?>assets/css/style.css" rel="stylesheet">
		<!-- <link href="<?=base_url()?>assets/vendors/vendors/pace-progress/css/pace.min.css" rel="stylesheet"> -->
		<link href="<?=base_url()?>assets/vendors/toastr/css/toastr.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/css/iuli.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.css" rel="stylesheet">
	    <!-- <link href="<?=base_url()?>assets/vendors/DataTables/datatables.min.css" rel="stylesheet"> -->
		<link href="<?=base_url()?>assets/vendors/select2/css/select2.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/select2/css/select2-bootstrap.min.css" rel="stylesheet">
		<!-- <link href="<?=base_url()?>assets/vendors/daterangepicker/daterangepicker.css" rel="stylesheet"> -->
	    
	    <!-- CoreUI and necessary plugins-->
		<script src="<?=base_url()?>assets/vendors/jquery/js/jquery.min.js"></script>
		<!-- <script src="<?=base_url()?>assets/vendors/moment/js/moment.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/daterangepicker/daterangepicker.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.js"></script> -->
	    <script src="<?=base_url()?>assets/vendors/popper.js/js/popper.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/bootstrap/js/bootstrap.min.js"></script>
	    <!-- <script src="<?=base_url()?>assets/vendors/pace-progress/js/pace.min.js"></script> -->
	    <!-- <script src="<?=base_url()?>assets/vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script> -->
	    <script src="<?=base_url()?>assets/vendors/@coreui/coreui/js/coreui.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/toastr/js/toastr.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery/js/jquery.blockUI.js"></script>
		<script src="<?=base_url()?>assets/vendors/quill/quill.js"></script>
		<script src="<?=base_url()?>assets/vendors/ckeditor/ckeditor.js"></script>
		<!-- <script src="<?=base_url()?>assets/vendors/DataTables/datatables.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/DataTables/Buttons-1.6.1/js/buttons.colVis.js"></script> -->
		<script src="<?=base_url()?>assets/vendors/select2/js/select2.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery.maskedinput/dist/jquery.maskedinput.min.js"></script>
        <!-- Plugins and scripts required by this view-->
	    <script src="<?=base_url()?>assets/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script>
	</head>
	<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
		<header class="app-header navbar">
			<a class="navbar-brand" href="<?= base_url()?>">
				<img class="navbar-brand-full" src="<?=base_url()?>assets/img/iuli.png" height="100%" alt="IULI">
				<img class="navbar-brand-minimized" src="<?=base_url()?>assets/img/iuli.png" height="100%" alt="IULI">
			</a>
			<ul class="nav navbar-nav d-md-down-none">
                <li class="nav-item px-3">
                    <a class="nav-link" href="<?=base_url()?>">Test</a>
                </li>
			</ul>
			<ul class="nav navbar-nav ml-auto d-md-down-none">
				<li class="nav-item dropdown">
					<a class="nav-link nav-link mr-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						<?=$this->session->userdata('name')?>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-header text-center">
							<strong>Settings</strong>
						</div>
						<a class="dropdown-item disabled" href="<?=site_url('user/profile')?>">
							<i class="fa fa-user"></i> Profile
						</a>
						<a class="dropdown-item disabled" href="<?=site_url('auth/logout')?>">
							<i class="fa fa-user"></i> Logout
						</a>
					</div>
				</li>
			</ul>
			<div class="d-lg-none cst-header-name">
				<div class="dropdown">
					<a class="nav-link nav-link mr-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						<?=$this->session->userdata('name')?>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-header text-center">
							<strong>Settings</strong>
						</div>
						<a class="dropdown-item" href="<?=site_url('user/profile')?>">
							<i class="fa fa-user"></i> Profile
						</a>
						<a class="dropdown-item" href="<?=site_url('auth/logout')?>">
							<i class="fa fa-user"></i> Logout
						</a>
					</div>
				</div>
			</div>
			<button class="navbar-toggler aside-menu-toggler d-lg-none" type="button" data-toggle="aside-menu-show">
				<span class="navbar-toggler-icon"></span>
			</button>
		</header>
		<div class="app-body">
            <div class="pt-3 container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <audio controls class="form-control">
                                    <source src="<?=base_url()?>assets/vendors/MINImusic-Player-master/data/cc6ed831b3c9be53fdbc37f461e9d197.mp3" type="audio/mpeg">
                                </audio> 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<aside class="aside-menu">
				<ul class="nav navbar-nav">
                    <li class="nav-item px-3">
                        <a class="nav-link" href="<?=base_url()?>">Test md</a>
                    </li>
				</ul>
			</aside>
		</div>

		<footer class="app-footer">
			<div>
				<span>&copy;<?=date('Y', time())?> IULI</span>
			</div>
			<div class="ml-auto">
				<span>Powered by</span>
				<a href="mailto:employee@company.ac.id">IULI Dev Team</a>
			</div>
		</footer>
	</body>
<?php
	if (strtoupper($this->session->userdata('environment')) == 'SANDBOX') {
?>
	<script>
		let toastrsession_options = {
			"closeButton": false,
			"debug": false,
			"newestOnTop": false,
			"progressBar": false,
			"positionClass": "toast-bottom-left",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "0",
			"hideDuration": "0",
			"timeOut": "0",
			"extendedTimeOut": "0",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};
		toastr['info']('<?=strtoupper($this->session->userdata('environment'))?>', 'Environment', toastrsession_options);
	</script>
<?php
	}
?>
</html>
