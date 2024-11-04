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
		<link href="<?=base_url()?>assets/vendors/toastr/css/toastr.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/css/iuli.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/select2/css/select2.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/select2/css/select2-bootstrap.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/daterangepicker/daterangepicker.css" rel="stylesheet">
		
	    <!-- CoreUI and necessary plugins-->
		<script src="<?=base_url()?>assets/vendors/jquery/js/jquery.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/moment/js/moment.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/daterangepicker/daterangepicker.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.js"></script>
	    <script src="<?=base_url()?>assets/vendors/popper.js/js/popper.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/bootstrap/js/bootstrap.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/pace-progress/js/pace.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/@coreui/coreui/js/coreui.min.js"></script>
	    <script src="<?=base_url()?>assets/vendors/toastr/js/toastr.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery/js/jquery.blockUI.js"></script>
		<script src="<?=base_url()?>assets/vendors/quill/quill.js"></script>
		<script src="<?=base_url()?>assets/vendors/ckeditor/ckeditor.js"></script>
		<script src="<?=base_url()?>assets/vendors/select2/js/select2.min.js"></script>
	    <!-- Plugins and scripts required by this view-->
		<script src="<?=base_url()?>assets/vendors/jquery/jquery_number/jquery.number.js"></script>
        <script>
            $(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();
			});

			function objectify_form(form_array){
				var return_array = {};
				for (var i = 0; i < form_array.length; i++){
					return_array[form_array[i]['name']] = form_array[i]['value'];
				}
				return return_array;
			}
        </script>
    </head>
    <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
        <div class="container pt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header" style="background-color: #001489 !important;">
                                <div class="row">
									<div class="col-md-10 offset-md-1">
										<div class="row align-items-end">
											<div class="col-sm-8 text-white pb-2"><h4>INTERNATIONAL UNIVERSITY LIAISON INDONESIA</h4></div>
											<div class="col-sm-4 my-auto">
												<img src="<?=base_url()?>assets/img/iuli.png" class="img-fluid"/>
											</div>
										</div>
									</div>
                                </div>
                            </div>
                            <div class="card-body">
								<div class="text-center">
									<h4>PENILAIAN KEPUASAN PENGGUNA LULUSAN</h4>
									<h5><i>ALUMNI USER SATISFACTION ASSESMENT</i></h5>
								</div>
                                <?=$form_survey;?>
                            </div>
                            <div class="card-footer text-white" style="background-color: #001489 !important;">
                                &copy; International University Liaison Indonesia.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
	
			toastr['error']('DEVELOPMENT', 'Environment', toastrsession_options);
		</script>
    </body>
</html>