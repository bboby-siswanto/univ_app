<div class="table-responsive">
    <caption>Mandatory / Elective Subjects</caption>
    <table id="table_score" class="table table-bordered table-hover table-striped table-sm">
        <thead class="bg-dark">
            <tr>
                <th>No</th>
                <th>Academic Semester</th>
                <th></th>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Q1</th>
                <th>Q2</th>
                <th>Q3</th>
                <th>Q4</th>
                <th>Q5</th>
                <th>Q6</th>
                <th>Quiz</th>
                <th>Final Exam</th>
                <th>Repetition Exam</th>
                <th>Final Score</th>
                <th>Absence</th>
                <th>Grade</th>
                <th>Grade Point</th>
                <th>Credit / SKS</th>
                <th>Merit</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="18">Total</td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>
            <tr>
                <td colspan="18">GPA</td>
                <td colspan="2" align="center" id="gpa_result">0</td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="table-responsive mt-5">
    <caption>Extracurricular Subject</caption>
    <table id="score_extracurricular" class="table table-bordered table-hover table-striped table-sm">
        <thead class="bg-dark">
            <tr>
                <th>No</th>
                <th>Academic Semester</th>
                <th></th>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Q1</th>
                <th>Q2</th>
                <th>Q3</th>
                <th>Q4</th>
                <th>Q5</th>
                <th>Q6</th>
                <th>Quiz</th>
                <th>Final Exam</th>
                <th>Repetition Exam</th>
                <th>Final Score</th>
                <th>Absence</th>
                <th>Grade</th>
                <th>Grade Point</th>
                <th>Credit / SKS</th>
                <th>Merit</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
	var dt_table = [];
	
	function view_score(table_id, curriculum_subject_type = null){
		if($.fn.DataTable.isDataTable('table#' + table_id)){
			dt_table[table_id].ajax.reload();
		}
		else{
			dt_table[table_id] = $('table#' + table_id).DataTable({
				order: [[1, 'asc'],[2, 'asc'],[4, 'asc']],
	            searching: false,
	            info: false,
	            paging: false,
	            processing: true,
	            // responsive: true,
	            ajax: {
	                url: '<?= base_url()?>academic/score/filter_score_student',
	                type: 'POST',
	                data: function(d){
		                d.student_id = '<?= $student_id;?>';
		                d.academic_year_id =$('#filter_academic_year_id').val();
		                d.semester_type_id = $('#filter_semester_type_id').val();
		                if(curriculum_subject_type !== null){
			                d.curriculum_subject_type = curriculum_subject_type;
		                }
	                }
	            },
	            columns: [
	                {
	                    data: 'score_id', responsivePriority: 1
	                },
	                {
	                    data: 'academic_year_score',
	                    // visible: false
	                },
	                {
	                    data: 'semester_type_id',
	                    visible: false
	                },
	                {
						data: 'subject_code',
	                    render: function ( data, type, row, meta ) {
							var html = data + '<span class="d-none">' + row.score_id + '</span>';
					<?php
					if ((isset($personal_data_allowed_setting_score)) AND (in_array($this->session->userdata('user'), $personal_data_allowed_setting_score))) {
					?>
							if (row.score_display == 'TRUE') {
								html += '<i class="fas fa-eye text-success" id="score_is_display"></i>';
							}
							else {
								html += '<i class="fas fa-eye text-secondary" id="score_is_hide"></i>';
							}
					<?php
					}
					?>
	                        return html;
	                    }
					},
	                {
						data: 'subject_name', 
						responsivePriority: 2,
						render: function(data, type, row) {
							var subject_name = data + ' <br>(' + row.lecturer_class + ')';
							if (('<?=$this->session->userdata('type');?>' == "student") && ('<?=$this->session->userdata("student_id")?>' == "d9868ebf-ef1a-4ede-80df-b16ea0df93ee")) {
								// console.log('examlink' + row.class_master_link_exam);
								// console.log('avail' + row.class_master_link_exam_available);
								if ((row.class_master_link_exam !== null) && (row.class_master_link_exam_available == 'enable')) {
									subject_name = '<a href="' + row.class_master_link_exam + '" target="_blank">' + subject_name + '</a>';
								}
							}
							return subject_name;
						}
					},
	                {data: 'score_quiz1'},
	                {data: 'score_quiz2'},
	                {data: 'score_quiz3'},
	                {data: 'score_quiz4'},
	                {data: 'score_quiz5'},
	                {data: 'score_quiz6'},
	                {data: 'score_quiz'},
	                {
	                    data: 'score_final_exam', responsivePriority: 8,
	                    render: function ( data, type, row, meta ) {
	                        return Number(data).toFixed(2);
	                    }
	                },
	                {data: 'score_repetition_exam', responsivePriority: 9},                
	                {data: 'score_sum', responsivePriority: 7, render: function(data, type, row) {
	                    return Number(data).toFixed(2);
	                }},
	                {
	                    data: 'score_absence',
	                    render: function ( data, type, row, meta ) {
	                        return Number(data).toFixed(2) + ' %';
	                    }
	                },
	                {data: 'score_grade', responsivePriority: 5},
	                {data: 'score_grade_point', responsivePriority: 6},
	                {data: 'curriculum_subject_credit', responsivePriority: 3, render: function(data, type, row) {
	                    if ($('select#filter_semester_type_id').val() == 4) {
	                        return '0';
	                    }else{
	                        return data;
	                    }
	                }},
	                {data: 'score_merit', responsivePriority: 4}
	            ],
	            columnDefs: [
	            <?php
	            if ($this->session->userdata('type') != 'staff') {  
	            ?>
	                {
	                    targets: [1,2,5,6,7,8,9,10],
	                    // targets: [3,4,5,6,7,8],
	                    visible: false
	                },
	            <?php
	            }  
	            ?>
	                {
	                    targets: [5, 6, 7, 8, 9, 10, 11, 13, 14, -1],
	                    // targets: [3, 4, 5, 6, 7, 8, 9, 11, 12, -1],
	                    render: function(data, type, row) {
	                        if (data != null) {
	                            return Number(data).toFixed(2);
	                        }else{
	                            return data;
	                        }
	                    }
	                },
	                {
	                    targets: [0,1,2,3,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19],
	                    orderable: false
	                }
	            ],
	            footerCallback: function(row, data, start, end, display) {
	                if (table_id != 'score_extracurricular') {
	                    var api = this.api(), data;
	
	                    var intVal = function ( i ) {
	                        return typeof i === 'string' ?
	                            i.replace(/[\$,]/g, '')*1 :
	                            typeof i === 'number' ?
	                                i : 0;
	                    };
	
	                    total_sks = api.column(-2, {page:'current'}).data().reduce(function(a, b) {
	                        return intVal(a) + intVal(b);
	                    }, 0);
	
	                    total_merit = api.column(-1, {page:'current'}).data().reduce(function(a, b) {
	                        return intVal(a) + intVal(b);
	                    }, 0);
	                    
	                    var gpa_result = 0;
	                    
	                    if ($('select#filter_semester_type_id').val() == 4) {
	                        $(api.column(-2).footer()).html(0);
	                        $(api.column(-1).footer()).html(0);
	                        $('#gpa_result').html(gpa_result);
	                    }else{
	                        $(api.column(-2).footer()).html(total_sks);
	                        $(api.column(-1).footer()).html(total_merit.toFixed(2));
	                        if(total_sks != 0){
		                        gpa_result = total_merit / total_sks;
	                        }
	                        $('#gpa_result').html(gpa_result.toFixed(2));
	                    }
	                }
	            }
			});
			
			dt_table[table_id].on( 'order.dt search.dt', function () {
	            dt_table[table_id].column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	                cell.innerHTML = i+1;
	            } );
	        } ).draw();
		}
	}
	
	view_score('table_score');
	view_score('score_extracurricular', 'extracurricular');

	$('button#filter_semester_score').on('click', function(e) {
        e.preventDefault();
		view_score('table_score');
		view_score('score_extracurricular', 'extracurricular');

        view_supplement({
            student_id: '<?= $student_id;?>', 
            academic_year_id: $('#filter_academic_year_id').val(), 
            semester_type_id: $('#filter_semester_type_id').val(),
        });
    });

	$('table#table_score tbody').on('click', '#score_is_hide', function(e) {
		e.preventDefault();
		
		var table_data = dt_table['table_score'].row($(this).parents('tr')).data();
		$.post('<?=base_url()?>academic/score/show_score', {score_id: table_data.score_id}, function (result) {
			view_score('table_score');
		}, 'json').fail(function() {
			view_score('table_score');
		});
	});
	
	$('table#score_extracurricular tbody').on('click', '#score_is_hide', function(e) {
		e.preventDefault();
		
		var table_data = dt_table['score_extracurricular'].row($(this).parents('tr')).data();
		$.post('<?=base_url()?>academic/score/show_score', {score_id: table_data.score_id}, function (result) {
			view_score('score_extracurricular', 'extracurricular');
		}, 'json').fail(function() {
			view_score('score_extracurricular', 'extracurricular');
		});
	});

	$('table#table_score tbody').on('click', '#score_is_display', function(e) {
		e.preventDefault();
		
		var table_data = dt_table['table_score'].row($(this).parents('tr')).data();
		$.post('<?=base_url()?>academic/score/hide_score', {score_id: table_data.score_id}, function (result) {
			view_score('table_score');
		}, 'json').fail(function() {
			view_score('table_score');
		});
	});

	$('table#score_extracurricular tbody').on('click', '#score_is_display', function(e) {
		e.preventDefault();
		
		var table_data = dt_table['score_extracurricular'].row($(this).parents('tr')).data();
		$.post('<?=base_url()?>academic/score/hide_score', {score_id: table_data.score_id}, function (result) {
			view_score('score_extracurricular', 'extracurricular');
		}, 'json').fail(function() {
			view_score('score_extracurricular', 'extracurricular');
		});
	});
</script>