<div class="card d-none" id="card_question_option">
    <div class="card-body">
        <label id="option_question_form"></label>
        <form id="form_option" onsubmit="return false">
            <input type="hidden" name="question_id_option" id="question_id_option">
            <input type="hidden" name="question_option_id" id="question_option_id" value="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="number_option_question">Number of Option</label>
                        <div class="input-group mb-3">
                            <input type="number" name="number_option_question" id="number_option_question" class="form-control" aria-label="Number of Option" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-success btn-sm" type="button" id="btn_generate_option">Generate Option</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="table_option_question" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Option Description</th>
                                    <th>Answer</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-primary" id="submit_option_question">Submit</button>
                    <button type="button" class="btn btn-secondary" id="cancel_option_question">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card d-none" id="card_question">
    <div class="card-body">
        <form id="form_question" onsubmit="return false">
            <input type="hidden" name="question_id" id="question_id" value="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Number</label>
                        <input type="text" class="form-control" name="number_question" id="number_question">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Section</label>
                        <select name="section_question" id="section_question" class="form-control">
                            <option value="1">LISTENING</option>
                            <option value="2" selected="selected">NONLISTENING</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Question</label>
                        <textarea name="question_desc" id="question_desc"></textarea>
                        <input type="hidden" name="question_desc_body" id="question_desc_body">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Level</label>
                        <select name="answer_level" id="answer_level" class="form-control">
                            <option value="1">A</option>
                            <option value="2">B</option>
                            <option value="3">C</option>
                            <option value="4">D</option>
                            <option value="5">E</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
        <button type="button" class="btn btn-primary" id="submit_question">Submit</button>
        <button type="button" class="btn btn-secondary" id="cancel_question">Cancel</button>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Question List
        <div class="card-header-actions">
            <button id="new_question" class="card-header-action btn btn-link">
                <i class="fas fa-plus"></i> New Question
            </button>
        </div>
        <!-- <a class="card-header-action" href="#" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
            <i class="fa fa-gear"></i> Quick Actions
        </a> -->
    </div>
    <div class="card-body">
        <!-- <textarea name="question_desc" id="question_desc"></textarea> -->
        <div class="table-responsive">
            <table id="exam_question" class="table table-bordered table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>No.</th>
                        <th>Section Number</th>
                        <th>Section</th>
                        <th>Question</th>
                        <th>Option</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- <div class="modal" tabindex="-1" role="dialog" id="modal_input_question">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div> -->
