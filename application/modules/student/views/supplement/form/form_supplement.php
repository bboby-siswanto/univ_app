<form url="<?=base_url()?>student/supplement/submit_supplement" id="form_input_supplement" onsubmit="return false" method="post">
    <input type="hidden" name="supplement_id" id="supplement_id">
    <input type="hidden" name="supplement_student_id" id="supplement_student_id" value="<?=$student_id;?>">
    <div class="row">
        <div class="col-12">
            <div class="form group">
                <label for="supplement_desc" class="required_text">Description</label>
                <textarea name="supplement_desc" id="supplement_desc" class="form-control"></textarea>
            </div>
        </div>
        <div class="col-12">
            <hr>
        </div>
        <div class="col-12">
            
        </div>
        <div class="col-12">
            <table class="table" id="supplement_table_doc">
                <thead>
                    <tr>
                        <td class="pt-3 required_text">Certificate / Assignment Letter / Other Supporting Documents</td>
                        <td class="w-25">
                            <button type="button" id="btn_files_new" class="btn btn-success float-right"><i class="fa fa-plus"></i> Add File</button>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-danger">
                            <input type="file" name="filesupplement_1" id="filesupplement" class="form-control fileattachment">
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <i><small class="text-danger">allowed file format: (pdf,jpg,jpeg,png)</small></i>
        </div>
    </div>
</form>
<script>
var tf = $('#supplement_table_doc').DataTable({
    "searching": false,
    "paging": false,
    "info": false,
    "ordering": false,
    "language": {
        'emptyTable': "No attached file..."
    }
});

$(function() {
    var r_index = 2;
    $('#btn_files_new').on( 'click', function (e) {
        e.preventDefault();
        tf.row.add( [
            '<input type="file" name="filesupplement_' + r_index + '" id="filesupplement" class="form-control fileattachment">',
            '<button id="remove_file" type="button" class="btn btn-danger"><i class="fas fa-minus"></i></button>'
        ] ).draw(false);
        r_index++;
    });

    $('table#supplement_table_doc tbody').on('click', 'button#remove_file', function(e) {
        e.preventDefault();
        tf.row($(this).parents('tr')).remove().draw();

        var file_form = $('table#supplement_table_doc tbody').find('input');
        if (file_form.length > 0) {
            r_index = 1;
            for (let idx = 0; idx < file_form.length; idx++) {
                var form_file = file_form[idx];
                form_file.name = 'filesupplement_' + r_index;
                // console.log(form_file.name);
                r_index++;
            }
        }
    });
    

})
</script>