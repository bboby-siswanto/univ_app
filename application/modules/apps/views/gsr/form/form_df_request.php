<?php
$date_request = date('d F Y');
$s_transaction_type = ((isset($target_type)) AND ($target_type == 'DF')) ? 'Bank Disbursement' : 'Bank Receipt';

?>
<div class="row mb-2">
    <div class="col-12">
        <div class="btn-toolbar float-right" role="toolbar" aria-label="Action Button">
            <div class="btn-group mr-2" role="group" aria-label="Action">
                <a href="<?=base_url()?>apps/gsr/<?=strtolower($target_type);?>_list" class="btn btn-success" id="request_list_df"><i class="fas fa-list"></i> <?=$target_type;?> List</a>
            </div>
        </div>
    </div>
</div>
<form action="<?=base_url()?>apps/gsr/submit_df" id="submit_form" method="post" onsubmit="return false">
    <input type="hidden" name="df_type" id="df_type" value="<?=$s_transaction_type;?>">
    <input type="hidden" name="df_update_key" id="df_update_key" value="<?=(isset($df_main_data)) ? $df_main_data->df_id : '';?>">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <?=$target_type;?> Form
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label class="required_text">Vch No :</label>
                                <input type="text" class="form-control" id="df_number" name="df_number" value="<?=(isset($df_main_data)) ? $df_main_data->df_number : $optional_df_number; ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Date :</label>
                                <input type="text" class="form-control" id="df_date" name="df_date" value="<?=$date_request;?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Reff No :</label>
                                <select name="gsr_id" id="gsr_id" class="form-control">
                                    <option value=""></option>
                            <?php
                            if ((isset($gsr_list)) AND ($gsr_list)) {
                                foreach ($gsr_list as $o_gsr) {
                            ?>
                                    <option value="<?=$o_gsr->gsr_id;?>" data-account="<?=$o_gsr->account_no;?>"><?=$o_gsr->gsr_code;?></option>
                            <?php
                                }
                            }
                            ?>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">Term of Payment :</label>
                                <select name="df_top" id="df_top" class="form-control">
                                    <option value=""></option>
                            <?php
                            if ((isset($top)) AND ($top)) {
                                foreach ($top as $s_top) {
                            ?>
                                    <option value="<?=$s_top;?>"><?=$s_top;?></option>
                            <?php
                                }
                            }
                            ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">Account :</label>
                                <select name="df_account" id="df_account" class="form-control">
                                    <option value=""></option>
                                    <option value="MDR">MDR</option>
                                    <option value="BNI">BNI</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <label id="paidreceive_account">
                            <?php
                            if ($target_type == 'DF') {
                                print('Paid to');
                            }
                            else {
                                print('Receive From');
                            }
                            ?>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="transaction_paidreceive" name="transaction_paidreceive">
                                    <select name="bank_code" id="bank_code" class="form-control">
                                        <option value=""></option>
                                <?php
                                if ((isset($bank_list)) AND ($bank_list)) {
                                    foreach ($bank_list as $o_bank) {
                                ?>
                                        <option value="<?=$o_bank->bank_code;?>"><?=$o_bank->bank_code.' / '.$o_bank->bank_name;?></option>
                                <?php
                                    }
                                }
                                ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Budget Dept :</label>
                                <input type="text" class="form-control" id="budget" name="budget" value="<?= (isset($gsr_main_data)) ? $gsr_main_data->gsr_budget_proposal_number : ''; ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Activity : <small class="text-danger"><i>in english</i></small> </label>
                                <input type="" class="form-control" id="activity_no" name="activity_no" value="<?= (isset($gsr_main_data)) ? $gsr_main_data->gsr_activity : ''; ?>">
                            </div>
                            <!-- <div class="form-group col-md-3">
                                <label class="required_text">Requested By :</label>
                                <input type="text" value="<?=$request_name;?>" class="form-control" id="requested" name="requested" readonly> 
                            </div>
                            <div class="form-group col-md-4">
                                <label class="required_text">Reviewed By :</label>
                                <input type="" class="form-control" id="reviewed_by" name="reviewed_by" value="Finance Dept." readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="required_text">Approved By :</label>
                                <input type="text" class="form-control" id="approved_by" name="approved_by" value="<?=$rector_name;?>" readonly> 
                            </div> -->
                            <div class="form-group col-md-12">
                                <label>Remarks :</label>
                                <textarea name="df_remarks" id="df_remarks" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Item List
                        <button type="button" id="btn_loop" class="btn btn-primary float-right"><i class="fa fa-plus"></i> Add Item</button>
                        <!-- <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal_df_detail">
                            Add Item
                        </button> -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table_input" class="table table-bordered table-hover table-sm">
                                <thead class="bg-ligth text-center">
                                    <tr>
                                        <th class="w-25"><span class="required_text">Account No</span></th>
                                        <th><span>Remark Transaction</span></th>
                                        <th><span>Debet (IDR)</span></th>
                                        <th><span>Kredit (IDR)</span></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th>
                                            <input type="text" class="form-control form-control-sm form-number" id="total_amount_debet" name="total_amount_debet" readonly>
                                        </th>
                                        <th>
                                            <input type="text" class="form-control form-control-sm form-number" id="total_amount_kredit" name="total_amount_kredit" readonly>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Attachment
                        <button type="button" id="btn_files_new" class="btn btn-primary float-right"><i class="fa fa-plus"></i> Add File</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="table_file">
                                <thead>
                                    <tr>
                                        <td></td>
                                        <td class="w-25"></td>
                                    </tr>
                                    <!-- <tr>
                                        <td><input type="file" name="filedf[]" id="filedf" class="form-control"></td>
                                        <td class="w-25"></td>
                                    </tr> -->
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" style="padding-right:1cm;">
                <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group mb-3" role="group" aria-label="First group">
                        <button type="button" class="btn btn-info" id="save"><i class="far fa-save" ></i> Submit Request</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
