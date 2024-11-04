<div class="table-responsive">
    <table id="ofse_lists_table" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Study Program</th>
                <th>Examiner 1</th>
                <th>Examiner 2</th>
                <th>OFSE Status</th>
                <th>Count Student</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    $(function() {
        var ofse_table_lists = $('table#ofse_lists_table').DataTable({
            ajax: {
                // url: '<?= base_url()?>academic/ofse/get_ofse_subject_lists',
                url: '<?= base_url()?>academic/ofse/get_ofse_subject_class',
                type: 'POST',
                data: function(params) {
                    let a_form_data = $('form#form_ofse_filter').serializeArray();
                    var a_filter_data = objectify_form(a_form_data);
                    return a_filter_data;
                }
            },
            columns: [
                {data: 'subject_code'},
                {data: 'subject_name'},
                {data: 'study_program_abbreviation'},
                {data: 'examiner_ofse'},
                {data: 'examiner_ofse'},
                {data: 'ofse_status'},
                {data: 'student_count'},
                {data: 'class_group_id'}
            ],
            columnDefs: [
                {
                    targets: -1,
                    orderable: false,
                    render: function(data, type, row) {
                        var html = '';
                        if (data != null) {
                            html += '<div class="btn-group" role="group" aria-label="">';
                            html += '<?= $btn_html?>';
                            html += '</div>';
                        }
                        return html;
                    }
                },
                {
                    targets: 3,
                    render: function(data, type, row) {
                        let lect = row.a_examiner_ofse;
                        if (lect.length > 0) {
                            return lect[0];
                        }else{
                            return 'N/A';
                        }
                    }
                },
                {
                    targets: 4,
                    render: function(data, type, row) {
                        let lect = row.a_examiner_ofse;
                        if (lect.length > 1) {
                            return lect[1];
                        }else{
                            return 'N/A';
                        }
                    }
                },
                {
                    targets: 5,
                    render: function(data, type, row) {
                        if (data != null) {
                            return data.replace('_', ' ').toUpperCase();
                        }else{
                            return '';
                        }
                    }
                }
            ]
        });

        $('button#filter_ofse').on('click', function(e) {
            e.preventDefault();
            ofse_table_lists.ajax.reload();
        });

        $('button#download_subject_examiner').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            let url = '<?= base_url()?>academic/ofse/generate_ofse_csv';
            download_ofse_template(url);
        });

        $('button#download_subject_student_ofse').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            let url = '<?= base_url()?>academic/ofse/generate_ofse_student';
            download_ofse_template(url);
        });

        $('button#download_ofse_registration_structure').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            let url = '<?= base_url()?>academic/ofse/generate_ofse_registration_structure';
            download_ofse_template(url);
        });

        $('button#download_ofse_result').on('click', function(e) {
            e.preventDefault();
            $.blockUI();

            let url = '<?= base_url()?>academic/ofse/generate_ofse_result';
            download_ofse_template(url);
        });

        function download_ofse_template(url) {
            var data = $('form#form_ofse_filter').serialize();
            $.post(url , data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    window.location.href = '<?= base_url()?>file_manager/download_template/' + result.file;
                }else{
                    toastr['error'](result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
            });
        }
    });
</script>