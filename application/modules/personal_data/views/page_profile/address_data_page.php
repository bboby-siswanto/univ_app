<!-- 
    district
    sub district
    rt
    rw
    zip cod
 -->
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="pr_ad_address_street">Street</label>
            <input type="text" class="form-control" name="pr_ad_address_street" id="pr_ad_address_street" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->address_street : '';?>" >
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="pr_ad_country_id" class="required_text">Country</label>
            <select  class="form-control" name="pr_ad_country_id" id="pr_ad_country_id">
                <option value=""></option>
        <?php
        if ($country_list) {
            $address_country_id = ((isset($student_data)) AND ($student_data)) ? $student_data->address_country_id : '';
            foreach ($country_list as $o_country) {
                $selected = ($address_country_id == $o_country->country_id) ? 'selected="selected"' : '';
        ?>
                <option value="<?=$o_country->country_id;?>" <?=$selected;?>><?=$o_country->country_name;?></option>
        <?php
            }
        }
        ?>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <label for="pr_ad_address_province">Province</label>
        <input type="text" class="form-control" name="pr_ad_address_province" id="pr_ad_address_province" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->address_province : '';?>">
    </div>
    <div class="col-md-4">
        <label for="pr_ad_address_city" class="required_text">City</label>
        <input type="text" class="form-control" name="pr_ad_address_city" id="pr_ad_address_city" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->address_city : '';?>">
    </div>
    <div class="col-md-4">
        <label for="pr_ad_address_district" class="required_text">District</label>
        <select class="form-control" name="pr_ad_address_district" id="pr_ad_address_district">
            <option value=""></option>
    <?php
    if ($district_list) {
        $dikti_wilayah_id = ((isset($student_data)) AND ($student_data)) ? $student_data->dikti_wilayah_id : '';
        foreach ($district_list as $o_district) {
            $selected = ($dikti_wilayah_id == $o_district->id_wilayah) ? 'selected="selected"' : '';
    ?>
            <option value="<?=$o_district->id_wilayah;?>" <?=$selected;?>><?=$o_district->nama_wilayah;?></option>
    <?php
        }
    }
    ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="pr_ad_address_sub_district" class="required_text">Sub District</label>
        <input type="text" class="form-control" name="pr_ad_address_sub_district" id="pr_ad_address_sub_district" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->address_sub_district : '';?>">
    </div>
    <div class="col-md-1">
        <label for="pr_ad_address_rt">RT</label>
        <input type="text" class="form-control" name="pr_ad_address_rt" id="pr_ad_address_rt" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->address_rt : '';?>">
    </div>
    <div class="col-md-1">
        <label for="pr_ad_address_rw">RW</label>
        <input type="text" class="form-control" name="pr_ad_address_rw" id="pr_ad_address_rw" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->address_rw : '';?>">
    </div>
    <div class="col-md-3">
        <label for="pr_ad_address_zip_code">Zip Code</label>
        <input type="text" class="form-control" name="pr_ad_address_zip_code" id="pr_ad_address_zip_code" value="<?=((isset($student_data)) AND ($student_data)) ? $student_data->address_zipcode : '';?>">
    </div>
</div>