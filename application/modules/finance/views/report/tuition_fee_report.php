<div class="card-header bg-white mb-2">
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="row">
                <div class="col-md-3 col-sm-5 col-12">
                    Semester:
                </div>
                <div class="col-md-9 col-sm-7 col-12">
                    <select name="select_semester" id="select_semester" class="form-control">
                <?php
                foreach ($academic_semester as $o_semester) {
                    $semester_active = $semester_selected;
                    $semesterperiod = $o_semester->academic_year_id.'-'.$o_semester->semester_type_id;
                    $selected = ($semester_active == $semesterperiod) ? 'selected="selected"' : '';
                ?>
                        <option value="<?= $semesterperiod; ?>" <?=$selected;?>><?=$semesterperiod;?></option>
                <?php
                }
                ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-8">
            <button type="button" class="btn btn-success float-right" id="btn_download_report"><i class="fas fa-excel"></i> Download Excel</button>
        </div>
    </div>
</div>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-summary-tab" data-toggle="tab" href="#nav-summary" role="tab" aria-controls="nav-summary" aria-selected="true">
            Summary
        </a>
        <a class="nav-item nav-link" id="nav-list-student-tab" data-toggle="tab" href="#nav-list-student" role="tab" aria-controls="nav-list-student" aria-selected="false">
            List of Active Student
        </a>
        <a class="nav-item nav-link" id="nav-list-student-graduate-tab" data-toggle="tab" href="#nav-list-student-graduate" role="tab" aria-controls="nav-list-student-graduate" aria-selected="false">
            List of Graduated Student
        </a>
        <a class="nav-item nav-link" id="nav-invoice-tab" data-toggle="tab" href="#nav-invoice" role="tab" aria-controls="nav-invoice" aria-selected="false">
            Semester Selected TF
        </a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab">
        <?=$summary_table;?>
    </div>
    <div class="tab-pane fade" id="nav-list-student" role="tabpanel" aria-labelledby="nav-list-student-tab">
        <?= $this->load->view('report/tuition_fee/list_student', ['status_filter' => 'active'], true); ?>
    </div>
    <div class="tab-pane fade" id="nav-list-student-graduate" role="tabpanel" aria-labelledby="nav-list-student-graduatetab">
        <?= $this->load->view('report/tuition_fee/list_student', ['status_filter' => 'graduated'], true); ?>
    </div>
    <div class="tab-pane fade" id="nav-invoice" role="tabpanel" aria-labelledby="nav-invoice-tab">
        <?=$semester_selected_body;?>
    </div>
</div>
<script>
$(function() {
    $('#select_semester').on('change', function(e) {
        e.preventDefault();

        // table_student_body.ajax.reload();
        // table_semester_body.ajax.reload();
        window.location.href = '<?=base_url()?>finance/report/report_tuition_fee/' + $('#select_semester').val();
    })

    $("#btn_download_report").on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        $.post('<?=base_url()?>finance/report/generate_report', {semester: $('#select_semester').val()}, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                window.location.href = result.uri;
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error generating file!');
        })
    })
})
</script>