var today = new Date();
var end_date = new Date(today.getFullYear(), today.getMonth(), today.getDate());

var td = $('#table_input').DataTable({
    "searching": false,
    "paging": false,
    "info": false,
    "ordering": false,
    "language": {
        'emptyTable': "Please input at least one item"
    }
});

var tf = $('#table_file').DataTable({
    "searching": false,
    "paging": false,
    "info": false,
    "ordering": false,
    "language": {
        'emptyTable': "No attached file..."
    }
});

$(function() {
// $(document).ready(function() {
    function counter_select(special_class = '') {
        return '<select name="accountno[]" class="form-control form-control-sm select2ok ' + special_class + '"><option value=""></option><?php if($account_list){foreach ($account_list as $o_account) {?> <option value="<?=$o_account->account_no;?>"><?=$o_account->account_no.' '.$o_account->account_name;?></option> <?php }} ?></select>';
    }
    var counter2 =  '<input type="text" name="df_desc[]" id="df_desc" class="form-control form-control-sm">';
    var counter3 =  '<input type="text" name="df_debet[]" id="df_debet" class="form-control form-control-sm form-number">';
    var counter4 =  '<input type="text" name="df_kredit[]" id="df_kredit" class="form-control form-control-sm form-number">';
    var btn_remove = '<button id="remove_row_detail" type="button" class="btn btn-sm btn-danger"><i class="fas fa-minus"></i></button>';

    $('input#df_date').datepicker({
        dateFormat: 'dd MM yy',
        changeMonth: true,
        changeYear: true,
        maxDate: end_date
    });

    $('#btn_loop').on( 'click', function (e) {
        e.preventDefault();
        td.row.add( [
            counter_select() +'',
            counter2 +'',
            counter3 +'',
            counter4 +'',
            btn_remove +''
        ] ).draw(false);

        $('.form-number').number(true, 0);

        var allselect = $("#table_input").find(".select2ok");
        if (allselect.length > 0) {
            $.each(allselect, function(i, v) {
                reinitializeselect2(v);
            });
        }
    });

    var r_index = 1;
    $('#btn_files_new').on( 'click', function (e) {
        e.preventDefault();
        tf.row.add( [
            '<input type="file" name="filedf_' + r_index + '" id="filedf" class="form-control fileattachment">',
            '<button id="remove_file" type="button" class="btn btn-danger"><i class="fas fa-minus"></i></button>'
        ] ).draw(false);
        r_index++;
    });

    $('table#table_input tbody').on('click', 'button#remove_row_detail', function(e) {
        e.preventDefault();
        td.row($(this).parents('tr')).remove().draw();
        sum_total();
    });

    $('table#table_file tbody').on('click', 'button#remove_file', function(e) {
        e.preventDefault();
        tf.row($(this).parents('tr')).remove().draw();

        var file_form = $('table#table_file tbody').find('input[type="file"]');
        if (file_form.length > 0) {
            r_index = 1;
            for (let idx = 0; idx < file_form.length; idx++) {
                var form_file = file_form[idx];
                form_file.name = 'filedf_' + r_index;
                console.log(form_file.name);
                r_index++;
            }
        }
    });

    $('#gsr_id, #df_top, #df_account, #bank_code').select2({
        allowClear: true,
        placeholder: "Please select",
        theme: "bootstrap",
    });

    $('#gsr_id').on('select2:select', function (e) {
        var key_id = e.params.data.id;
        var data_account = $('#gsr_id').select2().find(":selected").data("account");
        var data_text = e.params.data.text;
        $.blockUI();
        $.post('<?=base_url()?>apps/gsr/get_gsr_for_df', {data_id : key_id}, function(result) {
            $.unblockUI();
            var data_detail = result.data_detail;
            var data_attach = result.data_attach;

            if (data_detail) {
                td.clear().draw();
                $.each(data_detail, function(i, v) {
                    td.row.add( [
                        counter_select(data_account) +'',
                        '<input type="text" name="df_desc[]" id="df_desc" class="form-control form-control-sm" value="' + v.gsr_details_description + '">',
                        counter3 +'',
                        counter4 +'',
                        btn_remove +''
                    ] ).draw(false);

                    $('.form-number').number(true, 0);
                    $('select.select2ok').select2({
                        allowClear: true,
                        placeholder: "Please select",
                        theme: "bootstrap",
                    });
                })
            }
            $('.' + data_account).val(data_account).trigger('change');
            
            if (data_attach) {
                tf.clear().draw();
                $.each(data_attach, function(i, v) {
                    tf.row.add( [
                        '<i class="fas fa-paperclip"></i>&ensp;' + v.document_name + '<input type="hidden" name="fileattach[]" id="fileattach" class="form-control d-none" value="' + v.gsr_file_id + '">',
                        ''
                    ] ).draw(false);
                    r_index++;
                })
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error retrieve data!');
        });
    });

    $('table#table_input tbody').on('keyup', 'input#df_debet, input#df_kredit', function(e) {
        e.preventDefault();
        
        sum_total();
    });

    $('#save').on('click', function(e) {
        e.preventDefault();

        if (confirm("Submit <?=$target_type;?> ?")) {
            $.blockUI();
            // var data = $('form#submit_form').serialize();

            var form = $('#submit_form');
            var form_data = new FormData(form[0]);
            var uri = form.attr('action');
            
            $.ajax({
                url: uri,
                data: form_data,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: function (xhr, status, error) {
                    $.unblockUI();
                    toastr.error('Error processing data!', 'Error');
                    console.log(xhr.responseText);
                },
                success: function(rtn){
                    $.unblockUI();
                    if (rtn.code == 0) {
                        setTimeout(function() {
                            window.location.href = '<?=base_url()?>apps/gsr/<?=strtolower($target_type);?>_list';
                        }, 2000);
                        // toastr.success('Success!', 'Success!');
                    }else{
                        toastr.warning(rtn.message, 'Warning!');
                    }
                }
            });
        }
    });
})

function reinitializeselect2(elemen) {
    if ($(elemen).hasClass("select2-hidden-accessible")) {
        $(elemen).select2("destroy");
    }

    $(elemen).select2({
        allowClear: true,
        placeholder: "Please select",
        theme: "bootstrap",
    });
}

function sum_total() {
    let rows_debit = $('input#df_debet');
    let rows_kredit = $('input#df_kredit');
    var total_debet = 0;
    var total_kredit = 0;
    $.each(rows_kredit, function(i, v) {
        var value_amount_kredit = (v.value).replace(/\,/g, "");
        var value_amount_kredit = value_amount_kredit.replace(/\./g, "");
        var amount_kredit = ((!value_amount_kredit.trim()) || (!value_amount_kredit)) ? 0 : value_amount_kredit;
        var amount_kredit = (isNaN(amount_kredit)) ? 0 : amount_kredit;
        total_kredit += parseFloat(amount_kredit);
    });

    $.each(rows_debit, function(i, v) {
        var value_amount_debit = (v.value).replace(/\,/g, "");
        var value_amount_debit = value_amount_debit.replace(/\./g, "");
        var amount_debit = ((!value_amount_debit.trim()) || (!value_amount_debit)) ? 0 : value_amount_debit;
        var amount_debit = (isNaN(amount_debit)) ? 0 : amount_debit;
        total_debet += parseFloat(amount_debit);
    });
    // console.log(total);
    // var amount_spelling = $.spellingNumber(total);

    $('input#total_amount_kredit').val(total_kredit);
    $('input#total_amount_debet').val(total_debet);
    // $('input#amount_speeling_input').val(amount_spelling);
    // $('#amount_speeling').text(amount_spelling);
}
</script>