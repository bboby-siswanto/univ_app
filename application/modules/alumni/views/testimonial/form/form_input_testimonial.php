<div class="card">
    <div class="card-header">Testimonial</div>
    <div class="card-body">
        <form id="form_testimonial">
            <input type="hidden" name="testimonial_id" id="testimonial_id" value="<?= ($o_student_testimoni) ? $o_student_testimoni[0]->testimonial_id : ''; ?>">
            <div class="form-group">
                <label>Your Testimoni</label>
                <textarea class="form-control" name="testimoni" id="testimoni" rows="6" maxlength="500"><?= ($o_student_testimoni) ? $o_student_testimoni[0]->testimoni : '' ?></textarea>
            </div>
            <button type="button" id="btn_save_testimoni" class="btn btn-info">Save</button>
        </form>
    </div>
</div>
<script>
    $(function() {
        // toastr['success']('Thanks, data has been saved', 'Success!');
        $('button#btn_save_testimoni').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            var data = $('form#form_testimonial').serialize();
            $.post('<?= base_url()?>alumni/testimonial/save_testimoni', data, function(rtn) {
                $.unblockUI();
                if (rtn.code == 0) {
                    toastr['success']('Thanks, data has been saved', 'Success!');
                }else{
                    toastr['error'](rtn.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        });
    });
</script>