<div class="row">
    <div class="col">
        <form action="<?=base_url()?>academic/ofse/submit_new_ofse" onsubmit="return false" id="form_new_ofse">
            <input type="hidden" name="ofse_period_id" id="ofse_period_id">
            <div class="form-group">
                <label class="required_text">Period</label>
                <input type="text" class="form-control" name="ofse_period_name" id="ofse_period_name">
            </div>
            <div class="form-group">
                <label class="required_text">Student Registration Date</label>
                <input type="text" name="study_plan_ofse_period" id="study_plan_ofse_period" class="form-control">
            </div>
        </form>
    </div>
</div>
<script>
var date_now = new Date();
$(function() {
    $('input#study_plan_ofse_period').daterangepicker({
        showDropdowns: true,
		minYear: 2015,
		showWeekNumbers: true,
        autoApply: true,
        minDate: '<?=date('m/d/Y')?>'
    });
});
</script>