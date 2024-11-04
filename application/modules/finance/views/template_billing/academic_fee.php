<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Fee</title>
    <style>
        .table {
            border-collapse: collapse;
        }

        /* table, td, th {
            border: 1px solid black;
        } */
    </style>
</head>
<body>
<p>Greetings from IULI.</p>
<p>A kindly reminder about your fee:</p>
<p><br></p>
<table>
    <tr>
        <td>Student Name</td>
        <td>:</td>
        <td><?=$student_data->personal_data_name?></td>
    </tr>
    <tr>
        <td>Amount Billed</td>
        <td>:</td>
        <td align="right">Rp. <?= number_format($billing['total_amount'], 0, ',', '.')?>,-</td>
    </tr>
    <tr>
        <td>Total Fine</td>
        <td>:</td>
        <td align="right">Rp. <?= number_format($billing['total_fined'], 0, ',', '.')?>,-</td>
    </tr>
    <tr>
        <td>Total Paid</td>
        <td>:</td>
        <td align="right">Rp. <?= number_format($billing['total_paid'], 0, ',', '.')?>,-</td>
    </tr>
    <tr>
        <td>Total Amount Billed</td>
        <td>:</td>
        <td align="right">Rp. <?= number_format($billing['total_billed'], 0, ',', '.')?>,-</td>
    </tr>
</table>
<p>
    Fee Details:
    <ul>
        <?php
        if (count($fee_detail) > 0) {
            foreach ($fee_detail as $s_detail) {
        ?>
                <li><?=$s_detail;?></li>
        <?php
            }
        }
        ?>
    </ul>
</p>
<p><br></p>
<p>Please transfer payment through:</p>
<table>
    <tr>
        <td>Virtual Account</td>
        <td>:</td>
        <td><strong><?=implode(' ', str_split($va_number, 4))?></strong></td>
    </tr>
    <tr>
        <td>Account Holder Name</td>
        <td>:</td>
        <td><strong><?=$student_data->personal_data_name?></strong></td>
    </tr>
    <tr>
        <td>Deadline</td>
        <td>:</td>
        <td><strong><?=date('d F Y', strtotime($billing['deadline']))?></strong></td>
    </tr>
    <tr>
        <td>Bank</td>
        <td>:</td>
        <td><strong>Bank Negara Indonesia 46 (BNI 46)</strong></td>
    </tr>
    <tr>
        <td>Full Payment</td>
        <td>:</td>
        <td><strong>Rp. <?=number_format($billing['total_billed'], 0, ',', '.')?>,-</strong></td>
    </tr>
    <tr>
        <td>Minimum Payment</td>
        <td>:</td>
        <td><strong>Rp. <?=number_format($billing['min_payment'], 0, ',', '.')?>,-</strong></td>
    </tr>
</table>
<p><br></p>
<ul>
Notes:
    <li>BNI Will reject payment is not at the exact of <b>minimum payment amount</b> and account as stated above.</li>
    <li>If students making a payment without using a virtual account, then we assume no payment has been made.</li>
    <li>Please ignore this email if you have already paid.</li>
</ul>
</body>
</html>