<?php
// print('<pre>');var_dump($event_field);exit;
?>
<div class="container">
    <div class="card">
        <div class="card-header bg-iuli text-white">
            <div class="row">
                <div class="col-lg-3 my-auto text-center">
                    <img src="<?= base_url()?>assets/img/iuli.png" class="img-fluid"/>
                </div>
            </div>
        </div>
        <div class="card-body pb-5">
            <?= (isset($event_data)) ? '<h3 class="text-center pt-2 pb-2">'.$event_data->event_name.'</h3>' : '' ;?>
            <h5 class="text-center">Form Registration</h5>
            <hr class="pb-3">
            <form url="<?=base_url()?>event/event_check_in" id="form_event_registration" onsubmit="return false">
                <input type="hidden" name="booking_id">
                <input type="hidden" name="event_id" id="event_id" value="<?=$event_data->event_id;?>">
                <!-- <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="required_text">Full Name</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="email" class="required_text">Email <small class="text-danger">(Your active email)</small></label>
                            <input type="email" class="form-control" name="email" id="email">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="phone" class="required_text">Mobile Phone Number</label>
                            <input type="text" class="form-control" name="phone" id="phone">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="reference" class="required_text"><?= ((isset($event_data)) AND ($event_data->event_type == 'general')) ? 'Origin' : 'High School Name' ?></label>
                            <input type="text" class="form-control" name="reference" id="reference">
                        </div>
                    </div>
                    <div class="col-sm-6 <?= ((isset($event_data)) AND ($event_data->event_type == 'general')) ? 'd-none' : '' ?>">
                        <div class="form-group">
                            <label for="event_personal_grade" class="required_text">Grade / Class</label>
                            <select class="form-control" name="event_personal_grade" id="event_personal_grade">
                                <option value=""></option>
                                <option value="x">X</option>
                                <option value="xi">XI</option>
                                <option value="xii">XII</option>
                                <option value="graduated">Graduated</option>
                            </select>
                        </div>
                    </div>
                    <div id="form_graduated" class="col-sm-6 d-none">
                        <div class="form-group">
                            <label for="event_personal_graduated_year" class="required_text">Graduated Year</label>
                            <input type="text" class="form-control" name="event_personal_graduated_year" id="event_personal_graduated_year">
                        </div>
                    </div>
                </div>= -->
                <div class="row">
        <?php
            if ((isset($event_field)) AND ($event_field)) {
                foreach ($event_field as $o_field) {
                    if ($o_field->field_type == 'option') {
                        $a_option = explode(';', $o_field->field_option);
        ?>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="booking_<?=$o_field->field_name;?>" class="required_text"><?=$o_field->field_title;?></label>
                                <select class="form-control for_select2" name="booking_<?=$o_field->field_name;?>" id="booking_<?=$o_field->field_name;?>">
                                    <option value=""></option>
            <?php
                        foreach ($a_option as $s_option) {
            ?>
                                    <option value="<?=$s_option;?>"><?=$s_option;?></option>
            <?php
                        }
            ?>
                                </select>
                            </div>
                        </div>
        <?php
                    }
                    else {
            ?>
                        <div class="col-sm-6 <?=($o_field->field_name == 'graduate_year') ? 'form_graduate_year d-none' : '';?>">
                            <div class="form-group">
                                <label for="booking_<?=$o_field->field_name;?>" class="required_text"><?=$o_field->field_title;?></label>
                            <?php
                            if ($o_field->field_input_type == 'textarea') {
                            ?>
                                <textarea name="booking_<?=$o_field->field_name;?>" id="booking_<?=$o_field->field_name;?>" class="form-control"></textarea>
                            <?php
                            }
                            else {
                            ?>
                                <input type="<?=$o_field->field_input_type;?>" class="form-control" name="booking_<?=$o_field->field_name;?>" id="booking_<?=$o_field->field_name;?>">
                            <?php
                            }
                            ?>
                            </div>
                        </div>
            <?php
                    }
                }
            }
        ?>
                </div>
                <div class="row">
                    <div class="col-12 pt-3">
                        <button type="button" class="btn btn-primary float-right" name="event_registration_submit" id="event_registration_submit">Submit</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- <div class="card-footer bg-white"></div> -->
    </div>
</div>
<script>
    $(function() {
        $('select.for_select2').select2({
            placeholder: 'Select an option',
            theme: "bootstrap"
        });

        $('select#booking_grade').on('change', function(e) {
            if ($('select#booking_grade').val().toLowerCase() == 'graduated') {
                $('.form_graduate_year').removeClass('d-none');
            }
            else {
                $('.form_graduate_year').addClass('d-none');
            }
        });

        $('button#event_registration_submit').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            var form = $('form#form_event_registration');
            var data = form.serialize();
            var url = form.attr('url');

            $.post(url, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thank You!',
                        html: 'Thank you for participating in the <?= (isset($event_data)) ? $event_data->event_name : '' ;?> event,<br>' +
                            'we will contact you as soon as possible via the registered email contact or telephone number<br>' +
                            'make sure your cellphone is always active',
                        // text: "Please check your email",
                        showCloseButton: true,
                    }).then((result) => {
                        // window.location.href = result.uri; 
                    });

                    setTimeout(function(){
                        location.href = result.uri; 
                    }, 5000);
                }
                else {
                    toastr.warning(result.message);
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
        });
    });
</script>