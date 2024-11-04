<div class="row">
    <div class="col-4">
        <!-- Verified  -->
<?php
    if ((isset($class_verified)) AND ($class_verified)) {
?>
        <div class="border pt-2 pl-2 pr-2 border-success text-success">
            VERIFIED BY <i class="fas fa-user-check"></i> <br>
            <ul>
    <?php
    foreach ($class_verified as $o_verif) {
    ?>
                <li><?=$o_verif->employee_name;?> <small>(<?=date('d F Y H:i', strtotime($o_verif->sign_datetime));?>)</small></li>
    <?php
    }
    ?>
            </ul>
        </div>
<?php
    }
?>
    </div>
    <div class="col-8">
        <div class="btn-group float-right" role="group" aria-label="Basic example">
            <button type="button" id="download_report" class="btn btn-info mb-3"><i class="fas fa-file-download"></i> Download Report</button>
<?php
if ((isset($is_inhod)) AND ($is_inhod) AND (!$hod_sign)) {
?>
            <button type="button" id="btn_sign_hod" class="btn btn-success mb-3"><i class="fas fa-file-signature"></i> Verified</button>
<?php
}
?>
<?php
if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
?>
            <!-- <button id="fe_absence" class="btn btn-info mb-3"><i class="fas fa-file"></i> Final Exam Absence</button> -->
            <!-- retrieve_absence -->
            <button id="retrieve_absence" class="btn btn-info mb-3"><i class="fas fa-file"></i> Retrieve Absence</button>
<?php
}
?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?= modules::run('academic/class_group/view_table_unit_delivered', $class_master_id);?>
    </div>
    <div class="col-12">
        <?= modules::run('academic/class_group/view_table_class_group', $class_master_id);?>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal_choose_lecturer_report">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Lecturer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="form_generate_class_report">
                    <input type="hidden" name="class_master_id" value="<?=$class_master_id;?>">
                    <div class="form-group">
                        <label for="select_lecturer_for_reporting">Please Select Lecturer for Generate Report</label>
                        <select name="employee_id" id="select_lecturer_for_reporting" class="form-control">
                            <option value="">All Lecturer</option>
    <?php
    $a_employee_id = [];
    if ($class_lecturer) {
        foreach ($class_lecturer as $o_employee) {
            if (!in_array($o_employee->employee_id, $a_employee_id)) {
                array_push($a_employee_id, $o_employee->employee_id);
    ?>
                            <option value="<?=$o_employee->employee_id;?>"><?=$o_employee->personal_data_name;?></option>
    <?php
            }
        }
    }
    ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"id="submit_class_report">Generate and Download</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    $('button#download_report').on('click', function(e) {
        e.preventDefault();

        $('div#modal_choose_lecturer_report').modal('show');
    });

    $('#btn_sign_hod').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        $.post('<?=base_url()?>academic/class_group/sign_class_hod', {class_master_id: '<?=$class_master_id;?>'}, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            toastr.error('Error processing data!', 'Error!');
            $.unblockUI();
        })
    })

    $('button#retrieve_absence').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        $.post('<?=base_url()?>portal/academic/test_sync_subject_delivered/<?=$class_master_id;?>', function(result) {
            $.unblockUI();
            if (result.code == 0) {
                location.reload();
            }else{
                toastr.warning(result.message, 'Warning!');
                console.log(result);
            }
        } ,'json').fail(function(params) {
            toastr.error('Error processing data!', 'Error!');
            $.unblockUI();
        });
    });
    
    $('button#submit_class_report').on('click', function(e) {
        e.preventDefault();

        // if ($('#select_lecturer_for_reporting').val() == '') {
            // var url = '<?=base_url()?>download/excel_download/generate_class_report/<?=$class_master_id;?>';
        // }else{
            // var url = '<?=base_url()?>download/excel_download/generate_class_report/<?=$class_master_id;?>/' + $('#select_lecturer_for_reporting').val();
        // }

        // window.location.href = url;
        // console.log(url);
        var data = {
            class_master_id: '<?=$class_master_id;?>',
            employee_id: $('#select_lecturer_for_reporting').val()
        };
        $.blockUI({baseZ: 9000});

        $.post('<?=base_url()?>download/pdf_download/generate_class_report', data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                var a_file = result.a_file;
                var s_files = '';

                $.each(a_file, function(i, v) {
                    var filedata = 'file' + i + '=' + v;
                    s_files += '&' + filedata;
                });

                var uri = '<?=base_url()?>download/pdf_download/download_class_report?semester=' + result.s_semester + '&class=' + result.s_message + '&file_count=' + a_file.length;
                uri += s_files;

                window.location.href = uri;

                // console.log(uri);
                // $('div#modal_choose_lecturer_report').modal('show');
            }else{
                toastr.warning(result.message, 'Warning!');
            }
            // console.log(result);
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error');
        });

    });
});
</script>