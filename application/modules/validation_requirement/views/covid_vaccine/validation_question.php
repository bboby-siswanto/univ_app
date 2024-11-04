<?php
if (($this->session->userdata('vaccine_covid') !== null) AND ($this->session->userdata('vaccine_covid') == false)) {
    echo modules::run('validation_requirement/vaccine/modal_input');
?>
<script>
Swal.fire({
    title: 'Have you been vaccinated Covid-19?',
    icon: 'warning',
    showCancelButton: true,
    allowOutsideClick: false,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes!',
    cancelButtonText: 'No'
}).then((result) => {
    if (result.value) {
        $('div#modal-input-vaccine').modal('show');
    }
    else{
        $.post('<?=base_url()?>validation_requirement/submit_confirmation', {valid: 'valid'}, function(result) {
            if (result.code == 0) {
                Swal.fire(
                    'Thank You',
                    'if you want to make changes to the data, please go to the covid vaccine certificate page by going to the profile page and then selecting the covid vaccine certificate menu !',
                    'success'
                );
            }else{
                toastr.warning(result.message);
            }
        }, 'json').fail(function(e) {
            toastr.error('Fail processing your data!', 'Error');
        });
    }
});
</script>
<?php
}
?>