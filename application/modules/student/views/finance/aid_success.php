<div class="card">
    <div class="card-body">
        <h4>Request Success for <?=date("F", mktime(0, 0, 0, $period_data->aid_period_month, 10));?> <?= $period_data->aid_period_year ?></h4>
        <hr>
        <div class="form group">
            <div class="row">
                <div class="col-md-2">Name</div>
                <div class="col-md-10">: <strong><?=$registration_data->personal_data_name;?></strong></div>
            </div>
            <div class="row">
                <div class="col-md-2">Student Number</div>
                <div class="col-md-10">: <strong><?=$registration_data->student_number;?></strong></div>
            </div>
            <div class="row">
                <div class="col-md-2">Study Program</div>
                <div class="col-md-10">: <strong><?=$registration_data->study_program_name;?></strong></div>
            </div>
            <div class="row">
                <div class="col-md-2">Request Date</div>
                <div class="col-md-10">: <strong><?=date('d F Y H:i', strtotime($registration_data->registration_time));?></strong></div>
            </div>
        </div>
        <hr>
        <div class="form group">
            <div class="row">
                <div class="col-md-4">Total Amount Requested of Internet Expense</div>
                <div class="col-md-8">: <strong>Rp <?=number_format($registration_data->request_amount, 2, ',', '.');?></strong></div>
            </div>
            <div class="row">
                <div class="col-md-4">Name of Bank</div>
                <div class="col-md-8">: <strong><?=$registration_data->bank_name;?></strong></div>
            </div>
            <div class="row">
                <div class="col-md-4">Bank Account Number</div>
                <div class="col-md-8">: <strong><?=$registration_data->request_account_number;?></strong></div>
            </div>
            <div class="row">
                <div class="col-md-4">Full Name Registered in Bank Account</div>
                <div class="col-md-8">: <strong><?=$registration_data->request_beneficiary;?></strong></div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <div class="row">
                <div class="col-3">Receipt Bill of Internet Provider:</div>
                <div class="col-9">
                    <ul>
        <?php
        if ($registration_files) {
            $i_count_file = 1;
            foreach ($registration_files as $o_files) {
        ?>
                        <li>
                            <a href="<?=base_url()?>file_manager/student_files/refund/<?=$student_id;?>/<?=$o_files->request_receipt_bill_file;?>" target="_blank" class="btn-link">file_<?=$i_count_file++;?></a>
                        </li>
        <?php
            }
        }
        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>