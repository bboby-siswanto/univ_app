<style>
    .ui-datepicker-calendar {
        display: none;
    }
    button.ui-datepicker-current { display: none; }
</style>
<div class="row mb-3 wizard-inner">
    <div class="connecting-line"></div>
    <div class="col-4 text-center">
        <button type="button" class="wizard-survey btn btn-circle btn-lg btn-outline-primary active" target="#form_institution"><i class="fas fa-building"></i></button>
    </div>
    <div class="col-4 text-center">
        <button type="button" class="wizard-survey btn btn-circle btn-lg btn-outline-secondary bg-white" target="#form_alumni" disabled><i class="fas fa-user-graduate"></i></button>
    </div>
    <div class="col-4 text-center">
        <button type="button" class="wizard-survey btn btn-circle btn-lg btn-outline-secondary bg-white" target="#form_assesment" disabled><i class="fas fa-poll-h"></i></button>
    </div>
</div>
<main id="form_institution">
    <form id="institution_form" onsubmit="return false">
        <div class="row">
            <div class="col-12 text-center pb-3"><h4>Identitas Perusahaan/Institusi <i>(Identity of Company/name of Institution)</i></h4><hr></div>
            <div class="col-md-8 offset-md-2">
                <div class="form-group">
                    <label for="company_name" class="required_text">Nama Perusahaan/Institusi <i>(Company/name of Institution)</i></label>
                    <small class="reminder_field text-danger" id="company_name_req">Please fill this field</small>
                    <input type="text" class="form-control" name="company_name" id="company_name">
                </div>
                <div class="form-group">
                    <label for="company_address" class="required_text">Alamat <i>(Address)</i></label>
                    <small class="reminder_field text-danger" id="company_address_req">Please fill this field</small>
                    <input type="text" class="form-control" name="company_address" id="company_address">
                </div>
                <div class="row">
                    <div class="col-sm-7">
                        <div class="form-group">
                            <label for="company_email" class="required_text">Email <i>(Email)</i></label>
                            <small class="reminder_field text-danger" id="company_email_req">Please fill this field</small>
                            <input type="text" class="form-control" name="company_email" id="company_email">
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="company_phone" class="required_text">Nomor Telepon <i>(Contact Number)</i></label>
                            <small class="reminder_field text-danger" id="company_phone_req">Please fill this field</small>
                            <input type="text" class="form-control" name="company_phone" id="company_phone">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="company_user_name" class="required_text">Nama Pejabat Penilai <i>(Name of Evaluator/assessor)</i></label>
                    <small class="reminder_field text-danger" id="company_user_name_req">Please fill this field</small>
                    <input type="text" class="form-control" name="company_user_name" id="company_user_name">
                </div>
                <div class="form-group">
                    <label for="company_occupation" class="required_text">Jabatan <i>(Position)</i></label>
                    <small class="reminder_field text-danger" id="company_occupation_req">Please fill this field</small>
                    <input type="text" class="form-control" name="company_occupation" id="company_occupation">
                </div>
            </div>
            <div class="col-md-8 offset-md-2">
                <button type="button" id="btn_institution_form" class="btn btn-primary btn-block" target="#form_alumni">Next</button>
            </div>
        </div>
    </form>
