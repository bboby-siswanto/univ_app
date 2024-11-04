<script src="<?= base_url() ?>assets/vendors/chart.js/js/Chart.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script>

<div class="row">
    <div class="col-12">
        <div class="btn-group mb-2 float-right" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-info" id="btn_responden_view">Responden View</button>
            <button type="button" class="btn btn-info" id="btn_tablelist_view">Table Result View</button>
            <button type="button" class="btn btn-info" id="btn_graph_view">Graph Result View</button>
        </div>
    </div>
</div>
<div id="card-filter-result" class="card">
    <div class="card-header">
        Student Satisfaction Filter
    </div>
    <div class="card-body">
        <form onsubmit="return false" id="form_result_satisfaction">
        <input type="hidden" name="assessment_id" value="<?=$assessment_id;?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="study_program_id">Study Program</label>
                        <select name="study_program_id" id="study_program_id" class="form-control">
                            <option value="all">All</option>
                    <?php
                    if ($study_program_list) {
                        foreach ($study_program_list as $o_study_program) {
                    ?>
                            <option value="<?=$o_study_program->study_program_id;?>"><?=$o_study_program->study_program_name;?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-info float-right" id="btn_filter_result">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="card-responden" class="card d-none">
    <div class="card-header">
        Responden List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_respondentlist_result" class="table table-hover table-border">
                <thead class="bg-dark">
                    <tr>
                        <th>Study Program</th>
                        <th>Total Respondent</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th>Total Responden</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div id="card-result" class="card">
    <div class="card-header">
        Question Result
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_questionlist_result" class="table table-hover table-border">
                <thead class="bg-dark">
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Question</th>
                        <th <?= ($option_list) ? 'colspan="'.count($option_list).'"' : '';?>>Result</th>
                    </tr>
                    <tr>
                    <?php
                    if ($option_list) {
                        foreach ($option_list as $o_option) {
                    ?>
                        <th><?=$o_option->option_name;?></th>
                    <?php
                        }
                    }
                    ?>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div id="card-graph" class="card d-none">
    <div class="card-header">Graph Result</div>
    <div class="card-body">
        <div class="row">
<?php
    // if ($question_list) {
    //     foreach ($question_list as $o_question) {
?>
            <div class="col-md-12">
                <div class="chart-wrapper w-100">
                    <canvas class="chart" id="chart_q_1"></canvas>
                </div>
                <div class="chart-wrapper w-100">
                    <canvas class="chart" id="chart_q_1_pie"></canvas>
                </div>
            </div>
<?php
    //     }
    // }
?>
        </div>
        <div class="row">
<?php
    // if ($question_list) {
    //     foreach ($question_list as $o_question) {
?>
            <div class="col-md-12">
                <div class="chart-wrapper w-100">
                    
                </div>
            </div>
<?php
    //     }
    // }
?>
        </div>
    </div>
</div>
<script>

var option_list = JSON.parse('<?=($option_list) ? json_encode($option_list) : "";?>');
var ctx = document.getElementById("chart_q_1").getContext("2d");
var ctxpie = document.getElementById("chart_q_1_pie").getContext("2d");
var mainChart;
var pieChart;
var data_option = [];
var data_option_id = [];
var data_result = [];
console.log(option_list);
$.each(option_list, function(i, v) {
    data_option.push(v.option_name);
    data_option_id.push(v.assessment_option_id);
});

var table_respondentlist_result = $('#table_respondentlist_result').DataTable({
    ordering: false,
    paging: false,
    searching: false,
    info: false,
    ajax: {
        url: '<?=base_url()?>validation_requirement/university_assessment/get_responden_result',
        type: 'POST'
    },
    columns: [
        {data: 'study_program_name'},
        {data: 'total_responden'}
    ],
    footerCallback: function (row, data, start, end, display) {
        var api = this.api();
        var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        };
        // Total over this page
        pageTotal = api
            .column(1, { page: 'current' })
            .data()
            .reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

        // Update footer
        $(api.column(1).footer()).html(pageTotal + ' Students');
    }
});

var table_questionlist_result = $("#table_questionlist_result").DataTable({
    ordering: false,
    paging: false,
    searching: false,
    info: false,
    ajax: {
        url: '<?=base_url()?>validation_requirement/university_assessment/get_question_result',
        type: 'POST',
        data: function(params) {
            let a_form_data = $('form#form_result_satisfaction').serialize();
            // var a_filter_data = objectify_form(a_form_data);
            return a_form_data;
        }
    },
    columns: [
        {data: 'question_number'},
        {data: 'question_name'},
    <?php
    if ($option_list) {
        foreach ($option_list as $o_option) {
    ?>
        {data: 'result_option_<?=$o_option->assessment_option_id;?>'},
    <?php
        }
    }
    else {
    ?>
        {data: ''}
    <?php
    }
    ?>
    ]
});

function get_chart_data() {
    let a_form_data = $('form#form_result_satisfaction').serialize();
    
    $.post('<?=base_url()?>validation_requirement/university_assessment/get_question_result', a_form_data, function(result) {
        var hexindex = 0;
        data_result = [];
        var a_hex_color = ['#aee4ff', '#6a9cfd', '#ffb8do', '#fee5e1', '#033495'];
        $.each(result.data, function(i, v) {
            var dataresult = [];
            $.each(data_option_id, function(idx, vopt) {
                if (vopt == 1) {
                    dataresult.push(v.result_option_1);
                }
                else if (vopt == 2) {
                    dataresult.push(v.result_option_2);
                }
                else if (vopt == 3) {
                    dataresult.push(v.result_option_3);
                }
                else if (vopt == 4) {
                    dataresult.push(v.result_option_4);
                }
                else if (vopt == 5) {
                    dataresult.push(v.result_option_5);
                }
            });
            
            data_result.push({
                'label': v.question_name,
                'backgroundColor': a_hex_color[hexindex],
                'borderColor': a_hex_color[hexindex],
                'pointHoverBackgroundColor': '#fff',
                'borderWidth': 2,
                'data': dataresult
            });
            hexindex++;
        });

        reinitialize_chart(data_result, null);
        if (typeof mainChart != 'undefined') {
            mainChart.update();
        }
        if (typeof pieChart != 'undefined') {
            pieChart.update();
        }
    }, 'json').fail(function(params) {
        toastr.error('error retrieve result!');
    });
}

function reinitialize_chart(dataresult, dataresultpie) {
    mainChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data_option,
            datasets: (dataresult === null) ? [] : dataresult
        },
        options: {
            legend: {
                display: true,
                position: 'top',
                align: 'start',
            },
        }
    });

    pieChart = new Chart(ctxpie, {
        type: 'pie',
        data: {
            labels: data_option,
            datasets: (dataresultpie === null) ? [] : dataresultpie
        },
        options: {
            title: {
                display: true,
                text: 'Persentase Hasil',
                fontSize: 29,
                padding: 40
            }
        }
    })
}
    
