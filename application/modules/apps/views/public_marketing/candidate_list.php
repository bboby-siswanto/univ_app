<div class="card">
    <div class="card-header">
        Candidate List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <!-- <div class="row">
                <div class="col-12">
                    <div class="button-group float-right mb-3">
                        <button type="button" class="btn btn-success" id="btn_bulk_mail">Bulk Mail</button>
                    </div>
                </div>
            </div> -->
            <table id="candidate_list" class="table table-border">
                <thead class="bg-dark">
                    <tr>
                        <th></th>
                        <th>Candidate Name</th>
                        <th>Candidate Email</th>
                        <th>School</th>
                        <th>Program</th>
                        <th>Study Program</th>
                        <th>Batch</th>
                        <th>Register Date</th>
                        <th>Finish Online Test</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_email">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compose Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_candidate_message" enctype="multipart/form-data" class="row" onsubmit="return false">
                    <div class="form-group col-12">
                        <!-- <div class=""> -->
                            <button type="button" class="btn btn-success float-right" id="btn_attach_file">
                                <i class="fa fa-paperclip"></i> Attach File
                            </button>
                        <!-- </div> -->
                    </div>
                    <div class="form-group col-12">
                        <p class="float-right"><small class="text-danger text-end">(Max total file 3Mb, with supported file format [jpeg|jpg|png|doc|docx|pdf|xls|xlsx|bmp])</small></p>
                    </div>
                    <div class="form-group col-12">
                        <label for="message_subject">Subject Email</label>
                        <input type="text" class="form-control" name="message_subject" id="message_subject" >
                    </div>
                    <div class="form-group col-12">
                        <label for="message_body">Text Body</label>
                        <textarea name="message_body" id="message_body"></textarea>
                        <input type="hidden" name="message_body_input" id="message_body_input">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="send_message">Send</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
var a_student_id = [];
CKEDITOR.replace('message_body');
let candidate_list = $('#candidate_list').DataTable({
    responsive: true,
    dom: 'Bfrltip',
    buttons: [
        {
            text: 'Download Excel',
            extend: 'excel',
            title: 'IULI Candidate List Data',
        },
        {
            text: '<i class="fa fa-check"></i> Checklist All Candidate',
            className: 'selectall',
            action : function(e) {
                e.preventDefault();
                candidate_list.rows({ search: 'applied'}).deselect();
                candidate_list.rows({ search: 'applied'}).select();
            }
        },
        {
            text: '<i class="fas fa-email"></i> Compose Bulk Email',
            className: 'btn-success',
            action : function(e) {
                e.preventDefault();
                
                // toastr.warning('function is not ready!');
                console.log(a_student_id);
                var checked = candidate_list.rows( { selected: true } );
                var count_checked = checked.count()
                if (count_checked > 0) {
                    $('#modal_email').modal('show');

                    var data_checked = checked.data();
                    for (let i = 0; i < count_checked; i++) {
                        a_student_id.push(data_checked[i].student_id);
                    }
                }
                else {
                    toastr.warning('Please select at least 1 candidate!');
                }
            }

        }
    ],
    ajax: {
        url: '<?=site_url('student/filter_result')?>',
        type: 'POST',
        data: function(param) {
            param.target_from = 'external';
            return param;
        }
    },
    columns: [
        {
            data: 'personal_data_id',
            orderable: false,
            className: 'select-checkbox',
            render: function(data, type, ui) {
                var html = '<input type="hidden" value="' + data + '" name="student_id">';
                return html;
            }
        },
        {data: 'personal_data_name'},
        {data: 'personal_data_email'},
        {data: 'institution_name'},
        {data: 'program_name'},
        {data: 'study_program_name'},
        {data: 'academic_year_id'},
        {data: 'register_date'},
        {data: 'has_finished_online_test'},
        {
            data: 'student_id',
            render: function(data, type, row) {
                return '';
            }
        },
    ],
    select: {
        style:    'multi',
        selector: 'td:first-child'
    },
});

$(function() {
    $('#modal_email').on('hidden.bs.modal', function (e) {
        a_student_id = [];
        CKEDITOR.instances.message_body.setData('');
        $('#form_candidate_message input').val('');
        $('.student_append').remove();
    });
    $('#modal_email').on('shown.bs.modal', function (e) {
        CKEDITOR.instances.message_body.setData('<p>Hello $candidate_name,</p>');
    });
    $('#btn_attach_file').on('click', function(e) {
        e.preventDefault();

        var inputfile = $('<input>').attr({'type':'file', 'name':'fileattach[]','class':'student_append d-none'});
        var form = $('#form_candidate_message');
        form.append(inputfile);
        inputfile.trigger('click');
        inputfile.on('change', function() {
            var value_upload = $(this).val();
            let array_value = value_upload.split("\\");
            let string = array_value[array_value.length - 1];
            
            var label = $('<label>').attr({'class': 'student_append text-muted row col-12 ml-2'});
            var stringlabel = '<i class="fas fa-paperclip pr-2"></i> ' + string;
            var removelabel = $('<a>').attr({'class': 'student_append remove_attach text-to-pointer text-danger ml-1'});
            var stringremovelabel = '<i class="fas fa-times"></i>';
            removelabel.append(stringremovelabel);
            label.append(stringlabel);
            label.append(removelabel);
            form.append(label);

            label.on('click', function() {
                label.remove();
                inputfile.remove();
            })
        });
    });
    
    $('#send_message').on('click', function(e) {
        e.preventDefault();

        var body_editor = CKEDITOR.instances.message_body.getData();
        $('input#message_body_input').val(body_editor);
        
        var form = $("#form_candidate_message");
        if (a_student_id.length > 0) {
            $.each(a_student_id, function(i, v) {
                var inputstudent = $('<input>').attr({'type': 'hidden', 'name': 'student_id[]', 'class': 'student_append'}).val(v);
                form.append(inputstudent);
            });

            var formdata = new FormData(form[0]);
            $.ajax({
                url: '<?=base_url()?>apps/iuli_marketing/send_mail',
                data: formdata,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                success: function(result){
                    toastr.warning('Under development!');
                    console.log(result);
                    $('#modal_email').modal('hide');
                },
                error: function(params) {
                    toastr.error('error');
                }
            });
        }
        else {
            toastr.warning('No candidate selected');
        }
    });
})
</script>