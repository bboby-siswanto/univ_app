<form id="form_input_lecturer" onsubmit="return false;">
    <input type="hidden" name="class_group_subject_lecturer_id" id="class_group_subject_lecturer_id">
    <input type="hidden" name="subject_type" id="offered_subject_type">
    <input type="hidden" name="offer_subject_access" id="offer_subject_access" value="0">
    <input type="hidden" name="row_index_offer_subject" id="row_index_offer_subject">
    <input type="hidden" name="offered_subject_id" id="offered_subject_id">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Lecturer <span class="urgent">*</span></label>
                <input type="text" name="personal_data_name" id="personal_data_name" class="form-control">
                <small class="text-danger dosen_pengampu d-none">Lecturer is not listed in the feeder. Please input another lecturer to be reported</small>
                <input type="hidden" name="employee_id" id="employee_id">
                <input type="hidden" name="lecturer_reported" id="lecturer_reported" value="0">
            </div>
            <div class="form-group dosen_pengampu d-none">
                <label>Lecturer Reported <span class="urgent">*</span></label>
                <input type="text" name="personal_data_name_reported" id="personal_data_name_reported" class="form-control">
                <input type="hidden" name="employee_id_reported" id="employee_id_reported">
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Subject Credit</label> : 
                        <strong id="curriculum_subject_credit"></strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Remaining Credit</label> : 
                        <strong id="remaining_allocation"></strong>
                        <input type="hidden" name="remaining_credit" id="remaining_credit">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Credit Allocation <span class="urgent">*</span></label>
                        <input type="text" name="credit_allocation" id="credit_allocation" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="class_group_lecturer_priority" id="class_group_lecturer_priority" class="form-control">
                            <option value="">Please select...</option>
                    <?php
                        foreach ($lecturer_priority as $priority) {
                    ?>
                            <option value="<?= $priority; ?>"><?= $priority; ?></option>
                    <?php
                        }
                    ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Day</label>
                        <select name="class_group_lecturer_preferable_day" id="class_group_lecturer_preferable_day" class="form-control">
                            <option value="">Please select...</option>
                    <?php
                        foreach ($preferable_day as $day) {
                    ?>
                            <option value="<?= $day; ?>"><?= $day; ?></option>
                    <?php
                        }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Time</label>
                        <select name="class_group_lecturer_preferable_time" id="class_group_lecturer_preferable_time" class="form-control">
                            <option value="">Please select...</option>
                    <?php
                        foreach ($preferable_time as $time) {
                    ?>
                            <option value="<?= $time; ?>"><?= $time; ?></option>
                    <?php
                        }
                    ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="float-right">
                <button id="save_class_lecturer_offered_subject" type="button" class="btn btn-info action_form_input_lecturer">Save</button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal" aria-label="Close">Close</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $('input#personal_data_name').autocomplete({
            minLength: 1,
            appendTo: $('#class_modal_input_lecturer'),
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
                            value: m.personal_data_name,
                            reported: m.employee_lecturer_is_reported
                        }
                    });
                    response(arr);
                }, 'json');
            },
            select: function(event, ui){
                var id = ui.item.id;
                $('input#employee_id').val(id);
                // console.log($('input#offered_subject_type').val());
                if ($('input#offered_subject_type').val() != 'extracurricular') {
                    if (ui.item.reported != 'TRUE') {
                        $('.dosen_pengampu').removeClass('d-none').addClass('show');
                        $('#lecturer_reported').val('1');
                    }else{
                        $('.dosen_pengampu').removeClass('show').addClass('d-none');
                        $('#lecturer_reported').val('0');
                    }
                }else{
                    $('.dosen_pengampu').removeClass('show').addClass('d-none');
                }
            },
            change: function(event, ui){
                if(ui.item === null){
                    $('input#personal_data_name').val('');
                    $('input#employee_id').val('');
                    $('.dosen_pengampu').removeClass('show').addClass('d-none');
                    toastr['warning']('Please use the selection provided', 'Warning!');
                }
            }
        });

        $('input#personal_data_name_reported').autocomplete({
            minLength: 1,
            appendTo: $('#class_modal_input_lecturer'),
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
                            value: m.personal_data_name,
                            reported: m.employee_lecturer_is_reported
                        }
                    });
                    response(arr);
                }, 'json');
            },
            select: function(event, ui){
                var id = ui.item.id;
                console.log()
                if (ui.item.reported != 'TRUE') {
                    $('input#personal_data_name_reported').val('');
                    $('input#employee_id_reported').val('');
                    toastr['warning']('Please select the lecturer listed in the feeder', 'Warning!');
                    return false;
                }else{
                    $('input#employee_id_reported').val(id);
                }
            },
            change: function(event, ui){
                if(ui.item === null){
                    $('input#personal_data_name_reported').val('');
                    $('input#employee_id_reported').val('');
                    toastr['warning']('Please use the selection provided', 'Warning!');
                }
            }
        });

        $('button#save_class_lecturer_offered_subject').on('click', function(e) {
            e.preventDefault();

            var data = $('form#form_input_lecturer').serialize();
            var eksternal_data = $('#target form#form_filter_offer_subject').serialize();
            var data_eksternal = 'semester_type_id=' + $('#os_form_semester_type_id').val() + '&academic_year_id=' + $('#os_form_academic_year_id').val() + '&program_id=' + $('#os_form_program_id').val() + '&study_program_id=' + $('#os_form_study_program_id').val();
            
            eksternal_data = data_eksternal;
            data += '&' + eksternal_data;
            var url = '<?= base_url()?>academic/offered_subject/save_team_teaching';

            $.blockUI({baseZ: 2000});
            $.post(url, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr['success']('Success', 'Success');
                    $('#class_modal_input_lecturer').modal('hide');
                    $('input#offer_subject_access').val('0');
                    
                    offered_subject_table.ajax.reload(null, false);
                }else if(result.code == 2){
                    toastr['warning'](result.message, 'Warning!');
                }else{
                    toastr['warning'](result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        });

        function show_pengampu(status) {
            if (!status) {
                console.log('tidak dilaporkan');
            }
        }
    });

    function show_modal_ext() {
        // var row_index_offer_subject = $('#row_index_offer_subject').val();
        // var row_data = offer_subject_datatable.row( row_index_offer_subject ).data();
        // var lect_data = row_data.lecturer_data;
        // if (lect_data.length > 0) {
            $('div#input_password').modal('show');
        // }
    }
</script>