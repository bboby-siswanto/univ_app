<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form id="form_survey_exchange_student" onsubmit="return false" style="background-color: #fff;">
    <p>
        <span style="font-size: 24px;">STUDENT EXCHANGE PROGRAM</span><br>
        <span style="font-size: 24px;">STUDENT SURVEY</span>
    </p>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid;">
        <tr>
            <td>
                <table>
                    <tr>
                        <td>Student Name</td>
                        <td>:</td>
                        <td><?=$exchange_data->personal_data_name;?></td>
                    </tr>
                    <tr>
                        <td>Home University</td>
                        <td>:</td>
                        <td><?=$exchange_data->institution_name;?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td>Study Period at IULI</td>
                        <td>:</td>
                        <td><?=$exchange_data->academic_year_id;?></td>
                    </tr>
                    <tr>
                        <td>Study Program</td>
                        <td>:</td>
                        <td><?=$exchange_data->study_program_name;?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table style="width: 100%; border-collapse: collapse; border: 1px solid; margin-top: 20px;">
<?php
if ((isset($question_list)) AND ($question_list)) {
foreach ($question_list as $o_question) {
?>
            <tr>
                <td style="background-color:#6c92ea; border-top: 1px solid; border-bottom: 1px solid; padding: 5px;">
                    <strong><?= $o_question->question_name; ?></strong>
                </td>
            </tr>
<?php
    if ($o_question->question_child) {
        foreach ($o_question->question_child as $o_question_child) {
?>
            <tr>
                <td style="padding: 10px; border: 1px solid;">
                    <?= $o_question_child->question_name; ?>
    <?php
        if ($o_question_child->question_choices) {
            foreach ($o_question_child->question_choices as $o_choices) {
                $s_input_name = $o_choices->question_id;
                $s_input_names = ($o_question_child->have_description) ? $s_input_name.'['.$o_choices->question_choice_id.']' : $s_input_name;
                print('<div style="margin-left: 5px; width: 100%;">');
                if ($o_choices->has_free_text == 0) {
                    $checked = '';
                    if ((isset($o_choices->answer_data)) AND ($o_choices->answer_data)) {
                        $checked = 'checked="checked"';
                    }
    ?>
                    <div class="custom-control custom-radio pb-2">
                        <input type="radio" class="custom-control-input" <?=$checked;?> key="<?=$o_choices->question_id;?>" id="<?=$o_choices->question_choice_id;?>" name="<?=$s_input_names;?>" value="<?= ($o_question_child->have_description) ? 'on' : $o_choices->question_choice_id ?>">
                        <label class="custom-control-label" for="<?=$o_choices->question_choice_id;?>">
                        <?=trim($o_choices->question_choice_name);?>
                        </label>
                    </div>
    <?php
                }
                else if ($o_choices->question_choice_description == 'TRUE') {
                    $s_text = str_replace('_', '', trim($o_choices->question_choice_name));
                    // $s_text = $o_choices->question_choice_id.'^';
    ?>
                    <!-- <div style="width: 100%; border: 1px solid; padding-left: 10px; padding-bottom: 10px; padding-right: 10px;"> -->
                        <i><small><?=$s_text;?></small></i>
                        <br>
                        <span><strong>
                    <?php
                    if ((isset($o_choices->answer_data)) AND ($o_choices->answer_data)) {
                        $o_answerdata = $o_choices->answer_data;
                        print($o_answerdata->answer_content);
                    }
                    ?>
                        </strong></span>
                    <!-- </div> -->
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
    </table>
</form>
</body>
</html>