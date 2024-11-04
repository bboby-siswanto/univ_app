<div style="padding-top: 1px;">
    <hr style="height:3px; background-color: black;">
    <p><?=$df_data->df_type;?> <?= ($df_data->df_bank_account == 'MDR') ? 'Mandiri' : 'BNI' ?></p>
    <hr>
    <table style="width: 100%; margin-bottom: 5px;">
        <tr>
            <td width="60%">
                <table>
                    <tr>
                        <td>Term of Payment</td>
                        <td>:</td>
                        <td><?=$df_data->df_top;?></td>
                    </tr>
                    <tr>
                        <td>Ref No</td>
                        <td>:</td>
                        <td><?=$df_data->gsr_code;?></td>
                    </tr>
                    <tr>
                        <td>Paid To</td>
                        <td>:</td>
                        <td><?=$df_data->df_transaction;?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td>VCH No</td>
                        <td>:</td>
                        <td><?=$df_data->df_number;?></td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td><?=date('d F Y', strtotime($df_data->df_date_created));?></td>
                    </tr>
                    <tr>
                        <td>Budget Dept</td>
                        <td>:</td>
                        <td><?=$df_data->df_budget_dept;?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p></p>
    <table style="width: 100%; margin-bottom: 5px; border-collapse: collapse;">
        <tr style="border-top: 2px solid thin; border-bottom: 2px solid thin; font-weight: bold;">
            <td style="padding: 2px; text-align: center;">Desscription</td>
            <td style="padding: 2px; text-align: center;">Debet (Rp)</td>
            <td style="padding: 2px; text-align: center;">Credit (Rp)</td>
            <td style="padding: 2px; text-align: center;">Contra Account</td>
        </tr>
<?php
    $d_total_debet = 0;
    $d_total_kredit = 0;
    if ($df_details) {
        $i_numb = 1;
        foreach ($df_details as $o_detail) {
            $d_total_debet += $o_detail->df_details_debet;
            $d_total_kredit += $o_detail->df_details_kredit;
?>
        <tr style="border-top: solid thin; border-bottom: solid thin;">
            <td style="padding: 2px;"><?=$o_detail->df_details_remarks;?></td>
            <td style="padding: 2px; text-align: right;">Rp <?=number_format($o_detail->df_details_debet, 2, ",", ".");?></td>
            <td style="padding: 2px; text-align: right;">Rp <?=number_format($o_detail->df_details_kredit, 2, ",", ".");?></td>
            <td style="padding: 2px;"><?=$o_detail->account_name.'&nbsp;&nbsp;&nbsp;'.$o_detail->account_no;?></td>
        </tr>
<?php
        }
    }
?>
        <tr style="border-top: 2px solid thin; border-bottom: 2px solid thin; font-weight: bold;">
            <td style="text-align: right; padding-right: 10px;">TOTAL</td>
            <td style="padding: 2px; text-align: right;">Rp <?=number_format($d_total_debet, 2, ",", ".");?></td>
            <td style="padding: 2px; text-align: right;">Rp <?=number_format($d_total_kredit, 2, ",", ".");?></td>
            <td style="padding: 2px;"></td>
        </tr>
    </table>
    <p><br></p>
    <table style="width: 100%">
        <tr>
            <td style="width: 20%;">Prepared By:</td>
            <td style="width: 20%;">Checked By:</td>
            <td style="width: 20%;">Approved By:</td>
            <td style="width: 20%;">Approved By:</td>
            <td>Booked By:</td>
        </tr>
        <!-- <tr>
            <td>Date: <?= ($df_request_data) ? date('d F Y', strtotime($df_request_data->date_added)) : '' ?></td>
            <td>Date: <?= ($df_check_data) ? date('d F Y', strtotime($df_check_data->date_added)) : '' ?></td>
            <td>Date: <?= ($df_approve_data) ? date('d F Y', strtotime($df_approve_data->date_added)) : '' ?></td>
            <td>Date: <?= ($df_finish_data) ? date('d F Y', strtotime($df_finish_data->date_added)) : '' ?></td>
            <td></td>
        </tr> -->
        <tr>
            <td>
                <?= ($df_request_data) ? '<img src="'.base_url().'public/files/get_sign/'.$df_request_data->personal_data_id.'" alt="Signed" class="img-fluid" style="max-width: 100px;">' : '' ?>
            </td>
            <td>
                <?= ($df_check_data) ? '<img src="'.base_url().'public/files/get_sign/'.$df_check_data->personal_data_id.'" alt="Signed" class="img-fluid" style="max-width: 100px;">' : '' ?>
            </td>
            <td>
                <?= ($df_approve_data) ? '<img src="'.base_url().'public/files/get_sign/'.$df_approve_data->personal_data_id.'" alt="Signed" class="img-fluid" style="max-width: 100px;">' : '' ?>
            </td>
            <td>
                <?= ($df_finish_data) ? '<img src="'.base_url().'public/files/get_sign/'.$df_finish_data->personal_data_id.'" alt="Signed" class="img-fluid" style="max-width: 100px;">' : '' ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <td><?= ($df_request_data) ? $df_request_data->name_user : '' ?></td>
            <td><?= ($df_check_data) ? $df_check_data->name_user : '' ?></td>
            <td><?= ($df_approve_data) ? $df_approve_data->name_user : '' ?></td>
            <td><?= ($df_finish_data) ? $df_finish_data->name_user : '' ?></td>
            <td></td>
        </tr>
        <tr>
            <td>Cashier</td>
            <td>Head of ANF</td>
            <td>Rector</td>
            <td>Foundation</td>
            <td>Accounting</td>
        </tr>
    </table>
    <p></p>
    <hr>
</div>