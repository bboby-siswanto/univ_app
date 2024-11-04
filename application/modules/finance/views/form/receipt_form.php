<table class="table fn-9" style="padding-right: 20px;">
    <tr class="bt bb">
        <td class="bt bb" colspan="5"><strong>RECEIPT</strong></td>
        <!-- <td colspan="5"><strong>RECEIPT</strong></td> -->
    </tr>
    <tr>
        <td>No.</td>
        <td colspan="2" class="bb"><?= (isset($receipt_no)) ? $receipt_no : '' ?></td>
        <td class="text-right">Date</td>
        <td class="bb"><?=(isset($transaction_data) AND ($transaction_data)) ? date('d F Y H:i:s', strtotime($transaction_data[0]->datetime_payment)) : '';?></td>
    </tr>
    <tr>
        <td>Invoice No.</td>
        <td colspan="4" class="bb"><?=(isset($invoice_no)) ? $invoice_no : '';?></td>
    </tr>
    <tr>
        <td>Received From</td>
        <td colspan="4" class="bb"><?=(isset($transaction_data) AND ($transaction_data)) ? $transaction_data[0]->customer_name : '';?></td>
    </tr>
    <tr>
        <td>Method of payment</td>
        <td>Cash / <u>Bank Transfer</u> / Debit Card / Credit Card</td>
        <td class="text-right" colspan="2">(VA No/Trx.ID)</td>
        <td class="text-right bb"><?=(isset($transaction_data) AND ($transaction_data)) ? implode(' ', str_split($transaction_data[0]->virtual_account, 4)) : '';?></td>
    </tr>
<?php
$this->load->helper('iuli_helper');
$d_total_amount_payment = 0;
if ((isset($transaction_data)) AND ($transaction_data)) {
    foreach ($transaction_data as $key => $o_transaction) {
        $d_total_amount_payment += $o_transaction->payment_amount;
?>
    <tr>
        <td><?=($key == 0) ? 'Payment of' : '';?></td>
        <td colspan="2" class="bb"><?=$o_transaction->sub_invoice_details_description;?></td>
        <td class="text-right">Rp.</td>
        <td class="text-right bb"><?=number_format($o_transaction->payment_amount, 0, '.', '.');?></td>
    </tr>
<?php
    }
}
else {
?>
    <tr>
        <td>Payment of</td>
        <td colspan="2" class="bb">-</td>
        <td class="text-right">Rp.</td>
        <td class="bb">0,-</td>
    </tr>
<?php
}
// print($d_total_amount_payment);exit;
?>
    <tr>
        <td></td>
        <td class="bb" colspan="2"></td>
        <td class="text-right">Rp.</td>
        <td class="bb"></td>
    </tr>
    <tr>
        <td></td>
        <td class="text-right" colspan="2"><b>Total</b></td>
        <td class="text-right"><b>Rp.</b></td>
        <td class="text-right bb"><strong><?=number_format($d_total_amount_payment, 0, '.', '.');?></strong></td>
    </tr>
    <tr>
        <td>In Words,</td>
        <td colspan="4" class="bb"><?= ucwords(strtolower(number_to_words($d_total_amount_payment))).' Rupiah';?></td>
    </tr>
    <tr>
        <td>Received by,</td>
        <td>Name:</td>
        <td colspan="3">Signature/Stamp:</td>
    </tr>
    <tr>
        <td></td>
        <td style="vertical-align: bottom;">International University Liaison Indonesia</td>
        <td colspan="3">
            <?= (isset($personal_document_id)) ? '<img src="'.base_url().'public/files/get_sign/'.$personal_document_id.'" alt="Signed" class="img-fluid" style="max-width: 100px;">' : '' ?>
        </td>
    </tr>
</table>