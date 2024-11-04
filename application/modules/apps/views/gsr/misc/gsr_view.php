<?php
if ($gsr_data->last_status == 'requested') {
    $gsr_review_data = false;
    $gsr_approve_data = false;
    $gsr_finish_data = false;
}
else if ($gsr_data->last_status == 'reviewed') {
    $gsr_approve_data = false;
    $gsr_finish_data = false;
}
else if ($gsr_data->last_status == 'approved') {
    $gsr_finish_data = false;
}
?>
<div style="padding-top: 1px;">
    <hr style="height:3px; background-color: black;">
    <p><b>Goods / Service Request Form</b></p>
    <hr>
    <table style="width: 100%; margin-bottom: 5px;">
        <tr>
            <td width="60%">
                <table>
                    <tr>
                        <td>GSR No</td>
                        <td>:</td>
                        <td><?=$gsr_data->gsr_code;?></td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td><?=date('d F Y', strtotime($gsr_data->gsr_date_request));?></td>
                    </tr>
                    <tr>
                        <td>Booking Code</td>
                        <td>:</td>
                        <td><?=$gsr_data->account_no;?>&nbsp;&nbsp;&nbsp;&nbsp;<?=$gsr_data->account_name;?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td>Department</td>
                        <td>:</td>
                        <td><?=$gsr_data->department_abbreviation;?></td>
                    </tr>
                    <tr>
                        <td>Budget Proposal No.</td>
                        <td>:</td>
                        <td><?=$gsr_data->gsr_budget_proposal_number;?></td>
                    </tr>
                    <tr>
                        <td>Activity</td>
                        <td>:</td>
                        <td><?=$gsr_data->gsr_activity;?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p></p>
    <table style="width: 100%; margin-bottom: 5px; border-collapse: collapse;">
        <tr style="border-top: solid thin; border-bottom: solid thin; font-weight: bold;">
            <td style="padding: 2px; text-align: center;border: 1px solid #000;">No</td>
            <td style="padding: 2px; text-align: center;border: 1px solid #000;">Description</td>
            <td style="padding: 2px; text-align: center;border: 1px solid #000;">Activity No</td>
            <td style="padding: 2px; text-align: center;border: 1px solid #000;">Quantity</td>
            <td style="padding: 2px; text-align: center;border: 1px solid #000;">Unit Price</td>
            <td style="padding: 2px; text-align: center;border: 1px solid #000;">Total Price</td>
            <td style="padding: 2px; text-align: center;border: 1px solid #000;">Remarks</td>
        </tr>
<?php
    $d_total_price = 0;
    if ($gsr_details) {
        $i_numb = 1;
        foreach ($gsr_details as $o_detail) {
            $d_total_price += $o_detail->gsr_details_total_price;
?>
        <tr style="border-top: solid thin; border-bottom: solid thin;">
            <td style="padding: 2px; text-align: center;border: 1px solid #000;"><?=$i_numb++;?></td>
            <td style="padding: 2px;border: 1px solid #000;"><?=$o_detail->gsr_details_description;?></td>
            <td style="padding: 2px;border: 1px solid #000;"><?=$o_detail->gsr_details_activity_id;?></td>
            <td style="padding: 2px; text-align: center;border: 1px solid #000;"><?=number_format($o_detail->gsr_details_qty, 0);?></td>
            <td style="padding: 2px; text-align: right;border: 1px solid #000;">Rp <?=number_format($o_detail->gsr_details_price, 2, ",", ".");?></td>
            <td style="padding: 2px; text-align: right;border: 1px solid #000;">Rp <?=number_format($o_detail->gsr_details_total_price, 2, ",", ".");?></td>
            <td style="padding: 2px;border: 1px solid #000;"><?=$o_detail->gsr_details_remarks;?></td>
        </tr>
<?php
        }
    }
?>
        <tr style="border-top: solid thin; border-bottom: solid thin; font-weight: bold;">
            <td colspan="5" style="text-align: right;border: 1px solid #000;">TOTAL</td>
            <td style="padding: 2px; text-align: right;border: 1px solid #000;">Rp <?=number_format($d_total_price, 2, ",", ".");?></td>
            <td style="padding: 2px;border: 1px solid #000;"></td>
        </tr>
        <tr>
            <td colspan="7" style="padding-top: 15px;"></td>
        </tr>
        <tr style="font-weight: bold;">
            <td colspan="2" style="text-align: right;">Sum of (In Word) :</td>
            <td colspan="4" style="padding: 2px; text-align: right; border: solid thin;"><?=ucwords(strtolower($gsr_data->gsr_total_amount_text));?> Rupiahs</td>
            <td></td>
        </tr>
    </table>
    <p><br></p>
    <table style="width: 100%">
        <tr>
            <td style="width: 33%;">Requested By:</td>
            <td style="width: 33%;">Reviewed By:</td>
            <!-- <td style="width: 33%;">Approved By:</td> -->
            <td>Approved By:</td>
        </tr>
        <tr>
            <td></td>
            <td>Date: <?= ($gsr_review_data) ? date('d F Y', strtotime($gsr_review_data->date_added)) : '' ?></td>
            <td>Date: <?= ($gsr_approve_data) ? date('d F Y', strtotime($gsr_approve_data->date_added)) : '' ?></td>
        </tr>
        <tr>
            <td>
                <?= (($gsr_request_data) AND (!is_null($gsr_request_data->personal_document_id))) ? '<img src="'.base_url().'public/files/get_sign/'.$gsr_request_data->personal_document_id.'" alt="Signed" class="img-fluid" style="max-width: 100px;">' : '' ?>
            </td>
            <td>
                <?= ($gsr_review_data) ? '<img src="'.base_url().'public/files/get_sign/'.$gsr_review_data->personal_document_id.'" alt="Signed" class="img-fluid" style="max-width: 100px;">' : '' ?>
            </td>
            <td>
                <?= ($gsr_approve_data) ? '<img src="'.base_url().'public/files/get_sign/'.$gsr_approve_data->personal_document_id.'" alt="Signed" class="img-fluid" style="max-width: 100px;">' : '' ?>
            </td>
        </tr>
        <tr>
            <td><?= ($gsr_request_data) ? $gsr_request_data->personal_data_name : '' ?></td>
            <td><?= ($gsr_review_data) ? $gsr_review_data->personal_data_name : '' ?></td>
            <td><?= ($gsr_approve_data) ? $gsr_approve_data->personal_data_name : '' ?></td>
            <!-- <td><?= ($gsr_finish_data) ? $gsr_finish_data->personal_data_name : '' ?></td> -->
        </tr>
    </table>
    <p></p>
    <hr>
</div>