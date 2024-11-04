<div class="table-responsive">
    <table id="ofse_registered_participants" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th>Student ID</th>
                <th>Student Number</th>
                <th>Nr. of Registered Subjects</th>
                <th>Nr. of Approved Subjects</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    $(function() {

		function load_ofse_participants(params){
			if($.fn.DataTable.isDataTable('table#ofse_registered_participants')){
				ofse_table_participants.DataTable.destroy();
			}
			
			ofse_table_participants = $('table#ofse_registered_participants').DataTable({
	            ajax: {
	                url: '<?= base_url()?>academic/ofse/get_ofse_participants_list',
	                type: 'POST',
	                data: params
	            },
	            columns: [
	                {data: 'student_number'},
	                {data: 'personal_data_name'},
	                {data: 'selected_subjects' },
	                {data: 'selected_subjects' },
	                { data: 'student_id' }
	            ],
	            columnDefs: [
	                {
		                targets: 2,
		                orderable: false,
		                render: function(data, type, row){
			                return data.length;
		                }
	                },
	                {
		                targets: 3,
		                orderalbe: false,
		                render: function(data, type, row){
			                let approvals = 0;
			                for(let i = 0; i < data.length; i++){
				                if(data[i].score_approval === 'approved'){
					                approvals++;
				                }
			                }
			                return [approvals, '5'].join('/');
		                }
	                },
	                {
		                targets: -1,
		                render: function(data, type, row){
			                let links = [
				                row['student_id'],
				                params.academic_year_id,
				                params.semester_type_id
			                ].join('/');
			                let html = '<div class="btn btn-group">'+
			                '<a href="<?=site_url('academic/ofse/approval/')?>'+links+'" id="view" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></button>';
			                '</div>';
			                return html;
		                }
	                }
	            ]
	        });
		}

        $('button#filter_ofse').on('click', function(e) {
            e.preventDefault();
            let form = $('form#form_ofse_filter');
            let formData = objectify_form(form.serializeArray());
            console.log(formData);
            load_ofse_participants(formData);
        });
    });
</script>