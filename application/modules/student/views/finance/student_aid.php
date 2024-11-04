<div class="card">
    <div class="card-header">
        Form Refund Request
    </div>
    <div class="card-body">
        <form id="form_request_aid" onsubmit="return false">
            <input type="hidden" name="aid_period_id" id="aid_period_id" value="<?=$aid_period_id;?>">
            <div class="form group">
                <div class="row">
                    <div class="col-md-2">Name:</div>
                    <div class="col-md-10"><strong><?=$student_data->personal_data_name;?></strong></div>
                </div>
                <div class="row">
                    <div class="col-md-2">Student Number:</div>
                    <div class="col-md-10"><strong><?=$student_data->student_number;?></strong></div>
                </div>
                <div class="row">
                    <div class="col-md-2">Study Program:</div>
                    <div class="col-md-10"><strong><?=$student_data->study_program_name;?></strong></div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <form-group>
                        <label for="request_amount" class="required_text">Total Amount Requested of Internet Expense</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon_request_amount">Rp</span>
                            </div>
                            <input type="text" name="request_amount" id="request_amount" class="form-control" aria-label="Amount Requested" aria-describedby="basic-addon_request_amount">
                        </div>
                    </form-group>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="bank_code" class="required_text">Name of Bank</label>
                        <select name="bank_code" id="bank_code" class="form-control">
                            <option value=""></option>
<?php
    if ($a_bank_list) {
        foreach ($a_bank_list as $o_bank) {
?>
                            <option value="<?=$o_bank->bank_code;?>"><?=$o_bank->bank_name;?></option>
<?php
        }
    }
?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="request_bank_branch" class="required_text">Branch (City of Bank Account)</label>
                        <input type="text" class="form-control" name="request_bank_branch" id="request_bank_branch">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="request_account_number" class="required_text">Bank Account Number</label>
                        <input type="text" class="form-control" name="request_account_number" id="request_account_number">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="request_beneficiary" class="required_text">Full Name Registered in Bank Account</label>
                        <input type="text" class="form-control" name="request_beneficiary" id="request_beneficiary">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="required_text">Receipt Bill of Internet Provider</label>
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control" name="request_receipt_bill_file[]">
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control" name="request_receipt_bill_file[]">
                    </div>
                    <div class="form-group">
                        <input type="file" class="form-control" name="request_receipt_bill_file[]">
                    </div>
                    <!-- <small></small> -->
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col">
                <button id="btn_submit_request_aid" class="btn btn-info" type="button">Submit</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('select#bank_code').select2({
            allowClear: true,
            placeholder: "Please select..",
            theme: "bootstrap",
            minimumInputLength: 1
            // dropdownParent: $("#activity_adviser_modal"),
        });

        $('#request_amount').number( true, 0 );

        $('button#btn_submit_request_aid').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            var data = $('form#form_request_aid').serialize();

            save_request_aid().then((res) => {
                $.unblockUI();
                if(res.code != 0){
                    toastr['warning'](res.message, 'Warning!');
                }
                else{
                    toastr['success']('Data saved', 'Success!');
                    setTimeout( function(){ 
                        location.reload();
                    }  , 3000 );
                }
            }).catch((err) => {
                $.unblockUI();
                toastr['error']('Error processing data', 'Error!');
                console.log(err);
            });
        });

        function save_request_aid(){
            return new Promise((resolve, reject) => {
                var request_form = $('form#form_request_aid');
                var request_form_data = new FormData(request_form[0]);
                
                $.ajax({
                    url: '<?=base_url()?>student/finance/submit_refund_request',
                    data: request_form_data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    dataType: 'json',
                    success: function(rtn){
                        resolve(rtn);
                    }
                });
            }, (err) => {
                reject(err);
            });
        }
    });
</script>