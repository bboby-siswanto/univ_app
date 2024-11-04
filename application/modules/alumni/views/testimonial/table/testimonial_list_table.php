<div class="table-responsive">
    <table id="testimonial" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th width="20px">No</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Testimoni</th>
                <th>Date Added</th>
                <th>Timestamp</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    $(function() {
        var table_testimoni = $('table#testimonial').DataTable({
            ajax: {
                url: '<?=base_url()?>alumni/testimonial/get_testimonial_list',
                type: 'POST',
                data: false
            },
            columns: [
                {
                    data: 'testimonial_id',
                    orderable: false,
                    render: function ( data, type, row, meta ) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'student_number'},
                {data: 'personal_data_name'},
                {data: 'testimoni'},
                {
                    data: 'date_added',
                    // render: $.fn.dataTable.render.moment( 'Do    MMM YYYYY' )
                },
                {data: 'timestamp'}
            ]
        });
    });
</script>