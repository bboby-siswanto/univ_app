<div class="card">
    <div class="card-header">
        Filter Data
    </div>
    <div class="card-body">
        <form onsubmit="return false" id="filter_participant_entrance_test">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_participant">Select Participant</label>
                        <select name="select_participant" id="select_participant" class="form-control">
                            <option value="all">All</option>
                            <option value="pmb">PMB</option>
                            <option value="event">Event</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4" id="filter_participant_batch">
                    <div class="form-group">
                        <label for="academic_year_id_filter">Batch</label>
                        <select name="academic_year_id" id="academic_year_id_filter" class="form-control">
                            <option value="">All</option>
<?php
    if ($academic_yar_list) {
        foreach ($academic_yar_list as $o_academic_year) {
            $selected = ($o_academic_year->academic_year_intake_status == 'active') ? 'selected="selected"' : '';
?>
                            <option value="<?=$o_academic_year->academic_year_id;?>" <?=$selected;?>><?=$o_academic_year->academic_year_id;?></option>
<?php
        }
    }
?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4" id="filter_participant_event">
                    <div class="form-group">
                        <label for="event_key">Event</label>
                        <select name="event_key" id="event_key" class="form-control">
                            <option value="">All</option>
<?php
if (($event_list !== null) AND ($event_list)) {
    foreach ($event_list as $o_event) {
?>
                            <option value="<?=$o_event->event_id;?>"><?=$o_event->event_name;?></option>
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
                    <button type="button" id="filter_participant" class="btn btn-info float-right">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function(e) {
        $('select#select_participant').on('change', function(e) {
            e.preventDefault();

            if ($('select#select_participant').val() == 'pmb') { 
                $('#filter_participant_event').hide();
                $('#filter_participant_batch').show();
            }
            else if ($('select#select_participant').val() == 'event') {
                $('#filter_participant_event').show();
                $('#filter_participant_batch').hide();
            }
            else {
                $('#filter_participant_event').show();
                $('#filter_participant_batch').show();
            }
        });
    });
</script>