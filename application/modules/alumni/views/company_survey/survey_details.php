<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="h5 text-center">Identity of Company/name of Institution</div>
                <table>
                    <tbody>
                        <tr>
                            <td>Company/name of Institution</td>
                            <td class="ml-3">: </td>
                            <td><?=$company_data->institution_name;?></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>: </td>
                            <td><?=$company_data->address_street;?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: </td>
                            <td><?=$company_data->institution_email;?></td>
                        </tr>
                        <tr>
                            <td>Contact Number</td>
                            <td>: </td>
                            <td><?=$company_data->institution_phone_number;?></td>
                        </tr>
                        <tr>
                            <td>Name of Evaluator/assessor</td>
                            <td>: </td>
                            <td><?=$company_data->personal_data_name;?></td>
                        </tr>
                        <tr>
                            <td>Position</td>
                            <td>: </td>
                            <td><?=$company_data->ocupation_name;?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-6">
                <div class="h5 text-center">Employee Identity (IULI Alumni)</div>
                <table>
                    <tbody>
                        <tr>
                            <td>Employee Name</td>
                            <td>:</td>
                            <td><?=$alumni_data->personal_data_name;?></td>
                        </tr>
                        <tr>
                            <td>Batch</td>
                            <td>:</td>
                            <td><?=$alumni_data->academic_year_id;?></td>
                        </tr>
                        <tr>
                            <td>Graduation Year</td>
                            <td>:</td>
                            <td><?=$alumni_data->graduated_year_id;?></td>
                        </tr>
                        <tr>
                            <td>Fac / Study Program</td>
                            <td>:</td>
                            <td><?=$alumni_data->faculty_abbreviation.' / '.$alumni_data->study_program_name;?></td>
                        </tr>
                        <tr>
                            <td>Position</td>
                            <td>:</td>
                            <td><?=$alumni_data->ocupation_name;?></td>
                        </tr>
                        <tr>
                            <td>Month and Year working as employee</td>
                            <td>:</td>
                            <td><?=(!is_null($alumni_data->academic_year_start_date) ? date('F Y', strtotime($alumni_data->academic_year_start_date)) : '');?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="h5 text-center">Assessment</div>
            <table id="question_table" class="table table-sm">
                <thead><tr>
                    <th></th>
                    <th>
                        <div class="row">
                            <div class="col-md-6 pb-2">Aspek Penilaian <i>(Assesment Aspects)</i></div>
                            <div class="col-md-6">Tingkat Penilaian <i>(Rank of Assesment)</i></div>
                        </div>
                    </th>
                </tr></thead>
                <tbody>
        <?php
            if ($question_list) {
                foreach ($question_list as $o_question) {
        ?>
                    <tr>
                        <td><?=$o_question->question_number;?></td>
                        <td>
                            <div class="row">
                                <div class="col-md-6 pb-4">
                                    <span class="<?=($o_question->is_required == 'TRUE') ? 'required_text' : '';?>"><?=trim($o_question->question_name);?></span>
                                    <?= (!is_null($o_question->question_english_name)) ? '<br><i>'.$o_question->question_english_name.'</i>' : ''; ?>
                                </div>
                                <div class="col-md-6">
            <?php
                if ($o_question->question_choices) {
                    foreach ($o_question->question_choices as $o_choices) {
                        $checked = '';
                        if ($o_choices->answer_data !== false) {
                            $checked = 'checked="true"';
                        }

                        $input_value = '';
                        if (strpos(strtolower($o_choices->question_choice_name), 'rp.') !== false) {
                            $input_value = '0';
                        }

                        if ($o_question->is_multiple == 1) {
            ?>
                                    <div class="custom-control custom-checkbox pb-2">
                                        <input type="checkbox" class="custom-control-input" id="<?=$o_choices->question_choice_id;?>" name="<?=$o_choices->question_id;?>" value="<?=$o_choices->question_choice_id;?>" <?=$checked;?> disabled>
                                        <label class="custom-control-label w-100" for="<?=$o_choices->question_choice_id;?>">
            <?php
                            $s_choices = trim($o_choices->question_choice_name);
                            $s_choices.= ' <i>('.trim($o_choices->question_choice_name_english).')</i>';
                            if ($o_choices->answer_data !== null) {
                                $o_answer_data = $o_choices->answer_data;
                                $s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
                            }
                            print($s_choices);
            ?>
                                        </label>
                                    </div>
            <?php
                        }else if($o_choices->has_free_text == 1){
                            $s_choices = trim($o_choices->question_choice_name);
                            $s_choices.= ' <i>('.trim($o_choices->question_choice_name_english).')</i>';
                            if ($o_choices->answer_data !== false) {
                                $o_answer_data = $o_choices->answer_data;
                                $s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
                            }
                            print($s_choices);
                        }else {
            ?>
                                    <div class="custom-control custom-radio pb-2">
                                        <input type="radio" class="custom-control-input" id="<?=$o_choices->question_choice_id;?>" name="<?=$o_choices->question_id;?>" value="<?=$o_choices->question_choice_id;?>" <?=$checked;?> disabled>
                                        <label class="custom-control-label w-100" for="<?=$o_choices->question_choice_id;?>">
            <?php
                            $s_choices = trim($o_choices->question_choice_name);
                            $s_choices.= ' <i>('.trim($o_choices->question_choice_name_english).')</i>';
                            if ($o_choices->answer_data !== false) {
                                $o_answer_data = $o_choices->answer_data;
                                $s_choices = str_replace('_', '<strong><u>'.$o_answer_data->answer_content.'</u></strong>', $s_choices);
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
                }
            }
        ?>
                </tbody>
            </table>
    </div>
</div>