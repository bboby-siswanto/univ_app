<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="./">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="IULI Portal">
		<meta name="author" content="IULI IS&T">
		<title>Univ Portal</title>
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
		<link href="<?=base_url()?>assets/vendors/quill/quill.snow.css" rel="stylesheet">
	    <link href="<?=base_url()?>assets/vendors/DataTables/datatables.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/select2/css/select2.min.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/select2/css/select2-bootstrap.min.css" rel="stylesheet">
		<link href="https://pmb.iuli.ac.id/assets/vendors/sweetalertmaster/sweetalert2.min.css" rel="stylesheet" type="text/css" />
		<link href="<?=base_url()?>assets/vendors/daterangepicker/daterangepicker.css" rel="stylesheet">
		<link href="<?=base_url()?>assets/vendors/animate/animate.min.css" rel="stylesheet">
		<!-- <link rel="stylesheet" href="<?=base_url()?>assets/vendors/bootstrap/datepicker/bootstrap-datepicker.min.css"> -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css">
	    
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
		<script src="<?=base_url()?>assets/vendors/DataTables/datatables.min.js"></script>
		<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/DataTables/Buttons-1.6.1/js/buttons.colVis.js"></script>
		<script src="<?=base_url()?>assets/vendors/select2/js/select2.min.js"></script>
		<script src="https://pmb.iuli.ac.id/assets/vendors/sweetalertmaster/sweetalert2.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery.maskedinput/dist/jquery.maskedinput.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/canvasjs-3.2.9/jquery.canvasjs.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquerySpellingNumber/jquerySpellingNumber.min.js"></script>
	    <!-- Plugins and scripts required by this view-->
		<script src="<?=base_url()?>assets/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script>
		<!-- <script src="<?=base_url()?>assets/vendors/bootstrap/datepicker/bootstrap-datepicker.min.js"></script> -->
		<script src="<?=base_url()?>assets/vendors/jquery/jquery_number/jquery.number.js"></script>
		<style>
		.form-control {
			height: calc(1.8048438rem + 2px);
			padding: 0.25rem 0.5rem;
			font-size: 0.765625rem;
			font-weight: 400;
			line-height: 1.5;
			color: #5c6873;
			background-color: #fff;
			background-clip: padding-box;
			border: 1px solid #e4e7ea;
			border-radius: 0.2rem;
			transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		}
		.select2-container--bootstrap .select2-selection--single {
			height: calc(1.8048438rem + 2px);
			line-height: 1.5;
			padding: 4px 24px 4px 12px;
			font-size: 0.765625rem;
		}
		.select2-container--bootstrap .select2-search--dropdown .select2-search__field {
			font-size: 0.765625rem;
		}
		.bootstrap-select.form-control {
			/* border: 1px solid #e4e7ea; */
		}
		.bootstrap-select > .dropdown-toggle {
			padding: 0.25rem 0.5rem;
			font-size: 0.765625rem;
			line-height: 1.5;
			border-radius: 0.2rem;
			height: calc(1.8048438rem + 2px);
			font-size: 0.765625rem !important;
			background-color: #fff !important;
		}
		.input-group-text {
			height: calc(1.8048438rem + 2px);
			font-size: 0.765625rem !important;
		}
		
		</style>
	    <script>
			CKEDITOR.plugins.addExternal( 'ckeditor_wiris', '<?=base_url()?>assets/vendors/ckeditor/plugins/ckeditor_wiris/plugin.js' );
			// CKEDITOR.plugins.addExternal( 'mathjax', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML' );
		    const currency_options = {
				style: 'currency',
				currency: 'IDR'
			};
			
			function format_currency(amount){
				return new Intl.NumberFormat('id-ID', currency_options).format(amount);
			}
		    
			toastr.options = {
				"closeButton": false,
				"debug": false,
				"newestOnTop": false,
				"progressBar": true,
				"positionClass": "toast-top-center",
				"preventDuplicates": true,
				"onclick": null,
				"showDuration": "300",
				"hideDuration": "1000",
				"timeOut": "5000",
				"extendedTimeOut": "1000",
				"showEasing": "swing",
				"hideEasing": "linear",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			};
			
			const options = {style: 'currency', currency: 'idr', minimumFractionDigits: 2, maximumFractionDigits: 2};
			const formatter = new Intl.NumberFormat('id', options);
		
			function objectify_form(form_array){
				var return_array = {};
				for (var i = 0; i < form_array.length; i++){
					return_array[form_array[i]['name']] = form_array[i]['value'];
				}
				return return_array;
			}

			function send_ajax_error(err_message = '') {
				$.post('<?=base_url()?>dashboard/push_notification', {message: err_message}, function(result) {}, 'json');
			}

			function ucwords(str){
				let prepositions = [
					'and', 'of', 'in', 'on', 'to', 'at', 'for', 'dan', 'di'
				];
				
				let str_array = str.toLowerCase().split(' ');
				// str_array = str.toLowerCase().split('.');
				for(let i = 0; i < str_array.length; i++){
					let str_array_titik = str_array[i].toLowerCase().split('.');
					for(let i = 0; i < str_array_titik.length; i++) {
						if(prepositions.indexOf(str_array_titik[i]) == -1) {
							let first_capital_letter = str_array_titik[i].charAt(0).toUpperCase();
							let rest_string = str_array_titik[i].slice(1);
							let result = [first_capital_letter, rest_string].join('');
							str_array_titik[i] = result;
						}
					}
					str_array[i] = str_array_titik.join('.').trim();
				}
				
				return str_array.join(' ').trim();
			}

			$(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();
			});
		</script>
	</head>
	<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
		<?php
		if ($this->session->has_userdata('session_old')) {
		?>
			<div class="alert alert-warning font-weight-bold text-light bg-very-danger fixed-bottom w-25" role="alert">
				View as <?=$this->session->userdata('name_as');?> / <a href="<?=base_url()?>devs/devs_employee/restore_session">restore</a>
			</div>
		<?php
		}
		?>
		<header class="app-header navbar">
			<button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
				<span class="navbar-toggler-icon"></span>
			</button>
			<a class="navbar-brand" href="<?= base_url()?>">
				<img class="navbar-brand-full" src="<?=base_url()?>assets/img/iuli.png" height="100%" alt="IULI">
				<img class="navbar-brand-minimized" src="<?=base_url()?>assets/img/iuli.png" height="100%" alt="IULI">
			</a>
			<button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
				<span class="navbar-toggler-icon"></span>
			</button>
			<?= modules::run('dashboard/get_topbarpage', $top_bar, 'topmenu');?>
			<ul class="nav navbar-nav ml-auto d-xs-down-none">
				<li class="nav-item dropdown d-md-down-none">
					<a class="nav-link nav-link mr-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
						<img class="img-fluid mr-1" style="height: 43px; width: 43px; border-radius: 50px;" src="<?=base_url()?>file_manager/view/0bde3152-5442-467a-b080-3bb0088f6bac/<?=$this->session->userdata('user')?>" alt="">
						<?=$this->session->userdata('name')?>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-header text-center">
							<strong>Settings</strong>
						</div>
						<a class="dropdown-item" href="<?=site_url('user/profile')?>">
							<i class="fa fa-user"></i> Profile
						</a>
						<?php
				if ($this->session->userdata('type') == 'staff') {
				?>
						<a class="dropdown-item" href="<?=site_url('hris/jobreport/default')?>">
							<i class="fa fa-calendar"></i> Calendar
						</a>
				<?php
				}
				?>
						<a class="dropdown-item" href="<?=site_url('auth/logout')?>">
							<i class="fa fa-user"></i> Logout
						</a>
					</div>
				</li>
			<?php
			if (in_array($this->session->userdata('type'), ['staff', 'lect', 'lecturer'])) {
			?>
				<li class="nav-item dropdown">
					<a class="nav-link nav-link mr-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" title="Shortcut">
						<i class="fas fa-ellipsis-v"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-header text-center">
							<strong>Shortcut</strong>
						</div>
						<a class="dropdown-item" href="<?=site_url('file_manager/view_doc/academic_calendar_2023')?>" target="_blank">
							<i class="fas fa-calendar"></i> Academic Calendar 2023
						</a>
						<a class="dropdown-item" href="<?=site_url('file_manager/view_doc/academic_calendar_2024')?>" target="_blank">
							<i class="fas fa-calendar"></i> Academic Calendar 2024
						</a>
						<a class="dropdown-item" href="<?=site_url('file_manager/view_doc/academic_regulation')?>" target="_blank">
							<i class="fas fa-calendar"></i> Academic Regulation
						</a>
						<a class="dropdown-item" href="<?=site_url('timetable/active/')?>" target="_blank">
							<i class="fas fa-calendar-week"></i> Timetable
						</a>
					</div>
				</li>
			<?php
			}
			?>
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
						<?php
				if ($this->session->userdata('type') == 'staff') {
				?>
						<a class="dropdown-item" href="<?=site_url('hris/jobreport/default')?>">
							<i class="fa fa-calendar"></i> Calendar
						</a>
				<?php
				}
				?>
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
			<?= modules::run('dashboard/get_sidebar', $side_bar);?>

			<main class="main mt-5 mt-md-0">
				<div class="container-fluid">
					<div class="pb-3"></div>
		<?php
		$s_dikti_required = '';
		if (($this->session->userdata('dikti_required') !== NULL) AND ($this->session->userdata('dikti_required') == false)) {
			$s_dikti_required = 'true';
		?>
					<div class="alert alert-danger font-weight-bold text-light bg-very-danger" role="alert">
						<marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
							To be able to access the student Portal, please fill the form bellow.
						</marquee>
					</div>
		<?php
		}
		?>
					<?=$body?>
				</div>
			</main>
			<aside class="aside-menu">
				<?= modules::run('dashboard/get_topbarpage', $top_bar, 'sidemenu');?>
			</aside>
		</div>
		<div id="wrapper">
			<button type="button" value="Scroll Top" id="tombolScrollTop" onclick="scrolltotop()"><i class="fas fa-chevron-up"></i></button>
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
		<div class="modal" tabindex="-1" role="dialog" id="modal_required_student" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-danger">Warning!</h5>
					</div>
					<div class="modal-body">
						<h6>To be able to access the student Portal, please fill the form bellow.</h6>
						<ol>
							<li>
								Your Identification Number
								<div>Fill correctly and accurately, because it becomes a certificate number. If it is filled in incorrectly, the certificate can be considered fake.</div>
								<div>For Indonesian Citizens fill with NIK (Nomor Induk Kependudukan).</div>
								<div class="pb-1">For Foreign Citizens fill in with a Passport Number.</div>
							</li>
							<li>Country of Birth</li>
							<li>
								Nationality
								<div>WNI is an Indonesian citizen</div>
								<div class="pb-1">WNA is a Foreign Citizens</div>
							</li>
							<li>Citizenship (Choose from the drop down menu)</li>
							<li>Forms marked with <span class="required_text"></span></li>
						</ol>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Fill it</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal" tabindex="-1" role="dialog" id="filter_halfway_transcript">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Halfway Transcript <span id="student_halfway"></span></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form id="form_filter_halfway" onsubmit="return false;">
							<input type="hidden" id="transcript_send_email" name="send_email" value="">
							<input type="hidden" id="halfway_student_id" name="student_id">
							<div class="row">
								<?=modules::run('academic/score/form_filter_halfway')?>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="float-right mt-3 pr-3">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="asc_sign" name="asc_sign">
											<label class="custom-control-label" for="asc_sign">ASC Sign</label>
										</div>
									</div>
									<div class="float-right mt-3 pr-3">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="halfway_ects" name="ects">
											<label class="custom-control-label" for="halfway_ects">With ECTS</label>
										</div>
									</div>
									<div class="float-right mt-3 pr-3">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="f_grade" name="f_grade" checked>
											<label class="custom-control-label" for="f_grade">With F Grade</label>
										</div>
									</div>
									<!-- <div class="float-right mt-3 pr-3">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" id="short_semester" name="short_semester" checked>
											<label class="custom-control-label" for="short_semester">Include score short semester</label>
										</div>
									</div> -->
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="btn_send_generate_halfway">Download and Send Transcript</button>
						<button type="button" class="btn btn-primary" id="btn_generate_halfway">Download Halfway Transcript</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<div class="modal" tabindex="-1" role="dialog" id="modal_email_halfway_transcript">
			<div class="modal-dialog  modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Send Email to Student</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<?= modules::run('messaging/academic_email_form')?>
					</div>
					<div class="modal-footer">
						<button id="send_transcript_mail" type="button" class="btn btn-primary">Send</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			if ("<?= $s_dikti_required;?>" != '') {
				$('#modal_required_student').modal('show');
			}
			<?php
			if ($this->session->has_userdata('student_id')) {
				if ((intval(date('d')) <= 10) OR (intval(date('d')) > 28)) {
					if (!$this->session->has_userdata('has_show_notification_alert')) {
						$this->session->set_userdata('has_show_notification_alert', true);
					?>
						Swal.fire({
							title: '<strong>Announcement</strong>',
							icon: 'info',
							html:
								"<p><strong>For students who still have payment arrears, will not receive final exam results, wifi access and the student portal will be temporarily disabled.</strong></p>" +
								'<p><strong>For students who are working on a thesis, if all administration has not been completed, they are not allowed to take part in the thesis defense in August 2023</strong></p>',
							showCloseButton: false,
							showCancelButton: true,
							showConfirmButton: false,
							focusConfirm: false,
							// confirmButtonText:
							// 	'<i class="fa fa-thumbs-up"></i> Great!',
							// confirmButtonAriaLabel: 'Thumbs up, great!',
							cancelButtonText:
								'Close',
							cancelButtonAriaLabel: 'Close'
						});
					<?php
					}
				}

				if (date('Y-m-d') < date('Y-m-d', strtotime('2023-12-01'))) {
					if (!$this->session->has_userdata('has_show_international_information')) {
						$this->session->set_userdata('has_show_international_information', true);
						?>
						Swal.fire({
							title: '<strong>Call for applications</strong>',
							icon: 'warning',
							html: '<p>University Summer Course Scholarships for Indonesian Bachelor and Master Students from All Majors 2024 </p>',
							footer: '<a href="" target="_blank">Click here for more information?</a>',
							showDenyButton: false,
							showCloseButton: false,
							showCancelButton: false,
							showConfirmButton: false,
						});
						<?php
					}
				}
			}
			?>
		</script>
		<?php
			// echo modules::run('validation_requirement/staff_assessment_univ');
			// echo modules::run('validation_requirement/lecturer_assesment/validate');
		?>
