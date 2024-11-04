<div class="card">
    <div class="row p-3">
        <div class="col-md-4 col-12">
            <div class="form-group">
                <label for="input_year">Graduation Registration Year</label>
                <select name="input_year" id="input_year" class="form-control">
                    <option value=""></option>
        <?php
        if ((isset($academic_year_list)) AND ($academic_year_list)) {
            foreach ($academic_year_list as $o_year) {
                $selected = ($o_year->academic_year_id == date('Y')) ? 'selected="selected"' : '';
        ?>
                    <option value="<?=$o_year->academic_year_id;?>" <?=$selected;?>><?=$o_year->academic_year_id;?></option>
        <?php
            }
        }
        ?>
                </select>
            </div>
        </div>
        <hr>
        <div class="col-12">
            <div class="table-responsive">
                <table id="table_list" class="table table-hover table-bordered">
                    <thead class="bg-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Study Program</th>
                            <th>Thesis Softfile</th>
                    <?php
                    if ((isset($option_checklist)) AND (is_array($option_checklist))) {
                        foreach ($option_checklist as $s_option) {
                    ?>
                            <th><?= ucwords(str_replace('_', ' ', strtolower($s_option)));?></th>
                    <?php
                        }
                    }
                    ?>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_detail_check">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checklist Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" name="checkdetail_checklist_id" id="checkdetail_checklist_id">
                        <table class="w-100">
                            <thead>
                                <tr>
                                    <td>Student Name</td>
                                    <td>:</td>
                                    <td><span id="checkdetailtext_student"></span></td>
                                </tr>
                                <tr>
                                    <td>Study Program</td>
                                    <td>:</td>
                                    <td><span id="checkdetailtext_prodi"></span></td>
                                </tr>
                                <tr>
                                    <td>Checklist for</td>
                                    <td>:</td>
                                    <td><span id="checkdetailtext_type"></span></td>
                                </tr>
                                <tr>
                                    <td>Checklist By</td>
                                    <td>:</td>
                                    <td><span id="checkdetailtext_user"></span></td>
                                </tr>
                                <tr>
                                    <td>Checklist Datetime</td>
                                    <td>:</td>
                                    <td><span id="checkdetailtext_datetime"></span></td>
                                </tr>
                                <tr>
                                    <td>Checklist Note</td>
                                    <td>:</td>
                                    <td><span id="checkdetailtext_note"></span></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger d-none" id="remove_checklist">Remove Checklist</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="model_checklist_note">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checklist Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form url="<?=base_url()?>academic/student_academic/submit_check_graduation" method="post" onsubmit="return false" id="form_check_graduation">
                    <input type="hidden" name="check_type" id="check_type">
                    <input type="hidden" name="check_student_id" id="check_student_id">
                    <div class="row">
                        <div class="col-12">
                            <table class="w-100">
                                <thead>
                                    <tr>
                                        <td>Student Name</td>
                                        <td>:</td>
                                        <td><span id="checktext_student"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Study Program</td>
                                        <td>:</td>
                                        <td><span id="checktext_prodi"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Checklist for</td>
                                        <td>:</td>
                                        <td><span id="checktext_type"></span></td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="input_note">Note</label>
                                <textarea name="input_note" id="input_note" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_submit_checklist">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var table_list = $('#table_list').DataTable({
    pageLength: 25,
    ordering: false,
    processing: true,
    ajax: {
        url: '<?=base_url()?>academic/student_academic/get_graduation_registration',
        type: 'POST',
        data: function(d){
            d.graduation_registration_year = $('#input_year').val();
        }
    },
    columns: [
        {data: 'personal_data_name'},
        {data: 'student_number'},
        {data: 'study_program_abbreviation'},
        {
            data: 'thesis_final_submit',
            render: function(data, type, row) {
                let title = 'Checked by sistem if student has uploaded final thesis in the portal';
                // let title = 'Checked by sistem if student has paid graduation fee';
                var html = '<button type="button" id="btn_checklist" class="btn btn-warning" disabled="disabled" title="' + title + '""><i class=" fa fa-check-circle "></i></button>';
                if (data) {
                    html = '<button type="button" class="btn btn-sm btn-success">received by:<br>Sistem Portal<br>&#8810;' + data.thesis_file_uploaded + '&#8811;</button>';
                }
                return html;
            }
        },
<?php
if ((isset($option_checklist)) AND (is_array($option_checklist))) {
    foreach ($option_checklist as $s_option) {
?>
        {
            data: 'student_id',
            // visible: true,
            render: function(data, type, row) {
                var checklist = false;
                var check_date = 'tanggal';
                var check_by = 'nama';
                var checklist_student = row.checklist_data;
                if (checklist_student) {
                    $.each(checklist_student, function(i, v) {
                        if (v.checklist_type == '<?=$s_option;?>') {
                            checklist = true;
                            check_date = v.date_added;
                            check_by = v.personal_data_name;
                        }
                    })
                }
                if (checklist) {
                    return '<button type="button" class="btn btn-sm btn-success" id="btn_check_approve" data-option="<?=$s_option;?>">received by:<br>' + check_by + '<br>&#8810;' + check_date + '&#8811;</button>';
                }
                else {
                    return '<button type="button" id="btn_checklist" class="btn btn-warning" data-option="<?=$s_option;?>"><i class=" fa fa-check-circle "></i></button>';
                }
            }
        },
<?php
    }
}
?>
    ]
});
$(function() {
    $('#input_year').select2({
        allowClear: true,
        placeholder: 'Select an option',
        theme: "bootstrap"
    });

    $('#input_year').on('change', function(e) {
        table_list.ajax.reload();
    });

    $('table#table_list tbody').on('click', 'button#btn_checklist', function(e) {
        var tabledata = table_list.row($(this).parents('tr')).data();

        $('#checktext_student').html(tabledata.personal_data_name);
        $('#checktext_prodi').html(tabledata.study_program_name);
        $('#checktext_type').html($(this).attr('data-option'));
        
        $("#check_student_id").val(tabledata.student_id);
        $("#check_type").val($(this).attr('data-option'));
        $('#model_checklist_note').modal('show');
    });

    $('table#table_list tbody').on('click', 'button#btn_check_approve', function(e) {
        var tabledata = table_list.row($(this).parents('tr')).data();
        var checklist_option = $(this).attr('data-option');
        // console.log(tabledata);
        var checklist_id = '';
        var checklist_userid = '';
        var checklist_by = '';
        var checklist_datetime = '';
        var checklist_note = '';
        
        var checklist_student = tabledata.checklist_data;
        if (checklist_student) {
            $.each(checklist_student, function(i, v) {
                if (v.checklist_type == checklist_option) {
                    checklist_id = v.checklist_id;
                    checklist_note = v.checklist_note;
                    checklist_datetime = v.date_added;
                    checklist_by = v.personal_data_name;
                    checklist_userid = v.personal_data_id;
                }
            })
        }

        $('#checkdetailtext_student').html(tabledata.personal_data_name);
        $('#checkdetailtext_prodi').html(tabledata.study_program_name);
        $('#checkdetailtext_note').html(checklist_note);
        $('#checkdetailtext_type').html($(this).attr('data-option'));
        $('#checkdetailtext_user').html(checklist_by);
        $('#checkdetailtext_datetime').html(checklist_datetime);

        if (checklist_userid == '<?=$this->session->userdata('user');?>') {
            $('#checkdetail_checklist_id').val(checklist_id);
            $('#remove_checklist').removeClass('d-none');
        }
        
        // $("#checkdetail_student_id").val(tabledata.student_id);
        // $("#checkdetail_type").val($(this).attr('data-option'));
        $('#modal_detail_check').modal('show');
    });

    $('#remove_checklist').on('click', function(e) {
        e.preventDefault();

        if ($('#checkdetail_checklist_id').val() != '') {
            $.blockUI({ baseZ: 2000 });
            $.post('<?=base_url()?>academic/student_academic/remove_checklist', {check_id: $('#checkdetail_checklist_id').val()}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                    $('#modal_detail_check').modal('hide');
                    table_list.ajax.reload();
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function() {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error!');
            });
        }
    })

    $('#btn_submit_checklist').on('click', function(e) {
        let form = $('#form_check_graduation');
        let url = form.attr('url');
        var data = form.serialize();

        $.blockUI({ baseZ: 2000 });
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                $('#model_checklist_note').modal('hide');
                table_list.ajax.reload();
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        });
    });

    $('#model_checklist_note').on('hidden.bs.modal', function (e) {
        $('#checktext_student').html('');
        $('#checktext_prodi').html('');
        $('#checktext_type').html('');
        
        $("#check_student_id").val('');
        $("#check_type").val('');
    })
    
    $('#modal_detail_check').on('hidden.bs.modal', function (e) {
        $('#checkdetail_checklist_id').val('');
        // $('#checktext_student').html('');
        // $('#checktext_prodi').html('');
        // $('#checktext_type').html('');
        
        // $("#check_student_id").val('');
        // $("#check_type").val('');
        $('#remove_checklist').addClass('d-none');
    })
})
</script>