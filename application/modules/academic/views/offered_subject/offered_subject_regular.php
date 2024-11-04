<style>
.self_filter {
    display: none;
}
</style>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Subject Filter
            </div>
            <div class="card-body">
                <form id="filter_curriculum_subject" url="<?=base_url()?>" onsubmit="return false">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="filter_follow_curriculum_subject" name="filter_follow_curriculum_subject" checked>
                        <label class="form-check-label" for="filter_follow_curriculum_subject">Same Program & Study Program</label>
                    </div>
                    <div class="form-group self_filter">
                        <label>Program</label>
                        <select name="cs_program_id" id="cs_program_id" class="form-control">
                <?php
                    if ($program_lists) {
                        foreach ($program_lists as $program) {
                ?>
                            <option value="<?= $program->program_id;?>"><?= $program->program_name;?></option>
                <?php
                        }
                    }
                ?>
                        </select>
                    </div>
                    <div class="form-group self_filter">
                        <label for="cs_study_program_id" id="cs_form_study_program_id_label">Study Program</label>
                        <select name="cs_study_program_id" id="cs_study_program_id" class="form-control">
                            <option value="">Please select ...</option>
                        </select>
                    </div>
                    <div class="form-group self_filter">
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-info float-right d-none" type="button" id="btn_filter_curriculum_offered_subject">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <ul class="list-group list-notes text-danger">
                    <li class="list-group-item">
                        Mechatronics Engineering (MTE) and Biomedical Engineering (BME) are sub study program of Electrical Engineering (ELE). </br>
                        All subjects offered from ELE will also be offered to MTE and BME.
                    </li>
                    <li class="list-group-item">
                        Aviation Management (AVM) is a sub study program of Management (MGT).</br>
                        All subjects offered from MGT will also be offered to AVM.
                    </li>
                    <li class="list-group-item">
                        Automotive Engineering (AUE) is a sub study program of Mechanical Engineering (MEE).<br>
                        All subjects offered from MEE will also be offered to AUE.
                    </li>
                    <li class="list-group-item">
                        Subjects offered in a specific sub study program will be marked automatically as "ELECTIVE". <br>
                        Please be aware of multiple subjects with different subject type (Elective or Mandatory) and subject credits. 
                    </li>
                    <li class="list-group-item">
                        Sub study program curriculum will not be available and the previous record will remain in archive. 
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card" id="target">
            <div class="card-header">
                Offered Subject Filter
            </div>
            <div class="card-body">
                <form id="filter_offered_subject" url="<?=base_url()?>" onsubmit="return false">
                    <div class="form-group">
                        <label for="os_form_program_id">Program</label>
                        <select name="os_form_program_id" id="os_form_program_id" class="form-control">
                            <option value=""></option>
<?php
    if ($program_lists) {
        foreach ($program_lists as $program) {
?>
            <option value="<?= $program->program_id;?>"><?= $program->program_name;?></option>
<?php
        }
    }
?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="os_form_study_program_id" id="os_form_study_program_id_label">Study Program</label>
                        <select name="os_form_study_program_id" id="os_form_study_program_id" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="os_form_academic_year_id">Academic Year</label>
                                <select name="os_form_academic_year_id" id="os_form_academic_year_id" class="form-control">
                                    <option value=""></option>
<?php
    if ($academic_year_lists) {
        $s_academic_year_id_active = ($this->session->userdata('academic_year_id_active') !== null) ? $this->session->userdata('academic_year_id_active') : '';
        foreach ($academic_year_lists as $year) {
?>
            <option value="<?= $year->academic_year_id;?>" <?= ($s_academic_year_id_active == $year->academic_year_id) ? 'selected' : '' ?>><?= $year->academic_year_id.'-'.(intval($year->academic_year_id) + 1);?></option>
<?php
        }
    }
?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="os_form_semester_type_id">Semester Academic</label>
                                <select name="os_form_semester_type_id" id="os_form_semester_type_id" class="form-control">
                                    <option value=""></option>
