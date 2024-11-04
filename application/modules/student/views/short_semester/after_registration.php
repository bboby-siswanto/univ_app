<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <p>
                You have already registered your short semester KRS, please check your transcript for the respective semester. If you need to change the subject please contact your dean.
            </p>
            <hr>
            <table id="subject_registration_short_semester" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Subject Name</th>
                        <th>SKS</th>
                        <th>Subject Type</th>
                        <th>Lecturer</th>
                        <th>Approval</th>
                    </tr>
                </thead>
                <tbody>
<?php
    $i_sks_count = 0;
    if ($mbo_registration_data) {
        $i_num = 1;
        foreach ($mbo_registration_data as $o_score) {
            $s_line_color = '';
            if ($o_score->score_approval == 'approved') {
                $i_sks_count += $o_score->curriculum_subject_credit;
                $s_line_color = 'bg-success';
            }else if ($o_score->score_approval == 'rejected') {
                $s_line_color = 'bg-danger';
            }
?>
                    <tr class="<?= $s_line_color; ?>">
                        <td><?= $i_num++; ?></td>
                        <td><?= $o_score->subject_name; ?></td>
                        <td><?= $o_score->curriculum_subject_credit; ?></td>
                        <td><?= strtoupper($o_score->curriculum_subject_type); ?></td>
                        <td><?= $o_score->lecturer_name; ?></td>
                        <td><?= strtoupper($o_score->score_approval); ?></td>
                    </tr>
<?php
        }
    }else{
?>
                    <tr>
                        <td colspan="6" align="center">No data available in table</td>
                    </tr>
<?php
    }
?>
                </tbody>
            </table>
            <p>
                Total credit approved: <?= $i_sks_count;?> SKS
            </p>
        </div>
    </div>
</div>