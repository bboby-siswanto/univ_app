<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Member OFSE <?= $class_details['subject']?></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-12">
                                Examiner 1: <?= (($class_details['examiner_lists']) AND ($class_details['examiner_lists'][0] !== null)) ? $class_details['examiner_lists'][0] : 'N/A' ?>
                            </div>
                            <div class="col-12">
                                Examiner 2: <?= (($class_details['examiner_lists']) AND ($class_details['examiner_lists'][1] !== null)) ? $class_details['examiner_lists'][1] : 'N/A' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-12">
                                Student Count: <?= $class_details['student_count'] ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?= modules::run('academic/ofse/view_ofse_lists_member', $class_group_id);?>
            </div>
        </div>
    </div>
</div>