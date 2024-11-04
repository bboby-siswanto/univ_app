<?php
if (in_array('student', $assessment_purpose)) {
?>
<div class="row">
    <div class="col-sm-6 col-lg-4">
        <div class="form-group">
            <label for="assessment_prodi">Study Program</label>
            <div class="input-group mb-3">
                <select name="assessment_prodi[]" id="assessment_prodi" class="form-control selectpicker" multiple data-live-search="true" data-actions-box="true">
                    <option value="all">All</option>
        <?php
            if ((isset($study_program_list)) AND ($study_program_list)) {
                foreach ($study_program_list as $o_study_program) {
            ?>
                    <option value="<?=$o_study_program->study_program_id;?>" selected="selected"><?=$o_study_program->study_program_name;?></option>
            <?php
                }
            }
        ?>
                </select>
                <div class="input-group-append">
                    <button class="btn btn-info btn-sm" type="button" id="btn_filter_assessment_result">Filter</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <a href="<?=base_url()?>validation_requirement/university_assessment/responden_view" target="blank" class="btn btn-info btn-sm float-right" id="btn_reponden">Responden View</a>
    </div>
</div>
<?php
}
?>
<div class="table-responsive">
    <table id="table_questionlist_result" class="table table-hover table-border">
        <thead class="bg-dark">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Question</th>
                <th <?= ($option_list) ? 'colspan="'.count($option_list).'"' : '';?>>Result</th>
                <th rowspan="2">#</th>
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
<div class="row">
    <div class="col-12">
        <div class="col-md-12">
            <div class="chart-wrapper w-100">
                <canvas class="chart" id="chart_bar"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_question_chart">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="body_modal_chart">
            <canvas class="chart" id="question_chart_bar"></canvas>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>
<script>
var option_list = JSON.parse('<?=($option_list) ? json_encode($option_list) : "[]";?>');
var ctx = document.getElementById("chart_bar").getContext("2d");
var modalctx = document.getElementById("question_chart_bar").getContext("2d");
var prodimulti = $('#assessment_prodi').selectpicker();
var data_option = [];
var data_option_id = [];
var data_result = [];
$.each(option_list, function(i, v) {
    data_option.push(v.option_name);
    data_option_id.push(v.assessment_option_id);
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
            params.assessment_id = '<?=$assessment_id;?>';
    <?php
    if (in_array('student', $assessment_purpose)) {
    ?>
            params.study_program_id = $('#assessment_prodi').val();
    <?php
    }
    ?>
            return params;
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
        {
            data: 'question_id',
            render: function(data, type, row) {
                return '<button type="button" class="btn btn-info" id="btn_view_graph_question"><i class="fa fa-chart-bar"></i></button>';
            }
        }
    ],
    fnDrawCallback: function( oSettings ) {
        let jsondata = oSettings.json;
        var data_result = [];
        if (jsondata !== undefined) {
            var dynamicColors = function() {
                var r = Math.floor(Math.random() * 255);
                var g = Math.floor(Math.random() * 255);
                var b = Math.floor(Math.random() * 255);
                return "rgb(" + r + "," + g + "," + b + ")";
            };
            $.each(jsondata.data, function(i, v) {
                var dataresult = [];
                $.each(data_option_id, function(idx, vopt) {
                    var resultoption_value = eval('v.result_option_' + vopt);
                    dataresult.push(resultoption_value);
                });
                
                var set_color = dynamicColors();
                data_result.push({
                    'label': v.question_name,
                    'backgroundColor': set_color,
                    'borderColor': set_color,
                    'pointHoverBackgroundColor': '#fff',
                    'borderWidth': 2,
                    'data': dataresult
                });
                // hexindex++;
            });

            reinitialize_chart(data_result, null);
            if (typeof mainChart != 'undefined') {
                mainChart.update();
            }
        }
    }
});

$(function() {
    $('#table_questionlist_result').on('click', '#btn_view_graph_question', function(e) {
        var tabledata = table_questionlist_result.row($(this).parents('tr')).data();
        var dynamicColors = function() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return "rgb(" + r + "," + g + "," + b + ")";
        };

        var dataresult = [];
        $.each(data_option_id, function(idx, vopt) {
            var resultoption_value = eval('tabledata.result_option_' + vopt);
            dataresult.push(resultoption_value);
        });
        console.log(dataresult);
        
        var set_color = dynamicColors();
        var data_result = [
            {
                'label': tabledata.question_name,
                'backgroundColor': set_color,
                'borderColor': set_color,
                'pointHoverBackgroundColor': '#fff',
                'borderWidth': 2,
                'data': dataresult
            }
        ];
        reinitialize_modal_chart(data_result, null);
        if (typeof modalChart != 'undefined') {
            modalChart.update();
        }

        $('#modal_question_chart').modal('show');
    });

    $('#myModal').on('hidden.bs.modal', function (e) {
        $('#body_modal_chart').empty();
        $('#body_modal_chart').html('<canvas class="chart" id="question_chart_bar"></canvas>');
    })
<?php
if (in_array('student', $assessment_purpose)) {
?>
    $('#btn_filter_assessment_result').on('click', function(e) {
        e.preventDefault();

        table_questionlist_result.ajax.reload();
    })
    // $('#assessment_prodi').on('change', function(e) {
    //     e.preventDefault();

    //     table_questionlist_result.ajax.reload();
    // })
<?php
}
?>
});

function reinitialize_chart(dataresult) {
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
}
function reinitialize_modal_chart(dataresult) {
    if (typeof modalChart != 'undefined') {
        modalChart.destroy();
    }
    modalChart = new Chart(modalctx, {
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
}
</script>