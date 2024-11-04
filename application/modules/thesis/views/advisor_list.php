<div class="card">
    <div class="card-header">Advisor List</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="table_advisor">
                <thead>
                    <tr class="bg-dark">
                        <th>Advisor Name</th>
                <?php
                if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    print('<th>Personal Name</th>');
                }
                ?>
                        <th>Institution</th>
                        <th>Employee Email</th>
                        <th>Login Portal</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
var table_advisor = $('#table_advisor').DataTable({
    dom: 'Bfrtip',
    ajax:{
        url: '<?=base_url()?>thesis/get_list_advisor',
        type: 'POST',
        // data: function(d) {
        //     d.thesis_defense_id = $('#thesis_defense_id').val();
        // }
    },
    columns: [
        {data: 'advisor_name'},
    <?php
    if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
    ?>
        {data: 'personal_data_name'},
    <?php
    }
    ?>
        {data: 'institution_name'},
        {data: 'employee_email'},
        {data: 'advisor_id'},
        {data: 'personal_data_id'}
    ]
});
</script>