<div class="row mb-2">
    <div class="col-12">
        <div class="btn-toolbar float-right" role="toolbar" aria-label="Action Button">
            <div class="btn-group mr-2" role="group" aria-label="Action">
                <button type="button" class="btn btn-info" id="save"><i class="far fa-save" ></i> Submit Request</button>
                <a href="<?=base_url()?>apps/gsr/request_list" class="btn btn-success" id="request_list_gsr"><i class="fas fa-list"></i> Request List</a>
            </div>
        </div>
    </div>
</div>
<form action="<?=base_url()?>apps/gsr/submit_gsr" id="submit_form" method="post" onsubmit="return false">
    <input type="hidden" name="gsr_update_key" id="gsr_update_key" value="<?=(isset($gsr_main_data)) ? $gsr_main_data->gsr_id : '';?>">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        GSR Form
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="required_text">GSR No :</label>
                                <input type="text" class="form-control" id="gsr_code" name="gsr_code" value="<?=(isset($gsr_main_data)) ? $gsr_main_data->gsr_code : $optional_gsr_number; ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="">Budget Proposal No :</label>
                                <input type="text" class="form-control" id="budget" name="budget" value="<?= (isset($gsr_main_data)) ? $gsr_main_data->gsr_budget_proposal_number : ''; ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="required_text">Department :</label>
                                <select name="department" id="department" class="form-control" style="width: 100%!important">
                                    <option value="">Please select...</option>
                                <?php 
                                    if ($department_list){
                                        foreach ($department_list as $o_department){
                                            $selected = ((isset($gsr_main_data)) AND ($gsr_main_data->department_id == $o_department->department_id)) ? 'selected="selected"' : '';
                                ?> 
                                    <option value="<?=$o_department->department_id?>" <?=$selected;?>><?=strtoupper($o_department->department_name)?> </option>
                                <?php
                                    }
                                }
                                ?>
                                </select>
                            </div>
                            <div class="form-group col-md-7">
                                <label class="required_text">Booking Code / Account Name:</label>
                                <select name="booking_code" id="booking_code" class="form-control" >
                                    <option value="">Please select...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="">Activity : <small class="text-danger"><i>in english</i></small> </label>
                                <input type="" class="form-control" id="activity_no" name="activity_no" value="<?= (isset($gsr_main_data)) ? $gsr_main_data->gsr_activity : ''; ?>">
                            </div>
                            <!-- <div class="form-group col-md-4">
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
                                <textarea name="gsr_remarks" id="gsr_remarks" class="form-control"></textarea>
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
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table_input" class="table table-bordered table-hover table-sm">
                                <thead class="bg-ligth text-center">
                                    <tr>
                                        <th><span class="required_text">Description</span></th>
                                        <th><span>Activity No</span></th>
                                        <th><span class="required_text">Quantity</span></th>
                                        <th style="width: 80px"><span class="required_text">Unit</span></th>
                                        <th><span class="required_text">Unit Price (IDR)</span></th>
                                        <th><span>Remarks</span></th>
                                        <th><span>Total Price (IDR)</span></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6">TOTAL</th>
                                        <th>
                                            <input type="text" class="form-control form-control-sm form-number" id="total_amount" name="total_amount" readonly>
                                        </th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="7">
                                            <span id="amount_speeling" class="float-right"></span>
                                            <input type="hidden" name="amount_speeling_input" id="amount_speeling_input">
                                        </th>
                                        <th> </th>
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
                                        <td><input type="file" name="filegsr[]" id="filegsr" class="form-control"></td>
                                        <td class="w-25"></td>
                                    </tr> -->
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-12" style="padding-right:1cm;">
                <div class="btn-toolbar float-right" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group mb-3" role="group" aria-label="First group">
                        
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</form>

