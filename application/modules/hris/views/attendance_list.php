<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-1">Search: </div>
            <div class="col-sm-4">
                <select name="att_employee" id="att_employee" class="form-control form-control-sm">
                    <option value=""></option>
<?php
    if ($employee_list) {
        foreach ($employee_list as $o_employee) {
?>
                    <option value="<?=$o_employee->personal_data_id;?>"><?=$o_employee->personal_data_name;?></option>
<?php
        }
    }
?>
                </select>
            </div>
            <div class="col-sm-4">
                <input type="text" name="att_month" id="att_month" class="form-control form-control-sm">
            </div>
            <div class="col-sm-3">
                <button type="button" id="btn_att_search" class="btn btn-info btn-sm float-right">Search</button>
            </div>
            <div class="col-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                gambar dan data karyawan
            </div>
            <div class="col-sm-9">
                <div class="table-responsive">
                    <table id="attendance_list" class="table table-sm table-bordered table-hover">
                        <thead class="bg-dark">
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>In</th>
                                <th>Out</th>
                                <th id="att_LI">LI</th>
                                <th id="att_EO">EO</th>
                                <th id="att_EWH">EWH</th>
                                <th id="att_SWH">SWH</th>
                                <th>Notes</th>
                                <th id="att_OT">OT</th>
                                <th id="att_AL">AL</th>
                                <th id="att_RD">RD</th>
                                <th id="att_S">S</th>
                                <th id="att_A">A</th>
                                <th id="att_O">O</th>
                                <th id="att_ACT"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Wednesday</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>Berangkat Pelatihan PDDIKTI ke bandung</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                                <td>08:00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var date = new Date();
// $( "input#att_month" ).datepicker( "setDate", date);
var current_month = new Date(date.getFullYear(), date.getMonth(), date.getDate());
var datepicker_selected = $('input#att_month').datepicker({
    dateFormat: 'MM yy',
    showButtonPanel: true,
    defaultDate: 'today',
    maxDate: current_month,
    onClose: function(dateText, inst) { 
        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
}).on('focus', function () {
    $(".ui-datepicker-calendar").hide();
    $("#ui-datepicker-div").position({
        my: "top",
        at: "bottom",
        of: $(this)
    });
});

var employee_list = $('#att_employee').select2({
    placeholder: 'Please select ...',
    theme: "bootstrap",
    minimumInputLength: 3
});

var attendance_list = $('table#attendance_list').DataTable({
    ordering: false,
    paging: false,
    info: false,
    searching: false,
    processing: true,
    ajax: {
        url: '<?= base_url()?>hris/api/get_attendance_employee',
        type: 'POST',
        data: function(d){
            d.personal_data_id = $('#att_employee').val(),
            d.date_selected = $('#att_month').val()
            // d.date_selected = $("#att_month").datepicker( 'getDate' ); // ini ga bisa dipake
        }
    },
    columns: [
        {data: 'date'},
        {data: 'day'},
        {data: 'log_checkin'},
        {data: 'log_checkout'},
        {data: 'log_late_in'},
        {data: 'log_early_out'},
        {data: 'log_working_hour'},
        {data: 'hid_standar_working_hour'},
        {data: 'hid_id'},
        {data: 'hid_id'},
        {data: 'hid_id'},
        {data: 'hid_id'},
        {data: 'hid_id'},
        {data: 'hid_id'},
        {data: 'hid_id'},
        {data: 'hid_id'}
    ],
    createdRow: function( row, data, dataIndex){
        if (data.day == 'Sun') {
            $(row).addClass('bg-secondary');
        }
        else if (data.day == 'Sat') {
            $(row).addClass('bg-secondary');
        }
    },
});

$(function() {
    datepicker_selected.datepicker( "setDate", date);
    var year = 2020;
    var month = 2;
    var datemonth = Math.round(((new Date(year, month))-(new Date(year, month-1)))/86400000);
    
    $('#att_LI').tooltip({title: "Late In"});
    $('#att_EO').tooltip({title: "Early Out"});
    $('#att_EWH').tooltip({title: "Effective Working Hour"});
    $('#att_SWH').tooltip({title: "Standar Working Hour"});
    $('#att_OT').tooltip({title: "Over Time"});
    $('#att_AL').tooltip({title: "Annual Leave"});
    $('#att_RD').tooltip({title: "Replacement Day"});
    $('#att_S').tooltip({title: "Sick"});
    $('#att_A').tooltip({title: "Absent"});
    $('#att_O').tooltip({title: "Other"});
    $('#att_ACT').tooltip({title: "Action"});

    $('button#btn_att_search').on('click', function(e) {
        e.preventDefault();

        // var date_selected = new Date($('#att_month').val());
        // var month = date_selected.getMonth();
        // var month_real = month + 1 ;
        // var year = date_selected.getFullYear();
        // var month_text = date_selected.toLocaleString('default', { month: 'long' });
        // var total_date_in_month = Math.round(((new Date(year, month_real))-(new Date(year, month_real-1)))/86400000);
        // console.log(total_date_in_month);
        attendance_list.ajax.reload();
    });
})
</script>