<script src="<?= base_url() ?>assets/vendors/chart.js/js/Chart.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.6.0"></script> -->
<!-- <script src="<?=base_url()?>assets/vendors/chart.js/js/chartjsplugin/chartjs-plugin-datalabels.min.js"></script> -->

<div class="card">
    <div class="card-header">
        <?=($employee_data) ? $employee_data[0]->employee_name : 'Employee Not Found!'; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="list_history_teaching" class="table table-bordered table-hover">
                <thead class="bg-dark">
                    <tr>
                        <!-- <th>Academic Semester</th> -->
                        <th>Subjects that have been taught</th>
                        <!-- <th>Study Program</th>
                        <th>Count Student</th>
                        <th>Absence Lecturer</th> -->
                    </tr>
                </thead>
                <tbody>
        <?php
        if ($class_list) {
            foreach ($class_list as $o_class) {
        ?>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-link" id="btn_show_report" 
                                data-username="<?=($employee_data) ? $employee_data[0]->employee_name : ''; ?>" 
                                data-lecturer="<?=$o_class->employee_id;?>" 
                                data-subject="<?=$o_class->subject_name;?>" 
                                data-sks="<?=$o_class->curriculum_subject_credit;?>"
                            ><?=$o_class->subject_name;?></button>
                        </td>
                    </tr>
        <?php
            }
        }
        ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_lecturer_report">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lecturer Teaching Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <span id="lecturer_name" class="h5"></span>
                    </div>
                    <div class="col-sm-6 float-right">
                        <span id="subject_name_text" class="float-right h5"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" id="employee_id" name="employee_id">
                        <input type="hidden" id="subject_name" name="subject_name">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" id="table_view-tab" data-toggle="tab" href="#table_view" role="tab" aria-controls="table_view" aria-selected="true">Table View</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" id="graph_view-tab" data-toggle="tab" href="#graph_view" role="tab" aria-controls="graph_view" aria-selected="false">Graph View</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade" id="table_view" role="tabpanel" aria-labelledby="table_view-tab">
                                <table id="table_lecturer_teaching_report" class="table table-hover table-border">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th>Academic Semester</th>
                                            <th>Study Program</th>
                                            <th>Count Student</th>
                                            <th>Lecturer Absence (in hours)</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="tab-pane fade show active" id="graph_view" role="tabpanel" aria-labelledby="graph_view-tab">
                                <div class="w-100">
                                    <canvas id="chart_result"></canvas>
                                </div>
                            </div>
                        </div>
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
var ctxline = document.getElementById("chart_result").getContext("2d");
let list_history_teaching = $('#list_history_teaching').DataTable({
    paging: false,
    info: false,
    // ordering: false
});
let table_lecturer_teaching_report = $('#table_lecturer_teaching_report').DataTable({
    paging: false,
    info: false,
    ordering: false,
    searching: false,
    ajax: {
        url: '<?= base_url()?>academic/class_group/get_lecturer_teaching',
        type: 'POST',
        data: function(d){
            d.employee_id = $('#employee_id').val(),
            d.subject_name = $('#subject_name').val()
        },
        complete: function (data) {
            set_chart_data(data.responseJSON.data);
        }
    },
    columns: [
        {
            data: 'running_year',
            render: function(data, type, row) {
                return row.running_year + ' ' + row.semester_type_name;
            }
        },
        {data: 'class_prodi'},
        {data: 'student_class'},
        {data: 'total_time_absence'},
    ]

});
$(function() {
    $('table#list_history_teaching tbody').on('click', 'button#btn_show_report', function(e) {
        e.preventDefault();

        var lecturer = $(this).data('lecturer');
        var subject = $(this).data('subject');
        var lectname = $(this).data('username');
        var credit = $(this).data('sks');

        $('#lecturer_name').text(lectname);
        $('#subject_name_text').text(subject + '(' + credit + ' SKS)');
        $('#employee_id').val(lecturer);
        $('#subject_name').val(subject);
        $('#modal_lecturer_report').modal('show');

        table_lecturer_teaching_report.ajax.reload();
    });

    $('#modal_lecturer_report').on('hidden.bs.modal', function (e) {
        $('#employee_id').val('');
        $('#subject_name').val('');
    });
});

function set_chart_data(datatable = false) {
    var data_option = []
    var dataresult = []
    
    $.each(datatable, function(i, v) {
        let option_name = v.running_year + ' ' + v.semester_type_name;
        data_option.push(option_name);
        dataresult.push(v.total_time_absence);
    });
    var data_result = [{
        'label': 'Total Attendance (in Hours)',
        'backgroundColor': "rgb(247, 246, 187)",
        'borderColor': "rgb(17, 66, 50)",
        'pointHoverBackgroundColor': '#fff',
        'borderWidth': 2,
        'data': dataresult
    }]
    // console.log(data_option);
    if (typeof mainChart != 'undefined') {
        mainChart.destroy();
    }

    mainChart = new Chart(ctxline, {
        type: 'line',
        data: {
            labels: data_option,
            datasets: data_result
        },
        options: {
            legend: {
                display: true,
                position: 'top',
                align: 'start',
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}
var dynamicColors = function() {
    var r = Math.floor(Math.random() * 255);
    var g = Math.floor(Math.random() * 255);
    var b = Math.floor(Math.random() * 255);
    return "rgb(" + r + "," + g + "," + b + ")";
};
</script>