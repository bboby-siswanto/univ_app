<div class="row mb-2">
    <div class="col-12">
        <div class="btn-group float-right" role="group" aria-label="Basic example">
            <a href="<?=base_url()?>apps/gsr/new_form/<?=$target_type;?>" id="create_req" class="btn btn-primary"><i class="fa fa-plus"></i> New <?=$target_type;?></a>
        </div>
    </div>
</div>
<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Filter Disbursement List
                </div>
                <div class="card-body collapse show" id="card_body">
                    <form class="form-row" id="dfrf_form_filter">
                        <input type="hidden" name="df_type" id="df_type" value="<?=$target_type;?>">
                        <div class="form-group col-md-4">
                            <label for="">Range Date Created :</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="df_daterange_start" name="df_daterange_start">
                                <div class="input-group-append">
                                    <span class="input-group-text">to</span>
                                </div>
                                <input type="text" class="form-control" id="df_daterange_end" name="df_daterange_end">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="">Vch No : </label>
                            <input type="text" class="form-control" name="df_no" id="df_no">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="">GSR No :</label>
                            <input type="text" class="form-control" name="gsr_code" id="gsr_code">
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-info float-right" id="submit_filter">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Disbursement Lists
                </div>
                <div class="card-body collapse show" id="card_body">
                    <div class="table-responsive">
                        <table id="df_list" class="table table-bordered table-hover">
                            <thead class="bg-ligth">
                                <tr>
                                    <th>Vch No</th>
                                    <th>Date Created</th>
                                    <th>Reff No</th>
                                    <th>Department</th>
                                    <th>Term of Payment</th>
                                    <th>Paid to</th>
                                    <th>Budget Dept.</th>
                                    <th>Current Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <!-- <tbody>
                            </tbody> -->
                        </table>
                        <table id="df_account_list" class="table table-bordered table-hover d-none">
                            <thead class="bg-ligth">
                                <tr>
                                    <th>Vch No</th>
                                    <th>Date Created</th>
                                    <th>Account No / Name</th>
                                    <th>Description</th>
                                    <th>Debet</th>
                                    <th>Kredit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <!-- <tbody>
                            </tbody> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var today = new Date();
var end_date = new Date(today.getFullYear(), today.getMonth(), today.getDate());
// var select_department = $('select#department_id').select2();

