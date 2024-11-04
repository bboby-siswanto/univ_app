<form onsubmit="return false" id="form_17">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="academic_semester_community">Academic Semester</label>
                <select class="custom-select" name="academic_semester_key" id="academic_semester_community">
                    <option value=""></option>
            <?php
            if ((isset($academic_year_list)) AND ($academic_year_list)) {
                foreach ($academic_year_list as $o_academic_year) {
            ?>
                    <option value="<?=$o_academic_year->academic_year_id;?>"><?=$o_academic_year->academic_year_id.' / '.(intval($o_academic_year->academic_year_id) + 1);?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="semester_type_community">Semester Type</label>
                <select class="custom-select" name="semester_type_key" id="semester_type_community">
                    <option value=""></option>
            <?php
            if ((isset($semester_type_list)) AND ($semester_type_list)) {
                foreach ($semester_type_list as $o_semester_type) {
            ?>
                    <option value="<?=$o_semester_type->semester_type_id;?>"><?=$o_semester_type->semester_type_name;?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <span><i>assigns to:</i></span>
            <div class="btn-group btn-group-sm btn-group-toggle ml-4" data-toggle="buttons">
                <label class="btn btn-primary btn-sm active">
                    <input type="radio" name="letter_assigns_to" id="letter_assigns_to_lecturer" autocomplete="off" value="lecturer"> Lecturer
                </label>
                <label class="btn btn-primary btn-sm">
                    <input type="radio" name="letter_assigns_to" id="letter_assigns_student" autocomplete="off" value="student"> Student
                </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="option_lecturer" class="form-group">
                <label for="employee_add_community">Lecturer</label>
                <div class="input-group input-group-sm">
                    <select name="employee_add_community" id="employee_add_community" class="form-control" style="width: 80%">
                        <option value=""></option>
                    </select>
                    <button class="btn btn-success btn-sm w-10" id="btn_add_lecturer_community" type="button"><i class="fas fa-plus"></i> Add</button>
                </div>
            </div>
            <div id="option_student" class="form-group d-none">
                <label for="student_id_add_community">Student</label>
                <div class="input-group input-group-sm">
                <select name="student_id_add_community" id="student_id_add_community" class="form-control" style="width: 80%">
                    <option value=""></option>
                </select>
                    <button class="btn btn-success btn-sm w-10" id="btn_add_student_community" type="button"><i class="fas fa-plus"></i> Add</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="tablemember_community" class="table table-bordered table-hovered">
                    <thead class="bg-dark">
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-secondary float-right" type="button" data-dismiss="modal">Cancel</button>
            <button class="btn btn-info float-right" type="button" name="submit_form_17" id="submit_form_17">Generate</button>
        </div>
    </div>
</form>
<script>
var tablemember_community = $('#tablemember_community').DataTable({
    searching: false,
    info: false,
    paging: false,
    ordering: false
});

$(function() {
    $('select#academic_semester_community, select#semester_type_community').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        cache: false
    });

    $('select#employee_add_community').select2({
        allowClear: true,
        placeholder: "Please select..",
        minimumInputLength: 1,
        theme: "bootstrap",
        cache: false,
        ajax: {
            url: '<?=base_url()?>employee/get_lecturer_by_name',
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    keyword: params.term
                }
            },
            processResults: function(result) {
                data = result.data;
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.fullname,
                            id: item.personal_data_id,
                            employee_key: item.employee_id
                        }
                    })
                }
            }
        }
    });
    
    $('select#student_id_add_community').select2({
        allowClear: true,
        placeholder: "Please select..",
        minimumInputLength: 1,
        theme: "bootstrap",
        cache: false,
        ajax: {
            url: '<?=base_url()?>student/get_student_by_name',
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    keyword: params.term
                }
            },
            processResults: function(result) {
                data = result.data;
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.personal_data_name + ' - ' + item.study_program_abbreviation + '/' + item.finance_year_id,
                            id: item.personal_data_id,
                            student_key: item.student_id
                        }
                    })
                }
            }
        }
    });

    $('input[name=letter_assigns_to]').on('change', function(e) {
        e.preventDefault();

        if ($(this).val() == 'student') {
            $('#option_student').removeClass('d-none');
            $('#option_lecturer').addClass('d-none');
        }
        else {
            $('#option_lecturer').removeClass('d-none');
            $('#option_student').addClass('d-none');
        }
    });

    $('button#btn_add_lecturer_community').on('click', function(e) {
        e.preventDefault();

        var s_data = $('#employee_add_community').select2('data');
        add_table(s_data[0].text, s_data[0].employee_key, 'employee')
    });
    
    $('button#btn_add_student_community').on('click', function(e) {
        e.preventDefault();

        var s_data = $('#student_id_add_community').select2('data');
        add_table(s_data[0].text, s_data[0].student_key, 'student')
    })

    $('#tablemember_community tbody').on('click', 'button#remove_row_table', function(e) {
        e.preventDefault();

        tablemember_community.row($(this).parents('tr')).remove().draw();
    });

    $('button#submit_form_17').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });
        var form = $('form#form_17');
        var form_request = $('form#form_letter_number');
        var data = form.serialize();
        var request = form_request.serialize();
        var url = "<?=base_url()?>apps/letter_numbering/get_assignment_letter_community";
        var request_data = request + '&' + data;
        request_data += '&template_key=' + $('select#template_list').val();

        $.post(url, request_data, function(result) {
            $.unblockUI();
            table_list.ajax.reload(null, false);
            if (result.code == 0) {
                $('#modal_select_template').modal('hide');
                var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                // var win = window.open(loc, '_blank');
                // if (win) {
                //     win.focus();
                // }
                // else {
                    window.location.href = loc;
                // }
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            table_list.ajax.reload(null, false);
            // send_ajax_error(params.responseText);
            toastr.error('Error Processing request!', 'error');
        });
    });
})

function add_table(nametext, id, type) {
    let btn_remove = '<button name="remove_row_table" id="remove_row_table" type="button" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
    var nameid = (type == 'student') ? 'student_id[]' : 'employee_id[]';
    let namecolumn = nametext + ' <input type="hidden" name="' + nameid + '" value="' + id + '">';
    var rowNode = tablemember_community.row.add( [ namecolumn, type.toUpperCase(), btn_remove ] )
        .draw()
        .node();

    $( rowNode )
        .css( 'color', 'red' )
        .animate( { color: 'black' } );
}
</script>