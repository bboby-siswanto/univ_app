<?php
if ((isset($thesis_list)) AND ($thesis_list)) {
    foreach ($thesis_list as $o_thesis_student) {
?>
<div class="list-group">
    <div class="list-group-item">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">
                <a href="<?=base_url()?>public/files/file/<?=$o_thesis_student->linkfile.'/'.$o_thesis_student->filename;?>">
                    <?=$o_thesis_student->thesis_title;?>
                </a>
            </h5>
        </div>
        <p class="mb-1"><?=$o_thesis_student->personal_data_name;?> (<?=$o_thesis_student->study_program_abbreviation;?>/<?=$o_thesis_student->student_batch;?>)</p>
        <small>Advisor: Advisor name.</small>
    </div>
</div>
<?php
    }
}
?>