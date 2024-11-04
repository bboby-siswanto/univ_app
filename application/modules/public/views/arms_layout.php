<header class="app-header navbar">
    <!-- <a class="navbar-brand" href="<?= base_url()?>">
        <img class="navbar-brand-full" src="<?=base_url()?>assets/img/iuli.png" height="100%" alt="IULI">
        <img class="navbar-brand-minimized" src="<?=base_url()?>assets/img/iuli.png" height="100%" alt="IULI">
    </a> -->
    <ul class="nav navbar-nav d-md-down-none">
        <li class="nav-item px-3">
            <h5 class="text-white">
                Attendance Recording Management System
            </h5>
        </li>
    </ul>
    <ul class="nav navbar-nav ml-auto d-md-down-none pr-3">
        <li class="nav-item">
            <a class="text-white" href='#' id="clockbox"></a>
        </li>
    </ul>
    <div class="d-lg-none cst-header-name"></div>
</header>
<div class="app-body">
    <main class="w-100">
        <div class="container-fluid">
            <div class="pb-3"></div>
            <div id='search' style='display: block;' class='text-center'>
                <p class='text-center'>
                    <img src='<?=base_url();?>assets/img/howto.png' class='img-responsive' style='margin: 0 auto;'/>
                </p>
            </div>
        </div>
    </main>
</div>
<!--// SUCCESS //-->
<div id='scanSuccess' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-success">
                <h4 class="modal-title success"><i class='fa fa-check-circle'></i> Attendance Recorded</h4>
            </div>
            <div class="modal-body">
                <div class='row'>
                    <div class='col-md-3 col-xs-3 text-center'>
                        <img src='<?=base_url();?>assets/img/howto.png' id='photo' class='img-thumbnail'/>
                    </div>
                    <div class='col-md-9 col-xs-9 text-justify'>
                        <h1 id='name' class='caps' style='padding: 0px; margin: 0px;'></h4>
                        <h2 id='nip' style='padding: 0px;'>NIP: 123456</h2>
                        <h2 id='timeclock' style='padding: 0px;'><i class='fa fa-clock-o'></i> <span>00:00</span></h2>
                        <h3 class='text-success'>Note: Your attendance has been recorded.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--// END SUCCESS //-->
<!--// ERROR //-->
<div id='scanError' class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-danger">
                <h4 class="modal-title"><i class='fa fa-times-circle'></i> Undentified Badge</h4>
            </div>
            <div class="modal-body">
                <h2 class='text-danger'>Your Badge is not registered. Please contact IULI HR Dept.</h2>
                <h3 class='text-success'>Note: Your attendance has been recorded.</h3>
            </div>
        </div>
    </div>
</div>
<!--// END ERROR //-->
<script>
tday=new Array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
tmonth=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

function GetClock(){
    var d=new Date();
    var nday=d.getDay(),nmonth=d.getMonth(),ndate=d.getDate(),nyear=d.getFullYear();
    var nhour=d.getHours(),nmin=d.getMinutes(),nsec=d.getSeconds();
    if(nmin<=9) nmin="0"+nmin
    if(nsec<=9) nsec="0"+nsec;

    document.getElementById('clockbox').innerHTML=""+tday[nday]+", "+tmonth[nmonth]+" "+ndate+", "+nyear+" "+nhour+":"+nmin+":"+nsec+"";
}

window.onload=function(){
    GetClock();
    setInterval(GetClock,1000);
}
$(function() {
    var myTimeout;
    var hid_key = '';
    var actual_number = '';
    var wanted = {
        '13': 'ENTER',
        '48': '0',
        '49': '1',
        '50': '2',
        '51': '3',
        '52': '4',
        '53': '5',
        '54': '6',
        '55': '7',
        '56': '8',
        '57': '9',
        '92': '--',
        '103': '++',
        '98': '//'
    };
    $(document).on('keypress', function(e){
        // console.log(e.which);
        $('#scanSuccess, #scanError').modal('hide');
        clearTimeout(myTimeout);
        pressed_key = e.which;
        if(pressed_key in wanted){
            actual_number = wanted[pressed_key];
            if(actual_number == 'ENTER'){
                if(hid_key.length >= 1){
                    submitForm(hid_key);
                    // console.log(hid_key);
                    hid_key = '';
                }
            }
            else {
                hid_key += actual_number;
            }
        } else
            hid_key = '';
    });
    
    var submitForm = function(key) {
        $.blockUI();
        var url = '<?=base_url()?>/hris/api/post_attendance';
        var postBody = {hidkey: key};
        $.post(url, postBody, function(rtn){
            // console.log(rtn);
            var d = new Date();
            var hour = d.getHours();
            var min = d.getMinutes();
            hour = addZero(hour);
            min = addZero(min);
            // HoldOn.close();
            $.unblockUI();
            if(rtn.code == 0){
                // HoldOn.close();
                $('#photo').prop('src', rtn.photo);
                $('#name').html(rtn.name);
                $('#nip').html(rtn.nip);
                $('#timeclock span').html(rtn.time_current + ' ('+ rtn.att_type +')');
                $('#scanSuccess').modal('show');
                
                myTimeout = setTimeout(function(){
                    $('#scanSuccess').modal('hide');
                    $('#photo').prop('src', '');
                    $('#name').html('');
                    $('#nip').html('');
                    $('#timeclock span').html('');
                }, 3000);
            } else {
                $('#scanError').modal('show');

                myTimeout = setTimeout(function(){
                    $('#scanError').modal('hide');
                }, 5000);
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing your attendance!');
        });
    }
    
    function addZero(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }
})
</script>