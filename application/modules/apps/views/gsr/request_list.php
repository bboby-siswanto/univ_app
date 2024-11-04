<div class="row mb-2">
    <div class="col-12">
        <div class="btn-group float-right" role="group" aria-label="Basic example">
            <a href="<?=base_url()?>apps/gsr/new_request" id="create_req" class="btn btn-primary"><i class="fa fa-plus"></i> New GSR</a>
        </div>
    </div>
</div>
<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Filter Request List
                </div>
                <div class="card-body collapse show" id="card_body">
                    <form class="form-row" id="gsr_form_filter">
                        <div class="form-group col-md-4">
                            <label for="">Range Date :</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="gsr_daterange_start" name="gsr_daterange_start">
                                <div class="input-group-append">
                                    <span class="input-group-text">to</span>
                                </div>
                                <input type="text" class="form-control" id="gsr_daterange_end" name="gsr_daterange_end">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="">GSR No : </label>
                            <input type="text" class="form-control" name="gsr_code" id="gsr_code">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="">Booking Code :</label>
                            <select name="account_no" id="account_no" class="form-control">
                                <!-- <option value="">Please select...</option> -->
                            </select>
                        </div>
                <?php
                if ((isset($access)) AND (in_array($access, $special_access))) {
                ?>
                        <div class="form-group col-md-6">
                            <label for="">Department</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check_department" name="check_department" checked>
                                            <label class="custom-control-label" for="check_department">Show other department</label>
                                        </div>
                                    </span>
                                </div>
                                <select name="department_id" id="department_id" class="form-control d-none">
                                    <option value="all">All</option>

                            <?php
                            if ($department) {
                                foreach ($department as $o_department) {
                            ?>
                                    <option value="<?=$o_department->department_id;?>"><?=$o_department->department_name.' / '.$o_department->department_abbreviation;?></option>
                            <?php
                                }
                            }
                            ?>
                                </select>
                            </div>
                        </div>
                <?php
                }
                ?>
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
                    Request Lists
                </div>
                <div class="card-body collapse show" id="card_body">
                    <div class="table-responsive">
                        <table id="gsr_list" class="table table-bordered table-hover">
                            <thead class="bg-ligth">
                                <tr>
                                    <th>GSR No</th>
                                    <th>Request Date</th>
                                    <th>Department</th>
                                    <th>Bugdet Proposal No</th>
                                    <th>Booking No / Account Name</th>
                                    <th>Activity</th>
                                    <th>Requested By</th>
                                    <th>Reviewed By</th>
                                    <th>Action Rectorate (Pak Wahyu)</th>
                                    <!-- <th>Action Yayasan</th> -->
                                    <th>Total Item</th>
                                    <th>Total Amount</th>
                                    <th>Last Action</th>
                                    <?php
                                    if ((isset($userfinance)) AND ($userfinance)) {
                                    ?>
                                    <th>Finance Note</th>
                                    <?php
                                    }
                                    ?>
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
<div class="modal" tabindex="-1" role="dialog" id="modal_finance_note">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Finance Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form url="<?=base_url()?>apps/gsr/submit_finance_note" onsubmit="return false" id="form_finance_note">
                    <input type="hidden" name="note_gsr_id" id="note_gsr_id">
                    <textarea name="note_gsr_finance" id="note_gsr_finance" class="form-control"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_submit_finance_note">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var today = new Date();
var end_date = new Date(today.getFullYear(), today.getMonth(), today.getDate());

// var select_department = $('select#department_id').select2();