<script type="text/javascript">
    $('.form-number').number(true, 0);
    var gsr_details = [];
    var gsr_attachment = [];
    <?php
    if ((isset($gsr_main_data)) AND (isset($gsr_details_data))) {
        $gsr_details_data = json_encode($gsr_details_data);
        $gsr_details_data = str_replace('\r\n', ', ', $gsr_details_data);
    ?>
        var gsr_details = JSON.parse('<?=$gsr_details_data;?>');
    <?php
    }
    
    if ((isset($gsr_main_data)) AND (isset($gst_attachment_data))) {
        $gst_attachment_data = json_encode($gst_attachment_data);
    ?>
        var gsr_attachment = JSON.parse('<?=$gst_attachment_data;?>');
    <?php
    }
    ?>
    
    var t = $('#table_input').DataTable({
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
        var counter1 =  '<textarea type="text" name="description[]" id="description" class="form-control form-control-sm"></textarea>';
        var counter2 =  '<input type="text" name="activityno[]" id="activityno" class="form-control form-control-sm">';
        var counter3 =  '<input type="text" name="quantity[]" id="quantity" class="form-control form-control-sm form-number">';
        var counter4 =  '<select id="unit" name="unit[]"  id="unit" class="form-control form-control-sm" ><option value=""></option><?php if ($unit_list){ foreach($unit_list as $o_unit) { ?> <option value="<?=$o_unit->unit_id;?>"><?=strtoupper($o_unit->unit_name);?></option> <?php } } ?></select>';
        var counter5 =  '<input type="number" name="unitprice[]" id="unitprice" class="form-control form-control-sm">';
        var counter6 =  '<textarea type="text" name="remarks[]" id="remarks" class="form-control form-control-sm"></textarea>';
        var counter7 =  '<input type="text" name="amount[]" id="amount" class="form-control form-control-sm" readonly><input type="hidden" name="amountspell[]" id="amountspell">';
        var btn_remove = '<button id="remove_row" type="button" class="btn btn-sm btn-danger"><i class="fas fa-minus"></i></button>';
    
        $('#btn_loop').on( 'click', function (e) {
            e.preventDefault();
            t.row.add( [
                counter1 +'',
                counter2 +'',
                counter3 +'',
                counter4 +'',
                counter5 + '',
                counter6 +'',
                counter7 +'',
                btn_remove +''
            ] ).draw(false);
    
            $('.form-number').number(true, 0);
        });
        
        var r_index = 1;
        $('#btn_files_new').on( 'click', function (e) {
            e.preventDefault();
            tf.row.add( [
                '<input type="file" name="filegsr_' + r_index + '" id="filegsr" class="form-control fileattachment">',
                '<button id="remove_file" type="button" class="btn btn-danger"><i class="fas fa-minus"></i></button>'
            ] ).draw(false);
            r_index++;
        });

        if (gsr_attachment.length > 0) {
            $.each(gsr_attachment, function(i, v) {
                tf.row.add( [
                    v.document_name + '<input type="hidden" name="fileattach[]" id="fileattach" class="form-control d-none" value="' + v.gsr_file_id + '">',
                    // v.document_name,
                    '<button id="remove_file" type="button" class="btn btn-danger"><i class="fas fa-minus"></i></button>'
                ] ).draw(false);
                r_index++;
            });
        }
        // // Automatically add a first row of data
        if (gsr_details.length == 0) {
            $('#btn_loop').click();
        }
        else{
            var $newOption = $("<option selected='selected'></option>").val('<?= (isset($gsr_main_data)) ? $gsr_main_data->account_no : ""; ?>').text('<?= (isset($gsr_main_data)) ? $gsr_main_data->account_no.' / '.$gsr_main_data->account_name : ""; ?>');
            $("select#booking_code").append($newOption).trigger('change');
            var unit_lists = [];
            <?php
            if ((isset($unit_list)) AND ($unit_list)) {
                $unit_lists = json_encode($unit_list);
            ?>
                var unit_lists = JSON.parse('<?=$unit_lists;?>');
            <?php
            }
            ?>

// console.log(gsr_details);
            $.each(gsr_details, function(i, v) {
                var unit_select = '<select id="unit" name="unit[]" id="unit" class="form-control form-control-sm" ><option value=""></option>';
                $.each(unit_lists, function(index, o_unit) {
                    var selected = (o_unit.unit_id == v.unit_id) ? 'selected="selected"' : '';
                    unit_select += '<option value="' + o_unit.unit_id + '" ' + selected + '>' + o_unit.unit_name.toUpperCase() + '</option>';
                });

                unit_select += '</select>';
                t.row.add( [
                    '<textarea type="text" name="description[]" id="description" class="form-control form-control-sm">' + v.gsr_details_description + '</textarea>',
                    '<input type="text" name="activityno[]" id="activityno" class="form-control form-control-sm" value="' + v.gsr_details_activity_id + '">',
                    '<input type="text" name="quantity[]" id="quantity" class="form-control form-control-sm form-number" value="' + v.gsr_details_qty + '">',
                    unit_select,
                    '<input type="text" name="unitprice[]" id="unitprice" class="form-control form-control-sm" value="' + v.gsr_details_price + '">',
                    '<textarea type="text" name="remarks[]" id="remarks" class="form-control form-control-sm">' + v.gsr_details_remarks + '</textarea>',
                    '<input type="text" name="amount[]" id="amount" class="form-control form-control-sm" value="' + v.gsr_details_total_price + '" readonly><input type="hidden" name="amountspell[]" id="amountspell" value="' + v.gsr_details_total_price_text + '">',
                    btn_remove
                ] ).draw();

                sum_total_amount();
                $('.form-number').number(true, 0);
            });
        }

        $('table#table_input tbody').on('keyup', 'input#quantity', function(e) {
            e.preventDefault();
            var rows = t.row($(this).parents('tr')).index();
            var tr = $('table#table_input tbody').find('tr').eq(rows);
            var price = tr.find('input#unitprice');

            var total_price = calculate_($(this).val(), price.val(), tr);
        });

        $('table#table_input tbody').on('input', 'input#amount', function(e) {
            e.preventDefault();

            // console.log('show');
        });
        
        $('table#table_input tbody').on('keyup', 'input#unitprice', function(e) {
            e.preventDefault();
            var rows = t.row($(this).parents('tr')).index();
            var tr = $('table#table_input tbody').find('tr').eq(rows);
            var qty = tr.find('input#quantity');

            var total_price = calculate_(qty.val(), $(this).val(), tr);
        });

        $('table#table_input tbody').on('click', 'button#remove_row', function(e) {
            e.preventDefault();
            t.row($(this).parents('tr')).remove().draw();
            sum_total_amount();
        });

        $('table#table_file tbody').on('click', 'button#remove_file', function(e) {
            e.preventDefault();
            tf.row($(this).parents('tr')).remove().draw();

            var file_form = $('table#table_file tbody').find('input');
            if (file_form.length > 0) {
                r_index = 1;
                for (let idx = 0; idx < file_form.length; idx++) {
                    var form_file = file_form[idx];
                    form_file.name = 'filegsr_' + r_index;
                    // console.log(form_file.name);
                    r_index++;
                }
            }
        });
    });

    function calculate_(qty, price, row_tag) {
        var amount = 0;
        qty = (isNaN(qty)) ? 0 : qty;
        price = (isNaN(price)) ? 0 : price;
        total_price = qty * price;

        var amount = row_tag.find('input#amount');
        var inputamountspell = row_tag.find('input#amountspell');
        amount.val(total_price);
        
        var amountspell = $.spellingNumber(total_price);
        inputamountspell.val(amountspell);
        sum_total_amount();
    }

    function sum_total_amount() {
        let rows = $('input#amount');
        var total = 0;
        $.each(rows, function(i, v) {
            var value_amount = (v.value).replace(/\,/g, "");
            var value_amount = value_amount.replace(/\./g, "");
            var amount = ((!value_amount.trim()) || (!value_amount)) ? 0 : value_amount;
            var amount = (isNaN(amount)) ? 0 : amount;
            // console.log(amount);
            total += parseFloat(amount);
        });
        // console.log(total);
        var amount_spelling = $.spellingNumber(total);

        $('input#total_amount').val(total);
        $('input#amount_speeling_input').val(amount_spelling);
        $('#amount_speeling').text(amount_spelling);
    }

    $('select#booking_code').select2({
        // minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Please select...',
        theme: "bootstrap",
        ajax: {
            url: '<?=base_url()?>apps/gsr/get_list_account',
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
                            text: items.account_no + ' / ' + items.account_name,
                            id: items.account_no,
                            marked: items.account_marked_strong,
                            paddleft: items.level_of_padd
                        }
                    })
                }
            }
        },
        templateResult: select2asHtml
    });

    $('select#department').on('change', function(){
        var department_selected = $('select#department').val();
        if ($('select#department').val() == '') {
            var department_selected = '<?=$department_list[0]->department_id;?>';
        }
        $.post('<?=base_url()?>apps/gsr/get_gsr_number', {department_id: department_selected}, function(result) {
            $('input#gsr_code').val(result.gsr_number);
        }, 'json').fail(function(params){
            toastr.error('Error processing data!', 'error');
        });
    });

    function select2asHtml(optionElement) {
        if (!optionElement.id) { return optionElement.text; }
        if (optionElement.marked == '0') { return optionElement.text; }
        var textstate = optionElement.text;
        if (optionElement.marked == '0') {
            textstate = '<strong>' + optionElement.text + '</strong> ';
        }

        let i_padd = optionElement.paddleft;
        
        var paddstr = '';
        for (let i = 0; i < i_padd; i++) {
            paddstr += '&emsp;';
        }
        
        // $state = $(paddstr + textstate);
        let state = paddstr + textstate;
        // console.log(state);

        // return $state;
        return $('<strong>' + state + '</strong> ');
    }

    function refreshPage() {
        // location.reload(true);
        window.location.href = '<?=base_url()?>apps/gsr/request_list';
    }

    $('#save').on('click', function(e) {
        e.preventDefault();

        if (confirm("Submit GSR ?")) {
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
                        setInterval('refreshPage()', 1000);
                        toastr.success('Success!', 'Success!');
                    }else{
                        toastr.warning(rtn.message, 'Warning!');
                    }
                }
            });
        }
    });

   
</script>