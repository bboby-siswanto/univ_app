<div class="card">
    <div class="card-header">
        List Period
        <div class="card-header-actions">
            <button id="btn_new_period_aid" class="btn btn-link card-header-action" data-target="#modal_select_semester" data-toggle="modal">
                <i class="fas fa-plus"></i> Period
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="student_aid_period_list" class="table table-bordered table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>Period</th>
                        <th>Student Start Registration</th>
                        <th>Student End Registration</th>
                        <th>Period Status</th>
                        <th>Counter Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_select_semester">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Period Student Aid</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_new_period_aid" osubmit="return false()">
                    <input type="hidden" name="student_aid_period_id" id="student_aid_period_id" value="">
                    <div class="form-group">
                        <label for="period_year_month" class="required_text">Period</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select class="custom-select" id="aid_period_year" name="aid_period_year">
<?php
if ($academic_year) {
    foreach ($academic_year as $o_academic_year) {
?>
                                    <option value="<?=$o_academic_year->academic_year_id;?>"><?=$o_academic_year->academic_year_id;?></option>
<?php
    }
}
?>
                                </select>
                            </div>
                            <select class="custom-select" id="aid_period_month" name="aid_period_month">
<?php
$i = 1;
foreach ($a_month as $s_month) {
?>
                                <option value="<?=$i++;?>"><?=$s_month;?></option>
<?php
}
?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="required_text">Student Registration Start</label>
                        <input type="text" id="period_date_start" name="aid_period_datetime_start" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="" class="required_text">Student Registration End</label>
                        <input type="text" id="period_date_end" name="aid_period_datetime_end" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_period_aid_setting">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var student_aid_period_list_table = $('table#student_aid_period_list').DataTable({
            ajax: {
                url: '<?=site_url('finance/student_finance/get_aid_period_list')?>',
                method: 'POST'
            },
            columns: [
                {
                    data: 'aid_period_id',
                    render: function(data, type, row) {
                        var text = row['aid_period_year'] + '-' + row['aid_period_month'];
                        return '<a href="<?=base_url()?>finance/student_finance/student_aid_list/' + row['aid_period_id'] + '" class="btn btn-link" target="_blank">' + text + '</a>';
                    }
                },
                {data: 'aid_period_datetime_start'},
                {data: 'aid_period_datetime_end'},
                {
                    data: 'aid_period_status',
                    render: function(data, type, row) {
                        return data.toUpperCase();
                    }
                },
                {data: 'count_request'},
                {
                    data: 'aid_period_id',
                    orderable: false,
                    render: function(data, type, row) {
                        var btn_activated = '<button class="btn btn-sm btn-danger" id="btn_active" title="Inactivate" type="button"><i class="fas fa-times"></i></button>';
                        if (row['aid_period_status'] == 'inactive') {
                            btn_activated = '<button class="btn btn-sm btn-info" id="btn_active" title="Activate" type="button"><i class="fas fa-check"></i></button>';
                        }
                        var btn_edit = '<button id="edit_period_aid" class="btn btn-info btn-sm" type="button" title="Update Period"><i class="fas fa-edit"></i></button>';

                        var html = '<div class="btn-group">';
                        html += btn_activated;
                        html += btn_edit;
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });

        $('button#btn_new_period_aid').on('click', function(e) {
            e.preventDefault();

            $('form#form_new_period_aid').find('input, select').val('');
        });

        $('table#student_aid_period_list tbody').on('click', 'button#btn_active',function(e) {
            e.preventDefault();
            var table_data = student_aid_period_list_table.row($(this).parents('tr')).data();
            
            if (confirm('Are you sure ?')) {
                if (table_data.aid_period_status == 'active') {
                    var url = '<?=base_url()?>finance/student_finance/inactiv_period/' + table_data.aid_period_id;
                }else{
                    var url = '<?=base_url()?>finance/student_finance/activate_period/' + table_data.aid_period_id;
                }

                $.post(url, function(result) {
                    if (result.code == 0) {
                        toastr.success('Success', 'Success');
                        student_aid_period_list_table.ajax.reload(null, false);
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    toastr.error('Error processing data!', 'Error');
                });
            }
        });
        
        $('table#student_aid_period_list tbody').on('click', 'button#edit_period_aid',function(e) {
            e.preventDefault();
            var table_data = student_aid_period_list_table.row($(this).parents('tr')).data();
            
            $('#student_aid_period_id').val(table_data.aid_period_id);
            $('#aid_period_year').val(table_data.aid_period_year);
            $('#aid_period_month').val(table_data.aid_period_month);
            // $('#aid_period_datetime_start').val();
            $('#period_date_start').datepicker({dateFormat: 'yy-mm-dd'});
            $('#period_date_end').datepicker({dateFormat: 'yy-mm-dd'});
            $('#period_date_start').datepicker("setDate", new Date(table_data.aid_period_datetime_start));
            $('#period_date_end').datepicker("setDate", new Date(table_data.aid_period_datetime_end));
            $('div#modal_select_semester').modal('show');
        });

        var datepicker_aid_start = $('#period_date_start').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        }).on('change', function() {
            datepicker_aid_end.datepicker( "option", "minDate",  $(this).datepicker('getDate') );
            datepicker_aid_end.datepicker('setDate', '');
        });

        var element_date = new Date(datepicker_aid_start.val());
        // console.log(element+ ': ' + element_date);
        element_date = new Date(element_date.getFullYear(), element_date.getMonth(), element_date.getDate());

        var datepicker_aid_end = $('#period_date_end').datepicker({
            dateFormat: 'yy-mm-dd',
            changeYear: true,
            changeMonth: true,
            minDate: element_date
        });

        $('button#submit_period_aid_setting').on('click', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });

            var data = $('form#form_new_period_aid').serialize();
            var url = '<?=base_url()?>finance/student_finance/submit_period_aid';
            // console.log(data);

            $.post(url, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    student_aid_period_list_table.ajax.reload(null, false);
                    $('div#modal_select_semester').modal('hide');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error procesing data!', 'Error!');
            });
        });
    })
</script>