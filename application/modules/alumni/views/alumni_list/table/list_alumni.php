<table id="alumni_list" class="table table-bordered table-hover">
    <thead class="bg-dark">
        <tr>
            <th>Alumni Name</th>
            <th>Thesis Title</th>
            <th>Student ID</th>
            <th>Tahun Masuk</th>
            <th>Tahun Lulus</th>
            <th>Faculty</th>
            <th>Study Program</th>
            <th>Institution/Company</th>
            <th>Data Tracer</th>
            <th>Last Submit Tracer Study</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
<script>
    var alumni_list = $('table#alumni_list').DataTable({
        processing: true,
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Download Data Table',
                extend: 'excel',
                title: 'Alumni List Data',
                exportOptions: {columns: ':visible'}
            },
            {
                text: 'Download Tracer List',
                action: function ( e, dt, button, config ) {
                    window.location.href = '<?=base_url()?>download/download_tracer_alumni';
                },
                // extend: 'pdf',
                // title: 'Alumni List Data',
                // exportOptions: {columns: ':visible'}
            },
            // {
            //     text: 'Print',
            //     extend: 'print',
            //     title: 'Alumni List Data',
            //     exportOptions: {columns: ':visible'}
            // },
            // 'colvis'
        ],
        ajax:{
            url: '<?=base_url()?>student/filter_student_alumni',
            type: 'POST',
            data: function(params) {
                let a_form_data = $('form#filter_alumni_form').serialize();
                return a_form_data;
            }
        },
        columns: [
            {data: 'personal_data_name'},
            {data: 'student_thesis_title'},
            {data: 'student_number'},
            {data: 'academic_year_id'},
            {data: 'graduated_year_id'},
            {data: 'faculty_abbreviation'},
            {data: 'study_program_abbreviation'},
            {
                data: 'company_data',
                render: function(data, type, rows) {
                    if (data) {
                        return rows.institution_name;
                    }
                    else{
                        return '';
                    }
                }
            },
            {
                data: 'answer_tracer_data',
                render: function(data, type, row) {
                    return (data) ? 'OK' : '';
                }
            },
            {data: 'last_submit_tracer'},
            {
                data: 'personal_data_id',
                orderable: false,
                render: function(data, type, rows) {
                    
                    var html = '<div class="btn-group" role="group" aria-label="">';
                    if (rows['list_answer_dikti']) {
						html += '<a class="btn btn-sm btn-info" href="<?=site_url('alumni/question_answer/')?>' + data + '" target="_blank"><i class="fa fa-eye"></i></a>';
                    }

                    if (rows.thesis_final_fname !== '') {
                        var thesis_final_link = '<?=base_url()?>thesis/view_file/thesis_final/' + rows.thesis_final_fname;
                        html += '<a class="btn btn-sm btn-success" href="' + thesis_final_link + '" target="_blank"><i class="fa fa-download"></i></a>';
                    }

                <?php
                if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                ?>
                    html += '<a class="btn btn-sm btn-primary" href="<?=base_url()?>devs/submit_student_thesis_file_form/' + rows['student_id'] + '" target="_blank"><i class="fa fa-newspaper"></i></a>';
                <?php
                }
                ?>
                    
                    html += '</div>';
                    return html
                }
            }
        ]
    });

    $(function() {
        $('button#btn_filter_alumni').on('click', function(e) {
            e.preventDefault();

            alumni_list.ajax.reload();
        });
    });
</script>