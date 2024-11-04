<div id="accordion">
	<div class="card">
		<div class="card-header" id="filter_title">
			Student Filter
			<div class="card-header-actions">
				<button class="btn btn-link card-header-action" data-toggle="collapse" data-target="#card_body_student_filter" aria-expanded="true" aria-expanded="card_body_student_filter">
					<i class="fas fa-caret-square-down"></i>
				</button>
			</div>
		</div>
		<div class="card-body collapse show" id="card_body_student_filter" data-parent="#accordion">
			<form method="post" id="student_filter_form" method="POST">
		<?php
		if ((isset($class_type)) AND (is_array($class_type))) {
			if (in_array('karyawan', $class_type)) {
		?>
				<input type="hidden" name="program_id" value="2">
				<input type="hidden" name="student_class_type" value="karyawan">
		<?php
			}
			else if (in_array('course', $class_type)) {
				print('<input type="hidden" name="program_id" value="9">');
			}
			else if (in_array('national', $class_type)) {
				print('<input type="hidden" name="program_id" value="3">');
			}
			else {
		?>
				<input type="hidden" name="program_id" value="1">
		<?php
			}
		}
		?>
				<div class="row">
					<!-- <div class="col-md-6"></div> -->
					<div class="col-md-6">
						<div class="form-group">
							<label for="filter_study_program_id" id="label_filter_study_program">
								Study Program
								<div class="spinner-border-mini d-none" role="status">
									<span class="sr-only">Loading...</span>
								</div>
							</label>
							<select class="form-control" name="study_program_id" id="filter_study_program_id">
								<option value="all">All</option>
								<?php
								foreach($study_program as $value){
								?>
								<option value="<?=$value->study_program_id?>" data-abbr="<?= $value->study_program_abbreviation; ?>"><?=$value->study_program_name?></option>
								<?php
								}  
								?>
							</select>
						</div>  
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="studyprogram">Batch</label>
							<select class="form-control" id="academic_year_id" name="academic_year_id">
								<option value="all">All</option>
								<?php
								foreach($batch as $value){
								?>
								<option value="<?=$value->academic_year_id?>"><?=$value->academic_year_id?></option>
								<?php
								}  
								?>
							</select>
						</div>  
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="studyprogram">Status</label>
                            <select class="form-control" name="student_status" id="filter_student_status">
								<option value="all">All</option>
								<?php
								foreach ($status_lists as $status) {
								?>
								<option value="<?= $status?>"><?= strtoupper($status);?></option>
								<?php
								}
								?>
							</select>
						</div>  
					</div>
                    <div class="col-md-6" id="form_groups">
						<div class="form-group" >
							<label>Groups</label>
							<div class="row">
                                <div class="col">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" id="based_on_reference" name="based_on_reference">
                                        <label class="custom-control-label" for="based_on_reference">Based on Reference</label>
                                    </div>
                                    <div class="custom-control custom-checkbox custom-control-inline pl-5">
                                        <input type="checkbox" class="custom-control-input" id="based_on_scholarship" name="based_on_scholarship">
                                        <label class="custom-control-label" for="based_on_scholarship">Based on Scholarship</label>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</div>
                <div class="row" id="form_scholarship">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="scholarship_name">Scholarship</label>
                            <select name="scholarship_id" id="scholarship_id" class="form-control">
                                <option value="all">All</option>
								<?php
								if ($scholarship_list) {
									foreach ($scholarship_list as $o_scholarship) {
								?>
								<option value="<?=$o_scholarship->scholarship_id;?>"><?=$o_scholarship->scholarship_name.' ('.$o_scholarship->scholarship_description.')';?></option>
								<?php
									}
								}
								?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="registration_year">Registration Year</label>
                            <select name="registration_year" id="registration_year" class="form-control">
                                <option value="all">All</option>
                                <?php
								foreach($batch as $value){
								?>
								<option value="<?=$value->academic_year_id?>"><?=$value->academic_year_id?></option>
								<?php
								}  
								?>
                            </select>
                        </div>
                    </div>
                </div>
				<button type="button" id="filter_student" class="btn btn-primary float-right">Filter</button>
			</form>
		</div>
	</div>
</div>
<script>
	var class_selected = JSON.parse('<?= (isset($class_type)) ? json_encode($class_type) : [];?>');
	if ($.inArray('regular', class_selected) !== 0) {
		document.getElementById('form_groups').remove();
		document.getElementById('form_scholarship').remove();
		// $('#form_scholarship').clear();
	}
	var selectmulti = $('#filter_student_status2').selectpicker();
    $('div#form_scholarship').hide();

	$('#filter_program_id').on('change', function(e) {
		e.preventDefault();

		let program_id = $('#filter_program_id').val();
		// if (program_id == '') {
		// 	$('#filter_study_program_id').html('<option value="All">All</option>');
		// }else {
			show_filter_study_program();
		// }
	});

    $('#based_on_scholarship').on('change', function(e) {
        e.preventDefault();

        if (this.checked) {
            $('div#form_scholarship').show(300);
        }else{
            $('div#form_scholarship').hide(300);
        }
    });

	function show_filter_study_program() {
		let program_id = $('#filter_program_id').val();
		$('label#label_filter_study_program .spinner-border-mini').removeClass('d-none');

		$.post('<?=base_url()?>study_program/get_study_program_instititute', {program_id: program_id}, function(result) {
			$('label#label_filter_study_program .spinner-border-mini').addClass('d-none');
			var s_html = '<option value="all">All</option>';
			if (result.code == 0) {
				$.each(result.data, function(index, value) {
					s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
				});
			}
			$('#filter_study_program_id').html(s_html);
		}, 'json').fail(function(params) {
			$('label#label_filter_study_program .spinner-border-mini').addClass('d-none');
			
			var s_html = '<option value="">Please select..</option><option value="all">All</option>';
			toastr.error('Error getting data!', 'Error');
		});
	}
</script>