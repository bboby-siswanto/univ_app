<div class="card">
    <div class="card-header">
        Study Abroad Submission Files
    </div>
    <div class="card-body" id="card-body">
        <form url="<?=base_url()?>student/abroad/submit_data" id="form_abroad_submission" onsubmit="return false">
            <input type="hidden" name="abroad_id" id="abroad_id" value="<?=($student_abroad_data) ? $student_abroad_data[0]->exchange_id : '';?>">
            <div class="row">
                <div class="col-sm-6">
                    <div class="d-block p-1">
                        <div class="w-25 float-left font-weight-bold">Name</div>
                        : <?=($student_abroad_data) ? ucwords(strtolower($student_abroad_data[0]->personal_data_name)) : '';?>
                    </div>
                    <div class="d-block p-1">
                        <div class="w-25 float-left font-weight-bold">Student ID</div>
                        : <?=($student_abroad_data) ? $student_abroad_data[0]->student_number: '';?>
                    </div>
                    <div class="d-block p-1">
                        <div class="w-25 float-left font-weight-bold">Study Program</div>
                        : <?=($student_abroad_data) ? $student_abroad_data[0]->study_program_name: '';?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-block p-1">
                        <div class="w-25 float-left font-weight-bold">Program</div>
                        : <?=($student_abroad_data) ? $student_abroad_data[0]->program_name: '';?>
                    </div>
                    <div class="d-block p-1">
                        <div class="w-25 float-left font-weight-bold">Study Location</div>
                        : <?=($student_abroad_data) ? $student_abroad_data[0]->institution_name: '';?>
                    </div>
                    <div class="d-block p-1">
                        <div class="w-25 float-left font-weight-bold">Country</div>
                        : <?=($student_abroad_data) ? $student_abroad_data[0]->country_name: '';?>
                    </div>
                </div>
            </div>
            <hr>
            <ul class="list-group list-group-flush as_form_thesis">
                <li class="list-group-item">
                    <a href="<?= (isset($supporting_link['proposal_submission'])) ? $supporting_link['proposal_submission'] : base_url().'#'; ?>" target="_blank" class="pl-3">
                        <i class="fas fa-mouse"></i> Submit Thesis Proposal
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="<?= (isset($supporting_link['work_submission'])) ? $supporting_link['work_submission'] : base_url().'#'; ?>" target="_blank" class="pl-3">
                        <i class="fas fa-mouse"></i> Submit Thesis Work
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="<?= (isset($supporting_link['final_submission'])) ? $supporting_link['final_submission'] : base_url().'#'; ?>" target="_blank" class="pl-3">
                        <i class="fas fa-mouse"></i> Submit Thesis Final
                    </a>
                </li>
            </ul>
            <hr>
            <ul class="list-group list-group-flush as_form_internship">
                <li class="list-group-item">
                    <a href="<?= (isset($supporting_link['internship_submission'])) ? $supporting_link['internship_submission'] : base_url().'#'; ?>" target="_blank" class="pl-3">
                        <i class="fas fa-mouse"></i> Submit Internship Data
                    </a>
                </li>
            </ul>
            <hr>
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="h5">File Submission</div>
                </div>
                <div class="col-12">
                    <div class="row align-items-center mt-2">
                        <div class="col-md-4">
                            <label class="pt-1 pl-1 ">Transcript Files from partner University</label>
                        </div>
                        <div class="col-md-4">
                            <input type="file" name="ad_transcript_partner_univ" id="ad_transcript_partner_univ" class="form-control inputdoc" data-type="abroad-transcript">
                        </div>
                        <div class="col-md-4">
                            <label class="pl-1 <?=((isset($abroad_transcript)) AND ($abroad_transcript)) ? '' : 'd-none';?>"><a href="<?=base_url()?>student/abroad/view_doc/<?= ((isset($abroad_transcript)) AND ($abroad_transcript)) ? $abroad_transcript[0]->document_link : '#';?>" target="_blank" class="btn btn-link btn-sm text-left"><?=((isset($abroad_transcript)) AND ($abroad_transcript)) ? $abroad_transcript[0]->document_name : '';?> <i class="fas fa-download"></i></a></label>
                        </div>
                    </div>
                    <div class="row align-items-center mt-2">
                        <div class="col-md-4">
                            <label class="pt-1 pl-1 ">Certificate Degree from partner University</label>
                        </div>
                        <div class="col-md-4">
                            <input type="file" name="ad_crtificate_degree_partner_univ" id="ad_crtificate_degree_partner_univ" class="form-control inputdoc" data-type="abroad-certificate_degree">
                        </div>
                        <div class="col-md-4">
                            <label class="pl-1 <?=((isset($abroad_certificate)) AND ($abroad_certificate)) ? '' : 'd-none';?>"><a href="<?=base_url()?>student/abroad/view_doc/<?=((isset($abroad_certificate)) AND ($abroad_certificate)) ? $abroad_certificate[0]->document_link : '#';?>" target="_blank" class="btn btn-link btn-sm text-left"><?=((isset($abroad_certificate)) AND ($abroad_certificate)) ? $abroad_certificate[0]->document_name : '';?> <i class="fas fa-download"></i></a></label>
                        </div>
                    </div>
                    <div class="row align-items-center mt-2">
                        <div class="col-md-4">
                            <label class="pt-1 pl-1 ">Study Abroad Other Files</label>
                        </div>
                        <div class="col-md-4">
                            <input type="file" name="ad_abroad_other" id="ad_abroad_other" class="form-control inputdoc" data-type="abroad-other_file">
                        </div>
                        <div class="col-md-4">
                            <label class="pl-1 <?=((isset($abroad_otherfile)) AND ($abroad_otherfile)) ? '' : 'd-none';?>"><a href="<?=base_url()?>student/abroad/view_doc/<?=((isset($abroad_otherfile)) AND ($abroad_otherfile)) ? $abroad_otherfile[0]->document_link : '#';?>" target="_blank" class="btn btn-link btn-sm text-left"><?=((isset($abroad_otherfile)) AND ($abroad_otherfile)) ? $abroad_otherfile[0]->document_name : '';?> <i class="fas fa-download"></i></a></label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-block btn-success" id="btn_submit_abroad_student">
                        <i class="fas fa-save"></i>  Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let program_abroad = '<?=($student_abroad_data) ? $student_abroad_data[0]->program_id: '';?>';
