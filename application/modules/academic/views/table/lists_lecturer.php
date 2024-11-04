<div class="table-responsive">
    <table id="table_lists_lecturer" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th>Lecturer</th>
                <th>SKS Allocation</th>
                <th>Day</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    var show_table_data_lecturer_offer_subject = function(a_filter_data) {
        if($.fn.DataTable.isDataTable('table#table_lists_lecturer')){
            lists_lecturer_datatable.destroy();
        }

        lists_lecturer_datatable = $('#table_lists_lecturer').DataTable({
            processing: true,
            ajax: {
                url: '<?= base_url()?>academic/offered_subject/get_lecturer_lists',
                type: 'POST',
                data: a_filter_data
            },
            columns: [
                {data: 'personal_data_name'},
                {
                    data: 'credit_allocation',
                    render: function(data, type, row) {
                        return '<span id="lect_credit_data_' + row.class_group_lecturer_id + '"> ' + data + '</span><input type="text" class="form-control d-none" name="lect_credit_allocation" id="lect_credit_' + row.class_group_lecturer_id + '" value="' + data + '">';
                    }
                },
                {data: 'class_group_lecturer_preferable_day'},
                {data: 'class_group_lecturer_preferable_time'},
                {data: 'class_group_lecturer_id'}
            ],
            columnDefs: [
                {
                    targets: -1,
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        html += '<button name="btn_delete_academic_history" id="lect_btn_remove_' + data + '" type="button" data_id="' + data + '" class="btn btn-danger btn-sm" title="Remove data"><i class="fas fa-trash"></i></button>'
                        html += '<button name="btn_change_credit" id="lect_btn_change_credit_' + data + '" type="button" data_id="'+data+'" class="btn btn-warning btn-sm" title="Update SKS Allocation" ><i class="fas fa-edit"></i></button>';
                        html += '<button name="cancel_credits_lecturer" id="cancel_credits_lecturer_' + data + '" type="button" data_id="'+data+'" class="btn btn-danger btn-sm d-none" title="Cancel" ><i class="fas fa-times"></i></button>';
                        html += '<button name="save_credits_lecturer" id="save_credits_lecturer_' + data + '" type="button" data_id="'+data+'" class="btn btn-success btn-sm d-none" title="Save" ><i class="fas fa-check"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });
    }

    $(function() {
        show_table_data_lecturer_offer_subject({offered_subject_id : ''});

        $('table#table_lists_lecturer tbody').on('click', 'button[name="btn_delete_academic_history"]', function(e) {
            e.preventDefault();

            if(confirm("Are you sure deleted this item ?")) {
                var data_id = $(this).attr("data_id");
                var url = '<?= base_url()?>academic/offered_subject/remove_team_teaching';
                $.post(url, {class_group_lecturer_id: data_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr['success']('lecturer has been removed', 'Success');
                        lists_lecturer_datatable.ajax.reload(null, true);
                        // if ('<?=$this->session->userdata('user')?>' == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                            offered_subject_table.ajax.reload(null, false);
                        // }
                        // else {
                        //     offer_subject_datatable.ajax.reload(null, false);
                        // }
                    }else{
                        toastr['warning'](result.message, 'Warning!');
                    }
                },'json').fail(function(params) {
                    toastr['error']('error system', 'Error!');
                    $.unblockUI();
                });
            }
        });

        $('table#table_lists_lecturer tbody').on('click', 'button[name="btn_change_lecturer"]', function(e) {
            e.preventDefault();
            var row_data = lists_lecturer_datatable.row($(this).parents('tr')).data();
            console.log(row_data);
            if (($('#semester_type_id').val() == '4') || ($('#semester_type_id').val() == '6')) {
                //
            }else{
                $('#class_group_subject_lecturer_id').val(row_data.class_group_lecturer_id);
                $('#personal_data_name').val(row_data.personal_data_name);
                $('#employee_id').val(row_data.employee_id);
                var subject_data = row_data.subject_data;
                if (row_data.employee_lecturer_is_reported == 'FALSE') {
                    row_data_reported = row_data.employee_data_reported;
                    $('#lecturer_reported').val('1');
                    $('.dosen_pengampu').removeClass('d-none').addClass('show');
                    $('#personal_data_name_reported').val(row_data_reported.personal_data_name);
                    $('#employee_id_reported').val(row_data_reported.employee_id);
                }else{
                    $('.dosen_pengampu').removeClass('show').addClass('d-none');
                    $('#lecturer_reported').val('1');
                }
                $('#curriculum_subject_credit').html(subject_data.curriculum_subject_credit);
                let remaining_credit = ((subject_data.curriculum_subject_credit - subject_data.credit_filled) + parseFloat(row_data.credit_allocation));
                $('#remaining_allocation').html(remaining_credit);
                $('#remaining_credit').val(remaining_credit);
                $('#credit_allocation').val(row_data.credit_allocation);
                if (row_data.class_group_lecturer_priority !=  NULL) {
                    $('#class_group_lecturer_priority').val(row_data.class_group_lecturer_priority);
                }
                if (row_data.class_group_lecturer_preferable_day !=  NULL) {
                    $('#class_group_lecturer_preferable_day').val(row_data.class_group_lecturer_preferable_day);
                }
                if (row_data.class_group_lecturer_priority !=  NULL) {
                    $('#class_group_lecturer_priority').val(row_data.class_group_lecturer_priority);
                }
                $('div#class_modal_input_lecturer').modal('show');
                $('button.action_form_input_lecturer').removeAttr('id').attr('id', 'update_class_lecturer_offered_subject');
            }
        });

        $('table#table_lists_lecturer tbody').on('click', 'button[name="btn_change_credit"]', function(e) {
            e.preventDefault();
            var row_data = lists_lecturer_datatable.row($(this).parents('tr')).data();
            $('#lect_credit_data_' + row_data.class_group_lecturer_id).addClass('d-none');
            $('input#lect_credit_' + row_data.class_group_lecturer_id).removeClass('d-none');
            
            $('button#lect_btn_change_credit_' + row_data.class_group_lecturer_id).addClass('d-none');
            $('button#lect_btn_remove_' + row_data.class_group_lecturer_id).addClass('d-none');
            $('button#save_credits_lecturer_' + row_data.class_group_lecturer_id).removeClass('d-none');
            $('button#cancel_credits_lecturer_' + row_data.class_group_lecturer_id).removeClass('d-none');
        });
        
        $('table#table_lists_lecturer tbody').on('click', 'button[name="cancel_credits_lecturer"]', function(e) {
            e.preventDefault();
            var row_data = lists_lecturer_datatable.row($(this).parents('tr')).data();
            $('#lect_credit_data_' + row_data.class_group_lecturer_id).removeClass('d-none');
            $('input#lect_credit_' + row_data.class_group_lecturer_id).addClass('d-none');

            $('button#lect_btn_change_credit_' + row_data.class_group_lecturer_id).removeClass('d-none');
            $('button#lect_btn_remove_' + row_data.class_group_lecturer_id).removeClass('d-none');
            $('button#save_credits_lecturer_' + row_data.class_group_lecturer_id).addClass('d-none');
            $('button#cancel_credits_lecturer_' + row_data.class_group_lecturer_id).addClass('d-none');
        });

        $('table#table_lists_lecturer tbody').on('click', 'button[name="save_credits_lecturer"]', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });
            var row_data = lists_lecturer_datatable.row($(this).parents('tr')).data();
            var post_data = {
                offered_subject_id: row_data.offered_subject_id,
                class_group_lecturer_id: row_data.class_group_lecturer_id,
                credit_allocation: $('input#lect_credit_' + row_data.class_group_lecturer_id).val()
            };

            $.post('<?=base_url()?>academic/offered_subject/update_lect_credit', post_data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    show_table_data_lecturer_offer_subject({offered_subject_id : row_data.offered_subject_id});
                    // if ('<?=$this->session->userdata('user')?>' == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                        offered_subject_table.ajax.reload(null, false);
                    // }
                    // else {
                    //     offer_subject_datatable.ajax.reload(null, false);
                    // }
                }
                else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
        });
    });
</script>