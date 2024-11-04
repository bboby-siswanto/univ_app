<div class="card">
    <div class="card-header">
        List Univ Global
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="list-univ">
                <thead class="bg-dark">
                    <tr>
                        <th>ID Kode</th>
                        <th>Kode PT</th>
                        <th>Nama PT</th>
                        <th>Nama Singkat</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<script>
var datalist = $('#list-univ').DataTable({
    ajax: {
        url: '<?= base_url()?>feeder/get_list_university',
        type: 'POST',
        // data: function(params) {
        //     let a_form_data = $('form#form_filter_employee').serialize();
        //     return a_form_data;
        // }
    },
    columns: [
        {data: 'id_perguruan_tinggi'},
        {data: 'kode_perguruan_tinggi'},
        {data: 'nama_perguruan_tinggi'},
        {data: 'nama_singkat'},
    ]
});
</script>