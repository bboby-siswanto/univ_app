<div class="card">
    <div class="card-header">Add Installment (<?=$personal_data->personal_data_name;?>/<?=$personal_data->finance_year_id;?>)</div>
    <div class="card-body">
        <form method="post" id="initial_installment_form" onsubmit="return false">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Beneficiary Name: <?=$personal_data->personal_data_name;?></label>
                            </div>
                            <div class="form-group">
                                <label>Payment Amount Total: Rp. <?=number_format($invoice_data->sub_invoice_amount, 0, '.', '.');?></label>
                            </div>
                            <div class="form-group">
                                <label>Description: <?=$invoice_data->invoice_description;?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label></label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="down_payment_program" name="down_payment_program">
                            <label class="custom-control-label" for="down_payment_program">With Down Payment Program</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="number_installment_form">Number of Installment</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" id="number_installment_form" name="number_installment_form" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="generate_installment_input">Create Installment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row" id="installment_tag">
            </div>
        </form>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-primary" id="btn_submit_installment">Submit</button>
    </div>
</div>
<script>
// make sure the nominal and deadline are correct
    $(function() {
        $('#down_payment_program').on('change', function(e) {
            e.preventDefault();

            if (this.checked) {
                append_input_installment(0, 'down_payment_input_tag');
            }else{
                $(".down_payment_input_tag").remove();
            }
        });

        $('button#generate_installment_input').on('click', function(e) {
            e.preventDefault();

            var number_installment = $('#number_installment_form').val();

            if (number_installment > 9) {
                toastr.warning('Max installment is 9 !', 'Warning!');
            }else if((number_installment != '') && (parseInt(number_installment) > 0)) {
                $(".installment_input_tag").remove();

                for (let i = 1; i <= number_installment; i++) {
                    append_input_installment(i, 'installment_input_tag');
                }

            }else{
                $(".installment_input_tag").remove();
            }
            
        });
    });

    $('button#btn_submit_installment').on('click', function(e) {
        e.preventDefault();
        var child_installment = $('#installment_tag').children();
        // console.log(child_installment);
        if (child_installment.length > 0) {
            var row_number = $('#number_installment_form').val();

            var tags_down_payment = $('#installment_tag').find('.down_payment_input_tag');
            var tags_installment = $('#installment_tag').find('.installment_input_tag');

            var data_send = [];

            var input_oke = true;
            if (tags_down_payment.length > 0) {
                var input = tags_down_payment.find('input, textarea');

                $.each(input, function(i, v) {
                    if (v.value == '') {
                        input_oke = false;
                        return false;

                    }
                });

                if (input_oke) {
                    data_send.push(
                        {
                            installment_number: 0,
                            installment_billed_amount: $('#installment_form_amount_0').val(),
                            installment_fine_amount: $('#installment_form_fine_amount_0').val(),
                            installment_deadline: $('#installment_form_deadline_0').val(),
                            installment_description: $('#installment_form_descrption_0').val()
                        }
                    );
                }else {
                    toastr.warning('Please fill all input fields!');
                    return false;
                }
            }

            if (tags_installment.length > 0) {
                var input = tags_down_payment.find('input, textarea');

                $.each(input, function(i, v) {
                    if (v.value == '') {
                        input_oke = false;
                        return false;

                    }
                });

                if (input_oke) {
                    for (let i = 1; i <= parseInt(row_number); i++) {
                        data_send.push(
                            {
                                installment_number: i,
                                installment_billed_amount: $('#installment_form_amount_' + i).val(),
                                installment_fine_amount: $('#installment_form_fine_amount_' + i).val(),
                                installment_deadline: $('#installment_form_deadline_' + i).val(),
                                installment_description: $('#installment_form_descrption_' + i).val()
                            }
                        );
                    }
                }else{
                    toastr.warning('Please fill all input fields!');
                    return false;
                }
                
            }
            
            
            if (data_send.length > 0) {
                $.blockUI();

                let url = '<?=base_url()?>finance/invoice/create_initial_installment';
                // let url = '<?=base_url()?>devs/test_prepare_installment';
                var data_installmenet = {
                    data: data_send,
                    student_id: '<?=$personal_data->student_id;?>',
                    sub_invoice_id: '<?=$invoice_data->sub_invoice_id?>'
                };

                $.post(url, data_installmenet, function(result) {
                    $.unblockUI();

                    if (result.code == 0) {
                        toastr.success('success create installment', 'Success');

                        setInterval(function(){
                            window.location.href = '<?=base_url()?>finance/invoice/sub_invoice_details/<?=$invoice_data->sub_invoice_id;?>';
                        }, 5000);
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                },'json').fail(function(params) {
                    $.unblockUI();
                });
            }else{
                toastr.error('No data inputted!');
            }
            
        }else{
            toastr.warning('No installment founded!', 'Warning!');
        }
    });

    function append_input_installment(i, div_tag) {
        if (i == 0) {
            var div_col = $("<div class='col-md-6 " + div_tag + "'></div>").prependTo('#installment_tag');
        }else{
            var div_col = $("<div class='col-md-6 " + div_tag + "'></div>").appendTo('#installment_tag');
        }
        
        var div_card = $("<div class='card'></div>").appendTo(div_col);
        var input_installment = $("<div class='card-body'></div>").appendTo(div_card);
        
        if (i == 0) {
            var form_header = $('<div class="form-group strong"><strong>Down Payment : </strong></div>').appendTo(input_installment);
        }else{
            var form_header = $('<div class="form-group strong"><strong>Installment ' + i + ': </strong></div>').appendTo(input_installment);
        }

        var div_form_group = $('<div class="form-group"></div>');
        var form_amount = div_form_group.appendTo(input_installment);
        var form_fine_amount = div_form_group.appendTo(input_installment);
        var form_deadline = div_form_group.appendTo(input_installment);
        var form_description = div_form_group.appendTo(input_installment);
        
        var input_amount = '<label for="installment_form_amount_' + i + '">Billed Amount</label>';
        input_amount += '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text" id="basic-addon' + i + '">Rp. </span></div>';
        input_amount += '<input type="text" class="form-control" name="installment_amount[]" id="installment_form_amount_' + i + '" aria-describedby="basic-addon' + i + '"></div>';
        $(input_amount).appendTo(form_amount);
        
        var input_fine_amount = '<label for="installment_form_fine_amount_' + i + '">Fine Amount</label>';
        input_fine_amount += '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text" id="basic-fine-addon' + i + '">Rp. </span></div>';
        input_fine_amount += '<input type="text" class="form-control" name="installment_fine_amount[]" id="installment_form_fine_amount_' + i + '" aria-describedby="basic-fine-addon' + i + '" value="0"></div>';
        $(input_fine_amount).appendTo(form_fine_amount);

        var input_deadline = '<label for="installment_form_deadline_' + i + '">Deadline</label><input type="date" class="form-control" name="installment_deadline[]" id="installment_form_deadline_' + i + '"></div>';

        var input_description_value = ((i == 0) ? 'Down Payment ' : ('Installment ' + i + ' ')) + '<?=$invoice_data->invoice_description;?>';
        var input_description = '<label for="installment_form_descrption_' + i + '">Description</label><textarea class="form-control" name="installment_descrption[]" id="installment_form_descrption_' + i + '">' + input_description_value + '</textarea>';
        $(input_deadline).appendTo(form_deadline);
        $(input_description).appendTo(form_description);

        $('#installment_form_amount_' + i).number( true, 0 );
        $('#installment_form_fine_amount_' + i).number( true, 0 );
    }

    // $('.input_amount').number( true, 0 );
</script>