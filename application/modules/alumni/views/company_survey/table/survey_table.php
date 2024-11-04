<div class="card">
    <div class="card-header">
        ALUMNI USER SATISFACTION ASSESMENT
        <div class="card-header-actions">
			<button class="btn btn-link card-header-action" data-toggle="dropdown" id="download_dropdown" aria-expanded="true">
                <i class="fas fa-sliders-h"></i> Quick Actions
            </button>
			<div class="dropdown-menu" aria-labelledby="download_dropdown">
				<a class="dropdown-item card-header-action btn btn-link" href="<?=base_url()?>download/excel_download/download_report_company_survey" target="_blank" id="btn_dl_report" data-toggle="tooltip" title="Download result alumni user saticfaction">
					<i class="fa fa-file-download"></i> Download Result
				</a>
			</div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="survey_list" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Company/Institution</th>
                        <th>Evaluator/Assessor</th>
                        <th>Alumni</th>
                        <th>Prodi/Batch</th>
                        <th>Assesment date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    var survey_list = $('table#survey_list').DataTable({
        processing: true,
        dom: 'Bfrtip',
        ajax:{
            url: '<?=base_url()?>alumni/list_survey',
            type: 'POST',
            // data: function(params) {
            //     let a_form_data = $('form#filter_alumni_form').serialize();
            //     return a_form_data;
            // }
        },
        columns: [
            {data: 'institution_name'},
            {data: 'personal_data_name'},
            {data: 'alumni_name'},
            {
                data: 'study_program_abbreviation',
                render: function(data, type, row) {
                    return data + '/' + row.student_batch;
                }
            },
            {data: 'answer_timestamp'},
            {
                data: 'personal_data_id',
                orderable: false,
                render: function(data, type, rows) {
                    // if (rows['list_answer_dikti']) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
						html += '<a class="btn btn-sm btn-info" href="<?=site_url('alumni/survey_details/')?>' + data + '" target="_blank"><i class="fa fa-eye"></i></a>';
                        html += '</div>';

                        return html;
                    // }else{
                    //     return '';
                    // }
                }
            },
        ]
    });
    $(function() {
        // 
    });
</script>