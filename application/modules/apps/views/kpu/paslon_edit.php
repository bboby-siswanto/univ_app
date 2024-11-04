<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <?=((isset($vote_period)) AND ($vote_period)) ? $vote_period[0]->period_name : '';?>
            </div>
            <div class="card-body">
                <form url="<?=base_url()?>apps/kpu/submit_paslon" id="form_submit_paslon" onsubmit="return false">
                    <input type="hidden" name="paslon_id" id="paslon_id">
                    <div class="form-group" id="form_ketua">
                        <label for="ketua_student_id">Ketua</label>
                        <select name="ketua_student_id" id="ketua_student_id" class="form-control">
                            <option value=""></option>
<?php
if ($student_list) {
    foreach ($student_list as $o_student) {
        // $selected = (($paslon_id != '') AND ($o_kpu_member->student_id == $paslon_data->ketua_student_id)) ? 'selected="true"' : '';
?>
                            <option value="<?=$o_student->student_id;?>"><?=$o_student->personal_data_name.' ('.$o_student->study_program_abbreviation.'/'.$o_student->academic_year_id.')';?></option>
<?php
    }
}
?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ketua Photo</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="ketua_file_text" placeholder="Choose file..." readonly>
                            <div class="input-group-append">
                                <input type="file" name="ketua_picture" id="ketua_picture" class="d-none">
                                <label class="btn btn-outline-primary" for="ketua_picture" id="btn_choose_ketua_picture">Browse</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="form_wakil">
                        <label for="wakil_student_id">Wakil</label>
                        <select name="wakil_student_id" id="wakil_student_id" class="form-control">
                            <option value=""></option>
<?php
if ($student_list) {
    foreach ($student_list as $o_student) {
        // $selected = ($paslon_id != '') ? (($o_kpu_member->student_id == $paslon_data->wakil_student_id) ? 'selected="true"' : '') : '';
?>
                            <option value="<?=$o_student->student_id;?>"><?=$o_student->personal_data_name.' ('.$o_student->study_program_abbreviation.'/'.$o_student->academic_year_id.')';?></option>
<?php
    }
}
?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Wakil Photo</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="wakil_file_text" placeholder="Choose file..." readonly>
                            <div class="input-group-append">
                                <input type="file" name="wakil_picture" id="wakil_picture" class="d-none">
                                <label class="btn btn-outline-primary" for="wakil_picture" id="btn_choose_wakil_picture">Browse</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nomor_urut">Nomor Urut</label>
                        <input type="number" class="form-control" name="nomor_urut" id="nomor_urut" required="true">
                    </div>
                    <div class="form-group">
                        <label for="vision">Visi</label>
                        <textarea name="vision" id="vision" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="mission">Misi</label>
                        <textarea name="mission" id="mission" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-block btn-primary" type="button" id="submit_paslon">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('select#ketua_student_id').select2({
            theme: 'bootstrap',
            dropdownParent: $("#form_ketua"),
            minimumInputLength: 3
        });

        $('select#wakil_student_id').select2({
            theme: 'bootstrap',
            dropdownParent: $("#form_wakil"),
            minimumInputLength: 3
        });

        function submit_paslon_data(){
            return new Promise((resolve, reject) => {
                var data_form = $('form#form_submit_paslon');
                var form_data = new FormData(data_form[0]);
                // console.log(form_data);
                
                $.ajax({
                    url: data_form.attr('url'),
                    data: form_data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    dataType: 'json',
                    success: function(rtn){
                        resolve(rtn);
                    }
                });
            }, (err) => {
                reject(err);
            });
        }

        $('#wakil_picture').on('change', function(e) {
            var filename = $('input#wakil_picture').val().split('\\').pop();
            $('input#wakil_file_text').val(filename);
        });
        
        $('#ketua_picture').on('change', function(e) {
            var filename = $('input#ketua_picture').val().split('\\').pop();
            $('input#ketua_file_text').val(filename);
        });
        
        $('button#submit_paslon').click(function(e){
            e.preventDefault();
            $.blockUI();
            submit_paslon_data().then((res) => {
                $.unblockUI();
                if(res.code != 0){
                    toastr['warning'](res.message, 'Warning!');
                }
                else{
                    toastr['success']('Data saved', 'Success!');
                    $('#modal-paslon').modal('hide');
                    setTimeout( function(){ 
                        location.reload(); 
                        // window.location.href = '<?=base_url()?>admin/kpu_paslon/' + res.data;
                    }  , 3000 );
                }
            }).catch((err) => {
                $.unblockUI();
                toastr['error']('Error processing data', 'Error!');
                console.log(err);
            });
        });
    })
</script>