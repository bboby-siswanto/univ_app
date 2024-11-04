<?php
// if ($this->session->userdata('user') == '2c088deb-9143-4153-bdd7-7f6661fa8696') {
    if ((isset($o_student_data)) AND ($o_student_data->academic_year_id <= 2020)) {
?>
    <!-- <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>
            <h4 class="alert-heading">OPEN REGISTRATION FOR STUDY ABROAD (Germany/Taiwan)!</h4>
            <p>Please submit your application to International Office.</p>
            <p class="mb-0"><a href="mailto:employee@company.ac.id">employee@company.ac.id</a>.</p>
            <h5>deadline 28 February 2023</h5>
        </strong>
    </div> -->
<?php
    }
?>
<div class="row">
    <div class="col-md-<?=(!isset($o_student_data->from_staff)) ? '6' : '12'?>">
        <div class="card">
            <?php
            if ((isset($o_student_data->from_staff)) AND ($o_student_data->from_staff)) {
            ?>
            <div class="card-header bg-primary"><?= ((isset($o_student_data->from_admission)) AND ($o_student_data->from_admission)) ? 'Candidate' : 'Student'; ?> Profile</div>
            <?php
            }
            ?>
            <div class="card-body row">
            <?php
                if($a_avatar){
            ?>
                <div class="col-md-4">
                    <img src="<?=site_url('file_manager/view/'.$a_avatar[0]->document_id.'/'.$personal_data_id)?>" class="img-fluid img-thumbnail picture-personal">
                </div>
            <?php
                }else{
            ?>
                <div class="col-md-4">
                    <img src="<?=base_url()?>assets/img/silhouette.png" class="img-fluid img-thumbnail picture-personal" width="88%">
                </div>
            <?php
                }
            ?>
                <div class="col-md-8 profile_alumni">
                    <h3><?=$o_student_data->personal_data_name;?> 
                    <?php
                    if (($o_student_data) AND ($o_student_data->student_status == 'graduated')) {
                        if ((!is_null($o_student_data->alumni_nickname)) OR ($o_student_data->alumni_nickname != '')) {
                            print('('.$o_student_data->alumni_nickname.')');
                        }
                    ?>
                        <a href="<?= base_url()?>personal_data/alumni_profile" class="profile_link_account">edit account</a>
                    <?php
                    }else if ((isset($o_student_data->from_staff)) AND ($o_student_data->from_staff)) {
                    ?>
                        <a href="<?= base_url()?>personal_data/profile/<?= $o_student_data->student_id ?>/<?= $o_student_data->personal_data_id ?>" class="profile_link_account">edit account</a>
                    <?php
                    }else{
                    ?>
                        <a href="<?= base_url()?>user/profile" class="profile_link_account">edit account</a>
                    <?php
                    }
                    ?>
                    </h3>
                    <h5><?=($o_student_data) ? $o_student_data->study_program_name : '-';?> - <?=($o_student_data) ? $o_student_data->faculty_name : '-';?></h5>
                    <h5><?= ($o_student_data) ? $o_student_data->student_number : '-'?></h5>
                    <table class="first_page">
                        <tr>
                            <td width="30px"><i class="fa fa-venus-mars"></i></td>
                            <td><?= ($o_student_data->personal_data_gender == 'M') ? 'Male' : 'Female' ?></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-envelope"></i></td>
                            <td>
        <?php
            // var_dump($o_student_data->from_admission);
            if ((isset($o_student_data->from_admission)) AND ($o_student_data->from_admission == true)) {
                print $o_personal_data->personal_data_email;
            }else if (($o_student_data) AND (is_null($o_student_data->student_alumni_email))) {
                print $o_student_data->student_email;
            }else if ($o_student_data) {
                print $o_student_data->student_alumni_email;
            }else{
                print '';
            }
        ?>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-phone"></i></td>
                            <td>
                                <?= $o_student_data->personal_data_phone?>
                                <!-- <input type="text" class="form-control" name="personal_data_phone" id="personal_data_phone"> -->
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-mobile"></i></td>
                            <td><?= $o_student_data->personal_data_cellular?></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-calendar"></i></td>
                            <td><?= $o_student_data->personal_data_place_of_birth.', '.$o_student_data->personal_data_date_of_birth?></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-flag"></i></td>
                            <td><?= $o_student_data->citizenship_country_name?></td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-graduation-cap"></i></td>
                            <td><?= ucfirst(strtolower($o_student_data->student_status));?> Student</td>
                        </tr>
                    </table>
                </div>
            <?php
            if (!isset($o_student_data->from_staff)) {
            ?>
                <div class="col-md-12">
                    <hr><h4 class="card-title">Shortcuts</h4>
                    <table>
                        <tr>  
                            <td><button  class="btn btn-sm" style="border: transparent;"> <a href="https://www.iuli.ac.id/" target="_blank" title="IULI Site" class="fas fa-flag"> IULI Site</a></button></td>
                        </tr>
                        <tr>  
                            <td><button  class="btn btn-sm" style="border: transparent;"> <a href="<?=base_url()?>file_manager/view_doc/academic_calendar_2022" target="_blank" title="Academic Calendar" class="fas fa-flag"> Academic Calendar 2022/2023</a></button></td>
                        </tr>
                        <tr>  
                            <td><button  class="btn btn-sm" style="border: transparent;"> <a href="<?=base_url()?>file_manager/view_doc/academic_calendar_2023" target="_blank" title="Academic Calendar" class="fas fa-flag"> Academic Calendar 2023/2024</a></button></td>
                        </tr>
            <?php
            if ($o_student_data->student_status != 'graduated') {
            ?>
                        <tr>
                            <td><button class="btn btn-sm" style="border: transparent;"> <a href="https://portal.iuli.ac.id/timetable/active/" target="_blank" title="Timetable" class="fas fa-flag"> Timetable</a></button></td>
                        </tr>
                <?php
                if ($this->session->userdata('student_submit_thesis')) {
                ?>
                        <!-- <tr>
                            <td><button class="btn btn-sm" style="border: transparent;"> <a href="https://apps.iuli.ac.id/thesis/" target="_blank" title="Thesis Submission" class="fas fa-flag"> Thesis Submission</a></button></td>
                        </tr> -->
                <?php
                }
                ?>
            <?php }?>
                        <tr>
                            <td><button class="btn btn-sm" style="border: transparent;"> <a href="https://mail.stud.iuli.ac.id/" target="_blank" title="Student Webmail" class="fas fa-flag"> Webmail</a></button></td>
                        </tr>
                    </table>
                </div>
            <?php
            }
            ?>
            </div>
        </div>
    </div>
<?php
    if (!isset($o_student_data->from_staff)) {
?>
    <div class="col-md-6">
        <?php
        if ($this->session->userdata('module') == 'student_finance') {
            print(modules::run('student/finance/get_list_billing', $o_student_data->student_id, true));
        }
        else {
            print($this->load->view('student/studyplan/table/krs_list', $this->a_page_data, true));
        }
        ?>
    </div>
<?php
    }
?>
</div>