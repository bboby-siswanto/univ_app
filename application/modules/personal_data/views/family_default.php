<?php
    // (isset($student_data)) ? modules::run('student/show_name', $student_data->student_id) : '' ;
    print modules::run('student/show_name');
?>

<?= modules::run('personal_data/family/form_add_family', $o_personal_data->personal_data_id);?>

<div class="row">
    <div class="col-12">
        <?= modules::run('personal_data/family/view_family_lists_table', $o_personal_data->personal_data_id);?>
    </div>
</div>
<?php
// if (in_array($this->session->userdata('type'), ['student', 'alumni'])) {
    ?>
    <!-- <div class="alert alert-danger" role="alert">
    to make changes to your data, please contact the IT Department
    </div>
    <script>
        toastr.info('to make changes to your data, please contact the IT Department', 'Info!');
    </script> -->
    <?php
// }
?>