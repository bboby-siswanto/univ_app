<div class="card">
    <div class="card-header">
        My Achievements
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="btn_new_achievements">
                <i class="fa fa-plus"></i> Add New Achievements
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="table_achivements">
                <thead class="bg-dark">
                    <tr>
                        <th>Input Date</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_achievement">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">My Achievements</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?=modules::run('student/supplement/form_input');?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn_save_supplement">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
list_table = $('table#table_achivements').DataTable({
    ajax: {
        url: '<?= base_url()?>student/supplement/get_supplement_student',
        type: 'POST',
        data: {
            student_id: '<?=$student_id;?>',
            render_from: 'student'
        },
    },
    columns: [
        {data: 'date_upload'},
        {data: 'supplement_comment'},
        {
          data: 'supplement_id',
          order: false,
          render: function(data, type, row) {
            var html = '<div class="btn-group btn-group-sm" role="group" aria-label="">';
              html += '<button type="button" class="btn btn-info btn-sm dropdown-toggle" id="btn_download" data-toggle="dropdown">Download Files</button>';
              html += '<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">'

              if (row['supplement_files']) {
                $.each(row['supplement_files'], function(i, v) {
                  html += '<a class="dropdown-item" href="<?=base_url()?>student/supplement/view_doc/' + v.supplement_doc_id + '" target="_blank">' + v.supplement_doc_fname + '</a>';
                })
              }
              
              html += '</div>';
              html += '</div>';
              console.log(html);
            return html;
          }
        }
    ],
});
$(function() {
    $('#btn_new_achievements').on('click', function(e) {
        e.preventDefault();

        $('#modal_achievement').modal('show');
    });

    $('#btn_save_supplement').on('click', function(e) {
      e.preventDefault();
      $.blockUI({ baseZ: 2000 });

      var form = $('#form_input_supplement');
      var form_data = new FormData(form[0]);
      var uri = form.attr('url');
      
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
              toastr.error('Error processing data!', 'Error');
              console.log(xhr.responseText);
          },
          success: function(rtn){
              $.unblockUI();
              if (rtn.code == 0) {
                  setInterval(function() {
                    window.location.reload();
                  }, 1000);
                  toastr.success('Success!', 'Success!');
              }else{
                  toastr.warning(rtn.message, 'Warning!');
              }
          }
      });
    });
})
</script>