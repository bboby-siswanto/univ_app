<?php
    $approval = 'false';
    if ((isset($this_approval)) AND ($this_approval)) {
        $approval = 'true';
        print(modules::run('student/show_name', $o_personal_data->personal_data_id));
    }
    if (($_SERVER['REMOTE_ADDR'] == '202.93.225.254') AND ($this->session->userdata('student_id') == 'fd881b07-550e-4eef-9c38-78df4b4f22d6')) {
        // print('<pre>');var_dump($a_semester_selected);exit;
    }
    // var_dump($valid_registration);exit;
    if((isset($valid_registration)) AND (!$valid_registration)){
?> 
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-hourglass-end"></i>
         Registration Period is over!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
    }
    // elseif (condition) {
    //     # code...
    // }
?>
<!-- <div class="card">
    <div class="card-header">
        Recommended Subject
    </div>
    <div class="card-body">
        <input type="hidden" id="filter_academic_year_id" name="filter_academic_year_id" value="all">
        <input type="hidden" id="filter_semester_type_id" name="filter_semester_type_id" value="all">
        <input type="hidden" id="get_study_plan" name="get_study_plan" value="true">
        <?= modules::run('student/study_plan/view_recomendation', $personal_data_id);?>
    </div>
</div> -->
<div class="card">
    <div class="card-header">
        KRS Registrations
        <?= ((isset($this_approval)) AND ($this_approval)) ? '(Semester '.$a_semester_selected['academic_year_id'].'/'.$a_semester_selected['semester_type_id'].')' : '' ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="registration_studyplan" class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Subject Name</th>
                        <th>Academic Semester</th>
                        <th>Curriculum Semester</th>
                        <th>SKS</th>
                        <th>Subject Type</th>
                        <th>Subject Type</th>
                        <th>Lecturer</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
            <?php
            if (in_array($a_semester_selected['semester_type_id'], [7,8])) {
            ?>
                    <tr>
                        <td colspan="5">Max Credit for Short Semester Year <?=$a_semester_selected['academic_year_id'];?></td>
                        <td>9</td>
                        <td colspan="3"></td>
                    </tr>
            <?php
            }else{
            ?>
                    <tr>
                        <td colspan="5">Max Credit Available for Mandatory and Elective Subject <?= (in_array($a_semester_selected['semester_type_id'], [7,8])) ? 'This Short Semester' : '' ?></td>
                        <td>
                            <?= $max_credit;?>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="5">Total Selected Credit Mandatory and Elective Subject</td>
                        <td><span class="credit_show">0</span></td>
                        <td colspan="3"></td>
                    </tr>
            <?php }?>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <div class="container-fluid">
        <?php
        if ((isset($this_approval)) AND ($this_approval)) {
            if ($valid_approval) {
        ?>
            <button type="button" id="save_study_plan" class="btn btn-info float-right <?= (!$valid_registration) ? 'd-none' : ''; ?>" <?= (!$valid_registration) ? 'disabled' : ''; ?>>Approve Selection</button>
        <?php
            }
        }else{
        ?>
            <button type="button" id="save_study_plan" class="btn btn-info <?= (!$valid_registration) ? 'd-none' : ''; ?>" <?= (!$valid_registration) ? 'disabled' : ''; ?>>Submit</button>
        <?php
        }
        ?>
        </div>
    </div>
