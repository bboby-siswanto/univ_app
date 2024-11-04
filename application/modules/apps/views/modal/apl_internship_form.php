<form onsubmit="return false" id="form_1">
    <input type="hidden" name="template_data" id="template_data" value="generate_internship_student_letter">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="1_student_id">Student</label>
                <select name="student_key" id="1_student_id" class="form-control"></select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="1_spv_name">To</label>
                <input type="text" class="form-control" name="supervisor_name" id="1_spv_name">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="1_company_name">Company Name</label>
                <input type="text" class="form-control" name="company_name" id="1_company_name">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="1_spv_occupation">Position</label>
                <input type="text" class="form-control" name="supervisor_occupation" id="1_spv_occupation">
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label for="1_company_address">Company Address</label>
                <textarea name="company_address" id="1_company_address" class="form-control"></textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="1_start_date">Start of Internship</label>
                <input type="text" class="form-control" name="start_date" id="1_start_date">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="1_end_date">End of Internship</label>
                <input type="text" class="form-control" name="end_date" id="1_end_date">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-secondary float-right" type="button" data-dismiss="modal">Cancel</button>
            <button class="btn btn-info float-right" type="button" name="submit_form_1" id="submit_form_1">Generate</button>
        </div>
    </div>
</form>
<script>
var select1student_list = $('select#1_student_id').select2({
    minimumInputLength: 3,
    allowClear: true,
    placeholder: "Please select",
    theme: "bootstrap",
    ajax: {
        url: "<?=base_url()?>student/get_student_by_name_general",
        type: "POST",
        dataType: 'json',
        data: function (params) {
            return {
                term: params.term,
                status: 'active'
            };
        },
        processResults: function(result) {
            data = result.data;
            if (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.personal_data_name + '(' + item.study_program_abbreviation + ' / ' + item.student_batch + ')',
                            id: item.student_id
                        }
                    })
                }
            }
            else {
                return {result:[]};
            }
        }
    }
});

var datepicker_start = $('#1_start_date').datepicker({
    dateFormat: 'MM yy',
    changeYear: true,
    changeMonth: true,
    showButtonPanel: true,
    onClose: function(dateText, inst) { 
        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
}).on('change', function() {
    datepicker_end.datepicker( "option", "minDate",  $(this).datepicker('getDate') );
    datepicker_end.datepicker('setDate', '');
}).on('focus', function () {
    $(".ui-datepicker-calendar").hide();
    $("#ui-datepicker-div").position({
        my: "top",
        at: "bottom",
        of: $(this)
    });
});

var datepicker_end = $('#1_end_date').datepicker({
    dateFormat: 'MM yy',
    changeYear: true,
    changeMonth: true,
    showButtonPanel: true,
    onClose: function(dateText, inst) { 
        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
}).on('focus', function () {
    $(".ui-datepicker-calendar").hide();
    $("#ui-datepicker-div").position({
        my: "top",
        at: "bottom",
        of: $(this)
    });
});

$(function() {
    $('button#submit_form_1').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var form = $('form#form_1');
        var form_request = $('form#form_letter_number');
        var data = form.serialize();
        var request = form_request.serialize();
        var url = "<?=base_url()?>apps/letter_numbering/generate_number";
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
            table_list.ajax.reload(null, false);
            $.unblockUI();
            send_ajax_error(params.responseText);
            toastr.error('Error Processing request!', 'error');
        });
    });
});

</script>