var df_list = $('table#df_list').DataTable({
    processing: true,
    orderCellsTop: true,
    order: [[1, 'desc']],
    dom: 'Blfrtip',
    buttons: [
        {
            text: 'Download Excel',
            extend: 'excel',
            title: 'GSR List',
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            text: 'Download Pdf',
            extend: 'pdf',
            title: 'GSR List',
            exportOptions: {columns: ':visible'}
        },
        {
            text: 'Print',
            extend: 'print',
            title: 'GSR List',
            exportOptions: {columns: ':visible'}
        },
        'colvis'
    ],
    ajax: {
        url: '<?= base_url()?>apps/gsr/get_list_dfrf',
        type: 'POST',
        // data: function(d){
        //     d.gsr_code = $('#gsr_code').val();
        //     d.gsr_daterange_start = $('#gsr_daterange_start').val();
        //     d.gsr_daterange_end = $('#gsr_daterange_end').val();
        //     d.account_no = $('#account_no').val();
        //     d.account_no = $('#account_no').val();
        // }
        data: function() {
            return $('#dfrf_form_filter').serializeArray();
        }
    },
    columns: [
        { 
            data: 'df_number',
            render: function(data, type, rows) {
                return '<a href="<?=base_url()?>apps/gsr/view_df_detail/' + rows.df_id + '">' + data + '</a>';
            }
        },
        {
            data: 'df_date_created',
            render: function(data, type, row) {
                // var newdate = moment(data).format('DD MMMM YYYY HH:mm:ss');
                var newdate = moment(data).format('DD MMMM YYYY');
                return newdate;
            }
        },
        {
            data: 'gsr_code',
            // visible: false
        },
        {
            data: 'department_name',
            // visible: false
        },
        {
            data: 'df_top',
            // render: function(data, type, rows) {
            //     return data + ' / ' + rows.account_name;
            // }
        },
        {
            data: 'df_transaction',
            // visible: false
        },
        {
            data: 'df_budget_dept',
        },
        {
            data: 'current_status',
            render: function(data, type, row) {
                var status = data.toUpperCase();
                if ('<?=(isset($access)) ? $access : '' ?>' == 'review') {
                    if (status == 'REQUEST PENDING') {
                        status += ' <i class="fas fa-exclamation-circle text-danger"></i>';
                    }
                }
                else if ('<?=(isset($access)) ? $access : '' ?>' == 'approve') {
                    if (status == 'REVIEW APPROVE') {
                        status += ' <i class="fas fa-exclamation-circle text-danger"></i>';
                    }
                }
                else if ('<?=(isset($access)) ? $access : '' ?>' == 'finish') {
                    if (status == 'APPROV APPROVE') {
                        status += ' <i class="fas fa-exclamation-circle text-danger"></i>';
                    }
                }
                return status;
            }
        },
        {
            data: 'df_id',
            orderable: false,
            render: function(data, type, row){
                var html = '<div class="btn-group">';
                html += '<a href="<?=base_url()?>apps/gsr/view_df_detail/' + data + '" class="btn btn-sm btn-info" title="View" id="btn_view"><i class="fas fa-eye"></i></a>';
                html += '<a href="<?=base_url()?>apps/gsr/download/' + data + '" class="btn btn-sm btn-info" title="Download" id="btn_download"><i class="fas fa-file-download"></i></a>';
                if (row.gsr_allow_update == 'true') {
                    if ('<?=$this->session->userdata('user');?>' == row.personal_data_id_request) {
                        html += '<a href="<?=base_url()?>apps/gsr/new_request/' + data + '" class="btn btn-sm btn-warning" id="btn_update_gsr"><i class="fas fa-edit"></i></a>';
                    }
                }
                
                html += '</div>';
                return html;
            }
        }
    ],
});

$('button#submit_filter').on('click', function(e) {
    e.preventDefault();

    df_list.ajax.reload();
});


$(function(){
    var datepicker_start = $('input#df_daterange_start').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        maxDate: end_date
    }).on('change', function() {
        datepicker_end.datepicker( "option", "minDate",  $(this).datepicker('getDate') );
        datepicker_end.datepicker('setDate', '');
    });
    
    var element_date = new Date(datepicker_start.val());
    element_date = new Date(element_date.getFullYear(), element_date.getMonth(), element_date.getDate());

    var datepicker_end = $('input#df_daterange_end').datepicker({
        dateFormat: 'yy-mm-dd',
        changeYear: true,
        changeMonth: true,
        maxDate: end_date,
        minDate: element_date
    });

    $('select#account_no').select2({
        minimumInputLength: 2,
        allowClear: true,
        placeholder: 'Please select...',
        theme: "bootstrap",
        ajax: {
            url: '<?=base_url()?>apps/gsr/account_name',
            type: "POST",
            dataType: 'json',
            data: function (params) {
                var query = {
                    term: params.term
                }

                return query;
            },
            processResults: function (result) {
                data = result['data'];
                return {
                    results: $.map(data, function (items) {
                        return {
                            text: items.account_no + ' - ' + items.account_name,
                            id: items.account_no
                        }
                    })
                }
            }
        }
    });

    $('#check_department').on('change', function(e) {
        e.preventDefault();
        
        if ($('input#check_department').is(':checked')) {
            $('select#department_id').removeClass('d-none');
            $('select#department_id').select2({
                placeholder: "Please select..",
                theme: "bootstrap",
                allowClear: true
            });
        }
        else {
            $('select#department_id').addClass('d-none');
            $('select#department_id').select2("destroy");
        }
    })
})


</script>