<form id="form_survey_exchange_student" onsubmit="return false" style="background-color: #fff;">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="input_student" class="required_text">Student Name:</label>
                <input type="hidden" name="input_student_id" id="input_student_id">
                <input type="text" name="input_student" id="input_student" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="input_homeuniv" class="required_text">Home University:</label>
                <input type="text" name="input_homeuniv" id="input_homeuniv" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="input_period" class="required_text">Study Period at IULI:</label>
                <input type="text" name="input_period" id="input_period" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="input_prodi" class="required_text">Study Program:</label>
                <input type="text" name="input_prodi" id="input_prodi" class="form-control">
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th></th>
            </tr>
        </thead>
        <tbody>
<?php
if ((isset($question_list)) AND ($question_list)) {
foreach ($question_list as $o_question) {
?>
            <tr>
                <td>
                    <strong><?= $o_question->question_name; ?></strong>
                </td>
            </tr>
<?php
    if ($o_question->question_child) {
        foreach ($o_question->question_child as $o_question_child) {
?>
            <tr>
                <td class="pl-5">
                    <?= $o_question_child->question_name; ?>
    <?php
        if ($o_question_child->question_choices) {
            foreach ($o_question_child->question_choices as $o_choices) {
                $s_input_name = $o_choices->question_id;
                $s_input_names = ($o_question_child->have_description) ? $s_input_name.'['.$o_choices->question_choice_id.']' : $s_input_name;
                print('<div class="">');
                if ($o_choices->has_free_text == 0) {
                    $s_mark_inputradio = ($o_question_child->have_description) ? $s_input_name.'[is_double]' : $s_input_name;
                    if ($o_question_child->have_description) {
                        print('<input type="hidden" name="'.$s_mark_inputradio.'" value="true">');
                    }
    ?>
                    <div class="custom-control custom-radio pb-2">
                        <input type="radio" class="custom-control-input" key="<?=$o_choices->question_id;?>" id="<?=$o_choices->question_choice_id;?>" name="<?=$s_input_names;?>" value="<?= ($o_question_child->have_description) ? 'on' : $o_choices->question_choice_id ?>">
                        <label class="custom-control-label" for="<?=$o_choices->question_choice_id;?>">
                        <?=trim($o_choices->question_choice_name);?>
                        </label>
                    </div>
                    <script>
                        $('input[key=<?=$o_choices->question_id;?>]').change(function() {
                            $('input[key=<?=$o_choices->question_id;?>]:checked').not(this).prop('checked', false);
                        });
                    </script>
    <?php
                }
                else if ($o_choices->question_choice_description == 'TRUE') {
                    $s_text = str_replace('_', '', trim($o_choices->question_choice_name));
                    // $s_text = $o_choices->question_choice_id.'^';
    ?>
                    <small><?=$s_text;?></small>
                    <textarea name="<?=$s_input_names;?>" id="<?=$o_choices->question_id;?>" class="form-control"></textarea>
    <?php
                }
                print('</div>');
            }
        }
    ?>
                </td>
            </tr>
<?php
        }
    }
}
}
?>
            <tr>
                <td></td>
            </tr>
        </tbody>
    </table>
</form>