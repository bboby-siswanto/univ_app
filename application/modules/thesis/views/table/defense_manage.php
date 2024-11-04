<div class="row">
    <div class="col-12">
        <div class="btn-group float-right mb-2">
            <button type="button" id="submit_manage_defense" class="btn btn-success">Submit</button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Student List
        <div class="card-header-actions">
            <button class="card-header-action btn btn-link" id="retrieve_student">
                <i class="fa fa-eye"></i> Retrieve Student
            </button>
        </div>
    </div>
    <div class="card-body">
        <form url="<?=base_url()?>thesis/submit_defense_collective" id="form_sc_data" onsubmit="return false">
        <input type="hidden" name="academic_semester" value="<?=(isset($academic_semester)) ? $academic_semester : '';?>">
            <div class="table-responsive">
                <table id="defense_student_list" class="table table-border table-hover">
                    <thead class="bg-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Prodi</th>
                            <th>Defense Room</th>
                            <th>Defense Date</th>
                            <th>Defense Time</th>
                            <th>Advisor</th>
                            <th>Examiner</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                if ($defense_list) {
                    foreach ($defense_list as $o_defense) {
                ?>
                        <tr>
                            <td>
                                <?=$o_defense->personal_data_name;?>
                                <input type="hidden" name="thesis_student_id[]" id="thesis_student_id" value="<?=$o_defense->thesis_students_id;?>">
                            </td>
                            <td><?=$o_defense->student_number;?></td>
                            <td><?=$o_defense->study_program_abbreviation;?></td>
                            <td>
                                <?=$o_defense->thesis_defense_room;?>
                                <input type="hidden" name="defense_sc_room[]" id="defense_sc_room" value="<?=$o_defense->thesis_defense_room;?>">
                            </td>
                            <td>
                                <?=$o_defense->thesis_defense_date;?>
                                <input type="hidden" name="defense_sc_date[]" id="defense_sc_date" value="<?=$o_defense->thesis_defense_date;?>">
                            </td>
                            <td>
                                <?=$o_defense->thesis_defense_time_start.'-'.$o_defense->thesis_defense_time_end;?>
                                <input type="hidden" name="defense_sc_time[]" id="defense_sc_time" value="<?=$o_defense->thesis_defense_time_start.'-'.$o_defense->thesis_defense_time_end;?>">
                            </td>
                            <td>
                                <?php
                                if ($o_defense->advisor_data) {
                                    foreach ($o_defense->advisor_data as $o_advisor) {
                                ?>
                                <?=$o_advisor->personal_data_name;?>/
                                <?php
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($o_defense->examiner_data) {
                                    foreach ($o_defense->examiner_data as $o_examiner) {
                                ?>
                                <?=$o_examiner->personal_data_name;?>/
                                <input type="hidden" name="<?=$o_examiner->examiner_type;?>" value="<?=$o_examiner->advisor_id;?>">
                                <?php
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Group">
                                    <button type="button" class="btn btn-info btn-defense" id="btn-defense" title="Manage Defense"><i class="fas fa-calendar"></i></button>
                                    <button type="button" class="btn btn-danger btn-remove" id="btn-remove" title="Remove Row"><i class="fas fa-minus"></i></button>
                                </div>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_retrieve_student">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">List Student Approved Thesis Work</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-reponsive">
                    <table id="table_list_student" class="table table-border table-hover">
                        <thead class="bg-dark">
                            <tr>
                                <th></th>
                                <th>Student Name</th>
                                <th>Student ID</th>
                                <th>Thesis Title</th>
                                <th>Advisor</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_retrieve_student_data">Retrieve</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_defense_detail">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Defense Detail</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sc_room" class="required_text">Room</label>
                        <select name="sc_room" id="sc_room" class="form-control v_value">
                            <option value="R702">R702</option>
                            <option value="R703">R703</option>
                            <option value="R704">R704</option>
                            <option value="R705">R705</option>
                            <option value="R707">R707</option>
                            <option value="R715">R715</option>
                            <option value="R717">R717</option>
                            <option value="R718">R718</option>
                            <option value="R719">R719</option>
                            <option value="R720">R720</option>
                            <option value="R721">R721</option>
                            <option value="R722">R722</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sc_date" class="required_text">Date</label>
                        <input type="date" name="sc_date" id="sc_date" class="form-control v_value">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sc_time_start" class="required_text">Time</label>
                        <div class="row">
                            <div class="col-6">
                                <select name="sc_time_start" id="sc_time_start" class="form-control v_value">
                        <?php
                        for ($i=8; $i <=21 ; $i++) { 
                            $text_start = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':00:00' : $i.':00:00';
                            $text_start_15 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':15:00' : $i.':15:00';
                            $text_start_30 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':30:00' : $i.':30:00';
                            $text_start_45 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':45:00' : $i.':45:00';
                        ?>
                                    <option value="<?=$text_start;?>"><?= $text_start?></option>
                                    <option value="<?=$text_start_15;?>"><?= $text_start_15?></option>
                                    <option value="<?=$text_start_30;?>"><?= $text_start_30?></option>
                                    <option value="<?=$text_start_45;?>"><?= $text_start_45?></option>
                        <?php
                        }
                        ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="sc_time_end" id="sc_time_end" class="form-control v_value">
                        <?php
                        for ($i=9; $i <=22 ; $i++) {
                            $text_end = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':00:00' : $i.':00:00';
                            $text_end_15 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':15:00' : $i.':15:00';
                            $text_end_30 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':30:00' : $i.':30:00';
                            $text_end_45 = (strlen($i) == 1) ? str_pad($i, 2, "0", STR_PAD_LEFT).':45:00' : $i.':45:00';
                        ?>
                                    <option value="<?=$text_end;?>"><?= $text_end?></option>
                                    <option value="<?=$text_end_15;?>"><?= $text_end_15?></option>
                                    <option value="<?=$text_end_30;?>"><?= $text_end_30?></option>
                                    <option value="<?=$text_end_45;?>"><?= $text_end_45?></option>
                        <?php
                        }
                        ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sc_examiner_1" class="required_text">Examiner 1</label>
                        <select name="sc_examiner_1" id="sc_examiner_1" class="form-control">
                            <option value=""></option>
                    <?php
                    if ($examiner_list) {
                        foreach ($examiner_list as $o_examiner) {
                    ?>
                            <option value="<?=$o_examiner->advisor_id;?>" data-institute="<?=$o_examiner->institution_name;?>"><?=$o_examiner->personal_data_name;?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sc_examiner_2">Examiner 2</label>
                        <select name="sc_examiner_2" id="sc_examiner_2" class="form-control">
                            <option value=""></option>
                    <?php
                    if ($examiner_list) {
                        foreach ($examiner_list as $o_examiner) {
                    ?>
                            <option value="<?=$o_examiner->advisor_id;?>" data-institute="<?=$o_examiner->institution_name;?>"><?=$o_examiner->personal_data_name;?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sc_examiner_3">Examiner 3</label>
                        <select name="sc_examiner_3" id="sc_examiner_3" class="form-control">
                            <option value=""></option>
                    <?php
                    if ($examiner_list) {
                        foreach ($examiner_list as $o_examiner) {
                    ?>
                            <option value="<?=$o_examiner->advisor_id;?>" data-institute="<?=$o_examiner->institution_name;?>"><?=$o_examiner->personal_data_name;?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sc_examiner_4">Examiner 4</label>
                        <select name="sc_examiner_4" id="sc_examiner_4" class="form-control">
                            <option value=""></option>
                    <?php
                    if ($examiner_list) {
                        foreach ($examiner_list as $o_examiner) {
                    ?>
                            <option value="<?=$o_examiner->advisor_id;?>" data-institute="<?=$o_examiner->institution_name;?>"><?=$o_examiner->personal_data_name;?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btn_add_defense_detail">Add</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>
<script>
var defense_student_list = $('#defense_student_list').DataTable({
    ordering: false,
    paging: false,
    info: false
});
var table_list_student = $('#table_list_student').DataTable({
    ordering: false,
    paging: false,
    select: {
        style: 'multi'
    },
    ajax:{
        url: '<?=base_url()?>thesis/get_student_available_defense',
        type: 'POST',
        data: function(params) {
            // let a_form_data = $('form#form_filter_thesis_work').serialize();
            // return a_form_data;
            return false
        }
    },
    columns: [
        {
            data: 'thesis_student_id',
            className: 'select-checkbox',
            render: function(data, type, row) {
                var html = '<input type="hidden" value="' + data + '" name="thesis_id">';
                return html;
            }
        },
        {
            data: 'personal_data_name'
        },
        {data: 'student_number'},
        {data: 'thesis_title'},
        {data: 'advisor'}
    ],
});
var index_defense_selected = 0;
var btn_defense = '<div class="btn-group btn-group-sm" role="group" aria-label="Group">';
    btn_defense += '<button type="button" class="btn btn-info btn-defense" id="btn-defense" title="Manage Defense"><i class="fas fa-calendar"></i></button>';
    btn_defense += '<button type="button" class="btn btn-danger btn-remove" id="btn-remove" title="Remove Row"><i class="fas fa-minus"></i></button>';
    btn_defense += '</div>';

$(function() {
    $('#retrieve_student').on('click', function(e) {
        e.preventDefault();

        $('#modal_retrieve_student').modal('show');
    });

    $('#defense_student_list tbody').on( 'click', '.btn-remove', function () {
        defense_student_list.row( $(this).parents('tr') ).remove().draw();
    } );

    $('#sc_examiner_1, #sc_examiner_2, #sc_examiner_3, #sc_examiner_4').select2({
        minimumInputLength: 2,
        allowClear: true,
        placeholder: "Please select",
        theme: "bootstrap",
        dropdownParent: $('#modal_defense_detail'),
        templateResult: formatoption
    });
    
    $('#submit_manage_defense').on('click', function(e) {
        e.preventDefault();
        
        var form = $("#form_sc_data");
        var url = form.attr('url');
        var data = form.serialize();
        var defense_data = [];
        var datalist = defense_student_list.rows().data();
        for (let i = 0; i < datalist.length; i++) {
            const element = datalist[i];
            // var thesis_student_id = defense_student_list.row(i).data();
            var data = {};
            data['thesis_student_id'] = defense_student_list.cell(i, 0).nodes().to$().find('input').val();
            data['thesis_room'] = defense_student_list.cell(i, 3).nodes().to$().find('input').val();
            data['thesis_date'] = defense_student_list.cell(i, 4).nodes().to$().find('input').val();
            data['thesis_time'] = defense_student_list.cell(i, 5).nodes().to$().find('input').val();
            data['thesis_examiner1'] = defense_student_list.cell(i, 7).nodes().to$().find('input[name="examiner_1"]').val();
            data['thesis_examiner2'] = defense_student_list.cell(i, 7).nodes().to$().find('input[name="examiner_2"]').val();
            data['thesis_examiner3'] = defense_student_list.cell(i, 7).nodes().to$().find('input[name="examiner_3"]').val();
            data['thesis_examiner4'] = defense_student_list.cell(i, 7).nodes().to$().find('input[name="examiner_4"]').val();
            
            defense_data.push(data);
        }
        // console.log(defense_data);

        $.post(url, {data: defense_data}, function(result) {
            // 
        }, 'json').fail(function(params) {
            toastr.error('Error processing data!');
        });
    });

    $('#btn_retrieve_student_data').on('click', function(e) {
        e.preventDefault();

        var data_selected = table_list_student.rows('.selected').data();
        if (data_selected.length > 0) {
            // defense_student_list.rows().remove();
            $.each(data_selected, function(i, v) {
                var student_name = v.personal_data_name + '<input type="hidden" name="thesis_student_id[]" id="thesis_student_id" value="' + v.thesis_student_id + '">';
                defense_student_list.row.add([student_name, v.student_number, v.study_program_name, '', '','',v.advisor,'',btn_defense]).draw();
            });

            table_list_student.ajax.reload();
            $('#modal_retrieve_student').modal('hide');
        }
        else {
            toastr.warning('Please select at least one student!');
        }
    });

    $('table#defense_student_list tbody').on('click', '.btn-defense', function(e) {
        e.preventDefault();
        index_defense_selected = defense_student_list.row($(this).parents('tr')).index();
        defense_selected = defense_student_list.row($(this).parents('tr')).data();
        var defense_room = defense_student_list.cell(index_defense_selected, 3).nodes().to$().find('input').val();
        var defense_date = defense_student_list.cell(index_defense_selected, 4).nodes().to$().find('input').val();
        var defense_time = defense_student_list.cell(index_defense_selected, 5).nodes().to$().find('input').val();
        var examiner1 = defense_student_list.cell(index_defense_selected, 7).nodes().to$().find('input[name="examiner_1"]').val();
        var examiner2 = defense_student_list.cell(index_defense_selected, 7).nodes().to$().find('input[name="examiner_2"]').val();
        var examiner3 = defense_student_list.cell(index_defense_selected, 7).nodes().to$().find('input[name="examiner_3"]').val();
        var examiner4 = defense_student_list.cell(index_defense_selected, 7).nodes().to$().find('input[name="examiner_4"]').val();

        if ((defense_room) && (defense_date) && (defense_time) && (examiner1)) {
            defense_time = defense_time.split('-');
            $('#sc_room').val(defense_room);
            $('#sc_date').val(defense_date);
            $('#sc_time_start').val(defense_time[0]);
            $('#sc_time_end').val(defense_time[1]);
        }
        else {
            $('#sc_room').val('');
            $('#sc_date').val('');
            $('#sc_time_start').val('')
            $('#sc_time_end').val('')
        }
        
        if (examiner1) {$('#sc_examiner_1').val(examiner1).trigger('change');}else{$('#sc_examiner_1').val('').trigger('change')}
        if (examiner2) {$('#sc_examiner_2').val(examiner2).trigger('change')}else{$('#sc_examiner_2').val('').trigger('change')}
        if (examiner3) {$('#sc_examiner_3').val(examiner3).trigger('change')}else{$('#sc_examiner_3').val('').trigger('change')}
        if (examiner4) {$('#sc_examiner_4').val(examiner4).trigger('change')}else{$('#sc_examiner_4').val('').trigger('change')}

        $('#modal_defense_detail').modal('show');
    });

    $('#btn_add_defense_detail').on('click', function(e) {
        e.preventDefault();

        var data_existing = defense_student_list.row(index_defense_selected).data();
        var sc_time = $('#sc_time_start').val() + '-' + $("#sc_time_end").val();
        var sc_room = $('#sc_room').val();
        var sc_date = $('#sc_date').val();

        var sc_examiner1 = $('#sc_examiner_1').val();
        var sc_examiner2 = $('#sc_examiner_2').val();
        var sc_examiner3 = $('#sc_examiner_3').val();
        var sc_examiner4 = $('#sc_examiner_4').val();

        sc_time += '<input type="hidden" name="defense_sc_time[]" id="defense_sc_time" value="' + sc_time + '">';
        sc_room += '<input type="hidden" name="defense_sc_room[]" id="defense_sc_room" value="' + sc_room + '">';
        sc_date += '<input type="hidden" name="defense_sc_date[]" id="defense_sc_date" value="' + sc_date + '">';
        var sc_examiner = '';

        if (sc_examiner1 != '') {
            sc_examiner += $('#sc_examiner_1').select2('data')[0].text + '/<input type="hidden" name="examiner_1" value="' + sc_examiner1 + '">';
        }
        if (sc_examiner2 != '') {
            sc_examiner += $('#sc_examiner_2').select2('data')[0].text + '/<input type="hidden" name="examiner_2" value="' + sc_examiner2 + '">';
        }
        if (sc_examiner3 != '') {
            sc_examiner += $('#sc_examiner_3').select2('data')[0].text + '/<input type="hidden" name="examiner_3" value="' + sc_examiner3 + '">';
        }
        if (sc_examiner4 != '') {
            sc_examiner += $('#sc_examiner_4').select2('data')[0].text + '<input type="hidden" name="examiner_4" value="' + sc_examiner4 + '">';
        }

        var detail_data = [data_existing[0], data_existing[1], data_existing[2], sc_room, sc_date, sc_time, data_existing[6], sc_examiner, btn_defense];
        defense_student_list.row(index_defense_selected).data( detail_data ).draw();
    })
});

function formatoption (option) {
  if (!option.id) {
    return option.text;
  }
  var dataoption = option.element.dataset;
  var $option = $(
    '<span>' + option.text + '<br><small>' + dataoption.institute + '</small></span>'
  );
  return $option;
};
</script>