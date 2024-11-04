<div class="card">
    <div class="card-body row">
        <div class="col-md-4">
    <?php
        if($a_avatar){
    ?>
            <img src="<?=site_url('file_manager/view/'.$a_avatar[0]->document_id.'/'.$personal_data_id)?>" class="img-fluid img-thumbnail picture-personal">
    <?php
        }else{
    ?>
        
    <?php
        }
    ?>
            <!-- <div class="pt-2"><input type="file" name="file_image" id="file_image"></div> -->
        </div>
        <div class="col-md-8 profile_alumni">
            <h3><?=$o_personal_data->personal_data_name;?></h3>
            <h5><?=($o_student_data) ? $o_student_data->study_program_name : '-';?> - <?=($o_student_data) ? $o_student_data->faculty_name : '-';?></h5>
            <h5><?= ($o_student_data) ? $o_student_data->student_number : '-'?></h5>
            <form onsubmit="return false" id="form_alumni_profile">
                <input type="hidden" id="alumni_id" name="alumni_id" value="<?= ($o_alumni_data) ? $o_alumni_data->alumni_id : '' ?>">
                <input type="hidden" name="student_id" id="student_id" value="<?= ($o_student_data) ? $o_student_data->student_id : '';?>">
                <input type="hidden" id="alumni_fullname" name="alumni_fullname" value="<?= $a_show_input['alumni_fullname']?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text span-w-42" id="basic-addon1"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control" name="alumni_nickname" id="alumni_nickname" placeholder="Nick Name" value="<?= $a_show_input['alumni_nickname']?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text span-w-42" id="basic-addon1"><i class="fas fa-at"></i></span>
                            </div>
                            <input type="email" class="form-control" name="alumni_personal_email" id="alumni_personal_email" placeholder="Your Personal Email" value="<?= $a_show_input['alumni_personal_email']?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text span-w-42" id="basic-addon1"><i class="fas fa-map-marker-alt"></i></span>
                            </div>
                            <input type="text" class="form-control" name="alumni_place_of_birth" id="alumni_place_of_birth" placeholder="Place of Birth" value="<?= $a_show_input['alumni_place_of_birth']?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text span-w-42" id="basic-addon1"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" class="form-control" name="alumni_date_of_birth" id="alumni_date_of_birth" placeholder="Date of Birth" value="<?= $a_show_input['alumni_date_of_birth']?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text span-w-42" id="basic-addon1"><i class="fas fa-venus-mars"></i></span>
                            </div>
                            <!-- <input type="text" class="form-control" name="alumni_gender" id="alumni_gender" placeholder="Gender"> -->
                            <select name="alumni_gender" id="alumni_gender" class="form-control">
                                <option value="" selected disabled>Gender</option>
                            <?php
                            if ($o_option_gender) {
                                foreach ($o_option_gender as $gender) {
                            ?>
                                <option value="<?=$gender;?>"  <?= ($a_show_input['alumni_gender'] == $gender) ? 'selected' : '' ?>><?= ($gender == 'M') ? 'MALE' : 'FEMALE'?></option>
                            <?php
                                }
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text span-w-42" id="basic-addon1"><i class="fas fa-mobile-alt"></i></span>
                            </div>
                            <input type="text" class="form-control" name="alumni_cellular" id="alumni_cellular" placeholder="Cellular Number" value="<?= $a_show_input['alumni_personal_cellular']?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text span-w-42" id="basic-addon1"><i class="fas fa-heart"></i></span>
                            </div>
                            <!-- <input type="text" class="form-control" name="alumni_marital_status" id="alumni_marital_status" placeholder="Marital Status"> -->
                            <select name="marital_status" id="marital_status" class="form-control">
                                <option value="" selected disabled>Marital Status</option>
                            <?php
                            if ($o_option_marital_status) {
                                foreach ($o_option_marital_status as $status) {
                            ?>
                                <option value="<?=$status;?>" <?= ($a_show_input['alumni_marital_status'] == $status) ? 'selected' : '' ?>><?= strtoupper($status)?></option>
                            <?php
                                }
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <button id="save_alumni_profile" type="button" class="btn btn-info float-right">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(function() {
    var now_date = new Date();
    var max_date = new Date(now_date.getFullYear(), now_date.getMonth(), now_date.getDate());

    var date_of_birth = $('#alumni_date_of_birth').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        maxDate: max_date
    });

    $('button#save_alumni_profile').on('click', function(e) {
        e.preventDefault();
        var data = $('form#form_alumni_profile').serializeArray();
        data = objectify_form(data);
        
        var url = '<?=base_url()?>personal_data/save_alumni_profile';
        var formData = new FormData();
        formData.append('file', $('input#file_image')[0].files[0]);
        formData.append('personal_data_id', '<?=$personal_data_id?>');
        
        $.each(data, function(i, v) {
            formData.append(i, v);
        });
        
        $.blockUI();
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(rtn, status, jqXHR){
                $.unblockUI();
                if (rtn.code == 0) {
                    window.location.href = '<?= base_url()?>dashboard';
                }else{
                    toastr.warning(rtn.message, 'Warning!');
                }
            },
            error : function(xhr, ajaxOptions, thrownError) {
                $.unblockUI();
                console.log(xhr.responseText);
            }
        });
    });
});
</script>