</div>
<?php if(!isset($this_approval)){?>
<div class="alert alert-primary" role="alert">
    <?php if (in_array($a_semester_selected['semester_type_id'], [7, 8])) { ?>
        <p>You have taken total <?= 9 - intval($max_credit); ?> credits in this year for all short semester, and <?= $max_credit; ?> credits available for this semester</p>
    <?php } ?>
</div>
<?php } ?>
<?php if(!isset($this_approval)){?><div class="floating-info">
    <div class="collapse" id="collapse-info">
        <div class="info-credits">
    <?php
        if (in_array($a_semester_selected['semester_type_id'], [7, 8])) {
    ?>
            
    <?php
        }
    ?>
            Your choose <b id="all_subject_selected">0</b> subjects for a total of <b id="all_credit_selected">0</b> credits
            <ul><li><b id="mandatory_subject_selected">0</b> Subject Mandatory with <b id="mandatory_credit_selected">0</b> total credits</li>
            <li><b id="elective_subject_selected">0</b> Subject Elective with <b id="elective_credit_selected">0</b> total credits</li>
            <li><b id="extracurricular_subject_selected">0</b> Subject Extracurricular with <b id="extracurricular_credit_selected">0</b> total credits</li></ul>
        </div>
    </div>
    <button id="show_info" class="btn btn-info btn-circle float-right" data-toggle="collapse" data-target="#collapse-info" aria-expanded="false" aria-controls="collapse-info"><i class="fas fa-info"></i></button>
</div><?php } ?>
<script>
$(function() {
    let approval = '<?=$approval;?>';
    
    var active_periode = '<?=$valid_registration;?>';
    let disable = (active_periode != 0) ? '' : 'disabled';
    let has_registration = JSON.parse('<?=str_replace("'", "", json_encode($mbo_registration_data));?>');
    
    var i_sks_sum_all_credits = 0;
    var i_count_selected_all_subject = 0;

    var i_sks_sum_credit_mandatory = 0;
    var i_sks_sum_credit_elective = 0;
    var i_sks_sum_credit_extracurricular = 0;

    var i_count_selected_mandatory = 0;
    var i_count_selected_elective = 0;
    var i_count_selected_extracurricular = 0;

    let url_offered_subject_table = '<?=site_url('academic/offered_subject/filter_offered_subject_lists')?>';

    var offer_subject_table = $('table#registration_studyplan').DataTable({
        dom: '',
        responsive: true,
        processing: true,
		paging: false,
    <?php
    if ($this->session->userdata('type') == 'staff') {
    ?>
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Download Excel',
                extend: 'excel',
                title: 'Student List Data',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                text: 'Download Pdf',
                extend: 'pdf',
                title: 'Student List Data',
                exportOptions: {columns: ':visible'}
            },
            {
                text: 'Print',
                extend: 'print',
                title: 'Student List Data',
                exportOptions: {columns: ':visible'}
            }
        ],
    <?php
    }
    ?>
        ajax: {
            url: url_offered_subject_table,
            data: {
				term: {
					academic_year_id: '<?=$a_semester_selected['academic_year_id']?>',
		            study_program_id: '<?=$o_student_data->study_program_id?>',
		            semester_type_id: '<?=$a_semester_selected['semester_type_id']?>',
		            student_id: '<?=$o_student_data->student_id?>'
				}
			},
			method: 'POST'
        },
        columns: [
            {data: 'offered_subject_id'},
            {data: 'offered_subject_id'},
            {data: 'subject_name'},
            {data: 'semester_type_id'},
            {data: 'semester_number'},
            {data: 'curriculum_subject_credit'},
            {data: 'curriculum_subject_type'},
            {data: 'curriculum_subject_type'},
            {data: 'lecturer_subject'}
        ],
        columnDefs: [
            {
                targets: 0,
                render: function(data, type, rows) {
                    return '';
                }
            },
            {
                targets: [0,1,2,3,4,5,6,7,8],
                orderable: false
            },
	        {
		        targets: 6,
		        orderData: [7],
                render: function(data, type, rows) {
                    return data.toUpperCase();
                }
	        },
            {
                targets: 1,
                responsivePriority: 0
            },
	        {
		        targets: 7,
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
	        },
            {
                targets: 3,
                render: function(data, type, rows) {
                    return rows.academic_year_id + rows.semester_type_id;
                }
            },
            {
                targets: -1,
                render: function(data, type, rows) {
                    if (rows.lecturer_data.length > 1) {
                        var arr = rows.lecturer_data;
                        var lecturer = arr.join('</li><li>');
                    }else if(rows.lecturer_data.length == 1){
                        var lecturer = rows.lecturer_data[0];
                    }else{
                        var lecturer = 'N/A';
                    }
                    return '<li>' + lecturer + '</li>';
                }
            },
            {
                targets: 1,
                render: function(data, type, rows) {
                    var checked = '';
                    var input_disabled = disable;
                    var approval = '<?= ((isset($this_approval)) AND ($this_approval)) ? "true" : "false" ?>';

                    if (has_registration.length > 0) {
                        for (var i = 0; i < has_registration.length; i++) {
                            if ((has_registration[i]['score_approval'] == 'pending') || (has_registration[i]['score_approval'] == 'approved')) {
                                if (rows.curriculum_subject_id === has_registration[i]['curriculum_subject_id']) {
                                    checked = 'checked';
                                    break;
                                }
                            }

                            // if (approval == 'true') {
                            //     if ((rows.semester_type_id == '7') || (rows.semester_type_id == '8')) {
                            //         if ((has_registration[i]['score_approval'] == 'pending') || (has_registration[i]['score_approval'] == 'approved')) {
                            //             if (rows.curriculum_subject_id !== has_registration[i]['curriculum_subject_id']) {
                            //                 input_disabled = 'disabled';
                            //             }
                            //             // else{
                            //             //     input_disabled = disable;
                            //             // }
                            //         }
                            //     }
                            // }
                        }
                    }
                    // else if (approval == 'true') {
                    //     if ((rows.semester_type_id == '7') || (rows.semester_type_id == '8')) {
                    //         input_disabled = 'disabled';
                    //     }
                    // }

                    var checkbox =  '<div class="custom-control custom-checkbox">' +
                                '<input type="checkbox" class="custom-control-input" id="offered_subject_id_' + rows.offered_subject_id + '" name="offered_subject_id[]" value="' + rows.offered_subject_id + '" ' + checked + ' ' + input_disabled + '>' +
                                '<label class="custom-control-label" for="offered_subject_id_' + rows.offered_subject_id + '"></label>' +
                            '</div>';
                    return checkbox;
                }
            }
        ],
        createdRow: function(row, data, index) {
            if (has_registration.length > 0) {
                for (var i = 0; i < has_registration.length; i++) {
                    if (data.curriculum_subject_id === has_registration[i]['curriculum_subject_id']) {
                        if (has_registration[i]['score_approval'] == 'approved') {
                            $(row).addClass('bg-success');
                            console.log(has_registration[i]['subject_name']);
                        }else if(has_registration[i]['score_approval'] == 'rejected') {
                            $(row).addClass('bg-warning');
                        }
                        break;
                    }
                }
            }
        },
        order: [
	        [7, 'asc'],
	        [4, 'asc'],
	        [2, 'asc']
        ],
        initComplete: function(settings, json) {
            var checked_data = $('table#registration_studyplan tbody').find($("input[name='offered_subject_id[]']:checked"));
            $.each(checked_data, function(i, v) {
                var tr = $(this).parents('tr');
                var row = offer_subject_table.row( tr );
                var data = row.data();

                var subject_credit = parseInt(data.curriculum_subject_credit);
                i_sks_sum_all_credits += subject_credit;
                i_count_selected_all_subject++;

                switch (data.curriculum_subject_type) {
                    case 'mandatory':
                        i_sks_sum_credit_mandatory += subject_credit;
                        i_count_selected_mandatory++;
                        break;

                    case 'elective':
                        i_sks_sum_credit_elective += subject_credit;
                        i_count_selected_elective++;
                        break;
                
                    default:
                        i_sks_sum_credit_extracurricular += subject_credit;
                        i_count_selected_extracurricular++;
                        break;
                }
            });

            $('#all_subject_selected').text(i_count_selected_all_subject);
            $('#all_credit_selected').text(i_sks_sum_all_credits);

            $('#mandatory_subject_selected').text(i_count_selected_mandatory);
            $('#mandatory_credit_selected').text(i_sks_sum_credit_mandatory);
            $('#elective_subject_selected').text(i_count_selected_elective);
            $('#elective_credit_selected').text(i_sks_sum_credit_elective);
            $('#extracurricular_subject_selected').text(i_count_selected_extracurricular);
            $('#extracurricular_credit_selected').text(i_sks_sum_credit_extracurricular);
            $('.credit_show').text(i_sks_sum_credit_mandatory + i_sks_sum_credit_elective);
        }
    });

    $('table#registration_studyplan tbody').on('change', 'input[type="checkbox"]', function(e) {
        let data = offer_subject_table.row($(this).parents('tr')).data();
        let subject_credit = parseInt(data.curriculum_subject_credit);

        if (data.lecturer_data.length > 0) {
            // 
        }
		if(this.checked) {
            if (data.lecturer_data.length > 0) {
                if ((data.curriculum_subject_type == 'mandatory') || (data.curriculum_subject_type == 'elective')) {
                    if ((i_sks_sum_credit_mandatory + i_sks_sum_credit_elective + subject_credit) > parseInt('<?=$max_credit;?>')) {
                        if (('<?= $a_semester_selected['semester_type_id'] ?>' == 7) || ('<?= $a_semester_selected['semester_type_id'] ?>' == 8)) {
                            var notif = 'You have <?= $max_credit; ?> credits available for this short semester';
                        }else{
                            var notif = 'Total credits of the mandatory and elective subjects you take must not exceed <?=$max_credit;?> Credits';
                        }

                        toastr.error(notif);
                        $(this).prop('checked', false);
                    }else{
                        i_sks_sum_all_credits += subject_credit;
                        i_count_selected_all_subject++;

                        switch (data.curriculum_subject_type) {
                            case 'mandatory':
                                i_sks_sum_credit_mandatory += subject_credit;
                                i_count_selected_mandatory++;
                                break;

                            case 'elective':
                                i_sks_sum_credit_elective += subject_credit;
                                i_count_selected_elective++;
                                break;
                        
                            default:
                                break;
                        }
                    }
                }else{
                    i_sks_sum_all_credits += subject_credit;
                    i_count_selected_all_subject++;
                    i_sks_sum_credit_extracurricular += subject_credit;
                    i_count_selected_extracurricular++;
                }
            }else{
                if ('<?= $this->session->userdata('type')?>' == 'student') {
                    toastr.warning('Lecturer is Not Available, Please contact your deans!');
                }else{
                    toastr.warning('Lecturer is Not Available, Please fill lecturer first in the offered subject menu!');
                }

                $(this).prop('checked', false);
            }
        }else{
            i_sks_sum_all_credits -= subject_credit;
            i_count_selected_all_subject--;

            switch (data.curriculum_subject_type) {
                case 'mandatory':
                    i_sks_sum_credit_mandatory -= subject_credit;
                    i_count_selected_mandatory--;
                    break;

                case 'elective':
                    i_sks_sum_credit_elective -= subject_credit;
                    i_count_selected_elective--;
                    break;
            
                default:
                    i_sks_sum_credit_extracurricular -= subject_credit;
                    i_count_selected_extracurricular--;
                    break;
            }
        }

        $('#all_subject_selected').text(i_count_selected_all_subject);
        $('#all_credit_selected').text(i_sks_sum_all_credits);

        $('#mandatory_subject_selected').text(i_count_selected_mandatory);
        $('#mandatory_credit_selected').text(i_sks_sum_credit_mandatory);
        $('#elective_subject_selected').text(i_count_selected_elective);
        $('#elective_credit_selected').text(i_sks_sum_credit_elective);
        $('#extracurricular_subject_selected').text(i_count_selected_extracurricular);
        $('#extracurricular_credit_selected').text(i_sks_sum_credit_extracurricular);
        $('.credit_show').text(i_sks_sum_credit_mandatory + i_sks_sum_credit_elective);
    });

    $('button#save_study_plan').on('click', function(e) {
        e.preventDefault();

        var checked_data = $('table#registration_studyplan tbody').find($("input[name='offered_subject_id[]']:checked"));
        if (checked_data.length == 0) {
            if (has_registration.length > 0) {
                if (confirm('Are you sure to drop all subject?')) {
                    $.blockUI();
                    var url = "<?=base_url()?>krs/save_registration_study_plan";
                    var approval = '<?= ((isset($this_approval)) AND ($this_approval)) ? "true" : "false" ?>';
                    var param_data = {
                        student_id: '<?=$o_student_data->student_id?>',
                        academic_year_id: '<?= $a_semester_selected['academic_year_id'] ?>',
                        semester_type_id: '<?= $a_semester_selected['semester_type_id'] ?>'
                    };

                    $.post(url, param_data, function(result) {
                        $.unblockUI();
                        if (result.code == 0) {
                            toastr.success('Success reject all subject', 'Success!');
                            location.reload(true);
                        }else{
                            toastr.warning(result.message, 'Warning!');
                        }
                    }, 'json').fail(function(params) {
                        $.unblockUI();
                        toastr.error('Error proccessing data!', 'Error!');
                    });
                }
            }else{
                toastr.warning('Nothing subject selected!', 'Warning!');
            }
        }else{
            $.blockUI();
            
            var i_sks_sum = 0;
            var item_selected = {};
            $.each(checked_data, function(i, v) {
                var tr = $(this).parents('tr');
                var row = offer_subject_table.row( tr );
                var data = row.data();
                
                if (data.curriculum_subject_type != 'extracurricular') {
                    i_sks_sum += parseInt(data.curriculum_subject_credit);
                }
                item_selected[i] = data;
            });
            
            if (i_sks_sum > parseInt('<?=$max_credit;?>')) {
                $.unblockUI();
                toastr.warning('Max credit subject mandatory and elective is <?=$max_credit;?> SKS');
                return false;
            }else{
                var url = "<?=base_url()?>krs/save_registration_study_plan";
                var approval = '<?= ((isset($this_approval)) AND ($this_approval)) ? "true" : "false" ?>';
                
                var param_data = {
                    student_id: '<?=$o_student_data->student_id?>',
                    academic_year_id: '<?= $a_semester_selected['academic_year_id'] ?>',
                    semester_type_id: '<?= $a_semester_selected['semester_type_id'] ?>',
                    data: item_selected
                };

                $.post(url, param_data, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr.success('Success registration Study Plan', 'Success!');

                        setTimeout(function(){
                            location.reload(true);
                        }, 5000);
                        
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    $.unblockUI();
                    toastr.error('Error processing data!');
                });
            }
        }
    });
});
</script>