<?php
    if ($semester_type_lists) {
        $s_semester_type_id_active = ($this->session->userdata('semester_type_id_active') !== null) ? $this->session->userdata('semester_type_id_active') : '';
        foreach ($semester_type_lists as $semester) {
            // if ($semester->semester_type_id != 5) {
?>
            <option value="<?= $semester->semester_type_id;?>" <?= ($s_semester_type_id_active == $semester->semester_type_id) ? 'selected' : '' ?>><?= $semester->semester_type_name;?></option>
<?php
            // }
        }
    }
?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-info float-right" id="btn_submit_filter_offered_subject">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Curriculum Subject Lists <span id="cs_data_filter"></span>
                <input type="hidden" name="subject_prodi_id" id="subject_prodi_id">
                <input type="hidden" name="subject_program_id" id="subject_program_id">
                <div class="card-header-actions">
                    <button class="card-header-action btn btn-link" id="btn_new_subjectcurriculum">
                        <i class="fa fa-plus"></i> Subject
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="curriculum_subject_table" class="table table-bordered table-striped table-hover table-sm">
                        <thead class="bg-dark">
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>SKS</th>
                                <th>Curriculum Year / Semester</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Offered Subject Lists <span id="data_filter"></span>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table id="offered_subject_table" class="table table-bordered table-striped table-sm">
                        <thead class="bg-dark">
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>SKS</th>
                            <th>Lecturer</th>
                            <th>Action</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="class_modal_view_lecturer">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Team Teachings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="lists_lecturer_view">
                <?=modules::run('academic/offered_subject/view_lecturer_lists');?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="class_modal_view_member">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Students who have taken subject: <span id="subject_name_taken"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="offered_subject_id" id="offered_subject_id_taken">
                <table id="offer_subject_member" class="table table-bordered">
                    <thead class="bg-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Study Program</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="class_modal_input_lecturer">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Team Teaching</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_curriculum_offer_subject">
                <?=modules::run('academic/offered_subject/form_input_lecturer');?>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_subject_new">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New subject for Study Program <span id="prodi_new_subject"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- input semester, SKS, nama subject, code subject, subject type,  -->
                <form url="<?=base_url()?>academic/offered_subject/submit_new_subject" id="form_subject_offeredsubjects" onsubmit="return false">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="osns_semester_id">Semester</label>
                                <select name="osns_semester_id" id="osns_semester_id" class="form-control">
                                    <option value=""></option>
                            <?php
                            for ($i=1; $i <= 8; $i++) { 
                            ?>
                                    <option value="<?=$i;?>"><?=$i;?></option>
                            <?php
                            }
                            ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="osns_cur_subject_type">Subject Type</label>
                                <select name="osns_cur_subject_type" id="osns_cur_subject_type" class="form-control">
                                    <option value=""></option>
                                <?php
                                if ((isset($subject_type_enums)) AND ($subject_type_enums)) {
                                    foreach ($subject_type_enums as $s_subject_type) {
                                ?>
                                    <option value="<?=$s_subject_type;?>"><?=ucfirst(strtolower($s_subject_type));?></option>
                                <?php
                                    }
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="osns_subject_credit">Subject Credit</label>
                                <input type="text" name="osns_subject_credit" id="osns_subject_credit" class="form-control">
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="form-group">
                                <label for="osns_subject_name">Subject Name</label>
                                <input type="text" name="osns_subject_name" id="osns_subject_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="osns_subject_code">Subject Code</label>
                                <input type="text" name="osns_subject_code" id="osns_subject_code" class="form-control">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="ons_submit_new_subject">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('#os_form_program_id').val('1').trigger('change');
show_study_program();
show_study_program_curriculum();

var curriculum_subject_table = $('#curriculum_subject_table').DataTable({
    processing: true,
    // order: [[ 0, "asc" ]],
    ordering: false,
    dom: 'Bfrtip',
    buttons: [
        {
            text: 'Excel',
            extend: 'excel',
            title: 'Offered Subject List',
            exportOptions: {columns: ':visible'}
        },
        {
            text: 'Pdf',
            extend: 'pdf',
            title: 'Offered Subject List',
            exportOptions: {columns: ':visible'}
        },
        {
            text: 'Print',
            extend: 'print',
            title: 'Offered Subject List',
            exportOptions: {columns: ':visible'}
        }
    ],
    ajax: {
        url: '<?= base_url()?>academic/offered_subject/filter_curriculum_offered_subject_lists',
        type: 'POST',
        data: function(d) {
            d.term = {
                same_prodi: $('#filter_follow_curriculum_subject').is(':checked'),
                program_id: $('#cs_program_id').val(),
                study_program_id: $('#cs_study_program_id').val(),
                os_program_id: $('#os_form_program_id').val(),
                os_study_program_id: $('#os_form_study_program_id').val()
            }
        }
    },
    columns: [
        {data: 'subject_code'},
        {data: 'subject_name'},
        {data: 'curriculum_subject_credit'},
        {
            data: 'valid_academic_year',
            render: function(data, type, row) {
                return data + ' / ' + row.semester_number
            }
        },
        {
            data: 'curriculum_subject_id',
            orderable: false,
            render: function(data, type, row) {
                var html = '<div class="btn-group" role="group" aria-label="">';
                html += '<button name="btn_curriculum_add_offered_subject" type="button" data_id="'+data+'" class="btn btn-info btn-sm" data-toggle="tooltip" title="Offer Subject" ><i class="fas fa-angle-double-right"></i></button>';
                html += '</div>';

                return html;
            }
        }
    ],
});
var offered_subject_table = $('#offered_subject_table').DataTable({
    processing: true,
    // order: [[ 0, "asc" ]],
    ordering: false,
    dom: 'Bfrtip',
    buttons: [
        {
            text: 'Excel',
            extend: 'excel',
            title: 'Offered Subject List',
            exportOptions: {columns: ':visible'}
        },
        {
            text: 'Pdf',
            extend: 'pdf',
            title: 'Offered Subject List',
            exportOptions: {columns: ':visible'}
        },
        {
            text: 'Print',
            extend: 'print',
            title: 'Offered Subject List',
            exportOptions: {columns: ':visible'}
        }
    ],
    ajax: {
        url: '<?= base_url()?>academic/offered_subject/filter_offered_subject_lists',
        type: 'POST',
        data: function(d) {
            d.term = {
                academic_year_id: $('#os_form_academic_year_id').val(),
                program_id: $('#os_form_program_id').val(),
                study_program_id: $('#os_form_study_program_id').val(),
                semester_type_id: $('#os_form_semester_type_id').val()
            }
        }
    },
    columns: [
        {data: 'subject_code'},
        {data: 'subject_name'},
        {data: 'curriculum_subject_credit'},
        {data: 'lecturer_subject'},
        {
            data: 'offered_subject_id',
            orderable: false,
            render: function(data, type, row) {
                var html = '<div class="btn-group" role="group" aria-label="">';
                if(row['deleteable'] === true){
                    html += '<button name="btn_remove_offered_subject" type="button" data_id="' + data + '" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Remove Offer Subject"><i class="fas fa-trash"></i></button>';
                }
                html += '<button name="btn_team_teaching" type="button" data_id="' + data + '" class="btn btn-success btn-sm" data-toggle="tooltip" title="Team Teaching"><i class="fas fa-user-plus"></i></button>';
                html += '<button name="btn_view_lecturer" type="button" id="btn_view_lecturer" class="btn btn-info" title="View lecturer"><i class="fas fa-users"></i></button>';
                html += '<button name="btn_view_member" type="button" id="btn_view_member" class="btn btn-info" title="View Student Member"><i class="fas fa fa-child"></i></button>';
                html += '</div>';
                return html;
            }
        }
    ],
});
var offer_subject_member = $('#offer_subject_member').DataTable({
    processing: true,
    // order: [[ 0, "asc" ]],
    ordering: false,
    dom: 'Bfrtip',
    buttons: ['excel', 'pdf', 'print'],
    ajax: {
        url: '<?= base_url()?>academic/offered_subject/get_student_taken',
        type: 'POST',
        data: function(d) {
            d.offered_subject_id = $('#offered_subject_id_taken').val();
        }
    },
    columns: [
        {data: 'personal_data_name'},
        {data: 'student_number'},
        {data: 'study_program_name'}
    ]
});
$(function() {
    $('#os_form_program_id, #os_form_study_program_id, #os_form_academic_year_id, #os_form_semester_type_id').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap"
    });
    $('#cs_program_id, #cs_study_program_id').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap"
    });
    $('#osns_semester_id, #osns_cur_subject_type').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap"
    });

    $('input#osns_subject_name').autocomplete({
        autoFocus: true,
        minLength: 1,
        appendTo: 'div#modal_subject_new',
        source: function(request, response){
            var url = '<?=site_url('academic/subject/get_subject_name')?>';
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                if(rtn.data){
                    var arr = [];
                    arr = $.map(rtn.data, function(m){
                        return {
                            id: m.subject_name_id,
                            value: m.subject_name
                        };
                    });
                    response(arr);
                }
            }, 'json');
        },
    });

    $('#os_form_program_id').on('change', function(e) {
        e.preventDefault();

        let program_id = $('#os_form_program_id').val();
        if (program_id == '') {
            $('#os_form_study_program_id').html('<option value="">Please select...</option>');
        }else {
            show_study_program();
        }

        if ($('#filter_follow_curriculum_subject').is(':checked')) {
            $('#cs_program_id').val(program_id).trigger('change');
        }
    });
    $('#cs_program_id').on('change', function(e) {
        e.preventDefault();

        let program_id = $('#cs_program_id').val();
        if (program_id == '') {
            $('#cs_study_program_id').html('<option value="">Please select...</option>');
        }else {
            show_study_program_curriculum();
        }
    });

    $('#os_form_study_program_id').on('change', function(e) {
        e.preventDefault();

        if ($('#filter_follow_curriculum_subject').is(':checked')) {
            $('#cs_study_program_id').val($('#os_form_study_program_id').val()).trigger('change');
        }
    });

    $('#btn_new_subjectcurriculum').on('click', function(e) {
        e.preventDefault();

        if ($('#subject_prodi_id').val() == '') {
            toastr.warning('Please select study program!');
        }
        else {
            $('#modal_subject_new').modal('show');
        }
    })

    $('#btn_submit_filter_offered_subject').on('click', function(e) {
        e.preventDefault();
        // console.log($('#filter_follow_curriculum_subject').is(':checked'));
        // return false;

        if (($('#os_form_program_id').val() == '') || ($('#os_form_study_program_id').val() == '') || ($('#os_form_academic_year_id').val() == '') || ($('#os_form_semester_type_id').val() == '')) {
            toastr['warning']('Please select offered subject filter field ', 'Warning');
        }else{
            filter_offered_subject();
            // filter_curriculum_subject();
            // if ($('#filter_follow_curriculum_subject').is(':checked')) {
                $('#btn_filter_curriculum_offered_subject').click();
            // }
        }
    });

    $('#btn_filter_curriculum_offered_subject').on('click', function(e) {
        e.preventDefault();

        // console.log($('#filter_curriculum_subject').serialize());
        if ($('#filter_follow_curriculum_subject').is(':checked')) {
            if (($('#os_form_program_id').val() == '') || ($('#os_form_study_program_id').val() == '')) {
                toastr['warning']('Please select offered subject filter field ', 'Warning');
            }
            else {
                filter_curriculum_subject();
                $('html, body').animate({
                    scrollTop: $("#offered_subject_table").offset().top
                }, 500);
            }
        }
        else {
            if (($('#cs_program_id').val() == '') || ($('#cs_study_program_id').val() == '')) {
                toastr['warning']('Please completed Subject filter field ', 'Warning');
            }else{
                filter_curriculum_subject();
                $('html, body').animate({
                    scrollTop: $("#offered_subject_table").offset().top
                }, 500);
            }
        }
    });

    $('#filter_follow_curriculum_subject').on('change', function(e) {
        e.preventDefault();

        if ($('#filter_follow_curriculum_subject').is(':checked')) {
            $('.self_filter').css('display', 'none');
        }
        else{
            $('.self_filter').css('display', 'block');
        }
    });
    
    $('#ons_submit_new_subject').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var form = $('#form_subject_offeredsubjects');
        var data = form.serialize();
        data += '&study_program_id=' + $('#subject_prodi_id').val();
        data += '&program_id=' + $('#subject_program_id').val();
        var url = form.attr('url');

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                $('#modal_subject_new').modal('hide');
                var s_curriculum_subject_id = result.curriculum_subject_id;
                submit_offered_subject(s_curriculum_subject_id);
                curriculum_subject_table.ajax.reload(null, false);
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing your data!');
        });
    });

    $('table#curriculum_subject_table tbody').on('click', 'button[name="btn_curriculum_add_offered_subject"]', function(e) {
        e.preventDefault();

        // var s_semester_type_id = $('#semester_type_id').val();
        var s_curriculum_subject_id = $(this).attr("data_id");
        submit_offered_subject(s_curriculum_subject_id);
    });

    $('table#offered_subject_table tbody').on('click', 'button[name="btn_remove_offered_subject"]', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });
        var s_offered_subject_id = $(this).attr("data_id");
        $.post('<?=base_url()?>academic/offered_subject/validate_student_offered_subject', {offered_subject_id: s_offered_subject_id}, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                if (confirm("Are you sure?")) {
                    remove_offered_subject(s_offered_subject_id);
                }
            }else{
                toastr.warning('Student have already taken this offered subject!', 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error proccessing data!','Error');
        });
    });

    $('table#offered_subject_table tbody').on('click', 'button[name="btn_view_lecturer"]', function(e) {
        e.preventDefault();
        var row_data = offered_subject_table.row($(this).parents('tr')).data();

        show_table_data_lecturer_offer_subject({offered_subject_id : row_data.offered_subject_id});
        $('div#class_modal_view_lecturer').modal('show');
    });

    $('table#offered_subject_table tbody').on('click', 'button[name="btn_view_member"]', function(e) {
        e.preventDefault();
        var row_data = offered_subject_table.row($(this).parents('tr')).data();

        $('#offered_subject_id_taken').val(row_data.offered_subject_id);
        $("#subject_name_taken").html(row_data.subject_name);

        offer_subject_member.ajax.reload();
        $('div#class_modal_view_member').modal('show');
    });

    $('table#offered_subject_table tbody').on('click', 'button[name="btn_team_teaching"]', function(e) {
        e.preventDefault();
        var row_data = offered_subject_table.row($(this).parents('tr')).data();
        var row_index = offered_subject_table.row($(this).parents('tr')).index();

        $('#form_input_lecturer').find('input, select').val('');
        $('#curriculum_subject_credit').text('');
        $('#remaining_allocation').text('');
        $('.dosen_pengampu').removeClass('show').addClass('d-none');
        $('#lecturer_reported').val('0');
        $("#offered_subject_type").val(row_data.curriculum_subject_type);
        
        var sks_available = (row_data.curriculum_subject_credit - row_data.sks_count_total).toFixed(2);
        if (row_data.sks_count_total < row_data.curriculum_subject_credit) {
            $('#offered_subject_id').val(row_data.offered_subject_id);
            $('#curriculum_subject_credit').text(row_data.curriculum_subject_credit);
            $('#remaining_allocation').text(sks_available);
            $('#remaining_credit').val(sks_available);
            $('#row_index_offer_subject').val(row_index);
            $('div#class_modal_input_lecturer').modal('show');
        }else{
            toastr['warning']('subject credit available = ' + sks_available, 'Warning');
        }
    });

    $('#osns_semester_id, #osns_subject_credit, #osns_subject_name').on('change', function(e) {
        e.preventDefault();

        var data = {
            subject_name: $('#osns_subject_name').val(),
            subject_credit: $('#osns_subject_credit').val(),
            study_program_id: $('#subject_prodi_id').val(),
        }
        
        $.post('<?=base_url()?>academic/offered_subject/get_subject_code', data, function(result) {
            var code = result.code;

            $('#osns_subject_code').val(code);
        }, 'json').fail(function(params) {
            toastr.error('Error retrieve subject code!');
        });
    })
})

