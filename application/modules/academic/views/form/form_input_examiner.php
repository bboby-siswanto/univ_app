<form id="form_input_examiner_ofse" onsubmit="return false">
    <input type="hidden" name="offered_subject_id" id="offered_subject_id_ofse">
    <div class="row">
        <div class="col-md-12 form-group">
            <label>Examiner</label>
            <input type="text" name="personal_data_name" id="ex_personal_data_name" class="form-control">
            <input type="hidden" name="employee_id" id="ex_employee_id">
        </div>
        <div class="col-md-12">
            <button type="button" class="btn btn-info" id="submit_examiner">Save</button>
        </div>
    </div>
</form>
<script>
    $(function() {
        $('input#ex_personal_data_name').autocomplete({
            minLength: 1,
            appendTo: $('#class_modal_input_lecturer_ofse'),
            source: function(request, response){
                var url = '<?=site_url('employee/get_lecturer_sugestion')?>';
                var data = {
                    term: request.term
                };
                $.post(url, data, function(rtn){
                    var arr = [];
                    arr = $.map(rtn.data, function(m){
                        return {
                            id: m.employee_id,
                            value: ((m.personal_data_title_prefix != null) ? m.personal_data_title_prefix : '') + ' ' + m.personal_data_name + ' ' + ((m.personal_data_title_suffix != null) ? m.personal_data_title_suffix : '')
                        }
                    });
                    response(arr);
                }, 'json');
            },
            select: function(event, ui){
                var id = ui.item.id;
                $('input#ex_employee_id').val(id);
            },
            change: function(event, ui){
                if(ui.item === null){
                    $('input#ex_personal_data_name').val('');
                    $('input#ex_employee_id').val('');
                    toastr['warning']('Please use the selection provided', 'Warning!');
                }
            }
        });

        $('button#submit_examiner').on('click', function(e) {
            e.preventDefault();

            var data = $('form#form_input_examiner_ofse').serialize();
            var eksternal_data = $('#target form#form_filter_offer_subject').serialize();
            data += '&' + eksternal_data;

            $.blockUI({baseZ: 2000});

            $.post('<?= base_url()?>academic/offered_subject/save_examiner', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr['success']('Success', 'Success');
                    $('#class_modal_input_lecturer_ofse').modal('hide');
                    offer_subject_datatable.ajax.reload(null, false);
                    $('#ex_personal_data_name').val('');
                    $('#ex_employee_id').val('');
                }else{
                    toastr['warning'](result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.warning('Proccess failed, please refresh your browser or contact team IT', 'Warning!');
            });
        });
    });
</script>