<div class="card">
    <div class="card-header">
        Teaching Load: <?=$lecturer_data->employee_fullname;?> <?=(!is_null($lecturer_data->employee_lecturer_number)) ? ' / '.$lecturer_data->employee_lecturer_number : '';?>
        <div class="float-right">Academic Year <?=$academic_year;?> <?=strtoupper($semester);?></div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group btn-group-sm float-right">
        <?php
        if ($asl_teaching) {
        ?>
            <a href="<?=base_url()?>public/classlist/view/<?=$asl_teaching[0]->personal_document_id.'/'.urlencode($asl_teaching[0]->document_link);?>" class="btn btn-info btn-sm" name="view_asl_teaching" id="view_asl_teaching" target="_blank"><i class="fas fa-file"></i> ASL Teaching</a>
        <?php
        }
        ?>
            
            <button class="btn btn-info btn-sm" name="view_asl_community" id="view_asl_community"><i class="fas fa-file"></i> ASL Community</button>
            <button class="btn btn-info btn-sm" name="view_asl_reseach" id="view_asl_reseach"><i class="fas fa-file"></i> ASL Research</button>
            <button class="btn btn-info btn-sm" name="view_asl_advisor" id="view_asl_advisor"><i class="fas fa-file"></i> ASL Advisor Defense</button>
            <button class="btn btn-info btn-sm" name="view_asl_examiner" id="view_asl_examiner"><i class="fas fa-file"></i> ASL Examiner Defense</button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="lecturer_class" class="table table-sm table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>No</th>
                        <th>Class Description</th>
                        <th>Study Program</th>
                        <th>Subject</th>
                        <th>Credit</th>
                        <th>Count Student</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
        <?php
        if ((isset($list_class)) AND ($list_class)) {
            $i_numb = 1;
            foreach ($list_class as $o_class) {
        ?>
                    <tr>
                        <td><?=$i_numb++;?></td>
                        <td><?=$o_class->class_group_name;?></td>
                        <td><?=$o_class->study_programlist;?></td>
                        <td><?=$o_class->subject_name;?></td>
                        <td><?=$o_class->curriculum_subject_credit;?></td>
                        <td><?=$o_class->count_student;?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?=base_url()?>public/classlist/class_detail/<?=$o_class->class_master_id;?>" class="btn btn-info btn-sm" id="btn_view_class" name="btn_view_class" title="View Class Detail">
                                    <i class="fas fa-address-card"></i>
                                </a>
                                <!-- <div class="btn-group" role="group">
                                    <button id="btngroupfile" type="button" class="btn btn-info btn-sm dropdown-toggle" title="View Document" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-file"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btngroupfile">
                                        <a class="dropdown-item" href="#">Dropdown link</a>
                                        <a class="dropdown-item" href="#">Dropdown link</a>
                                    </div>
                                </div> -->
                            </div>
                        </td>
                    </tr>
        <?php
            }
        }
        else {
        ?>
                    <tr>
                        <td colspan="6">No data available. . .</td>
                    </tr>
        <?php
        }
        ?>
                </tbody>
            </table>
        </div>
    </div>
</div>