<div class="card">
    <div class="card-header">
        Unit of Subject Delivered - <?= $class_data->class_group_name ?> <?= ((isset($class_verified)) AND ($class_verified)) ? '<i class="fas fa-user-check text-success"></i>' : '' ?>
<?php
    if (($user_approved == 'yes') OR (in_array($this->session->userdata('user'), array('47013ff8-89df-11ef-8f45-0068eb6957a0')))) {
?>
        <div class="card-header-actions">
            <!-- <a href="<?= base_url()?>academic/class_group/class_absence/<?= $class_master_id?>" class="card-header-action btn btn-link" target="_blank">
                <i class="fa fa-plus"></i> Add Topics
            </a> -->
    <?php
    // if (in_array($this->session->userdata('user'), array('47013ff8-89df-11ef-8f45-0068eb6957a0', '37b0f8e9-e13c-4104-adea-6c83ca1f5855'))) {
    ?>
            <a href="<?= base_url()?>academic/class_group/class_absence/<?= $class_master_id?>/false/true" class="card-header-action btn btn-link" target="_blank">
                <i class="fa fa-plus"></i> Add Topics
            </a>
    <?php
    // }
    ?>
        </div>
<?php
    }
?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="table_unit_subject_lists">
                <thead class="bg-dark">
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Lecturer</th>
                        <th>Topics Covered</th>
                        <th>Input Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
            <div class="row">
                <div class="col-12">
                    Total Hours: <span id="totalhours"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_class_meeting">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Meeting Attendance of <span id="subject_meeting"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="meeting_subject_delivered_id" id="meeting_subject_delivered_id">
                <div class="row">
                    <div class="col-6">
                        Lecturer: <span id="subject_meeting_lecturer"></span>
                    </div>
                    <div class="col-6">
                        Date and Time: <span id="subject_meeting_date"></span> <span id="subject_meeting_time"></span>
                    </div>
                    <div class="col-12">
                        Topics Covered: <span id="subject_meeting_topics"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="table-attendance-meeting" class="table table-bordered table-stripped">
                                <thead>
                                    <tr class="bg-dark">
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Study Program</th>
                                        <th>Batch</th>
                                        <th>Attendance</th>
                                        <th>Attendance Note</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var table_attendance_meeting = $('#table-attendance-meeting').DataTable({
        ordering: false,
        paging: false,
        bInfo: false,
        searching: false,
        ajax: {
            url: '<?= base_url()?>academic/class_group/get_absence_student_lists',
            type: 'POST',
            data: function(d) {
                d.subject_delivered_id = $('#meeting_subject_delivered_id').val();
            }
        },
        columns: [
            {
                data: 'score_id',
                render: function(data, type, row) {
                    if (row.student_data) {
                        var student_data = row.student_data;
                        return student_data.student_number;
                    }
                    else {
                        return '';
                    }
                }
            },
            {
                data: 'absence_student_id',
                render: function(data, type, row) {
                    if (row.student_data) {
                        var student_data = row.student_data;
                        return student_data.personal_data_name;
                    }
                    else {
                        return '';
                    }
                }
            },
            {
                data: "absence_student_id",
                render: function(data, type, row) {
                    if (row.student_data) {
                        var student_data = row.student_data;
                        return student_data.study_program_abbreviation;
                    }
                    else {
                        return '';
                    }
                }
            },
            {
                data: 'score_id',
                render: function(data, type, row) {
                    if (row.student_data) {
                        var student_data = row.student_data;
                        return student_data.academic_year_id;
                    }
                    else {
                        return '';
                    }
                }
            },
            {data: 'absence_status'},
            {data: 'absence_note'}
        ],
    });

    $(function() {
        $('#btn_new_unit_subject').on('click', function(e) {
            e.preventDefault();
        });

        var unit_subject_table = $('#table_unit_subject_lists').DataTable({
            ajax: {
                url: '<?= base_url()?>academic/class_group/filter_class_subject_delivered',
                type: 'POST',
                data: {class_master_id: '<?= $class_master_id;?>'}
            },
            orderCellsTop: true,
            columns: [
                {data: 'date_operation'},
                {data: 'time_range'},
                {data: 'personal_data_name'},
                {data: 'subject_delivered_description'},
                {data: 'input_date'},
                {data: 'subject_delivered_id'}
            ],
            columnDefs: [
                {
                    type: 'date',
                    targets: 0
                },
                {
                    targets: -1,
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        if ('<?=$user_approved;?>' == 'yes') {
                            html += '<a href="<?=base_url()?>academic/class_group/class_absence/' + row.class_master_id + '/' + row.subject_delivered_id + '/true" class="btn btn-info btn-sm" title="Edit Unit of Subject Delivered" target="_blank"><i class="fas fa-edit"></i></a>';
                        }
                        html += '<button id="btn_action_view_report" name="btn_action_view_report" type="button" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';

                    <?php
                    if ((isset($is_inhod)) AND ($is_inhod)) {
                    ?>
                        if (row.hod_has_signed === false) {
                            // console.log(row.hod_has_signed);
                            // html += '<button id="btn_sign_hod" name="btn_sign_hod" type="button" class="btn btn-success btn-sm"><i class="fas fa-signature"></i></button>';
                        }
                    <?php
                    }
                    ?>
                        // html += '<button id="btn_action_delete" name="btn_action_delete" type="button" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ],
            order: [[0, 'desc']],
            "drawCallback": function( settings ) {
                var api = this.api();
                datatable = api.data();
                var total_hour = 0;
                for (let i = 0; i < datatable.length; i++) {
                    var rowdata = datatable[i];
                    var timestart = rowdata.subject_delivered_time_start;
                    var timeend = rowdata.subject_delivered_time_end;

                    timestart = timestart.substring(11, 13);
                    timeend = timeend.substring(11, 13);

                    var hours = parseInt(timeend) - parseInt(timestart);
                    console.log(timestart);
                    total_hour += hours;
                }

                $('#totalhours').text(total_hour);
            }
        });

        $('table#table_unit_subject_lists tbody').on('click', 'button[name="btn_action_absence"]', function(e) {
            e.preventDefault();
            var row_data = unit_subject_table.row($(this).parents('tr')).data();
            // console.log(row_data);
        });

        $('table#table_unit_subject_lists tbody').on('click', 'button[name="btn_action_view_report"]', function(e) {
            e.preventDefault();
            var row_data = unit_subject_table.row($(this).parents('tr')).data();
            // console.log(row_data);

            $('#meeting_subject_delivered_id').val(row_data.subject_delivered_id);
            $('#subject_meeting_lecturer').text(row_data.personal_data_name);
            $('#subject_meeting_date').text(row_data.date_operation);
            $('#subject_meeting_time').text(row_data.time_range);
            $('#subject_meeting_topics').text(row_data.subject_delivered_description);
            $('#subject_meeting').text('<?=$class_data->subject_name;?>');
            table_attendance_meeting.ajax.reload();
            $('#modal_class_meeting').modal('show');
        });

        $('table#table_unit_subject_lists tbody').on('click', 'button#btn_sign_hod', function(e) {
            e.preventDefault();
            var row_data = unit_subject_table.row($(this).parents('tr')).data();
            console.log(row_data);
            $.post('<?=base_url()?>academic/class_group/sign_attendance', {subject_delivered_id: row_data.subject_delivered_id}, function(result) {
                if (result.code == 0) {
                    toastr.success('Success!');
                    unit_subject_table.ajax.reload();
                } else {
                    toastr.warning(result.message);
                }
            }, 'json').fail(function(params) {
                toastr.error('Error processing data!, please info IT Dept.');
            })
        });
    });
</script>