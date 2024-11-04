<div class="card">
    <div class="card-header">
        My KRS
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_krs" class="table">
                <thead class="bg-dark">
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Credit</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
var table_krs = $('#table_krs').DataTable({
    paging: false,
    info: false,
    processing: true,
    ajax: {
        url: '<?= base_url()?>academic/score/filter_score_student',
        type: 'POST',
        data: function(d){
            d.student_id = '<?= $this->session->userdata('student_id');?>';
            d.academic_year_id = '<?= $this->session->userdata('academic_year_id_active');?>';
            d.semester_type_id = '<?= $this->session->userdata('semester_type_id_active');?>';
        }
    },
    columns: [
        {
            data: 'subject_code',
            render: function(data, type, row) {
                var subject_name = data;
                subject_name = set_link(subject_name, row.class_master_link_exam, row.class_master_link_exam_available);
                return subject_name;
            }
        },
        {
            data: 'subject_name',
            render: function(data, type, row) {
                var subject_name = data;
                subject_name = set_link(subject_name, row.class_master_link_exam, row.class_master_link_exam_available);
                return subject_name;
            }
        },
        {data: 'curriculum_subject_credit'}
    ]
});

function set_link(subject_name, class_master_link_exam = null, class_master_link_exam_available = 'enable') {
    var link_subject = subject_name;
    if ('<?=$this->session->userdata('type');?>' == "student") {
        // var teks = 'Material exam not ready';
        if (class_master_link_exam !== null) {
            var link_subject = '<a href="' + class_master_link_exam + '" target="_blank">' + subject_name + '</a>';
            if (class_master_link_exam_available == 'disable') {
                link_subject = '<a href="javascript:void(0)" onclick="show_notready()">' + subject_name + '</a>';
            }
        }
    }
    return link_subject;
}

function show_notready() {
    Swal.fire({
        title: '<strong>Sorry</strong>',
        icon: 'info',
        html: 'Material exam not ready',
        showCloseButton: true,
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: 'Close',
    });
}
$(function() {
    $('.show_notrady').on('click', function(e) {
        e.preventDefault();
        console.log('di click');

        Swal.fire({
            title: '<strong>Sorry</strong>',
            icon: 'info',
            html: '<i>Material exam not ready</i>',
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            cancelButtonText: 'Close',
        });
    });
})
</script>