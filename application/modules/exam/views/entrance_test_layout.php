<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IULI PMB</title>
    <meta name="description" content="IULI PMB">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url()?>assets/img/iuli-owl.png">

    <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/vendors/fontawesome/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/vendors/sweetalertmaster/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/css/iuli.css" rel="stylesheet">
    
    <script src="<?=base_url()?>assets/vendors/jquery/js/jquery.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/popper.js/js/popper.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/pace-progress/js/pace.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/@coreui/coreui/js/coreui.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/sweetalertmaster/sweetalert2.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/jquery/js/jquery.blockUI.js"></script>
    <script src="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.js" type="text/javascript"></script>
    <style>
        #section_2 {
            display: none;
        }
        .countdown {
            position: fixed;
            bottom: 5px;
            right: 10px;
        }
        .timer {
            margin: 0 0 45px;
            font-family: sans-serif;
            color: #fff;
            display: inline-block;
            font-weight: 100;
            text-align: center;
            font-size: 30px;
        }
        .timer div {
            padding: 10px;
            border-radius: 3px;
            background: #001489;
            display: inline-block;
            font-family: Oswald;
            font-size: 26px;
            font-weight: 400;
            width: 80px;
        }
        .timer .smalltext {
            color: #888888;
            font-size: 12px;
            font-family: Poppins;
            font-weight: 500;
            display: block;
            padding: 0;
            width: auto;
        }
        .timer #time-up {
            margin: 8px 0 0;
            text-align: left;
            font-size: 14px;
            font-style: normal;
            color: #001489;
            font-weight: 500;
            letter-spacing: 1px;
        }
        .footer_end {
            margin-bottom: 50px;
        }
    </style>
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
    <header class="app-header navbar">
        <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="#">
            <img class="navbar-brand-full" src="<?= base_url()?>assets/img/iuli.png" height="100%" alt="IULI">
            <img class="navbar-brand-minimized" src="<?= base_url()?>assets/img/iuli.png" height="100%" alt="IULI">
        </a>
        <!-- <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
            <span class="navbar-toggler-icon"></span>
        </button> -->
    </header>
    <div class="app-body">
        <div class="mt-3 container-fluid" id="section_1">
            <?=$body;?>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="end_exam" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title title_ending">
                    </h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body message_ending">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="ending_exam">Finish</button>
                </div>
            </div>
        </div>
    </div>
    <div class="countdown">
        <div class="timer">
            <div>
                <span class="hours" id="hour"></span> 
                <div class="smalltext">Hours</div>
            </div>
            <div>
                <span class="minutes" id="minute"></span> 
                <div class="smalltext">Minutes</div>
            </div>
            <div>
                <span class="seconds" id="second"></span> 
                <div class="smalltext">Seconds</div>
            </div>
            <p id="time-up"></p>
        </div>
    </div>
    <script>
        var time_now = new Date().getTime();
        
        var finish_section = '<?=(isset($finish)) ? $finish : ""?>';
        var section_id = '<?=$section_id;?>';
        
        var dbase_time = "<?=$end_time;?>";
        var btime = new Date(dbase_time);
        var ua = navigator.userAgent;
        var msie = ua.indexOf("MSIE ") > -1 || ua.indexOf("Trident/") > -1;
        if (msie) // If Internet Explorer, return version number
        {
            var dateArray = dbase_time.split(/\D/);
            
            var deadline = new Date(dateArray[0], (dateArray[1] - 1), dateArray[2], dateArray[3], dateArray[4], dateArray[5]).getTime();
        }
        else  // If another browser, return 0
        {
            var deadline = new Date(dbase_time).getTime();
        }
        
        var s_header_message = '<?= $this->config->item('message_finish_entrancetest')['title'] ?>';
        var s_body_message = '<?= $this->config->item('message_finish_entrancetest')['message'] ?>';

        // console.log(s_body_message);
        // var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        // var user_time = btime.toLocaleString("en-US", {timeZone: timezone});
        // var server_time = new Date().toLocaleString("en-US", {timeZone: "Asia/Jakarta"});
        // // console.log('-----------');
        // console.log((new Date(server_time)));
        // console.log(server_time);
        
        var x = setInterval(function() {
            var server_time = new Date().toLocaleString("en-US", {timeZone: "Asia/Jakarta"});
            var currentTime = new Date(server_time).getTime();
            var t = deadline - currentTime;
            var days = Math.floor(t / (1000 * 60 * 60 * 24));
            var hours = Math.floor((t % (1000 * 60 * 60 * 24))/(1000 * 60 * 60)); 
            var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60)); 
            var seconds = Math.floor((t % (1000 * 60)) / 1000);
            document.getElementById("hour").innerHTML =hours; 
            document.getElementById("minute").innerHTML = minutes; 
            document.getElementById("second").innerHTML =seconds; 
            if (t < 0) {
                clearInterval(x); 
                // window.location.reload();
                // document.getElementById("time-up").innerHTML = "TIME UP"; 
                // document.getElementById("hour").innerHTML ='0'; 
                // document.getElementById("minute").innerHTML ='0' ; 
                // document.getElementById("second").innerHTML = '0'; 
                submit_answer('Time is up', s_body_message);
            } 
        }, 1000);

        $('button#ending_exam').on('click', function(e) {
            e.preventDefault();
            
            if (finish_section != '') {
                window.location.href = '<?=base_url()?>exam/entrance_test/logout';
            }else{
                window.location.reload();
            }
        });
        // console.log(section_id);
        // $('button#next_section').on('click', function(e) {
        //     e.preventDefault();
        //     $('#section_2').show(200, 'swing');
        //     $('#section_1').hide(200, 'swing');
        // });

        // $('button#prev_section').on('click', function(e) {
        //     e.preventDefault();
        //     $('#section_1').show(200, 'swing');
        //     $('#section_2').hide(200, 'swing');
        // });
        
        // $('button#submit_quiz').on('click', function(e) {
        //     e.preventDefault();
        //     // sessionStorage.clear();
        //     // console.log(Object.keys(sessionStorage));
        //     submit_answer('Finish', 'Thank you for finishing the IULI english test, your answers have been saved and sent to the IULI Admission Department. You will get your result in 3 business days.');
        // });

        $('button#submit_this_section').on('click', function(e) {
            e.preventDefault();

            if (finish_section != '') {
                submit_answer(s_header_message, s_body_message);
                $.blockUI();
                $.post('<?=base_url()?>exam/entrance_test/send_notification_finish', function(result) {
                    $.unblockUI();
                }, 'json').fail(function(e) {
                    $.unblockUI();
                });
            }else{
                submit_answer('', '');
            }
        });

        function submit_answer(title_teks, teks) {
            var data = [];
            $.blockUI();
            if (sessionStorage.length > 0) {
                $.each(sessionStorage, function(i, v) {
                    if (i.length == 36) {
                        data.push({
                            question_id: i,
                            question_option_id: v
                        });
                    }
                });
            }
            list_data = {
                answer: data,
                section_id: section_id,
                token: '<?=$token?>',
                action: finish_section
            }
            
            $.post('<?=base_url()?>exam/entrance_test/submit_exam', list_data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    if (finish_section != '') {
                        $('.title_ending').html(title_teks);
                        $('.message_ending').html(teks);
                        $('#end_exam').modal('show');
                    }else{
                        window.location.reload();
                    }
                }else{
                    swal.fire('Warning!',result.message,'warning');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                swal.fire('Error!','Error submiting your answer!','error');
            });
        }
    </script>
</body>
</html>