</main>
<main id="form_alumni">
    <form id="alumni_form" onsubmit="return false">
        <div class="row">
            <div class="col-12 text-center pb-3"><h3>Identitas karyawan yang dinilai <i>(Employee Identity)</i></h3><hr></div>
            <div class="col-md-8 offset-md-2">
                <div class="form-group">
                    <label for="alumni_id" class="required_text">Nama Karyawan (Alumni IULI) <i>(Employee name (IULI Alumni))</i></label>
                    <small class="reminder_field text-danger" id="alumni_id_req">Please fill this field</small>
                    <select name="alumni_id" id="alumni_id" class="form-control">
                        <option value=""></option>
                <?php
                if ($alumni_list) {
                    foreach ($alumni_list as $o_alumni) {
                        $s_personal_data_name = ucwords(strtolower($o_alumni->personal_data_name));
                        $s_personal_data_name = $o_alumni->personal_data_title_prefix.' '.$s_personal_data_name.((!is_null($o_alumni->personal_data_title_suffix)) ? ', ' : ' ').$o_alumni->personal_data_title_suffix;
                ?>
                        <option value="<?=$o_alumni->student_id;?>"><?=$s_personal_data_name;?></option>
                <?php
                    }
                }
                ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="alumni_occupation" class="required_text">Posisi jabatan <i>(Position)</i></label>
                    <small class="reminder_field text-danger" id="alumni_occupation_req">Please fill this field</small>
                    <input type="text" class="form-control" name="alumni_occupation" id="alumni_occupation">
                </div>
                <div class="form-group">
                    <label for="entry_date" class="required_text">Bulan dan Tahun Menjadi Karyawan/Pegawai <i>(Month and Year working as employee)</i></label>
                    <small class="reminder_field text-danger" id="entry_date_req">Please fill this field</small>
                    <input type="text" class="form-control" name="entry_date" id="entry_date">
                    <input type="hidden" name="entry_month_year" id="entry_month_year">
                </div>
            </div>
            <div class="col-md-8 offset-md-2">
                <button type="button" id="btn_alumni_form" class="btn btn-primary btn-block" target="#form_assesment">Next</button>
            </div>
        </div>
    </form>
</main>
<main id="form_assesment">
    <form id="assesment_form">
        <div class="row">
            <div class="col-12 text-center pb-3"><h3>Silakan isikan penilaian <i>(Kindly fill the assessment)</i></h3><hr></div>
            <div class="col-12">
                <div>
                    <table id="question_table" class="table">
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
                                                <input type="checkbox" class="custom-control-input" id="<?=$o_choices->question_choice_id;?>" name="<?=$o_choices->question_id;?>" value="<?=$o_choices->question_choice_id;?>" <?=$checked;?>>
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
                                                <input type="radio" class="custom-control-input" id="<?=$o_choices->question_choice_id;?>" name="<?=$o_choices->question_id;?>" value="<?=$o_choices->question_choice_id;?>" <?=$checked;?>>
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
            <div class="col-12">
                <button type="button" id="btn_submit_result" class="btn btn-primary btn-block">Next</button>
            </div>
        </div>
    </form>
</main>
<!-- .wizard-inner hide -->
<div id="finish_survey">
    <hr class="mt-5">
    <div class="row text-center h5 mt-5 mb-5">
        <div class="col-md-8 offset-md-2 mb-3">
            Terimakasih sudah berpartisipasi dalam Penilaian Kepuasan Pengguna Lulusan terhadap <span class="alumni_name"></span>.
        </div>
        <div class="col-md-8 offset-md-2">
            <i>Thank you for participating in the Alumni User Satisfaction Assessment of  <span class="alumni_name"></span>.</i>
        </div>
    </div>
    <hr>
