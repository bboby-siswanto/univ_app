<?= modules::run('student/show_name');?>

<div class="card">
    <div class="card-body">
        <form action="<?=base_url()?>validation_requirement/vaccine/submit_vaccine" onsubmit="return false" id="form_vaccine">
            <div class="row">
        <?php
        $disabled = ($this->session->userdata('type') == 'staff') ? 'disabled="disabled"' : '';
        if ((isset($from_modal)) AND ($from_modal)) {
        ?>
                <div class="col-sm-12 pb-2">
                    <span><u>Please fill the form before using portal.</u></span>
                </div>
        <?php
        }
        ?>
                <div class="col-md-6">
                    <fieldset class="fieldset-border">
                        <input type="hidden" name="vaccine_number[0]" id="vaccine_number[0]" value="1">
                        <legend class="fieldset-border">
                            <h5>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="first_vaccine_check" name="first_vaccine_check" checked <?=$disabled;?>>
                                    <label class="custom-control-label" for="first_vaccine_check">First Vaccine</label>
                                </div>
                            </h5>
                        </legend>
                        <div class="first-form-field">
                            <div class="form-group">
                                <label class="required_text" for="vaccine_type">Vaccine Type:</label>
                                <select name="vaccine_type[0]" id="vaccine_type[0]" class="form-control" <?=$disabled;?>>
                        <?php
                            if (($vaccine_type !== null) AND (is_array($vaccine_type))) {
                                $s_vaccine_type_data = (($first_vaccine !== null) AND ($first_vaccine)) ? $first_vaccine[0]->vaccine_type : '';
                                foreach ($vaccine_type as $s_vaccine_type) {
                                    $selected = ($s_vaccine_type_data == $s_vaccine_type) ? 'selected="selected"' : '';
                        ?>
                                    <option value="<?=$s_vaccine_type;?>" <?=$selected;?>><?=strtoupper($s_vaccine_type);?></option>
                        <?php
                                }
                            }
                        ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="required_text" for="vaccine_date">Vaccine Date: </label>
                                <input type="date" name="vaccine_date[0]" id="vaccine_date[0]" class="form-control" value="<?=(($first_vaccine !== null) AND ($first_vaccine)) ? $first_vaccine[0]->vaccine_date : '';?>" <?=$disabled;?>>
                            </div>
                            <div class="form-group">
                                <label class="required_text" for="first_vaccine_link">Certificate:</label>
                        <?php
                            // print('<pre>');var_dump($first_cerificate_vaccine);
                            if(($first_vaccine) AND ($first_cerificate_vaccine)){
                        ?>
                                <img src="<?=site_url('file_manager/view/'.$first_cerificate_vaccine[0]->document_id.'/'.$first_vaccine[0]->personal_data_id)?>" class="img-fluid img-thumbnail">
                        <?php
                            }  
                        ?>
                                <input type="file" name="first_vaccine_link" id="first_vaccine_link" class="form-control" <?=$disabled;?>>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset class="fieldset-border">
                        <input type="hidden" name="vaccine_number[1]" id="vaccine_number[1]" value="2">
                        <legend class="fieldset-border">
                            <h5>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="second_vaccine_check" name="second_vaccine_check" <?= ($second_vaccine !== null) ? 'checked' : '';?> <?=$disabled;?>>
                                    <label class="custom-control-label" for="second_vaccine_check">Second Vaccine</label>
                                </div>
                            </h5>
                        </legend>
                        <div class="second-form-field">
                            <div class="form-group">
                                <label class="required_text" for="vaccine_type">Vaccine Type:</label>
                                <select name="vaccine_type[1]" id="vaccine_type[1]" class="form-control" <?=$disabled;?>>
                        <?php
                            if (($vaccine_type !== null) AND (is_array($vaccine_type))) {
                                $s_vaccine_type_data = (($second_vaccine !== null) AND ($second_vaccine)) ? $second_vaccine[0]->vaccine_type : '';
                                foreach ($vaccine_type as $s_vaccine_type) {
                                    $selected = ($s_vaccine_type_data == $s_vaccine_type) ? 'selected="selected"' : '';
                        ?>
                                    <option value="<?=$s_vaccine_type;?>" <?=$selected;?>><?=strtoupper($s_vaccine_type);?></option>
                        <?php
                                }
                            }
                        ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="required_text" for="vaccine_date">Vaccine Date: </label>
                                <input type="date" name="vaccine_date[1]" id="vaccine_date[1]" class="form-control" value="<?=(($second_vaccine !== null) AND ($second_vaccine)) ? $second_vaccine[0]->vaccine_date : '';?>" <?=$disabled;?>>
                            </div>
                            <div class="form-group">
                                <label class="required_text" for="second_vaccine_link">Certificate:</label>
                        <?php
                            if(($second_vaccine) AND ($second_cerificate_vaccine)){
                        ?>
                                <img src="<?=site_url('file_manager/view/'.$second_cerificate_vaccine[0]->document_id.'/'.$second_vaccine[0]->personal_data_id)?>" class="img-fluid img-thumbnail">
                        <?php
                            }  
                        ?>
                                <input type="file" name="second_vaccine_link" id="second_vaccine_link" class="form-control" <?=$disabled;?>>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset class="fieldset-border">
                        <input type="hidden" name="vaccine_number[2]" id="vaccine_number[2]" value="3">
                        <legend class="fieldset-border">
                            <h5>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="third_vaccine_check" name="third_vaccine_check" <?= (($third_vaccine !== null) AND ($third_vaccine)) ? 'checked' : '';?> <?=$disabled;?>>
                                    <label class="custom-control-label" for="third_vaccine_check">Third Vaccine (Booster)</label>
                                </div>
                            </h5>
                        </legend>
                        <div class="third-form-field">
                            <div class="form-group">
                                <label class="required_text" for="vaccine_type">Vaccine Type:</label>
                                <select name="vaccine_type[2]" id="vaccine_type[2]" class="form-control" <?=$disabled;?>>
                        <?php
                            if (($vaccine_type !== null) AND (is_array($vaccine_type))) {
                                $s_vaccine_type_data = (($third_vaccine !== null) AND ($third_vaccine)) ? $third_vaccine[0]->vaccine_type : '';
                                foreach ($vaccine_type as $s_vaccine_type) {
                                    $selected = ($s_vaccine_type_data == $s_vaccine_type) ? 'selected="selected"' : '';
                        ?>
                                    <option value="<?=$s_vaccine_type;?>" <?=$selected;?>><?=strtoupper($s_vaccine_type);?></option>
                        <?php
                                }
                            }
                        ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="required_text" for="vaccine_date">Vaccine Date: </label>
                                <input type="date" name="vaccine_date[2]" id="vaccine_date[2]" class="form-control" value="<?=(($third_vaccine !== null) AND ($third_vaccine)) ? $third_vaccine[0]->vaccine_date : '';?>" <?=$disabled;?>>
                            </div>
                            <div class="form-group">
                                <label class="required_text" for="third_vaccine_link">Certificate:</label>
                        <?php
                            if(($third_vaccine) AND ($third_cerificate_vaccine)){
                        ?>
                                <img src="<?=site_url('file_manager/view/'.$third_cerificate_vaccine[0]->document_id.'/'.$third_vaccine[0]->personal_data_id)?>" class="img-fluid img-thumbnail">
                        <?php
                            }  
                        ?>
                                <input type="file" name="third_vaccine_link" id="third_vaccine_link" class="form-control" <?=$disabled;?>>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-sm-12">
                    <small class="text-danger">* file type allowed (jpg|jpeg|png|pdf)</small>
                </div>
            </div>
        </form>
        <hr>
        <div class="row">
            <div class="col-12">
        <?php
        if ($this->session->userdata('type') != 'staff') {
        ?>
                <button type="button" class="btn btn-primary float-right" id="btn_submit_vaccine">Submit</button>
        <?php
        }
        ?>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('button#btn_submit_vaccine').on('click', function(e) {
        e.preventDefault();
        let form = $('form#form_vaccine');
        var data_send = new FormData(form[0]);
        $.blockUI({ baseZ: 2000 });
				
        $.ajax({
            url: form.attr('action'),
            data: data_send,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            success: function(rtn){
                $.unblockUI();
                if (rtn.code == 0) {
                    Swal.fire(
                        '',
                        'Thank you for participating our survey.!',
                        'success'
                    );
                    
                    if ('<?= ((isset($from_modal)) AND ($from_modal)) ? "true" : "false" ?>' == "true") {
                        $('div#modal-input-vaccine').modal('hide');
                    }
                    else {
                        window.location.reload();
                    }
                }
                else {
                    toastr.warning(rtn.message);
                }
            },
            fail: function(xhr, textStatus, errorThrown){
                $.unblockUI();
                toastr.error('Error processing your data!');
            }
        });
    });

    $('input#first_vaccine_check').on('click', function(e) {
        if( $(this).is(':checked') ) {
            $('div.first-form-field').show(500);
        }
        else{
            $('div.first-form-field').hide(500);
        }
    });

    $('input#second_vaccine_check').on('click', function(e) {
        if( $(this).is(':checked') ) {
            $('div.second-form-field').show(500);
        }
        else{
            $('div.second-form-field').hide(500);
        }
    });
    
    $('input#third_vaccine_check').on('click', function(e) {
        if( $(this).is(':checked') ) {
            $('div.third-form-field').show(500);
        }
        else{
            $('div.third-form-field').hide(500);
        }
    });
});
</script>