function submit_offered_subject(s_curriculum_subject_id) {
    var data = {
            program_id: $('#os_form_program_id').val(),
            study_program_id: $('#os_form_study_program_id').val(),
            academic_year_id: $('#os_form_academic_year_id').val(),
            semester_type_id: $('#os_form_semester_type_id').val(),
            is_ofse: false,
            curriculum_subject_id: s_curriculum_subject_id
        }
        $.blockUI({ baseZ: 2000 });

        $.post('<?= base_url()?>academic/offered_subject/save_offer_subject', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr['success']('Success transfer data', 'Success');
                offered_subject_table.ajax.reload(null, false);
            }else if(result.code == 2){
                toastr.warning(result.message, 'Warning');
            }else{
                toastr['warning'](result.message, 'Warning');
                // $('html, body').animate({
                //     scrollTop: $("#target").offset().top
                // }, 500);
            }
        },'json').fail(function(params) {
            $.unblockUI();
        });
}

function remove_offered_subject(s_offered_subject_id) {
    $.blockUI({ baseZ: 2000 });
    $.post('<?= base_url()?>academic/offered_subject/remove_offered_subject', {offered_subject_id: s_offered_subject_id}, function(result) {
        $.unblockUI();
        if (result.code == 0) {
            toastr['success']('Success remove offered subject', 'Success');
            offered_subject_table.ajax.reload(null, false);
        }else{
            toastr['warning'](result.message, 'Warning');
        }
    }, 'json').fail(function(xhr, txtStatus, errThrown) {
        $.unblockUI();
    });
}

