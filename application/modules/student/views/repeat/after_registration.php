<div class="container-fluid">
    <!-- <div class="row"> -->
        <div class="card">
            <div class="card-body">
                <p>You have registered <?=count($subject_repeat);?> subject for repetition</p>
                <table class="table" id="confirmation_table_registration">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Subject Credit</th>
                            <th>Fee</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
    $i_number = 1;
    foreach ($subject_repeat as $o_subject) {
?>
                        <tr>
                            <td><?=$i_number++;?></td>
                            <td><?=$o_subject->subject_code;?></td>
                            <td><?=$o_subject->subject_name;?></td>
                            <td><?=$o_subject->curriculum_subject_credit;?></td>
                            <td align="right"><?= "Rp. ".number_format(400000, 0, ',', '.').",-" ?></td>
                        </tr>
<?php
    }
?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><strong>Total Fee</strong></td>
                            <td align="right"><strong id="total_fee_repeat"><?= "Rp. ".number_format((count($subject_repeat) * 400000), 0, ',', '.').",-" ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <!-- </div> -->
</div>