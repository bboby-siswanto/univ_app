<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header" style="background-color: #001489 !important;">
                    <div class="row">
                        <div class="col-lg-5 my-auto text-center">
                            <img src="<?= base_url()?>assets/img/iuli.png" class="img-fluid"/>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form url="<?=base_url()?>apps/sponsor/create_invoice" id="form_create_open">
                        <div class="h2 text-center mb-3">IULIFest Sponsorship</div>
                        <hr>
                        <div class="row pt-2">
                            <div class="col-sm-3 required_text">
                                Sponsor Name
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="sponsor_name" id="sponsor_name" class="form-control">
                            </div>
                        </div>
                        <div class="row pt-2">
                            <div class="col-sm-3 required_text">
                                Email
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="sponsor_email" id="sponsor_email" class="form-control">
                            </div>
                        </div>
                        <div class="row pt-2">
                            <div class="col-sm-3 required_text">
                                Amount
                            </div>
                            <div class="col-sm-9">
                                <input type="number" name="sponsor_amount" id="sponsor_amount" class="form-control">
                            </div>
                        </div>
                        <div class="row pt-2">
                            <div class="col-sm-3">
                                Nominal Transfer
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="sponsor_amount_nom" id="sponsor_amount_nom" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row pt-2">
                            <div class="col-sm-12">
                                <button class="btn btn-primary float-right" id="btn_submit" type="button">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- please transfer the exact amount of money for identification -->
<script>
$(function() {
    $('#sponsor_amount').on('keyup', function(e) {
        e.preventDefault();
        
        $.post('<?=base_url()?>apps/sponsor/get_amount', {amount: $('#sponsor_amount').val()}, function(result) {
            $('#sponsor_amount_nom').val(result.am);
        }, 'json').fail(function(params) {
            // 
        });
    });

    $('#btn_submit').on('click', function(e) {
        e.preventDefault();

        var form = $('#form_create_open');
        var url = form.attr('url');
        var data = form.serialize();

        $.post(url, data, function(result) {
            if (result.code == 0) {
                Swal.fire({
                    title: 'Thank You',
                    text: 'Invoice has been generated, if it is not automatically generated, please click the link below or contact the officer',
                    footer: '<a href="<?=base_url()?>apps/sponsor/generate_invoice/' + result.inumber + '" target="blank">Download Invoice?</a>'
                });

                setTimeout(function(){
			        window.location.replace(result.redirectURL);
		        }, 2000);
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(a) {
            $('#invoice_open_id').val('');
            toastr.error('Error request invoice, please contact Call Office IULI!');
        });
    });
})
</script>