function show_study_program(setprodi = false) {
    $('label#os_form_study_program_id_label .spinner-border-mini').removeClass('d-none');
    let program_id = $('#os_form_program_id').val();

    $.post('<?=base_url()?>study_program/get_study_program_by_program', {program_id: program_id}, function(result) {
        $('label#os_form_study_program_id_label .spinner-border-mini').addClass('d-none');
        var s_html = '<option value="" selected>Please select...</option>';

        if (result.code == 0) {
            $.each(result.data, function(index, value) {
                var prodi_name = (program_id == '2') ? value.study_program_exp_name : value.study_program_name;
                s_html += '<option value="' + value.study_program_id + '" data-abbr="'+value.study_program_abbreviation+'">' + value.faculty_name + ' - ' + prodi_name + '</option>';
            });
        }
        $('#os_form_study_program_id').html(s_html);

        if (setprodi) {
            $('#os_form_study_program_id').val(setprodi);
        }
    }, 'json').fail(function(params) {
        $('label#os_form_study_program_id_label .spinner-border-mini').addClass('d-none');
        
        var s_html = '<option value="">Please select..</option><option value="All">All</option>';
        toastr.error('Error getting data!', 'Error');
    });
}
function show_study_program_curriculum(setprodi = false) {
    $('label#cs_form_study_program_id_label .spinner-border-mini').removeClass('d-none');
    let program_id = $('#cs_program_id').val();

    $.post('<?=base_url()?>study_program/get_study_program_by_program', {program_id: program_id}, function(result) {
        $('label#cs_form_study_program_id_label .spinner-border-mini').addClass('d-none');
        var s_html = '<option value="" selected>Please select...</option>';

        if (result.code == 0) {
            $.each(result.data, function(index, value) {
                s_html += '<option value="' + value.study_program_id + '" data-abbr="'+value.study_program_abbreviation+'">' + value.faculty_name + ' - ' + value.study_program_name + '</option>';
            });
        }
        $('#cs_study_program_id').html(s_html);

        if (setprodi) {
            $('#cs_study_program_id').val(setprodi);
        }
    }, 'json').fail(function(params) {
        $('label#cs_form_study_program_id_label .spinner-border-mini').addClass('d-none');
        
        var s_html = '<option value="">Please select..</option><option value="All">All</option>';
        toastr.error('Error getting data!', 'Error');
    });
}

function filter_curriculum_subject() {
    let text = [
        $('select#cs_study_program_id option:selected').data('abbr')
    ].join('/');

    $('#cs_data_filter').text(' (' + text + ')');
    $('#subject_prodi_id').val($('select#cs_study_program_id').val());
    $('#subject_program_id').val($('select#cs_program_id').val());
    $('#prodi_new_subject').text(text);
    curriculum_subject_table.ajax.reload();
}

function filter_offered_subject() {
    let text = [
        $('select#os_form_study_program_id option:selected').data('abbr'), 
        $('#os_form_academic_year_id option:selected').text(),
        $('#os_form_semester_type_id option:selected').text()
    ].join('/');

    $('#data_filter').text(' (' + text + ')');
    offered_subject_table.ajax.reload();
}
</script>