<form onsubmit="return false" id="form_letter_number">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="letter_type" class="required_text">Letter Type</label>
                <select name="letter_type" id="letter_type" class="form-control form-control-sm">
                    <option value=""></option>
            <?php
            if ((isset($letter_type)) AND ($letter_type)) {
                foreach ($letter_type as $o_letter_type) {
            ?>
                    <option value="<?=$o_letter_type->letter_type_id;?>"><?=$o_letter_type->letter_name.' ('.$o_letter_type->letter_abbreviation.')';?></option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="department" class="required_text">Department</label>
                <select name="department" id="department" class="form-control form-control-sm">
                    <option value=""></option>
            <?php
            if ((isset($list_dept)) AND ($list_dept)) {
                foreach ($list_dept as $o_dept) {
            ?>
                    <option value="<?=$o_dept->department_id;?>"><?=$o_dept->department_name;?> (<?=$o_dept->department_abbreviation;?>)</option>
            <?php
                }
            }
            ?>
                </select>
            </div>
        </div>
        <div class="col-md-4" id="input_backdate">
            <div class="form-group">
                <div class="d-flex">
                    <label for="backdate" class="pr-2">Back Dated</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input clsDatePicker" id="backdated_switch" name="backdated_switch">
                        <label class="custom-control-label" for="backdated_switch"></label>
                    </div>
                </div>
                <input type="text" name="backdate" id="backdate" class="form-control form-control-sm" disabled>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="purpose" class="required_text">To</label>
                <input type="text" name="purpose" id="purpose" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="description" class="required_text">Description</label>
                <textarea class="form-control form-control-sm" name="description" id="description"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <button type="button" class="btn btn-info float-right" id="submit_generate">Next Step</button>
        </div>
    </div>
</form>
<script>
var enforceModalFocusFn = $.fn.modal.Constructor.prototype.enforceFocus;
$.fn.modal.Constructor.prototype.enforceFocus = function() {};
$(function(e) {
    var date = new Date();
    var last_month = new Date(date.getFullYear(), date.getMonth() - 1, date.getDate());

    $('input#backdated_switch').on('change', function(e) {
        if ($('input#backdated_switch').is(':checked')) {
            $('input#backdate').removeAttr('disabled');
        }
        else {
            $('input#backdate').attr('disabled', 'disabled');
        }
    });

    $('select#letter_type').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        cache: false
    });
    
    $('select#department').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        cache: false
    });

    var datepicker_backdate = $('input#backdate').datepicker({
        dateFormat: 'MM yy',
        // changeYear: true,
        // changeMonth: true,
        showButtonPanel: true,
        zIndex: 2048,
        maxDate: last_month,
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

    $('button#submit_generate').on('click', function(e) {
        e.preventDefault();

        var letter_type = $('#letter_type').val();
        if ($('#letter_type').val() == '') {
            $('#letter_type').focus();
            toastr.warning('please input "Letter Type" field!');
            return false;
        }
        else if ($('#department').val() == '') {
            $('#department').focus();
            toastr.warning('please input "Department" field!');
            return false;
        }
        else if ($('#purpose').val().length < 5) {
            $('#purpose').focus();
            toastr.warning('please input "To" field, at least 5 characters!');
            return false;
        }
        else if ($('#description').val().length < 5) {
            $('#description').focus();
            toastr.warning('please input "Description" field, at least 5 characters!');
            return false;
        }
        $.blockUI({ baseZ: 2000 });

        $.post('<?=base_url()?>apps/letter_numbering/get_list_template', {letter_type_key: letter_type}, function(result) {
            if ($("select#template_list").hasClass("select2-hidden-accessible")) {
                $("select#template_list").select2("destroy");
                $('select#template_list').empty();
            }

            var data = result.data;
            var list_template = [];
            if (data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    var option = '<option value="' + data[i].template_id + '" data-force="' + data[i].template_available_generated + '">' + data[i].filename + '</option>';
                    $('select#template_list').append(option);
                }
            }

            $('select#template_list').select2({
                allowClear: true,
                placeholder: "Please select..",
                theme: "bootstrap",
                cache: false,
                // data: list_template
            });

            $('select#template_list').val(null).trigger("change");
            $('#modal_request').modal('hide');
            $('#modal_select_template').modal('show');
            $.unblockUI();
        }, 'json').fail(function(params) {
            $.unblockUI();
            send_ajax_error(params.responseText);
            toastr.error('Error retrieve file template data!');
        });
    });
});
</script>