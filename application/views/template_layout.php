<!DOCTYPE html>
<html lang="en">
	<head>
		<base href="./">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<meta name="description" content="IULI Event">
		<meta name="author" content="IULI IS&T">
		<title>IULI <?= (isset($title)) ? $title : '';?></title>
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css">
	    
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
	    <script>
	        WebFont.load({
	            google: {
	                "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
	            },
	            active: function() {
	                sessionStorage.fonts = true;
	            }
	        });
            var base_url = '<?= base_url()?>';
	    </script>

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
		<script src="<?=base_url()?>assets/vendors/DataTables/Buttons-1.6.1/js/buttons.colVis.js"></script>
		<script src="<?=base_url()?>assets/vendors/select2/js/select2.min.js"></script>
		<script src="https://pmb.iuli.ac.id/assets/vendors/sweetalertmaster/sweetalert2.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery.maskedinput/dist/jquery.maskedinput.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/canvasjs-3.2.9/jquery.canvasjs.min.js"></script>
	    <!-- Plugins and scripts required by this view-->
		<script src="<?=base_url()?>assets/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script>
		<script src="<?=base_url()?>assets/vendors/jquery/jquery_number/jquery.number.js"></script>
	    
	    <script>
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
	<body class="app header-fixed">
		<?=$body;?>
	</body>
</html>
