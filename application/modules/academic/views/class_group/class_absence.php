<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Unit of Subject Delivered</div>
            <div class="card-body">
        <?php
        $demo = false;

        if (isset($is_demo)) {
            $demo = $is_demo;
        }
        ?>
                <?=modules::run('academic/class_group/form_input_unit_subject', $class_master_id, $subject_delivered_id, $demo)?>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                Class Member
        <?php
            if (!$subject_delivered_id) {
        ?>
                <div class="card-header-actions">
                    <input type="hidden" name="with_quiz" id="with_quiz" value="0">
                    <!-- <button class="card-header-action btn btn-link" id="btn_add_quiz">
                        <i class="fa fa-plus"></i> Quiz
                    </button> -->
                </div>
        <?php
            }else if ($quiz_number) {
                print('<input type="hidden" name="with_quiz" id="with_quiz" value="1">');
            }
        ?>
            </div>
            <div class="card-body">
                <?= modules::run('academic/class_group/view_table_class_absence', $class_master_id, $subject_delivered_id);?>
            </div>
        </div>
    </div>
    <div class="col-12">
    <?php
    if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
        print('<button id="save_class_absence_develop" type="button" class="btn btn-warning btn-block">Test</button>');
    }
    ?>
        <button id="save_class_absence" type="button" class="btn btn-success btn-block">Save</button>
    </div>
</div>