var gsr_list = $('table#gsr_list').DataTable({
    processing: true,
    orderCellsTop: true,
    order: [[0, 'desc']],
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
        url: '<?= base_url()?>apps/gsr/get_list_request',
        type: 'POST',
        // data: function(d){
        //     d.gsr_code = $('#gsr_code').val();
        //     d.gsr_daterange_start = $('#gsr_daterange_start').val();
        //     d.gsr_daterange_end = $('#gsr_daterange_end').val();
        //     d.account_no = $('#account_no').val();
        //     d.account_no = $('#account_no').val();
        // }
        data: function() {
            return $('#gsr_form_filter').serializeArray();
        }
    },
    columns: [
        { 
            data: 'gsr_code',
            render: function(data, type, row) {
                return '<a href="<?=base_url()?>apps/gsr/submit_action/' + row.for_view + '">' + data + '</a>';
            }
        },
        {
            data: 'gsr_date_request',
            render: function(data, type, row) {
                var newdate = moment(data).format('DD MMMM YYYY HH:mm:ss');
                return newdate;
            }
        },
        {
            data: 'department_name',
            visible: false
        },
        {
            data: 'gsr_budget_proposal_number',
            visible: false
        },
        {
            data: 'account_no',
            render: function(data, type, rows) {
                return data + ' / ' + rows.account_name;
            }
        },
        {
            data: 'gsr_activity',
            visible: false
        },
        {
            data: 'user_request',
            render: function(data, type, row) {
                let laststatus_data = row.last_status_data;
                return (laststatus_data.status_action == 'reject') ? data + '.' : data;
            }
        },
        { data: 'user_review' },
        { data: 'user_approve' },
        // {
        //     data: 'user_finish',
        // },
        { 
            data: 'total_items',
            visible: false,
            render: $.fn.dataTable.render.number( ',', '.', 0)
        },
        {
            data: 'gsr_total_amount',
            visible: true,
            render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp. ' )
        },
        {
            data: 'gsr_id',
            visible: true,
            render: function(data, type, row) {
                let laststatus_data = row.last_status_data;
                var laststatus = laststatus_data.status_action;
                if (laststatus == 'approve') {
                    laststatus = 'approved';
                }
                return laststatus;
            }
        },
    <?php
    if ((isset($userfinance)) AND ($userfinance)) {
    ?>
        {
            data: 'gsr_finance_note',
            visible: true,
            // render: function(data, type, row) {
            //     let laststatus_data = row.last_status_data;
            //     return laststatus_data.status_action;
            // }
        },
    <?php
    }
    ?>
        {
            data: 'gsr_id',
            orderable: false,
            render: function(data, type, row){
                var html = '<div class="btn-group">';
                html += '<a href="<?=base_url()?>apps/gsr/submit_action/' + row.for_view + '" class="btn btn-sm btn-info" title="View" id="btn_view"><i class="fas fa-eye"></i></a>';
                // html += '<a href="<?=base_url()?>apps/gsr/download/' + data + '" class="btn btn-sm btn-info" title="Download" id="btn_download"><i class="fas fa-file-download"></i></a>';
                if (row.gsr_allow_update == 'true') {
                    if ('<?=$this->session->userdata('user');?>' == row.personal_data_id_request) {
                        html += '<a href="<?=base_url()?>apps/gsr/new_request/' + data + '" class="btn btn-sm btn-warning" id="btn_update_gsr"><i class="fas fa-edit"></i></a>';
                    }
                }
                
                <?php
                if ((isset($userfinance)) AND ($userfinance)) {
                ?>
                html += '<button id="btn_finance_note" type="button" class="btn btn-success btn-sm"><i class="fa fa-book-reader"></i></button>';
                <?php
                }
                ?>
                
                html += '</div>';
                return html;
            }
        }
    ],
});

$('button#submit_filter').on('click', function(e) {
    e.preventDefault();

    gsr_list.ajax.reload();
});

$(function(){
    var datepicker_start = $('input#gsr_daterange_start').datepicker({
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

    var datepicker_end = $('input#gsr_daterange_end').datepicker({
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
    });

    $('#btn_submit_finance_note').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });
        
        var form = $('#form_finance_note');
        var data = form.serialize();
        var url = form.attr('url');

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                $('#modal_finance_note').modal('hide');
                toastr.success('Success!', 'Success');
                gsr_list.ajax.reload(null, false);
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error System!');
            console.log(params.responseText);
        })
    });

    $('#gsr_list tbody').on('click', 'button#btn_finance_note', function(e) {
        e.preventDefault();
        var table_data = gsr_list.row($(this).parents('tr')).data();

        $('#note_gsr_id').val(table_data.gsr_id);
        $('#note_gsr_finance').val(table_data.gsr_finance_note);
        $('#modal_finance_note').modal('show');
    });

    $('#modal_finance_note').on('hidden.bs.modal', function (e) {
        $('#note_gsr_id').val('');
        $('#note_gsr_finance').text('');
    });
})


</script>