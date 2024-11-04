<div class="card">
    <div class="card-header">
        Validate Account
    </div>
    <div class="card-body">
        <form onsubmit="return false;">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <span>Name</span>
                        </div>
                        <div class="col-md-8">
                            <label>: <?= $personal_data->personal_data_name;?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span>Identification Number</span>
                        </div>
                        <div class="col-md-8">
                            <label>: <?= $personal_data->personal_data_id_card_number;?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span>Place of Birth</span>
                        </div>
                        <div class="col-md-8">
                            <label>: <?= $personal_data->personal_data_place_of_birth;?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span>Date of Birth</span>
                        </div>
                        <div class="col-md-8">
                            <label>: <?= date('d M Y', strtotime($personal_data->personal_data_date_of_birth));?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-3">
                            <span>Nationality</span>
                        </div>
                        <div class="col-md-9">
                            <label>: <?= $personal_data->personal_data_nationality;?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <span>Citizenship</span>
                        </div>
                        <div class="col-md-9">
                            <label>: <?= $personal_data->city_country;?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            Your Token
                        </div>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="input_token" id="input_token" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button" id="start_exam"><i class="fas fa-play-circle"></i> Start</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="show_notif_exam" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Thank you for your interest in IULI. Before you start your entrance test please read the instructions below:
                </h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body">
                <ol>
                    <li>You will have 60 minutes to do your test, which consists of a 35 minutes listening section & a 25 minutes reading section.</li>
                    <li>You can’t undo the answers you have already submitted, so please think carefully</li>
                    <li>After you finish, the answers will be automatically sent to the IULI Admissions Department</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_step">Continue..</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#start_exam').on('click', function(e) {
        e.preventDefault();

        if ($('#input_token').val() == '') {
            $('#input_token').focus();
            swal.fire('Warning!','Please input your token','warning');
        }else{
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: 'Start online Entrance Test ?',
                text: "You cannot cancel or exit it if you have already started the test, the time will continue for 60 minutes!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    start_online();
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                    'Cancelled',
                    '',
                    'error'
                    )
                }
            })
        }

        $('button#submit_step').on('click', function(e) {
            e.preventDefault();
            window.location.href = '<?=base_url()?>exam/entrance_test/exam/' + $('#input_token').val();
        });

        function start_online() {
            $.post('<?=base_url()?>exam/entrance_test/check_in', {token: $('#input_token').val()}, function(result) {
                if (result.code == 0) {
                    // console.log(result);
                    $('#show_notif_exam').modal('show');
                }else{
                    swal.fire('Warning!', result.message,'warning');
                }
            }, 'json').fail(function(params) {
                swal.fire('Error!', 'Error retrieve your token','error');
            });
        }
    });
</script>