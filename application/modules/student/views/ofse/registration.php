<?php
if ($this->session->userdata('type') == 'staff') {
	print modules::run('student/show_name', $student_data->student_id, true);
}
?>

<div class="row">
	<div class="col-md-12">
		<div class="card">
		    <div class="card-header">
			    OFSE Registration <?=ucfirst($module)?>
			</div>
		    <div class="card-body">
			    <div class="table-responsive">
				    <table class="table" id="ofse_subjects_table">
			        	<thead>
				        	<tr>
					        	<th></th>
					        	<th>Subject Name</th>
					        	<!-- <th>Examiner 1</th>
					        	<th>Examiner 2</th> -->
					        	<th>Subject Type</th>
				        	</tr>
			        	</thead>
		        	</table>
			    </div>
			    
			    <div class="form-group m-4">
				    <input type="checkbox" onclick="check_requirements()" class="form-check-input" id="agree" name="agree" required>
				    <label>I understand that this registration only can be done one time only. I can not go back and re-register my subject choices</label>
			    </div>
				<div class="pt-2">
					<i class="text-danger">- You can only choose 1 type of subject "ELECTIVE UNI" and 2 types of subject "ELECTIVE FAC"</i>
				</div>
		    </div>
		    
		    <div class="card-footer">
			    <?php
				$s_btn_id = 'save';
				if($module == 'academic'){
					$s_btn_id = 'approve';
				}
				?>
				<button class="btn btn-info save" id="<?=$s_btn_id?>"><?=ucfirst($s_btn_id)?></button>
		    </div>
		</div>
	</div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_confirm" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Subject Confirmation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>Please make sure you have chosen the right subjects right.<br>Once you are go past this page, there is no going back.</p>
				<p>These are the subjects that have been selected::</p>
				<p id="subject_list"></p>
				<p>Are you sure you want to choose these subjects?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="submit_confirm_ofse">Submit</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<?php
$a_exception = [
	'783cd20f-9006-4bba-9f8b-eb7f55a81a18',
	'47787338-e1d5-4b9f-ae7b-b3e93bdd4be5'
];
?>

