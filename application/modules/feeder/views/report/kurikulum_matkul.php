<div class="card">
    <div class="card-body">
        <h4>Curriculum IULI</h4>
        <div class="table-responsive">
            <table id="table_kurikulum_iuli" class="table table-border table-sm table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>Curriculum Name</th>
                        <th>Feeder Status</th>
                        <th>Curriculum Data Sync</th>
                        <th>Curriculum Subject Sync</th>
                        <th>Key ID</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_subject">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subject List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 table-responsive">
                        <table class="table table-hover" id="table_iuli_subject">
                            <thead>
                                <tr>
                                    <th colspan="4">Subject IULI</th>
                                </tr>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Subject Code</th>
                                    <th>SKS</th>
                                    <th>Subject ID</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col-md-6 table-responsive">
                        <table class="table table-hover" id="table_feeder_subject">
                            <thead>
                                <tr>
                                    <th colspan="4">Subject Feeder</th>
                                </tr>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Subject Code</th>
                                    <th>SKS</th>
                                    <th>Subject ID</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
let table_iuli_subject = $('#table_iuli_subject').DataTable({
    paging: false,
    info: false
});
let table_feeder_subject = $('#table_feeder_subject').DataTable({
    paging: false,
    info: false
});
let table_kurikulum_iuli = $('#table_kurikulum_iuli').DataTable({
    paging: false,
    ordering: false,
    info: false,
    ajax: {
        url: '<?= base_url()?>feeder/report/validate_kurikulum',
        type: 'POST'
    },
    columns: [
        {data: 'curriculum_name'},
        {data: 'feeder_avail'},
        {
            data: 'kurikulum_valid',
            render: function(data, type, row) {
                let valid = (data.length == 0) ? 'valid' : 'invalid';
                return valid;
            }
        },
        {
            data: 'kurikulum_subject_valid',
            render: function(data, type, row) {
                // let valid = (data.length == 0) ? 'valid' : 'invalid';
                if (data.length == 0) {
                    return 'valid';
                }
                else {
                    // console.log(data);
                    return '<button type="button" class="btn btn-sm btn-info" id="btn_view_subject">invalid</button>';
                    // return 'invalid';
                }
                // return valid;
            }
        },
        {
            data: 'curriculum_id',
            render: function(data, type, row) {
                let subjectvalid = row.kurikulum_subject_valid;
                var href = (subjectvalid.length == 0) ? data : '<a href="<?=base_url()?>feeder/curriculum_subject/sync_curriculum_subject/' + data + '" target="blank">' + data + '</a>';
                // if (subjectvalid.length > 0) {
                    // var href = '<a href="<?=base_url()?>feeder/curriculum_subject/sync_curriculum_subject/' + data + '" target="blank">' + data + '</a>';
                // }
                return href;
            }
        },
    ],
});
$(function() {
    $('table#table_kurikulum_iuli tbody').on('click', 'button#btn_view_subject', function(e) {
        e.preventDefault();
        var tabledata = table_kurikulum_iuli.row($(this).parents('tr')).data();
        let iulisubject = tabledata.kurikulum_iuli_subject;
        let feedersubject = tabledata.kurikulum_feeder_subject;

        table_iuli_subject.clear().draw();
        table_feeder_subject.clear().draw();

        $.each(iulisubject, function(i, v) {
            table_iuli_subject.row.add([
                '' + v.subject_name,
                '' + v.subject_code,
                '' + v.subject_credit,
                '' + v.subject_id,
            ]).draw().node();
        });
        $.each(feedersubject, function(i, v) {
            table_feeder_subject.row.add([
                '' + v.subject_name,
                '' + v.subject_code,
                '' + v.subject_credit,
                '' + v.subject_id,
            ]).draw().node();
        });

        $("#modal_subject").modal('show');
    })
})
</script>