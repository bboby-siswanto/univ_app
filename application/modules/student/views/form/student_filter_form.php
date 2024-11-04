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
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="filter_program_id">Institute</label>
							<select class="form-control" name="program_id" id="filter_program_id">
								<option value="all" selected="selected">All</option>
								<?php
								foreach($ref_program as $value){
								?>
								<option value="<?=$value->program_id?>"><?=$value->type_of_admission_name?></option>
								<?php
								}  
								?>
							</select>
						</div>  
					</div>
					<div class="col-md-5">
						<div class="form-group">
							<label for="filter_study_program_id" id="label_filter_study_program">
								Study Program
								<!-- <div class="spinner-border-mini d-none" role="status">
									<span class="sr-only">Loading...</span>
								</div> -->
							</label>
			<?php
			if (in_array($this->session->userdata('module'), ['academic'])) {
			?>
							<select class="form-control selectpicker" name="study_program_id[]" id="filter_study_program_id"  multiple data-live-search="true" data-actions-box="true">
							<?php
								foreach($study_program as $value){
								?>
								<option value="<?=$value->study_program_id?>" data-abbr="<?= $value->study_program_abbreviation; ?>" selected="selected"><?=$value->study_program_name?></option>
								<?php
								}  
								?>
							</select>
			<?php
			}else{
			?>
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
			<?php
			}
			?>
						</div>  
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="student_class_type">Class Type</label>
							<select name="student_class_type" id="student_class_type" class="form-control">
								<option value="all">All</option>
								<option value="regular">Regular Class</option>
								<option value="karyawan">Employee Class</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
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
					<div class="col-md-4">
						<div class="form-group">
							<label for="filter_student_status2">Status</label>
			<?php
			if (in_array($this->session->userdata('module'), ['academic', 'finance'])) {
			?>
							<select class="form-control selectpicker" name="student_status[]" id="filter_student_status2"  multiple data-live-search="true" data-actions-box="true">
								<!-- <option value="all">All</option> -->
								<?php
								foreach ($status_lists as $status) {
									if (!in_array($status, ['register', 'pending', 'candidate', 'cancel'])) {
									?>
										<option value="<?= $status?>"><?= strtoupper($status);?></option>
									<?php
										}
									}
								?>
							</select>
			<?php
			}else{
			?>
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
			<?php
			}
			?>
						</div>  
					</div>
			<?php

			if (($this->session->userdata('module') == 'finance')) {
			?>
					<div class="col-md-4">
						<div class="form-group">
							<label for="select_scholarship">Scholarship</label>
							<select name="scholarship_id" id="select_scholarship" class="form-control">
								<option value="">No Selected</option>
								<option value="all">All</option>
					<?php
					if ($scholarship_data_list) {
						foreach ($scholarship_data_list as $o_scholarship) {
					?>
								<option value="<?=$o_scholarship->scholarship_id;?>">ALL <?=$o_scholarship->scholarship_name;?> SCHOLARSHIP</option>
					<?php
							if ($o_scholarship->sub_scholarship) {
								foreach ($o_scholarship->sub_scholarship as $o_sub_scholarship) {
					?>
									<option value="<?=$o_sub_scholarship->scholarship_id;?>">-- <?=$o_sub_scholarship->scholarship_name.' ('.$o_sub_scholarship->scholarship_description.')';?></option>
					<?php
								}
							}
						}
					}
					?>
							</select>
						</div>
					</div>
			<?php
			}
			
			if ($this->session->userdata('module') == 'admission') {
			?>
					<div class="col-md-4">
						<div class="form-group" >
							<label>&nbsp;</label>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="based_on_reference" name="based_on_reference">
								<label class="custom-control-label" for="based_on_reference">Based on Reference</label>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group" >
							<label>&nbsp;</label>
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="based_on_scholarship" name="based_on_scholarship">
								<label class="custom-control-label" for="based_on_scholarship">Based on Scholarship</label>
							</div>
						</div>
					</div>
			<?php
			}

			if ($this->session->userdata('module') == 'academic') {
			?>
					<div class="col-md-4">
						<div class="form-group pt-4">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="passeddefense" name="passed_defense">
								<label class="custom-control-label" for="passeddefense">Passed Defense</label>
							</div>
						</div>
					</div>
			<?php
			}
			?>
				</div>
				<button type="button" id="filter_student" class="btn btn-primary float-right">Filter</button>
			</form>
		</div>
	</div>
</div>
<script>
	var selectmulti = $('#filter_student_status2').selectpicker();
	var selectmultiprodi = $('#filter_study_program_id').selectpicker();

	// $('#filter_program_id').on('change', function(e) {
	// 	e.preventDefault();

	// 	let program_id = $('#filter_program_id').val();
	// 	// if (program_id == '') {
	// 	// 	$('#filter_study_program_id').html('<option value="All">All</option>');
	// 	// }else {
	// 		show_filter_study_program();
	// 	// }
	// });

	// function show_filter_study_program() {
	// 	let program_id = $('#filter_program_id').val();
	// 	$('label#label_filter_study_program .spinner-border-mini').removeClass('d-none');

	// 	$.post('<?=base_url()?>study_program/get_study_program_instititute', {program_id: program_id}, function(result) {
	// 		$('label#label_filter_study_program .spinner-border-mini').addClass('d-none');
	// 		var s_html = '<option value="all">All</option>';
	// 		if (result.code == 0) {
	// 			$.each(result.data, function(index, value) {
	// 				s_html += '<option value="' + value.study_program_id + '">' + value.study_program_name + '</option>';
	// 			});
	// 		}
	// 		$('#filter_study_program_id').html(s_html);
	// 	}, 'json').fail(function(params) {
	// 		$('label#label_filter_study_program .spinner-border-mini').addClass('d-none');
			
	// 		var s_html = '<option value="">Please select..</option><option value="all">All</option>';
	// 		toastr.error('Error getting data!', 'Error');
	// 	});
	// }
</script>