</div>
<script>
    $(function() {
        let delay = 400;
        var dateObject;
        // $('main#form_institution').hide();
        $('main#form_alumni').hide();
        $('main#form_assesment').hide();
        $('div#finish_survey').hide();
        $('small.reminder_field').hide();

        $( "input#entry_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'MM yy',
            onClose: function(dateText, inst) { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                dateObject = (inst.selectedYear) + '-' + (inst.selectedMonth + 1) + '-1';
                $('input#entry_month_year').val(dateObject);
            }
        });

        $('select#alumni_id').select2({
            allowClear: true,
            placeholder: "Please select..",
            theme: "bootstrap",
            minimumInputLength: 3
        });

        $('button.wizard-survey').on('click', function(e) {
            e.preventDefault();

            $.each($('main'), function(i, v) {
                $('main#' + v.id).hide();
                var wizard_val = $('button.wizard-survey')[i].attributes['target'].value;
                $('button.wizard-survey[target="' + wizard_val + '"]').removeClass('active').addClass('bg-white');
            });

            $(this).removeClass('bg-white').addClass('active');
            var target = $(this).attr('target');

            $('main' + target).show(delay);
        });

        $('button#btn_institution_form').on('click', function(e) {
            e.preventDefault();

            let form = $('form#institution_form');
            var form_input = form.find('input, select, textarea');
            var ok = true;
            var target = $(this).attr('target');
            
            $.each(form_input, function(i, v) {
                if (v.value == '') {
                    v.focus();
                    $('small#' + v.id + '_req').show();
                    setInterval(function() {
                        $('small#' + v.id + '_req').hide(100);
                    }, 5000);
                    
                    ok = false;
                }
            });

            if (ok) {
                var data = form.serialize();
                data += '&form=form_institution';
                $.post('<?=base_url()?>alumni/validate_input', data, function(result) {
                    if (result.code == 0) {
                        $.each($('main'), function(i, v) {
                            $('main#' + v.id).hide();
                        });

                        $('main' + target).show(delay);
                        $('button.wizard-survey[target="#form_institution"]').removeAttr('disabled').removeClass('active').addClass('bg-white');
                        $('button.wizard-survey[target="' + target + '"]').removeAttr('disabled').removeClass('btn-outline-secondary bg-white').addClass('btn-outline-primary active');

                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    toastr.error('cannt validation input!', 'Error!');
                });
            }
            
        });

        $('button#btn_alumni_form').on('click', function(e) {
            e.preventDefault();

            let form = $('form#alumni_form');
            var form_input = form.find('input, select, textarea');
            var ok = true;
            var target = $(this).attr('target');

            $.each(form_input, function(i, v) {
                if (v.value == '') {
                    v.focus();
                    $('small#' + v.id + '_req').show();
                    setInterval(function() {
                        $('small#' + v.id + '_req').hide(100);
                    }, 5000);
                    
                    ok = false;
                }
            });

            if (ok) {
                var data = form.serialize();
                data += '&form=form_alumni';
                $.post('<?=base_url()?>alumni/validate_input', data, function(result) {
                    if (result.code == 0) {
                        $.each($('main'), function(i, v) {
                            $('main#' + v.id).hide();
                        });

                        $('main' + target).show(delay);
                        $('button.wizard-survey[target="#form_institution"]').removeAttr('disabled').removeClass('active').addClass('bg-white');
                        $('button.wizard-survey[target="#form_alumni"]').removeAttr('disabled').removeClass('active').addClass('bg-white');
                        $('button.wizard-survey[target="' + target + '"]').removeAttr('disabled').removeClass('btn-outline-secondary bg-white').addClass('btn-outline-primary active');
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    toastr.error('cannt validation input!', 'Error!');
                });
            }
        });

        $('button#btn_submit_result').on('click', function(e) {
            e.preventDefault();
            $.blockUI();
            var arr_data = [];

            $.each($('form'), function(i, v) {
                var form_data = $('form#' + v.id).serializeArray();
                $.each(form_data, function(ind, val) {
                    arr_data.push(val);
                })
            });

            var arr_data = objectify_form(arr_data);
            $.post('<?=base_url()?>alumni/company_survey', arr_data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                    data = $('select#alumni_id').select2('data');
                    $('.alumni_name').html(data[0].text);
                    $('.wizard-inner').hide();
                    $.each($('main'), function(i, v) {
                        $('main#' + v.id).hide();
                        var wizard_val = $('button.wizard-survey')[i].attributes['target'].value;
                        $('button.wizard-survey[target="' + wizard_val + '"]').removeClass('active').addClass('bg-white');
                    });

                    $('div#finish_survey').show(500);
                }else{
                    toastr.warning(result.message, 'Warning');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Cannt submit the assessment, please try again later!');
            });
            // console.log(arr_data);
        });
    });
</script>