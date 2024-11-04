<div class="table-responsive">
    <table id="roles_pages_table" class="table table-striped table-hover">
        <thead class="bg-dark">
            <tr>
                <th width="50px">No</th>
                <th>Pages Name</th>
                <th>Pages Description</th>
                <th>Pages URI</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<script>
    $(function() {
        var roles_pages_table = $('table#roles_pages_table').DataTable({
            processing: true,
            ajax: {
                url: '<?=base_url()?>devs/pages/get_pages',
                type: 'POST'
            },
            columns:[
                { data: 'pages_id', orderable: false },
                { data: 'pages_name' },
                { data: 'pages_description' },
                { data: 'pages_uri' },
                {
                    data: 'pages_id',
                    orderable: false,
                    render: function(data, type, rows) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        html += '<button type="button" id="remove_pages" class="btn btn-danger btn-sm" title="Remove from roles"><i class="fas fa-trash"></i></button>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });

        pages_table.on( 'order.dt search.dt', function () {
            pages_table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    });
</script>