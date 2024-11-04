<div class="modal fade" tabindex="-1" role="dialog" id="modal_question_answers">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="question_title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table id="table_question_answer" class="table table-striped table-bordered">
						<thead>
							<tr>
								<th>Candidate Name</th>
								<th>Answer</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-header">
		Question Section
	</div>
	<div class="card-body">
		<form id="filter_question_section">
			<div class="row">
				<div class="col-lg-4">
					<div class="form-group">
						<label for="question_section">Question Section</label>
						<select class="form-control select2" id="question_section" name="question_section">
							<?php
							if($question_sections){
								foreach($question_sections as $val){
							?>
							<option value="<?=$val->question_section_id?>"><?=$val->question_section_name?></option>
							<?php
								}
							}	
							?>
						</select>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<label for="student_batch">Batch</label>
						<select name="student_batch" id="student_batch" class="form-control">
							<option value="">All</option>
					<?php
					if ($batch) {
						foreach ($batch as $o_batch) {
					?>
							<option value="<?=$o_batch->academic_year_id;?>"><?=$o_batch->academic_year_id;?></option>
					<?php
						}
					}
					?>
						</select>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<label for="student_status">Status</label>
						<select name="student_status" id="student_status" class="form-control">
							<option value="">All</option>
					<?php
					if (count($status_lists) > 0) {
						foreach ($status_lists as $s_status) {
					?>
							<option value="<?=$s_status;?>"><?=strtoupper($s_status);?></option>
					<?php
						}
					}
					?>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<button id="button_filter_question_section" type="button" class="btn btn-info float-right">Filter</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="card">
    <div class="card-header">
        Question List
    </div>
    <div class="card-body">
	    <div class="table-responsive">
		    <table id="table_question_list" class="table table-striped table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>Question Name</th>
						<th>Total Answers</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<script>
	let [question_section_id, question_id, question_title, candidate_batch, candidate_status] = [null, null, null, '', ''];
	
	table_question_list = $('table#table_question_list').DataTable({
		processing: true,
		ajax: {
			url: '<?=site_url('admission/questionnaire/get_questions')?>',
			method: 'POST',
			data: function(d){
				candidate_batch = $('select#student_batch').val();
				candidate_status = $('select#student_status').val();

				d.question_section_id = $('select#question_section').val();
				d.academic_year_id = $('select#student_batch').val();
				d.student_status = $('select#student_status').val();
			}
		},
		columns: [
			{ data: 'question_content' },
			{ data: 'total_answers' },
			{
				data: 'question_id',
				render: function(data, type, row){
					let html = '<div class="btn-group">'+
					'<button type="button" class="btn btn-sm btn-secondary" id="view_question_answers"><i class="fas fa-eye"></i></button>'+
					'</div>';
					return html;
				}
			}
		],
		order: [[1, 'DESC']]
	});
	
	$('table#table_question_list tbody').on('click', 'button#view_question_answers', function(){
		let data = table_question_list.row($(this).parents('tr')).data();
		question_id = data['question_id'];
		question_section_id = data['question_section_id'];
		question_title = data['question_content'];
		
		$('h5#question_title').html(question_title);
		$('div#modal_question_answers').modal('toggle');
		
		table_question_answer.ajax.reload();
	});
	
	table_question_answer = $('table#table_question_answer').DataTable({
		ajax: {
			url: '<?=site_url('admission/questionnaire/get_answers')?>',
			method: 'POST',
			data: function(d){
				d.question_id = question_id;
				d.question_section_id = question_section_id;
				d.academic_year_id = candidate_batch;
				d.student_status = candidate_status;
			}
		},
		columns: [
			{ data: 'personal_data_name' },
			{
				data: 'answer_value',
				defaultContent: 'N/A'
			}
		],
		dom: 'Bfrltip',
		buttons: [
			{
				extend: 'excel',
				messageTop: function(){
					return question_title;
				}
			}
		]
	});

	$('button#button_filter_question_section').on('click', function(e) {
		e.preventDefault();

		table_question_list.ajax.reload();
	});
	
	// $('select#question_section').on('change', function(){
	// 	table_question_list.ajax.reload();
	// });
</script>