<script>
	var selected_el_uni = selected_el_fac = 0;
	var selected_score = <?=json_encode($selected_score)?>;
	var selected_subjects;
	var selected_subjects_name = [];
	var btn_state = false;
	var is_total_subjects_valid = false;
	var is_registered = false;
	var is_repeat = '<?=$has_repeat;?>';
	console.log(is_repeat);
	
	function check_requirements(){
		if(is_registered){
			$('input#agree').attr('disabled', true).attr('checked', true);
		}
		else{
			<?php
			if((in_array($this->session->userdata('student_id'), $a_exception)) OR ($has_repeat)){
			// if(in_array($this->session->userdata('student_id'), $a_exception)){
			?>
			let is_total_subjects_valid = true;
			<?php
			}
			?>
			if($('input#agree').is(':checked') && is_total_subjects_valid){
				$('button.save').attr('disabled', false);
			}
			else{
				$('button.save').attr('disabled', true);
			}
		}
	}
	
	function get_selected_subjects(){
		$('button.save').attr('disabled', true);
		
		selected_subjects = [];
		selected_subjects_name = [];
		$("input.curriculum_subject_id").each(function(k, v){
			if($(this).is(':checked')){
				selected_subjects.push($(this).val());

				var rows = $( ofse_subjects_table.$('input.curriculum_subject_id[value=' + $(this).val() + ']').map(function () {
					return $(this).closest('tr');
				}));

				var data_checked = ofse_subjects_table.row(rows).data();
				selected_subjects_name.push(data_checked.subject_name + ' <i>(' + data_checked.ofse_status + ')</i>');
			}
		});
		
		if(!btn_state){
			// $('button#save').addClass('d-none');
			is_total_subjects_valid = false;
			if(selected_subjects.length == 5){
				is_total_subjects_valid = true;
				check_requirements();
			}
		}
	}
	
	var ofse_subjects_table = $('table#ofse_subjects_table').DataTable({
		dom: '',
		paging: false,
		ajax: {
			url: '<?=site_url('academic/ofse/get_ofse_subject_student')?>',
			// url: '<?=site_url('academic/offered_subject/filter_offered_subject_lists')?>',
/*
			data: {
				academic_year_id: '<?=$academic_year_id?>',
				study_program_id: "<?=$student_data->study_program_id?>",
				semester_type_id: '<?=$semester_type_id?>'
			},
*/
			data: {
				term: {
					academic_year_id: '<?=$academic_year_id?>',
		            program_id: <?=$student_data->program_id?>,
		            study_program_id: "<?=$student_data->study_program_id?>",
		            semester_type_id: '<?=$semester_type_id?>',
		            student_id: '<?=($this->session->userdata('type') == 'student') ? $this->session->userdata('student_id') : $student_data->student_id;?>'
				}
			},
			dataSrc: 'data',
			method: 'POST'
		},
		columns: [
			{ data: 'curriculum_subject_id' },
			{ data: 'subject_name' },
			// { 
			// 	data: 'lecturer_data',
			// 	visible: false,
			// 	render: function(data, type, row){
			// 		let disp = 'N/A';
			// 		if(data.length >= 1){
			// 			disp = data[0];
			// 		}
			// 		return disp;
			// 	}
			// },
			// { 
			// 	data: 'lecturer_data',
			// 	visible: false,
			// 	render: function(data, type, row){
			// 		let disp = 'N/A';
			// 		if(data.length >= 2){
			// 			disp = data[1];
			// 		}
			// 		return disp;
			// 	}
			// },
			{ data: 'ofse_status' }
		],
		columnDefs: [
			{
				targets: 0,
				defaultContent: '',
				searchable: false,
				orderable: false,
				render: function(data, type, row){
					var html = '';
					
					if(row['selected'] === true){
						is_registered = true;
					}
					
					if(row['ofse_status'] == 'mandatory'){
						// let mandatory_disabled = '<?=(!in_array($this->session->userdata('student_id'), $a_exception)) ? 'disabled' : ''?>';
						let mandatory_disabled = 'disabled';
						let mandatory_checked = 'checked';
						
						if(row['selected'] === true){
							// if (!is_repeat) {
								mandatory_disabled = 'disabled';
							// }
						}

						if (is_repeat) {
							mandatory_disabled = '';
						}
						
						html = '<input type="checkbox" class="curriculum_subject_id" name="id[]" value="'+data+'" ' + mandatory_checked + '  ' + mandatory_disabled + ' >';
					}
					else{
						if(selected_score.length >= 1){
							var in_array = false;
							var approval = '';
							for(let i = 0; i < selected_score.length; i++){
								if(selected_score[i]['subject_id'] === data){
									in_array = true;
									<?php
									if($module == 'student'){
									?>
									if(selected_score[i]['approval'] === 'approved'){
										// approval = 'disabled';
										// btn_state = true;
									}
									<?php
									}
									?>
								}
								else if(selected_score[i]['subject_name'] === row.subject_name){
									in_array = true;
									<?php
									if($module == 'student'){
									?>
									if(selected_score[i]['approval'] === 'approved'){
										// approval = 'disabled';
										// btn_state = true;
									}
									<?php
									}
									?>
								}
							}
							
							if(in_array){
								html = '<input type="checkbox" class="curriculum_subject_id" name="id[]" value="'+data+'" checked '+approval+'>';
								return html;
							}
							else{
								html = '<input type="checkbox" class="curriculum_subject_id" name="id[]" value="'+data+'" '+approval+'>';
							}
						}
						else{
							html = '<input type="checkbox" class="curriculum_subject_id" name="id[]" value="'+data+'" '+approval+'>';
						}
					}
					return html;
				}
			},
			{
				targets: [0,1,2],
				orderable: false
			},
			{
				targets: -1,
				orderable: false,
				render: function(data, type, row){
					return data.replace('_', ' ').toUpperCase();
				}
			}
		],
		initComplete: function(settings, json){
			var data = json.data;
			
			for(let i = 0; i < selected_score.length; i++){
				for(let j = 0; j < data.length; j++){
					if(selected_score[i]['subject_id'] === data[j].curriculum_subject_id){
						if(data[j].ofse_status === 'elective_uni'){
							selected_el_uni++;
						}
						else{
							if(data[j].ofse_status === 'elective_fac'){
								selected_el_fac++;
							}
						}
					}
				}
			}
			get_selected_subjects();
			check_requirements();
		},
		order: [[2, 'ASC']]
	});
	
	$('table#ofse_subjects_table tbody').on('change', 'input[type="checkbox"]', function(e){
		let data = ofse_subjects_table.row($(this).parents('tr')).data();
		if(this.checked){
			switch(data['ofse_status'])
			{
				case 'elective_uni':
					selected_el_uni++;
					break;
					
				case 'elective_fac':
					selected_el_fac++;
					break;
			}
			
			if(selected_el_uni > 1){
				alert('You can only select 1');
				this.checked = false;
				selected_el_uni--;
			}
			
			if(selected_el_fac > 2){
				alert('You can only select 2');
				this.checked = false;
				selected_el_fac--;
			}
		}
		else{
			switch(data['ofse_status'])
			{
				case 'elective_uni':
					selected_el_uni--;
					break;
					
				case 'elective_fac':
					selected_el_fac--;
					break;
			}
		}
		get_selected_subjects();
	});
	
	$("input.curriculum_subject_id").on('change', function(e){
		console.log('boom');
	});
	
	<?php
	if($module == 'academic'){
	?>
	$('button#<?=$s_btn_id?>').on('click', function(e){
		var url = '<?=site_url('academic/ofse/approve_subjects')?>';
		var data = {
			subjects: selected_subjects,
			student_id: '<?=$student_id?>',
			academic_year_id: '<?=$academic_year_id?>',
			semester_type_id: '<?=$semester_type_id?>'
		};
		$.post(url, data, function(rtn){
			if(rtn.code == 0){
				toastr.success("Done!", "Success!");
			}
		}, 'json');
	});
	<?php
	}
	else{
	?>
	$('button#<?=$s_btn_id?>').on('click', function(e){
		let selected_subject_list = '<li>' + selected_subjects_name.join('</li><li>') + '</li>';
		$('p#subject_list').html('<ol>' + selected_subject_list + '</ol>');
		$('div#modal_confirm').modal('show');
	});

	$('button#submit_confirm_ofse').on('click', function(e) {
		e.preventDefault();

		$.blockUI({baseZ: 2000});
		var url = '<?=site_url('student/ofse/register')?>';
		var data = {
			subjects: selected_subjects,
			student_id: '<?=$student_id?>',
			academic_year_id: '<?=$academic_year_id?>',
			semester_type_id: '<?=$semester_type_id?>'
		};

		$.post(url, data, function(rtn){
			$.unblockUI();
			if(rtn.code == 0){
				toastr.success("Done!", "Success!");
				setTimeout(function() { 
					window.location.reload();
				}, 2000);
			}
			else{
				toastr.warning(rtn.message, 'Warning!');
			}
		}, 'json').fail(function(params) {
			$.unblockUI();
			toastr.error('Error processing data!', 'Error');
		});
	});

	<?php
	}
	?>
</script>