let tag_internship = document.getElementsByClassName('as_form_internship');
let tag_thesis = document.getElementsByClassName('as_form_thesis');

$(function() {
    $('.inputdoc').on('change', function(e) {
        e.preventDefault();
        $.blockUI();
        var thisdata = $(this);
        labelel = thisdata[0].parentElement.nextElementSibling;
        let label = $(labelel).find('label');
        let label_link = $(labelel).find('a');

        const fileinput = document.getElementById(thisdata[0].id);

        var formdefault = $('#form_abroad_submission');
        let typedoc = $(this).attr('data-type');
        let fields = formdefault.serializeArray();
        
        var formdata = new FormData();
        formdata.append('file', $(this)[0].files[0])
        formdata.append('typedoc', typedoc)
        $.each(fields, function(i, v) {
            formdata.append(v.name, v.value)
        })
        
        $.ajax({
            url: '<?=base_url()?>student/abroad/receive_doc',
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: function (xhr, status, error) {
                $.unblockUI();
                fileinput.value = '';
                toastr.error('Error send your document!');
            },
            success: function(rtn){
                if (rtn.code != 0) {
                    fileinput.value = '';
                    toastr.warning(rtn.message, 'Warning!');
                }
                else {
                    fileinput.value = '';
                    label.removeClass('d-none')
                    label_link.attr('href', rtn.target)
                    label_link.html(rtn.name + ' <i class="fas fa-download"></i>')
                }
                $.unblockUI();
            }
        });
    })

    $('#btn_submit_abroad_student').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('#form_abroad_submission');
        var btn_action = $('#btn_submit_abroad_student');
        var form_data = new FormData(form[0]);
        var uri = form.attr('url');
        btn_action.attr('disabled', 'disabled');

        $.ajax({
            url: uri,
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: function (xhr, status, error) {
                $.unblockUI();
                btn_action.removeAttr('disabled');
                toastr.error('Error processing data!', 'Error');
                console.log(xhr.responseText);
            },
            success: function(rtn){
                $.unblockUI();
                if (rtn.code == 0) {
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                    toastr.success('Success!', 'Success!');
                }else{
                    btn_action.removeAttr('disabled');
                    toastr.warning(rtn.message, 'Warning!');
                }
            }
        });
    });
})

function hideel(el, action) {
    $.each(el, function(i, v) {
        if (action == 'show') {
            v.classList.remove('d-none');
        }
        else {
            v.classList.add('d-none');
        }
    });
}

if (program_abroad == 7) {
    console.log(program_abroad);
    hideel(tag_thesis, 'hide');
    hideel(tag_internship, 'show');
}
else if (program_abroad == 4) {
    hideel(tag_thesis, 'hide');
    hideel(tag_internship, 'hide');
}
else {
    hideel(tag_thesis, 'show');
    hideel(tag_internship, 'show');
}
</script>