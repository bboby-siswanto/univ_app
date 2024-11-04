<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />  
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css" />   -->
<!-- <script src=" https://cdn.jsdelivr.net/npm/fullcalendar@6.1.6/index.global.min.js "></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
<style> 
    #calendar {  
        width: 100%;
        margin: 0 auto;  
    }
    .cke_dialog
    {
        z-index: 10055 !important;
    }
</style>  

<div class="card">
    <div class="card-header">
        Calendar
    </div>
    <div class="card-body">
        <div id='calendar'></div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="modal_event_calendar">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form url="<?=base_url()?>hris/jobreport/submit_eventcalendar" onsubmit="return false" id="form_eventcalendar">
                    <input type="hidden" name="calendar_event_report_id" id="calendar_event_report_id">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="calendar_event_title" class="required_text">Title</label>
                                <input type="text" class="form-control" name="calendar_event_title" id="calendar_event_title" <?= ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') ? 'value="work list"' : '' ?>>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="calendar_event_category" class="required_text">Category</label>
                                <select class="form-control" name="calendar_event_category" id="calendar_event_category">
                                    <option value="working list">Working List</option>
                                    <option value="event">Event</option>
                                    <option value="holiday">Holiday</option>
                            <?php
                            if ($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                            ?>
                                    <option value="bithday">Birthday</option>
                            <?php
                            }
                            ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="calendar_event_date" class="required_text">Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="calendar_event_start_date" id="calendar_event_start_date" readonly="">
                                    <input type="text" class="form-control" name="calendar_event_end_date" id="calendar_event_end_date" readonly="">
                                    <!-- <div class="input-group-append">
                                        <span class="input-group-text">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="true" name="calendar_event_allday" id="calendar_event_allday" checked="" readonly="">
                                                <label class="form-check-label" for="calendar_event_allday">
                                                    All Day
                                                </label>
                                            </div>
                                        </span>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="calendar_event_type" class="required_text">Type</label>
                                <select class="form-control" name="calendar_event_type" id="calendar_event_type">
                                    <option value="personal">Personal</option>
                                    <option value="public">Public</option>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="col-sm-6">
                            <div class="form-group">
                                <label for="calendar_event_assignor">Assignor</label>
                                <input type="text" class="form-control" name="calendar_event_assignor" id="calendar_event_assignor">
                                <input type="hidden" class="form-control" name="calendar_event_assignor_id" id="calendar_event_assignor_id">
                            </div>
                        </div> -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="calendar_event_desc" class="required_text">Description</label>
                                <textarea name="calendar_event_desc" id="calendar_event_desc"></textarea>
                                <input type="hidden" name="body_calendar_event_desc" id="body_calendar_event_desc">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_eventcalendar">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {  
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    CKEDITOR.replace('calendar_event_desc');

    var calendar = $('#calendar').fullCalendar({  
        editable: true,
        eventDurationEditable: false,
        eventStartEditable: false,
        disableResizing: true,
        disableDragging: true,
        header: {  
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        events: "get_event",
        eventRender: function(event, element, view) {  
            // console.log(event);
            element.draggable = false;
            element.resizable = false;
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },  
        selectable: true,  
        selectHelper: true,  
        select: function(start, end, allDay) {  
            // var title = prompt('Event Title:');  
            var startdate = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
            var enddate = $.fullCalendar.formatDate(end, "Y-MM-DD 23:59:59");
            var enddate = new Date(enddate);
            enddate.setDate(enddate.getDate() - 1);
            // var enddate = $.fullCalendar.formatDate(enddate, "Y-MM-DD HH:mm:ss");
            
            var enddate = [enddate.getFullYear(), ("0" + (enddate.getMonth() + 1)).slice(-2), ("0" + enddate.getDate()).slice(-2)].join('-')+' '+
              [("0" + enddate.getHours()).slice(-2), ("0" + enddate.getMinutes()).slice(-2), ("0" + enddate.getSeconds()).slice(-2)].join(':');

            $('#calendar_event_report_id').val('');
            $('#calendar_event_title').val('<?=($this->session->userdata('user') == '47013ff8-89df-11ef-8f45-0068eb6957a0') ? 'work list' : '';?>');
            $('#body_calendar_event_desc').val('');
            $('#calendar_event_start_date').val(startdate);
            $('#calendar_event_end_date').val(enddate);
            var oEditor =  CKEDITOR.instances.calendar_event_desc;
            oEditor.setData('');

            $('#modal_event_calendar').modal('show');
            // console.log(startdate);
            // console.log(enddate);
            calendar.fullCalendar('unselect');  
        },
        editable: true,  
        // eventDrop: function(event, delta) {  
        //     var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");  
        //     var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");  
        //     console.log(event);
        // },  
        eventClick: function(info) {
            var startdate = $.fullCalendar.formatDate(info._start, "Y-MM-DD HH:mm:ss");
            if (info._end === null) {
                var enddate = $.fullCalendar.formatDate(info._start, "Y-MM-DD 23:59:59");
            }else {
                var enddate = $.fullCalendar.formatDate(info._end, "Y-MM-DD 23:59:59");
            }
            
            console.log(info.id);
            $('#calendar_event_report_id').val(info.id);
            $('#calendar_event_title').val(info.title);
            $('#calendar_event_start_date').val(startdate);
            $('#calendar_event_end_date').val(enddate);
            $('#calendar_event_category').val(info.category);
            $('#calendar_event_type').val(info.type);
            var oEditor =  CKEDITOR.instances.calendar_event_desc;
            oEditor.setData(info.description);
            
            $('#modal_event_calendar').modal('show');
        },
        // eventResize: function(event) {
        //     return false;
        //     var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");  
        //     var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");  
        //     console.log(event);
        // }
    });

    $('#btn_save_eventcalendar').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var email_message = CKEDITOR.instances.calendar_event_desc.getData();
        $('input#body_calendar_event_desc').val(email_message);

        var form = $("#form_eventcalendar");
        var data = form.serialize();
        var url = form.attr('url');
        
        // console.log($('input#body_calendar_event_desc').val);
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success', 'Success!');
                calendar.fullCalendar('refetchEvents');
                $('#modal_event_calendar').modal('hide');
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('error processing data!', 'Error');
        });
    });
    
});
  
</script>