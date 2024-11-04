<?php
class Api extends Api_core
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('personal_data/Personal_data_model', 'Pdm');
    }

	public function set_sgs_promo()
	{
		$a_post_data = $this->a_api_data;
		
		$s_referrer_id = $a_post_data['referrer_id'];
		$s_referenced_id = $a_post_data['referenced_id'];
		
		$a_prepare_referrer_data = array(
			'referrer_id' => $s_referrer_id,
			'referenced_id' => $s_referenced_id
		);
		
		$this->Pdm->set_sgs_promo($a_prepare_referrer_data);
	}

    public function get_reference_code()
    {
        $a_post_data = $this->a_api_data;

        $s_reference_code = $a_post_data['reference_code'];
        // $s_referenced_id = $a_post_data['']
        $mbo_reference_data = $this->Pdm->get_reference_code($s_reference_code);

        $a_return = array('code' => (($mbo_reference_data) ? 0 : 1), 'data' => $mbo_reference_data);
        $this->return_json($a_return);
    }
}
