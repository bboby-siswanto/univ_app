<?php
// print('<pre>');var_dump($student_data);exit;
$b_has_sync_forlap = ((isset($forlap_sync)) AND ($forlap_sync)) ? true : false;
?>
<div class="row">
    <div class="col-md-4" style="max-height: 400px;">
        <!-- <div class="form-group"> -->
            <label for="pr_pd_personal_data_pict">Profil Picture</label><br>
            <img src="<?=base_url().$profile_src?>" class="img-fluid picture-personal" style="height: 80%;">
            <input type="file" class="" name="pr_pd_personal_data_pict" id="pr_pd_personal_data_pict">
        <!-- </div> -->
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="pr_pd_personal_data_name" class="required_text">Fullname</label>
                    <input type="text" class="form-control" id="pr_pd_personal_data_name" name="pr_pd_personal_data_name" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->personal_data_name : '';?>" disabled>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="pr_pd_personal_data_email" class="required_text">Personal Email</label>
                    <input type="text" class="form-control" id="pr_pd_personal_data_email" name="pr_pd_personal_data_email" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->personal_data_email : '';?>" disabled>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="pr_pd_personal_data_mother_name" class="required_text">Mother Maiden Name</label>
                    <input type="text" class="form-control" id="pr_pd_personal_data_mother_name" name="pr_pd_personal_data_mother_name" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->personal_data_mother_maiden_name : '';?>" <?= ($b_has_sync_forlap) ? 'disabled' : '' ?>>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="pr_pd_personal_data_phone">Phone Number</label>
                    <input type="text" class="form-control" id="pr_pd_personal_data_phone" name="pr_pd_personal_data_phone" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->personal_data_phone : '';?>">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="pr_pd_personal_data_cellular" class="required_text">Cellular Number</label>
                    <input type="text" class="form-control" id="pr_pd_personal_data_cellular" name="pr_pd_personal_data_cellular" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->personal_data_cellular : '';?>">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="pr_pd_personal_data_citizenship" class="required_text">Citizenship</label>
                    <select  class="form-control" name="pr_pd_personal_data_citizenship" id="pr_pd_personal_data_citizenship">
                        <option value=""></option>
                <?php
                if ($country_list) {
                    $citizenship_id = ((isset($student_data)) AND ($student_data)) ? $student_data->citizenship_id : '';
                    foreach ($country_list as $o_country) {
                        $selected = ($citizenship_id == $o_country->country_id) ? 'selected="selected"' : '';
                ?>
                        <option value="<?=$o_country->country_id;?>" <?=$selected;?>><?=$o_country->country_name;?></option>
                <?php
                    }
                }
                ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="form-group">
                    <label for="pr_pd_personal_data_identification_number" class="required_text">Identification Number</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="identification_type">NIK</span>
                        </div>
                        <input type="text" class="form-control" name="pr_pd_personal_data_identification_number" id="pr_pd_personal_data_identification_number" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->personal_data_id_card_number : '';?>">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="pr_pd_personal_data_country_birth" class="required_text">Country of Birth</label>
                    <select  class="form-control" name="pr_pd_personal_data_country_birth" id="pr_pd_personal_data_country_birth">
                        <option value=""></option>
                <?php
                if ($country_list) {
                    $country_birth_id = ((isset($student_data)) AND ($student_data)) ? $student_data->country_of_birth : '';
                    foreach ($country_list as $o_country) {
                        $selected = ($country_birth_id == $o_country->country_id) ? 'selected="selected"' : '';
                ?>
                        <option value="<?=$o_country->country_id;?>" <?=$selected;?>><?=$o_country->country_name;?></option>
                <?php
                    }
                }
                ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="form-group">
                    <label for="pr_pd_personal_data_city_birth" class="required_text">City of Birth</label>
                    <input type="text" class="form-control" name="pr_pd_personal_data_city_birth" id="pr_pd_personal_data_city_birth" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->personal_data_place_of_birth : '';?>">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="pr_pd_personal_data_gender" class="required_text">Gender</label>
                    <select class="form-control" name="pr_pd_personal_data_gender" id="pr_pd_personal_data_gender">
                        <option value=""></option>
                        <option value="M" <?= ((isset($student_data)) AND ($student_data->personal_data_gender == 'M')) ? 'selected="selected"' : '';?>>Male</option>
                        <option value="F"<?= ((isset($student_data)) AND ($student_data->personal_data_gender == 'F')) ? 'selected="selected"' : '';?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="form-group">
                    <label for="pr_pd_personal_data_date_birth" class="required_text">Date of Birth</label>
                    <div class="row">
                        <div class="col">
                            <select class="form-control" name="pr_pd_personal_data_date_birth" id="pr_pd_personal_data_date_birth" disabled>
                                <option value=""></option>
                            <?php
                            $d_dob = ((isset($student_data)) AND ($student_data) AND (!is_null($student_data->personal_data_date_of_birth))) ? date('d', strtotime($student_data->personal_data_date_of_birth)) : '';
                            $m_dob = ((isset($student_data)) AND ($student_data) AND (!is_null($student_data->personal_data_date_of_birth))) ? date('m', strtotime($student_data->personal_data_date_of_birth)) : '';
                            $y_dob = ((isset($student_data)) AND ($student_data) AND (!is_null($student_data->personal_data_date_of_birth))) ? date('Y', strtotime($student_data->personal_data_date_of_birth)) : '';
                            for($di = 1; $di <= 31; $di++){
                                $selected = ($d_dob == $di) ? 'selected="selected"' : '';
                            ?>
                                <option value="<?=$di?>" <?=$selected;?>><?=$di?></option>
                            <?php
                            }
                            ?>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control" name="pr_pd_personal_data_month_birth" id="pr_pd_personal_data_month_birth" disabled>
                                <option value=""></option>
                            <?php
                            for ($mt=1; $mt <= 12 ; $mt++) { 
                                $s_month = date('F', strtotime('2020-'.$mt.'-1'));
                                $selected = ($m_dob == $mt) ? 'selected="selected"' : '';
                            ?>
                                <option value="<?=$mt;?>" <?=$selected;?>><?=$s_month;?></option>
                            <?php
                            }
                            ?>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control" name="pr_pd_personal_data_year_birth" id="pr_pd_personal_data_year_birth" disabled>
                                <option value=""></option>
                            <?php
                            $yearstart = intval(date('Y')) - 28;
                            for ($yr=$yearstart; $yr < intval(date('Y')) ; $yr++) { 
                                $selected = ($y_dob == $yr) ? 'selected="selected"' : '';
                            ?>
                                <option value="<?=$yr;?>" <?=$selected;?>><?=$yr;?></option>
                            <?php
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="pr_pd_personal_data_religion" class="required_text">Religion</label>
                    <select class="form-control" name="pr_pd_personal_data_religion" id="pr_pd_personal_data_religion">
                        <option value=""></option>
                <?php
                if ($religion_list) {
                    $religion_id = ((isset($student_data)) AND ($student_data)) ? $student_data->religion_id : '';
                    foreach ($religion_list as $o_religion) {
                        $selected = ($religion_id == $o_religion->religion_id) ? 'selected="selected"' : '';
                ?>
                        <option value="<?=$o_religion->religion_id;?>" <?=$selected;?>><?=$o_religion->religion_name;?></option>
                <?php
                    }
                }
                ?>
                    </select>
                </div>
            </div>
            
        </div>
    </div>
</div>
<script>
let country_nik = '9bb722f5-8b22-11e9-973e-52540001273f';
$(function() {
    $('#pr_pd_personal_data_citizenship').on('change', function(e) {
        e.preventDefault();
        
        if ($('#pr_pd_personal_data_citizenship').val() !== country_nik) {
            $('#identification_type').html('Passport');
        }
        else {
            $('#identification_type').html('NIK');
        }
    });
})
</script>