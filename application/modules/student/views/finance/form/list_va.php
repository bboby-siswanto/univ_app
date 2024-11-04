<div class="card">
    <div class="card-header">
        My Billing
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_view_rules" data-toggle="tooltip" data-placement="bottom" title="View Rules" >
                <i class="fa fa-info"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
<?php
if ($billing_list) {
    foreach ($billing_list as $o_billing) {
        if ($o_billing->payment_type_code != '05') {
            $billing_detail = $o_billing->billing_detail;
?>
        <div class="accordion" id="accordion<?=$o_billing->payment_type_code;?>">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?=$o_billing->payment_type_code;?>" aria-expanded="true" aria-controls="collapse<?=$o_billing->payment_type_code;?>">
                <?=$o_billing->payment_type_name;?>
                </button>
            </h2>
            <div id="collapse<?=$o_billing->payment_type_code;?>" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion<?=$o_billing->payment_type_code;?>">
                <table class="table table-sm mb-4">
                    <tr>
                        <td>Amount Billed</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['total_amount'], 0, ',', '.');?></td>
                    </tr>
                    <tr>
                        <td>Total Fined</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['total_fined'], 0, ',','.');?></td>
                    </tr>
                    <tr>
                        <td>Total Paid</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['total_paid'], 0, ',','.');?></td>
                    </tr>
                    <tr>
                        <td>Total Amount Billed</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['total_billed'], 0, ',','.');?></td>
                    </tr>
                    <tr>
                        <td>Minimum Payment</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['min_payment'], 0, ',','.');?></td>
                    </tr>
                    <tr>
                        <td>Beneficiary Name</td>
                        <td>:</td>
                        <td><?=$student_data->personal_data_name;?></td>
                    </tr>
                    <tr>
                        <td>Virtual Account</td>
                        <td>:</td>
                        <td>
                            <?=implode(' ', str_split($billing_detail['va_number'], 4));?>
                            <?php
                            if ($billing_detail['va_status'] == 1) {
                                echo '<span class="badge badge-success">active</span>';
                            }
                            else if ($billing_detail['va_status'] == 2) {
                                echo '<span class="badge badge-danger">inactive</span>';
                            }
                            else if ($billing_detail['va_status'] == 3) {
                                echo '<span class="badge badge-danger">inactive</span>';
                            }
                            else {
                                echo '<span class="badge badge-danger">inactive</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Bank</td>
                        <td>:</td>
                        <td>Bank Negara Indonesia 46 (BNI 46)</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            Fee Details:
                            <ul>
                        <?php
                        if (count($billing_detail['fee_detail']) > 0) {
                            foreach ($billing_detail['fee_detail'] as $s_detail) {
                        ?>
                                <li><?=$s_detail;?></li>
                        <?php
                            }
                        }
                        ?>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
<?php
        }
    }
}
else {
?>
    -
<?php
}
?>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_rules">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rules of Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <?php
            $a_rules_payment = modules::run('messaging/text_template/rules_payment');
            if (count($a_rules_payment) > 0) {
                print('<ul>');
                foreach ($a_rules_payment as $s_rules) {
                    print('<li>'.$s_rules.'</li>');
                }
                print('</ul>');
            }
            ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('#btn_view_rules').on('click', function(e) {
        $('#modal_rules').modal('show');
    })
})
</script>
<?php
// print('<pre>');var_dump($billing_list[0]);
?>