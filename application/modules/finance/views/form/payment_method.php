<?php
if ((isset($req_style)) AND ($req_style)) {
?>
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table td,.table th {
        border: 1px solid #000;
        text-align: left;
        padding: 2px;
    }
    .mt-4 {
        margin-top: 4vh;
    }
    .text-right {
        text-align: right !important;
    }
</style>
<?php
}
if ((isset($student_data)) AND ($student_data)) {
    if ($req_style) {
?>
<div class="card">
    <div class="card-header">
        <strong>
        <?=((isset($student_data)) AND ($student_data)) ? $student_data->personal_data_name.' ('.$student_data->study_program_abbreviation.'/'.$student_data->academic_year_id.')' : '';?>
        </strong>
    </div>
</div>
<?php
    }
    else {
        echo modules::run('student/show_name', $student_data->student_id, false);
    }
}
?>
<table>
    <tr>
        <td>Invoice Number</td>
        <td>:</td>
        <td><?=$invoice_data->invoice_number;?></td>
    </tr>
    <tr>
        <td>Description</td>
        <td>:</td>
        <td><?=$invoice_data->invoice_description;?></td>
    </tr>
</table>
<?php
if (!$has_installment) {
    if ((isset($fullpayment_method)) AND ($fullpayment_method)) {
        ?>
        <table class="table table-hover mt-4" id="table_fullpayment">
            <thead>
                <tr>
                    <th colspan="6" class="bg-secondary">Full Payment Method</th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Deadline</th>
                    <th>Amount (IDR)</th>
                    <th>Fine (IDR)</th>
                    <th>Total (IDR)</th>
                    <th>Total Amount Paid (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?=$fullpayment_method->sub_invoice_details_description;?></td>
                    <td><?=date('d F Y', strtotime($fullpayment_method->sub_invoice_details_real_datetime_deadline));?></td>
                    <td class="text-right"><?=number_format($fullpayment_method->sub_invoice_details_amount, 0, '.', '.');?></td>
                    <td class="text-right"><?=number_format($fullpayment_method->sub_invoice_details_amount_fined, 0, '.', '.');?></td>
                    <td class="text-right"><?=number_format(($fullpayment_method->sub_invoice_details_amount + $fullpayment_method->sub_invoice_details_amount_fined), 0, '.', '.');?></td>
                    <td class="text-right"><?=number_format($fullpayment_method->sub_invoice_details_amount_paid, 0, '.', '.');?></td>
                </tr>
            </tbody>
        </table>
        <?php
    }
}

if ((isset($installment_method)) AND ($installment_method)) {
?>
<hr>
<table class="table table-hover mt-4" id="table_installment">
    <thead>
        <tr>
            <th colspan="7" class="bg-secondary">Installment Method</th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th>Description</th>
            <th>Deadline</th>
            <th>Amount (IDR)</th>
            <th>Fine (IDR)</th>
            <th>Total (IDR)</th>
            <th>Amount Paid (IDR)</th>
            <th>Last Paid Date</th>
        </tr>
    </thead>
    <tbody>
<?php
    $d_total_installment = 0;
    $d_total_installment_fined = 0;
    $d_total_installment_total = 0;
    $d_total_installment_paid = 0;
    foreach ($installment_method as $o_installment) {
        $d_total_installment += $o_installment->sub_invoice_details_amount;
        $d_total_installment_fined += $o_installment->sub_invoice_details_amount_fined;
        $d_total_installment_total += $o_installment->sub_invoice_details_amount_total;
        $d_total_installment_paid += $o_installment->sub_invoice_details_amount_paid;
?>
        <tr>
            <td><?=$o_installment->sub_invoice_details_description;?></td>
            <td><?= date('d F Y', strtotime($o_installment->sub_invoice_details_real_datetime_deadline));?></td>
            <td class="text-right"><?=number_format($o_installment->sub_invoice_details_amount, 0, '.','.');?></td>
            <td class="text-right"><?=number_format($o_installment->sub_invoice_details_amount_fined, 0, '.', '.');?></td>
            <td class="text-right"><?=number_format($o_installment->sub_invoice_details_amount_total, 0, '.', '.');?></td>
            <td class="text-right"><?=number_format($o_installment->sub_invoice_details_amount_paid, 0, '.','.');?></td>
            <td><?= (!is_null($o_installment->sub_invoice_details_datetime_paid_off)) ? date('d F Y', strtotime($o_installment->sub_invoice_details_datetime_paid_off)) : '';?></td>
        </tr>
<?php
    }
    $d_total_installment_unpaid = $d_total_installment_total - $d_total_installment_paid;
?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td class="text-right"><b><?=number_format($d_total_installment, 0, '.', '.');?></b></td>
            <td class="text-right"><b><?=number_format($d_total_installment_fined, 0, '.', '.');?></b></td>
            <td class="text-right"><strong><?= number_format($d_total_installment_total, 0, '.', '.');?></strong></td>
            <td class="text-right"><strong><?= number_format($d_total_installment_paid, 0, '.', '.');?></strong></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="5">Total Unpaid (IDR)</td>
            <td class="text-right"><b><?= number_format($d_total_installment_unpaid, 0, '.','.'); ?></b></td>
            <td></td>
        </tr>
    </tfoot>
</table>
<?php
}
?>