<div class="card">
    <div class="card-header">
        <strong>LIST OF QUESTIONS FOR DIKTI TRACER STUDY FORM</strong>
    </div>
<?php
    if ($user_has_answered) {
?>
    <div class="card-body">
        <h4>
            Thank you for filling out the Dikti questionnaire form, 
            <a href="<?=base_url()?>alumni/dikti_tracer_study/filling">click here</a> to fill out the Dikti questionnaire form
        </h4>
    </div>
<?php
    }else{
?>
    <div class="card-body">
        <form id="form_dikti_tracer_study" onsubmit="return false;">
        <div class="">
            <table class="table" id="table_questionaire">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
<?php
    if ($dikti_question) {
        $a_input_number = ['f502', 'f503', 'f1301', 'f1302', 'f1303', 'f302', 'f303', 'f6', 'f7', 'f7a'];
        foreach ($dikti_question as $o_question) {
            // $i_rowspan = ($o_question->question_child) ? count($o_question->question_child) : 0;
            
?>
                    <tr id="question_<?= $o_question->question_id ;?>">
                        <td> <?=$o_question->question_number;?></td>
                        <td>
                            <div class="row">
                                <div class="col-md-6">
                                    <span <?=($o_question->is_required == 'TRUE') ? 'class="required_text"' : '';?>>
                                        <?=trim($o_question->question_name);?>
                                    </span>
                                    <br>
                                    <?= (!is_null($o_question->question_english_name)) ? '<i>'.trim($o_question->question_english_name).'</i>' : '' ?>
                                </div>
                                <div class="col-md-6">
<?php
            if ($o_question->question_choices) {
                foreach ($o_question->question_choices as $o_choices) {
                    $input_value = '';
                    $s_input_type = (in_array($o_choices->dikti_input_code, $a_input_number)) ? 'number' : 'text';

                    if (strpos(strtolower($o_choices->question_choice_name), 'rp.') !== false) {
                        $input_value = '0';
                    }
                    
                    if ($o_question->is_multiple == 1) {
?>
                                    <div class="custom-control custom-checkbox pb-2">
                                        <input type="checkbox" class="custom-control-input" id="<?=$o_choices->question_choice_id;?>" name="<?=$o_choices->question_id;?>[]" value="<?=$o_choices->question_choice_id;?>">
                                        <label class="custom-control-label w-100" for="<?=$o_choices->question_choice_id;?>">
<?php
                        $s_choices = trim($o_choices->question_choice_name);
                        $s_form_control_width = (strpos($s_choices, '_') != (strlen($s_choices) - 1)) ? 'form-control-sm w-30' : 'form-control-sm w-100 mt-1';
                        if ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') == (strlen($s_choices) - 1))) {
                            $s_choices = str_replace('_', '', $s_choices);
                            $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                            $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                            $s_choices .= ' _';
                            $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        }
                        else {
                            $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                            $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                            $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                        }
                        // $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        // $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                        // $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                        print($s_choices);
?>
                                        </label>
                                    </div>
<?php
                    }else if($o_choices->has_free_text == 1){
                        $s_choices = trim($o_choices->question_choice_name);
                        $s_form_control_width = (strpos($s_choices, '_') != (strlen($s_choices) - 1)) ? 'form-control-sm w-30' : 'form-control-sm w-100 mt-1';
                        $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                        $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                        print($s_choices);
                    }else {
?>
                                    <div class="custom-control custom-radio pb-2">
                                        <input type="radio" class="custom-control-input" id="<?=$o_choices->question_choice_id;?>" name="<?=$o_choices->question_id;?>" value="<?=$o_choices->question_choice_id;?>">
                                        <label class="custom-control-label w-100" for="<?=$o_choices->question_choice_id;?>">
<?php
                        $s_choices = trim($o_choices->question_choice_name);
                        $s_form_control_width = (strpos($s_choices, '_') != (strlen($s_choices) - 1)) ? 'form-control-sm w-30' : 'form-control-sm w-100 mt-1';
                        if ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') == (strlen($s_choices) - 1))) {
                            $s_choices = str_replace('_', '', $s_choices);
                            $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                            $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                            $s_choices .= ' _';
                            $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        }
                        else {
                            $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                            $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                            $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                        }
                        print($s_choices);
?>
                                        </label>
                                    </div>
<?php
                    }
                }
            }
?>
                                </div>
                            </div>
                        </td>
                    </tr>
<?php
            if ($o_question->question_child) {
                foreach ($o_question->question_child as $o_question_child) {
?>
                    <tr id="question_<?= $o_question_child->question_id ;?>">
                        <td></td>
                        <td>
                            <div class="row">
                                <div class="col-md-6">
                                    <span <?=($o_question->is_required == 'TRUE') ? 'class="required_text"' : '';?>>
                                        <?=$o_question_child->question_number;?>. <?=trim($o_question_child->question_name);?>
                                        <?= (!is_null($o_question_child->question_english_name)) ? '<i>('.trim($o_question_child->question_english_name).')</i>' : '' ?>
                                    </span>
                                </div>
                                <div class="col-md-6">
<?php
            if ($o_question_child->question_choices) {
                foreach ($o_question_child->question_choices as $o_choices) {
                    $input_value = '';
                    $s_input_type = (in_array($o_choices->dikti_input_code, $a_input_number)) ? 'number' : 'text';

                    if (strpos(strtolower($o_choices->question_choice_name), 'rp.') !== false) {
                        $input_value = '0';
                    }

                    if ($o_question_child->is_multiple == 1) {
?>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="<?=$o_choices->question_choice_id;?>" name="<?=$o_choices->question_id;?>[]" value="<?=$o_choices->question_choice_id;?>">
                                        <label class="custom-control-label" for="<?=$o_choices->question_choice_id;?>">
<?php
                        $s_choices = trim($o_choices->question_choice_name);
                        $s_form_control_width = (strpos($s_choices, '_') != (strlen($s_choices) - 1)) ? 'form-control-sm w-30' : 'form-control-sm w-100 mt-1';
                        if ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') == (strlen($s_choices) - 1))) {
                            $s_choices = str_replace('_', '', $s_choices);
                            $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                            $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                            $s_choices .= ' _';
                            $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        }
                        else {
                            $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                            $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                            $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                        }
                        // $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        print($s_choices);
?>
                                        </label>
                                    </div>
<?php
                    }else if($o_choices->has_free_text == 1){
                        $s_choices = trim($o_choices->question_choice_name);
                        $s_form_control_width = (strpos($s_choices, '_') != (strlen($s_choices) - 1)) ? 'form-control-sm w-30' : 'form-control-sm w-100 mt-1';
                        $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                        $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                        print($s_choices);
                    }else {
?>
                                    <div class="custom-control custom-radio pb-2">
                                        <input type="radio" class="custom-control-input" id="<?=$o_choices->question_choice_id;?>" name="<?=$o_choices->question_id;?>" value="<?=$o_choices->question_choice_id;?>">
                                        <label class="custom-control-label" for="<?=$o_choices->question_choice_id;?>">
<?php
                        $s_choices = trim($o_choices->question_choice_name);
                        $s_form_control_width = (strpos($s_choices, '_') != (strlen($s_choices) - 1)) ? 'form-control-sm w-30' : 'form-control-sm w-100 mt-1';
                        if ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') == (strlen($s_choices) - 1))) {
                            $s_choices = str_replace('_', '', $s_choices);
                            $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                            $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                            $s_choices .= ' _';
                            $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        }
                        else {
                            $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                            $s_choices .= ((strpos($s_choices, '_')) AND (strpos($s_choices, '_') != (strlen($s_choices) - 1))) ? '<br>' : ' ';
                            $s_choices .= ((!is_null($o_choices->question_choice_name_english)) ? '<i>('.trim($o_choices->question_choice_name_english).')</i>' : '');
                        }
                        // $s_choices = str_replace('_', '<input type="'.$s_input_type.'" name="'.$o_choices->dikti_input_code.'" id="'.$o_choices->dikti_input_code.'" class="'.$s_form_control_width.'" value="'.$input_value.'">', $s_choices);
                        print($s_choices);
?>
                                        </label>
                                    </div>
<?php
                    }
                }
            }
?>
                                </div>
                            </div>
                        </td>
                    </tr>
<?php
                }
            }
        }
    }
