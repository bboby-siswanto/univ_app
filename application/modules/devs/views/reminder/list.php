
<div class="card">
    <div class="card-header">
        Filter Reminder Tuition Fee
    </div>
    <div class="card-body">
        <form method="post" id="filter_reminder" onsubmit="return false">
            <div class="row">
            <!-- tanggal, student name, parent email, student email -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="reminder_start_date" class="required_text">Date Range</label>
                        <div class="input-group">
                            <input type="text" id="reminder_start_date" name="reminder_start_date" class="form-control" placeholder="Start Date">
                            <div class="input-group-append">
                                <span class="input-group-text">to</span>
                            </div>
                            <input type="text" id="reminder_end_date" name="reminder_end_date" class="form-control" placeholder="End Date">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="student_name">Student Name</label>
                        <input type="text" class="form-control" name="student_name" id="student_name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="student_email">Student Email</label>
                        <input type="text" class="form-control" name="student_email" id="student_email">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="parent_email">Parent Email</label>
                        <input type="text" class="form-control" name="parent_email" id="parent_email">
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="form-group">
                        <label for="range_date">Date Range</label>
                        <input type="text" class="form-control" name="range_date" id="range_date">
                    </div>
                </div> -->
            </div>
        </form>
        <div class="col-12">
            <button class="btn btn-info float-right" type="button" id="btn_show_reminder">Show Data</button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Reminder Tuition Fee
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="list_reminder" class="table table-bordered table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Batch</th>
                        <th>Prodi</th>
                        <th>Email</th>
                        <th>CC Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    var list_reminder = $('table#list_reminder').DataTable({
        // searching: false,
        // ordering: false,
        processing: true,
        ajax: {
            url: '<?=base_url()?>devs/list_reminder',
            type: 'POST',
            data: function(d){
                d.reminder_start_date = $('#reminder_start_date').val(),
                d.reminder_end_date = $('#reminder_end_date').val()
            }
        },
        columns:[
            { data: 'date'},
            { data: 'personal_data_name'},
            { data: 'academic_year_id'},
            { data: 'study_program_abbreviation'},
            { data: 'email'},
            { data: 'cc'},
            {
                data: 'uid',
                render: function(data, type, rows) {
                    var html = '<div class="btn-group" role="group" aria-label="">';
                    // html += '<button type="button" id="edit_employee" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></button>';
                    // html += '<button type="button" id="remove_employee" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                    // html += '<a href="<?=base_url()?>devs/devs_employee/permission/' + rows.employee_id + '" target="blank" class="btn btn-sm btn-info" title="Permission Access"><i class="fas fa-sitemap"></i></a>';
                    html += '</div>';
                    return html;
                }
            }
        ]
    });

    $(function() {
        var date_now = new Date('<?= date("Y-m-d");?>');
        var date_now = new Date(date_now.getFullYear(), date_now.getMonth(), date_now.getDate());

        var reminder_start = $('#reminder_start_date').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            maxDate: date_now
        }).on('change', function() {
            reminder_end.datepicker( "option", "minDate",  $(this).datepicker('getDate') );
            reminder_end.datepicker('setDate', '');
        });

        var element_date = new Date(reminder_start.val());
        element_date = new Date(element_date.getFullYear(), element_date.getMonth(), element_date.getDate());

        var reminder_end = $('#reminder_end_date').datepicker({
            dateFormat: 'yy-mm-dd',
            changeYear: true,
            changeMonth: true,
            maxDate: date_now,
            minDate: element_date
        });

        $('button#btn_show_reminder').on('click', function(e) {
            e.preventDefault();

            if (($('#reminder_start_date').val() == '') || ($('#reminder_end_date').val() == '')) {
                toastr.warning('Please fill in the date range field!', 'Warning!');
            }else{
                list_reminder.ajax.reload(null, false);
            }
        })
    });
</script>