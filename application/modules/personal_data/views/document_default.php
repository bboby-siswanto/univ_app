<?php
    // (isset($student_data)) ? modules::run('student/show_name', $student_data->student_id) : '';
    print modules::run('student/show_name');
?>
<div class="row">
    <?=modules::run('personal_data/document/view_list_supporting_document', $personal_data_id)?>
</div>