<?php
	if (($this->session->userdata('alumni_required') !== NULL) AND ($this->session->userdata('alumni_required') == false)) {
		print modules::run('alumni/show_tracer_study_modal');
?>
		<script>$('div#alumni_tracer_study_modal').modal('show');</script>
<?php
	}else if (($this->session->userdata('has_working') !== NULL) AND ($this->session->userdata('has_working') == 'no')) {
		print modules::run('alumni/show_job_history_modal');
?>
		<script>
			Swal.fire({
				title: 'Have you found a job placement (already working)?',
				icon: 'warning',
				showCancelButton: true,
				allowEscapeKey : false,
				allowOutsideClick: false,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No',
			}).then((result) => {
				if (result.value) {
					$('div#modal_input_history_job').modal('show');
				}else{
					$.post('<?=base_url()?>alumni/update_job_data', {filled: 'yes'}, function(result) {
						if (result.code == 0) {
							Swal.fire(
								'',
								'Thank you for participating in filling out the IULI graduate user survey. When you get a job, please update the job data on the page <b>Job History</b>!.',
								'success'
							);
						}else{
							toastr.warning(result.message);
						}
					}, 'json').fail(function(e) {
						toastr.error('Fail processing your data!', 'Error');
					});
				}
			});
		</script>
<?php
	}

	if (($this->session->has_userdata('message_academic')) AND ($this->session->userdata('message_academic'))) {
?>
		<div class="modal" id="for_inactive_inactive" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Hello</h5>
				</div>
				<div class="modal-body">
					<p>Please contact Academic Service Center or your Head of Study Program for your KRS.!!</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
				</div>
			</div>
		</div>
		<script>
		$('#for_inactive_inactive').modal('show');
		</script>
<?php
	}
	if (($this->session->has_userdata('student_status')) AND (in_array($this->session->userdata('student_status'), ['inactive', 'onleave', 'resign', 'dropout', 'cancel']))) {
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
	
			toastr['warning']('You are <?=strtoupper($this->session->userdata('student_status'))?> student', 'Environment', toastrsession_options);
		</script>
	<?php
	}
?>
	<script>
		$(window).scroll(function(){
			if ($(window).scrollTop() > 100) {
				$('#tombolScrollTop').fadeIn();
			} else {
				$('#tombolScrollTop').fadeOut();
			}
		});

		function scrolltotop()
		{
			$('html, body').animate({scrollTop : 0},500);
		}
	</script>
	</body>
</html>