$(function() {
    get_chart_data();
    $('#btn_filter_result').on('click', function(e) {
        e.preventDefault();
        if (typeof mainChart != 'undefined') {
            var datasetchart = mainChart.data.datasets;
            for (let x = 0; x < datasetchart.length; x++) {
                mainChart.data.datasets.forEach((data_result) => {
                    data_result.data.pop();
                });
            }
            mainChart.destroy();
        }
        
        table_questionlist_result.ajax.reload();
        get_chart_data();
    });

    $('#btn_responden_view').on('click', function(e) {
        e.preventDefault();

        $('#card-responden').removeClass('d-none');
        $('#card-filter-result').addClass('d-none');
        $('#card-result').addClass('d-none');
        $('#card-graph').addClass('d-none');
    })
    $('#btn_tablelist_view').on('click', function(e) {
        e.preventDefault();

        $('#card-responden').addClass('d-none');
        $('#card-filter-result').removeClass('d-none');
        $('#card-result').removeClass('d-none');
        $('#card-graph').addClass('d-none');
    })
    $('#btn_graph_view').on('click', function(e) {
        e.preventDefault();

        $('#card-responden').addClass('d-none');
        $('#card-filter-result').removeClass('d-none');
        $('#card-result').addClass('d-none');
        $('#card-graph').removeClass('d-none');
    })
});
</script>