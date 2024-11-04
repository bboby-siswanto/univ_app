<div class="row mb-2">
    <div class="col-12">
        <div class="btn-group float-right" role="group" aria-label="Basic example">
            <button id="btn_new_coa" type="button" class="btn btn-primary"><i class="fas fa-plus"></i> New COA</button>
        </div>
    </div>
</div>
<!-- <div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Filter Chart of Account
                    <button class="btn btn-link card-header-action" data-toggle="collapse" data-target="#card_body" aria-expanded="true" aria-expanded="card_body" style="float: right;">
                        <i class="fas fa-caret-square-down"></i>
                    </button>
                </div>
                <div class="card-body collapse show" id="card_body">
                    <div class="table-responsive">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Chart of Account List
                </div>
                <div class="card-body" id="card_body">
                    <div class="table-responsive">
                        <table id="account_table" class="table table-bordered table-hover">
                            <thead class="bg-dark">
                                <tr>
                                    <th>Booking Code</th>
                                    <th>Account Name</th>
                                    <th>Account Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_account_form">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form url="<?=base_url()?>apps/gsr/submit_account" onsubmit="return false" id="form_account">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="input_sub_account_no">Sub Account</label>
                                <select name="input_sub_account_no" id="input_sub_account_no" class="form-control">
                                    <option value=""></option>
                            <?php
                            if ($account_list) {
                                foreach ($account_list as $o_account) {
                            ?>
                                    <option value="<?=$o_account->account_no;?>"><?=$o_account->account_no.' - '.$o_account->account_name;?></option>
                            <?php
                                }
                            }
                            ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="input_account_no" class="required_text">Account No</label>
                                <input type="text" class="form-control" name="input_account_no" id="input_account_no">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="input_account_name">Account Name</label>
                                <input type="text" class="form-control" name="input_account_name" id="input_account_name">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="input_account_type">Account Type</label>
                                <input type="text" class="form-control" name="input_account_type" id="input_account_type">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_submit_account">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
var input_sub_account_no_select = $("#input_sub_account_no").select2({
    allowClear: true,
    placeholder: "Select Account",
    theme: "bootstrap",
    // minimumInputLength: 2
});
var account_table = $('table#account_table').DataTable({
    processing : true,
    ordering: false,
    dom: 'Bfrtip',
    buttons: [
        {
            text: 'Download Excel',
            extend: 'excel',
            // title: 'Student List Data',
            exportOptions: {
                columns: ':visible'
            }
        },
        {
            text: 'Download Pdf',
            extend: 'pdf',
            // title: 'Student List Data',
            exportOptions: {columns: ':visible'}
        },
    ],
    ajax: {
        url: '<?=base_url()?>apps/gsr/get_list_account',
        type: 'POST'
    },
    columns:[
        {
            data: 'account_no',
            render: function(data, type, row) {
                if (row.account_marked_strong == 1) {
                    data = '<strong>' + data + '</strong>';
                }

                let i_padd = row.level_of_padd;
                var paddleft = '';
                for (let i = 0; i < i_padd; i++) {
                    paddleft += '&emsp;&emsp;';
                }
                
                return paddleft + data;
            }
        },
        {
            data: 'account_name',
            render: function(data, type, row) {
                if (row.account_marked_strong == 1) {
                    data = '<strong>' + data + '</strong>';
                }

                let i_padd = row.level_of_padd;
                var paddleft = '';
                for (let i = 0; i < i_padd; i++) {
                    paddleft += '&emsp;&emsp;';
                }
                
                return paddleft + data;
            }
        },
        {
            data: 'account_type',
            render: function(data, type, row) {
                if (row.account_marked_strong == 1) {
                    data = '<strong>' + data + '</strong>';
                }
                return data;
            }
        },
        {
            data: 'account_no',
            render: function(data, type, row) {
                return '';
            }
        }
    ]
});
$(function(){
    $('button#btn_new_coa').on('click', function(e) {
        e.preventDefault();

        $("#modal_account_form").modal('show');
    });

    $('#btn_submit_account').on('click', function(e) {
        e.preventDefault();

        var form = $('#form_account');
        var data = form.serialize();
        var url = form.attr('url');
        $.blockUI({baseZ: 9000900});

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                account_table.ajax.reload();
                $("#modal_account_form").modal('hide');
            }
            else {
                toastr.warning(result.message, 'warning');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error("error processing data");
        });
    })
});
</script>