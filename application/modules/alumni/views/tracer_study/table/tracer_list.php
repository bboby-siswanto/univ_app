<div class="card">
    <div class="card-header">
		Dikti Tracer List Alumni
		<div class="card-header-actions">
			<button class="btn btn-link card-header-action" data-toggle="dropdown" id="download_dropdown" aria-expanded="true">
                <i class="fas fa-sliders-h"></i> Quick Actions
            </button>
			<div class="dropdown-menu" aria-labelledby="download_dropdown">
				<a class="dropdown-item card-header-action btn btn-link" href="<?=base_url()?>download/excel_download/download_report_new_tracer_dikti" target="_blank" id="btn_dl_report" data-toggle="tooltip" title="Download result tracer study">
					<i class="fa fa-file-download"></i> Download Result
				</a>
				<a class="dropdown-item card-header-action btn btn-link" href="<?=base_url()?>download/download_template_report_tracer" target="_blank" data-toggle="tooltip" title="Download template for submit to kemendikbud">
					<i class="fa fa-file-download"></i> Download Template Report
				</a>
			</div>
        </div>
	</div>
	<div class="card-body">
	    <div class="table-responsive">
			<table id="tracer_table" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Name</th>
                        <th>Email</th>
                        <th>Faculty</th>
						<th>Study Program</th>
						<th>Batch</th>
						<th>Last Update</th>
                        <th>Action</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>
<script>
	var tracer_table = $('table#tracer_table').DataTable({
		ajax: {
                url: '<?=base_url()?>alumni/lists_tracer',
                type: 'POST'
			},
			columns:[
                { data: 'personal_data_name'},
				{ data: 'student_alumni_email' },
                { data: 'faculty_name' },
				{ data: 'study_program_name' },
				{ data: 'academic_year_id' },
				{ data: 'answer_timestamp' },
				{
                    data: 'personal_data_id',
                    orderable: false,
                    render: function(data, type, rows) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
						html += '<a class="btn btn-sm btn-info" href="<?=site_url('alumni/question_answer/')?>' + data + '" target="blank_"><i class="fa fa-eye"></i></a>';
                        html += '</div>';
                        return html;
                    }
                }
		]
	});
</script>


<?php 

// print_r($data_tracer);

// foreach ($personal_data as $key => $value){
// 	echo $value->personal_data_email;
// }