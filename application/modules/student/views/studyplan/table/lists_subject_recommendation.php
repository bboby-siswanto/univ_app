<div class="table-responsive">
    <table id="recommendation_subject" class="table">
        <thead>
            <tr>
                <th>Subject Name</th>
                <th>Final Score</th>
                <th>Grade</th>
                <th>Credit/SKS</th>
                <th>Subject Type</th>
                <th>Subject Type</th>
            </tr>
        </thead>
    </table>
</div>
<script>
$(function() {
    var table_recommendation = $('table#recommendation_subject').DataTable({
        dom: '',
        paging: false,
        processing: true,
        responsive: true,
        ajax: {
            url: '<?= base_url()?>academic/score/filter_score_student',
            type: 'POST',
            data: {
                student_id: '<?=$o_student_data->student_id;?>', 
                academic_year_id: $('#filter_academic_year_id').val(), 
                semester_type_id: $('#filter_semester_type_id').val(),
                get_study_plan: 'true'
            }
        },
        columns: [
            {data: 'subject_name'},
            {data: 'score_sum'},
            {data: 'score_grade'},
            {data: 'curriculum_subject_credit'},
            {data: 'curriculum_subject_type'},
            {data: 'curriculum_subject_type'}
        ],
        columnDefs: [
            {
                targets: [0,1,2,3,4],
                orderable: false
            },
            {
                targets: 4,
                orderData: [5],
                render: function(data, type, rows) {
                    if (data == 'mandatory') {
                        $(this).closest('tr').addClass('bg-danger');
                    }
                    return data.toUpperCase();
                }
            },
            {
                targets: 5,
                render: function(data, type, row){
			        var weight;
			        switch(data)
			        {
				        case 'elective':
				        	weight = 1;
				        	break;
				        	
				        case 'extracurricular':
				        	weight = 2;
				        	break;
				        	
				        default:
				        	weight = 0;
				        	break;
			        }
			        return weight;
		        },
		        visible: false
            }
        ],
        order: [
	        [5, 'asc'],
	        [0, 'asc']
        ],
        createdRow: function( row, data, dataIndex){
            if( data.curriculum_subject_type == 'mandatory'){
                $(row).addClass('bg-danger');
            }
        }
    });
});
</script>