<div class="card">
    <div class="card-header">
        Filter Student Status
    </div>
    <div class="card-body">
        <form id="form_filter_table_student_status" onsubmit="return false">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="filter_batch">Batch</label>
                        <select name="filter_batch[]" id="filter_batch" class="form-control selectpicker" multiple data-live-search="true" data-actions-box="true">
                            <!-- <option value="all">All</option> -->
                <?php
                if (($batch !== null) AND ($batch)) {
                    foreach ($batch as $o_academic_year_id) {
                ?>
                        <option value="<?=$o_academic_year_id->academic_year_id;?>"><?=$o_academic_year_id->academic_year_id;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="filter_prodi">Program Study</label>
                        <select name="filter_prodi[]" id="filter_prodi" class="form-control selectpicker" multiple data-live-search="true" data-actions-box="true">
                            <!-- <option value="all">All</option> -->
                <?php
                if (($study_program !== null) AND ($study_program)) {
                    foreach ($study_program as $o_prodi) {
                ?>
                        <option value="<?=$o_prodi->study_program_id;?>"><?=$o_prodi->study_program_name_feeder;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <!-- <div class="col-sm-4">
                    <div class="form-group">
                        <label for="filter_status">Student Status</label>
                        <select name="filter_status[]" id="filter_status" class="form-control selectpicker" multiple data-live-search="true" data-actions-box="true">
                            <option value="active">ACTIVE</option>
                            <option value="inactive">INACTIVE</option>
                            <option value="onleave">ONLEAVE</option>
                            <option value="dropout">DROPOUT</option>
                            <option value="resign">RESIGN</option>
                            <option value="graduated">GRADUATED</option>
                        </select>
                    </div>
                </div> -->
                <!-- <div class="col-sm-4">
                    <div class="form-group">
                        <label for="filter_prodi">Batch</label>
                        <select name="status_batch" id="status_batch" class="form-control">
                            <option value="all">All</option>
                        </select>
                    </div>
                </div> -->
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-info float-right" id="btn_filter_data">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive" id="table_data"></div>
    </div>
</div>
<script>
var selectmultiprodi = $('#filter_prodi').selectpicker();
var selectmultibatch = $('#filter_batch').selectpicker();
$(function() {
    $('#btn_filter_data').on('click', function(e) {
        e.preventDefault();
        show_tabledata();
    });

    function show_tabledata() {
        $.blockUI();
        var form = $('form#form_filter_table_student_status');
		// var filter_data = objectify_form(form.serializeArray());
        let filter_data = form.serialize();

        $.post('<?=base_url()?>accreditation/get_student_status', filter_data, function(result) {
            $.unblockUI();
            $('#table_data').html(result.data);
        }, 'json').fail(function name(params) {
            $.unblockUI();
            toastr.error('error processing request');
        });
    }
    show_tabledata();
})
</script>