<script>
    CKEDITOR.replace('question_desc', {
        extraPlugins: 'ckeditor_wiris'
        // extraPlugins: 'mathjax',
        // mathJaxLib: 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML',
        // height: 320,
        // removeButtons: 'PasteFromWord'
    });
    var table_option_question = $('#table_option_question').DataTable({
        paging: false,
        ordering: false,
        info: false,
        searching: false
    });
    var question_table = $('table#exam_question').DataTable({
        ordering: false,
        ajax: {
            url: '<?=base_url()?>admission/entrance_test/question_list',
            type: 'POST',
            data: false
        },
        order: [[1, 'asc'],[0, 'asc']],
        columns: [
            {
                data: 'exam_question_number'
            },
            {
                data: 'exam_section_id',
                visible: false
            },
            {data: 'exam_question_type'},
            {data: 'exam_question_description'},
            {data: 'exam_option'},
            {
                data: 'exam_question_id',
                render: function(data, type, row) {
                    var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                    html += '<button type="button" class="btn btn-warning btn-sm" id="btn_edit_question" title="edit question"><i class="fas fa-edit"></i></button>';
                    html += '<button type="button" class="btn btn-success btn-sm" id="btn_option_question" title="list option"><i class="fas fa-list"></i></button>';
                    html += '</div>'
                    return html;
                }
            }
        ]
    });

    $(function() {
        $('button#submit_question').on('click', function(e) {
            e.preventDefault();
            var body_editor = CKEDITOR.instances.question_desc.getData();
            $('input#question_desc_body').val(body_editor);

            var data = $('#form_question').serialize();
            $.post('<?=base_url()?>admission/entrance_test/save_question', data, function(result) {
                if (result.code == 0) {
                    toastr.success('Success submit question!', 'Success');
                    // $('#modal_input_question').modal('hide');
                    $('#card_question').addClass('d-none');
                    question_table.ajax.reload(null, false);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                toastr.error('Error submit data', 'Error!');
            });
        });
        $('button#submit_option_question').on('click', function(e) {
            e.preventDefault();
            
            let countrow = table_option_question.rows().count();
            if (countrow > 0) {
                startoption = 'A';
                for (let option = 0; option < countrow; option++) {
                    var body_editor = eval('CKEDITOR.instances.option_desc_' + startoption + '.getData()');
                    $('input#option_desc_value_' + startoption).val(body_editor);
                    
                    startoption = String.fromCharCode(startoption.charCodeAt(0) + 1);
                }

                var data = $('#form_option').serialize();
                $.post('<?=base_url()?>admission/entrance_test/save_option_question', data, function(result) {
                    if (result.code == 0) {
                        toastr.success('Success submit question!', 'Success');
                        $('#card_question_option').addClass('d-none');
                        question_table.ajax.reload(null, false);
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    toastr.error('Error submit data', 'Error!');
                });
            }
        });

        $('#cancel_question').on('click', function(e) {
            e.preventDefault();

            $('#card_question').addClass('d-none');
        });
        $('#cancel_option_question').on('click', function(e) {
            e.preventDefault();

            table_option_question.clear().draw();
            $('#card_question_option').addClass('d-none');
        });

        $('button#new_question').on('click', function(e) {
            e.preventDefault();

            $('#form_question').find('input, select').val('');
            CKEDITOR.instances.question_desc.setData( '' );
            $('#card_question').removeClass('d-none');
        });

        $('table#exam_question tbody').on('click', 'button#btn_edit_question', function(e) {
            var table_data = question_table.row($(this).parents('tr')).data();
            $('#question_id').val(table_data.exam_question_id);
            $('#number_question').val(table_data.exam_question_number);
            $('#section_question').val(table_data.exam_section_id);
            $('#answer_level').val(table_data.exam_question_part);
            CKEDITOR.instances.question_desc.setData(table_data.exam_question_description);

            $('#card_question').removeClass('d-none');
        });
        $('table#exam_question tbody').on('click', 'button#btn_option_question', function(e) {
            var table_data = question_table.row($(this).parents('tr')).data();

            $('#form_option').find('input, select').val('');
            table_option_question.clear().draw();
            $('#question_id_option').val(table_data.exam_question_id);

            $('#card_question_option').removeClass('d-none');
            $('#card_question').addClass('d-none');

            $('#option_question_form').html(table_data.trim_queston);

            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        });

        $('#btn_generate_option').on('click', function(e) {
            e.preventDefault();

            table_option_question.clear().draw();
            let numberoption = $('#number_option_question').val();
            var startoption = 'A';
            if (numberoption > 0) {
                for (let option = 0; option < numberoption; option++) {
                    table_option_question.row.add([
                        startoption + '<input type="hidden" name="option_number[]" value="' + startoption + '">',
                        '<textarea name="option_desc[]" id="option_desc_' + startoption + '"></textarea><input type="hidden" name="option_desc_value[]" id="option_desc_value_' + startoption + '">',
                            // '<div class="custom-control custom-radio"><input type="radio" id="answer_option_' + startoption + '" name="answer_option" class="custom-control-input"><label class="custom-control-label" for="answer_option_' + startoption + '">This is the answer</label></div>',
                            '<div class="custom-control custom-radio"><input type="radio" id="answer_option_' + startoption + '" name="answer_option" class="custom-control-input" value="' + startoption + '"><label class="custom-control-label" for="answer_option_' + startoption + '">This is the answer</label></div>',
                        ]).draw(false);
                    
                    CKEDITOR.replace('option_desc_' + startoption, {
                        extraPlugins: 'ckeditor_wiris',
                        removeButtons: 'Save,NewPage,CopyFormatting,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,basicstyles,cleanup,CreateDiv,BidiLtr,BidiRtl,Language,Link,Unlink,Anchor,Image,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Font,FontSize,Maximize,ShowBlocks'
                    });
                    startoption = String.fromCharCode(startoption.charCodeAt(0) + 1);
                }
            }
        })
    })
</script>