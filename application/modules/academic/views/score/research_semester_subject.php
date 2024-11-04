<?= modules::run('student/show_name', $student_id);?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Research Subject Curriculum</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="curriculum_research_semester" class="table table-bordered table-striped table-hover">
                        <thead class="bg-dark">
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Study Program</th>
                                <th>Subject Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Student Research Semester Subject</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="subject_research_semester" class="table table-bordered table-hover table-striped">
                        <thead class="bg-dark">
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    console.log('<?=$student_id;?>');
    var curriculum_research_semester = $('table#curriculum_research_semester').DataTable({
        ajax: {
            url: '<?= base_url()?>academic/ijd/get_subject_curriculum',
            type: 'POST',
            data: {student_id: '<?= $student_id;?>'}
        },
        columns: [
            {
                data: 'subject_code',
                render: function(data, type, row) {
                    return data + '<input type="hidden" value="' + row['curriculum_subject_id'] + '">';
                }
            },
            {data: 'subject_name'},
            {data: 'study_program_name'},
            {data: 'curriculum_subject_category'},
            {
                data: 'curriculum_subject_id',
                render: function(data, type, row) {
                    var btn_transfer = '<button id="btn_submit_research_semester_subject" type="button" class="btn btn-info btn-sm" title="Transfer Subject"><i class="fas fa-long-arrow-alt-right"></i></button>';
                    return btn_transfer;
                }
            }
        ]
    });

    var subject_research_semester = $('table#subject_research_semester').DataTable({
        paging: false,
        info: false,
        ajax: {
            url: '<?=base_url()?>academic/ijd/get_subject_student',
            type: 'POST',
            data: {
                student_id: '<?= $student_id;?>'
            }
        },
        columns: [
            {data: 'subject_code'},
            {data: 'subject_name'},
            {
                data: 'score_id',
                render: function(data, type, row) {
                    var html = '<div class="btn-group btn-group-sm">';

                    var btn_remove_subject = '<button type="button" class="btn btn-sm btn-danger btn-sm" id="remove_subject_research_semester" title="remove subject"><i class="fas fa-trash"></i></button>';
                    html += btn_remove_subject;

                    html += '</div>'
                    return html;
                }
            }
        ]
    });

    $('table#curriculum_research_semester tbody').on('click', 'button#btn_submit_research_semester_subject', function(e) {
        e.preventDefault();
        var row_data = curriculum_research_semester.row($(this).parents('tr')).data();
        $.blockUI();

        var data = {
            curriculum_subject_id : row_data.curriculum_subject_id,
            student_id : '<?= $student_id;?>'
        };
        $.post('<?=base_url()?>academic/ijd/submit_subject_research_semester', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                subject_research_semester.ajax.reload();
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error');
        });
    });

    $('table#subject_research_semester tbody').on('click', 'button#remove_subject_research_semester', function(e) {
        e.preventDefault();
        if (confirm('Are you sure?')) {
            var row_data = subject_research_semester.row($(this).parents('tr')).data();
            $.blockUI();

            var data = {
                data_id : row_data.score_id
            };
            $.post('<?=base_url()?>academic/ijd/remove_subject_data', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                    subject_research_semester.ajax.reload();
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
            });
        }
    });
</script>