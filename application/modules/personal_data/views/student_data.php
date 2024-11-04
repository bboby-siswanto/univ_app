<link rel="stylesheet" href="<?=base_url()?>assets/vendors/bootstrap/datepicker/bootstrap-datepicker.min.css">
<script src="<?=base_url()?>assets/vendors/bootstrap/datepicker/bootstrap-datepicker.min.js"></script>
<style>
.section, .univ-transfer {
    display: none;
    animation: fade-out 1s;
}

.section-show, .univ-transfer-show {
    display: block;
    animation: fade-in 1s;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes fade-out {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}
</style>
<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group btn-group-sm float-right">
            <button type="button" class="btn btn-info btn-data-nav" id="btn_page_academic" target="#page_academic">Academic Data</button>
            <button type="button" class="btn btn-info btn-data-nav active" id="btn_page_personal" target="#page_personal">Personal Data</button>
            <button type="button" class="btn btn-info btn-data-nav" id="btn_page_document" target="#page_document">Document List</button>
        </div>
    </div>
</div>
<div class="row section" id="page_academic">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3>Data Academic</h3>
            </div>
        </div>
    </div>
</div>
<div class="row section section-show" id="page_personal">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="form_profile_student" onsubmit="return false" url="<?=base_url()?>student/submit_profile_data">
                <input type="hidden" name="pr_student_id" id="pr_student_id" value="<?=$student_id;?>">
                <a id="nav_data_personal" href="#data_personal" data-toggle="collapse" aria-expanded="false" aria-controls="data_personal">
                    <i class="fas fa-minus"></i> Personal Data
                </a>
                <div class="collapse w-100 mt-3 show" id="data_personal">
                    <?=$page_personal_data;?>
                </div>
                <hr>
                <a id="nav_data_address" href="#data_address" data-toggle="collapse" aria-expanded="false" aria-controls="data_address">
                    <i class="fas fa-plus"></i> Address Data
                </a>
                <div class="collapse w-100 mt-3 show" id="data_address">
                    <?=$page_address_data;?>
                </div>
                <hr>
                <a id="nav_data_parent" href="#data_parent" data-toggle="collapse" aria-expanded="false" aria-controls="data_parent">
                    <i class="fas fa-plus"></i> Parent Data
                </a>
                <div class="collapse w-100 mt-3 show" id="data_parent">
                    <?=$page_parent_data;?>
                </div>
                <hr>
                <a id="nav_data_highschool" href="#data_highschool" data-toggle="collapse" aria-expanded="false" aria-controls="data_highschool">
                    <i class="fas fa-plus"></i> Last Highschool Data
                </a>
                <div class="collapse w-100 mt-3 show" id="data_highschool">
                    <?=$page_highschool_data;?>
                </div>
        <?php
        if ($student_data->student_class_type == 'karyawan') {
        ?>
                <hr>
                <a id="nav_data_employment" href="#data_employment" data-toggle="collapse" aria-expanded="false" aria-controls="data_employment">
                    <i class="fas fa-plus"></i> Employment Data
                </a>
                <div class="collapse w-100 mt-3 show" id="data_employment">
                    <?=$page_employment_data;?>
                </div>
        <?php
		}
        ?>
                </form>
                <hr>
                <div class="w-100 mt-4">
                    <button type="button" class="btn btn-block btn-success" id="btn_submit_profile_data"><i class="fas fa-save"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row section" id="page_document">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3>Data Document</h3>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('.btn-data-nav').on('click', function(e) {
        e.preventDefault();
        let el = $(this);
        let buttonnavs = $('.btn-data-nav');
        let eltarget = $(el.attr('target'))[0];
        let sections = $('.section');
        let classshown = eltarget.classList.contains('section-show');
        if (classshown === false) {
            $.each(sections, function(i, v) {
                v.classList.remove('section-show');
            });

            $.each(buttonnavs, function(i, b) {
                b.classList.remove('active');
            });

            eltarget.classList.add('section-show');
            el[0].classList.add('active');
        }
        // console.log(classtarget);
    })
    $('.collapse').on('show.bs.collapse', function () {
        let idnav = this.id;
        $('a#nav_' + idnav + ' i').removeClass('fa-plus').addClass('fa-minus');
    });

    $('.collapse').on('hide.bs.collapse', function () {
        let idnav = this.id;
        $('a#nav_' + idnav + ' i').removeClass('fa-minus').addClass('fa-plus');
    });

    $('#btn_submit_profile_data').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        let form = $('#form_profile_student');
        let url = form.attr('url');
        // let data = form.serialize();

        var formData = new FormData(form[0]);
        // formData.append('filepict', $('input#pr_pd_personal_data_pict')[0].files[0]);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(rtn, status, jqXHR){
                $.unblockUI();
                if(rtn.code == 0){
                    toastr['success']('Success', 'Success!');
                    setTimeout( function(){ 
                        window.location.reload();
                    }  , 3000 );
                }else{
                    toastr['warning'](rtn.message, 'Error!');
                }
            },
            error : function(xhr, ajaxOptions, thrownError) {
                $.unblockUI();
                // console.log(xhr.responseText);
                toastr['error']("Error processing your data!", 'Error!');
            }
        });
    });
})
</script>