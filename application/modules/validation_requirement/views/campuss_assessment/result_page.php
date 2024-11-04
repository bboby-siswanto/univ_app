<script src="<?= base_url() ?>assets/vendors/chart.js/js/Chart.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6 col-lg-4">
                <div class="form-group">
                    <label for="assessment_id_result">Select Assessment</label>
                    <div class="input-group mb-3">
                        <select name="assessment_id_result" id="assessment_id_result" class="form-control" aria-describedby="btn_filter_assessment">
                            <option value=""></option>
                <?php
                if ($list_assessment) {
                    foreach ($list_assessment as $o_assessment) {
                ?>
                            <option value="<?=$o_assessment->assessment_id;?>"><?=$o_assessment->assessment_name;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                        <!-- <div class="input-group-append">
                            <button class="btn btn-info btn-sm" type="button" id="btn_filter_assessment">Filter</button>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12" id="result_view"></div>
        </div>
    </div>
</div>
<script>
$(function() {
    $("#assessment_id_result").on('change', function() {
        if ($("#assessment_id_result").val() !== '') {
            $.post('<?=base_url()?>validation_requirement/university_assessment/get_assessment_data', {ass_id: $("#assessment_id_result").val()}, function(result) {
                // console.log(result);
                $('#result_view').empty('');
                $('#result_view').html(result.table_view);
                // console.log(result.table_view);
            }, 'json').fail(function(params) {
                toastr.error('Error get assessment data!');
            })
        }
        else {
            toastr.warning('Please select assessment before filtering!');
        }
    })
});
</script>