?>
                </tbody>
            </table>
        </div>
        </form>
    </div>
    <div class="card-footer">
        <button id="submit_answer" class="btn btn-info" type="button">Submit</button>
    </div>
<?php
}
?>
</div>

<script>
    $('button#submit_answer').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var data = $('form#form_dikti_tracer_study').serialize();
        $.post('<?=base_url()?>alumni/submit_dikti_tracer_study', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                window.location.reload();
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'error');
        });
    });

    $(function() {
        // hide
        $('input#f0328a53-d4e9-477e-b9d9-a1bad90552b6').on('click', function(e) {
            $('tr#question_f9').addClass('d-none');
            $('tr#question_f10').addClass('d-none');

            // sort_number();
        });

        // show
        $('input#c8651b7c-ed1c-4a95-be41-4cdc6a37defb').on('click', function(e) {
            $('tr#question_f9').removeClass('d-none');
            $('tr#question_f10').removeClass('d-none');

            // sort_number();
        });

        // hide
        $('input#462ad486-bf35-4fdd-b3d0-c284575f3b05').on('click', function(e) {
            $('tr#question_f4').addClass('d-none');
            // $('tr#question_f5').addClass('d-none');
            $('tr#question_f6').addClass('d-none');
            $('tr#question_f7').addClass('d-none');
            $('tr#question_f7a').addClass('d-none');
            
            // sort_number();
        });

        // show
        $('input#02557da5-d5e3-468c-836a-c73210aa7b64, input#cea06ac4-3989-476f-866a-702632622a32').on('click', function(e) {
            $('tr#question_f4').removeClass('d-none');
            // $('tr#question_f5').removeClass('d-none');
            $('tr#question_f6').removeClass('d-none');
            $('tr#question_f7').removeClass('d-none');
            $('tr#question_f7a').removeClass('d-none');

            // sort_number();
        });
    });

    // function sort_number() {
    //     let table = $('table#table_questionaire');
    //     var tr = table[0].children[1].children;
    //     $.each(tr, function(i, v) {
    //         var td = v.children[0];
    //         td.innerHTML = i+1;
    //     });
    // } ada parent question.
</script>