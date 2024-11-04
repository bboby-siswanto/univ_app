<?php
if ($thesis_data->current_status == 'approved_hsp') {
?>
<h4>Dear Deans</h4>
<p>Mr/Mrs.kaprodi telah menyetujui <?=$a_data['target'];?> mahasiswa atas nama <?=$thesis_data->personal_data_name;?>. dimohon untuk direview kembali <?=$a_data['target'];?> kembali di portal.</p>
<?php
}
else if ($thesis_data->current_status == 'approved') {
?>
<h4>Dear Student</h4>
<p>your Deans approve/reject your <?=$a_data['target'];?> with details:</p>
<?php
}
?>
<h4>Dear <?=$dear;?></h4>
<p><?=$message;?></p>
<table>
    <tr>
        <td>Student</td>
        <td>:</td>
        <td><?=$thesis_data->personal_data_name;?> <?=$thesis_data->study_program_abbreviation;?>/<?=$thesis_data->student_batch;?></td>
    </tr>
    <tr>
        <td>Thesis Title</td>
        <td>:</td>
        <td><?=$thesis_data->thesis_title;?></td>
    </tr>
    <tr>
        <td>Advisor/Co-Advisor</td>
        <td>:</td>
        <td><?=$advisor_approved;?></td>
    </tr>
    <tr>
        <td>Current Status</td>
        <td>:</td>
        <td><?=$thesis_data->current_status;?></td>
    </tr>
    <tr>
        <td>Remarks</td>
        <td>:</td>
        <td>catatan</td>
    </tr>
</table>
<p>
<a href="<?=base_url()?>">portal.iuli.ac.id</a>
</p>