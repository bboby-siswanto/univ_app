<?php
class Layout extends App_core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->view('layout');
	}
	
	public function compose_modal($a_compose_modal_config)
	{
		$s_modal_title = $a_compose_modal_config['modal_title'];
		$s_modal_id = trim(str_replace(' ', '_', strtolower($a_compose_modal_config['modal_title']))).'_modal';
		$s_modal_body = $a_compose_modal_config['modal_body'];
		
		$s_modal_html = <<<TEXT
<div class="modal" tabindex="-1" role="dialog" id="{$s_modal_id}">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{$s_modal_title}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				{$s_modal_body}
			</div>
		</div>
	</div>
</div>
TEXT;
		
        return $s_modal_html;
	}

	public function generate_modals($s_module_name, $mbs_sub_module = false)
	{
		$this->config->load('button_config');
		$b_module_exists = false;
		$a_module_config = $this->config->item('module');
		if ($mbs_sub_module) {
			if (isset($a_module_config[$s_module_name][$mbs_sub_module])) {
				$b_module_exists = true;
			}
		}
		else if (isset($a_module_config[$s_module_name])) {
			$b_module_exists = true;
		}

		$s_modal_html = '';
		if ($b_module_exists) {
			$a_modal_config = ($mbs_sub_module) ? $a_module_config[$s_module_name][$mbs_sub_module] : $a_module_config[$s_module_name];
			foreach($a_modal_config as $key => $value){
				if ($value['type'] == 'modal') {
					$a_compose_modal_config = array(
						'modal_title' => $value['title'],
						'modal_body' => modules::run($value['target'], true)
					);
					$s_modal_html .= $this->compose_modal($a_compose_modal_config);
					print $s_modal_html;
				}
				$s_modal_html .= "\n\n";
			}
		}
		return $s_modal_html;
	}

	public function generate_buttons($s_module_name, $mbs_sub_module = false)
	{	
		$this->config->load('button_config');
		$a_module_config = $this->config->item('module');
		
		$a_btn_config = [];
		if ($mbs_sub_module) {
			if (isset($a_module_config[$s_module_name][$mbs_sub_module])) {
				$a_btn_config = $a_module_config[$s_module_name][$mbs_sub_module];
			}
		}
		else if (isset($a_module_config[$s_module_name])) {
			$a_btn_config = $a_module_config[$s_module_name];
		}
		
		$s_btn_html = '';
		foreach($a_btn_config as $key => $value){
			switch($value['type'])
			{
				case "link":
					$s_param = '';
					if(isset($value['include_params'])){
						// $s_param = '\'+row[\'personal_data_id\']+\'';
						$s_param = '\'+data+\'';
					}
					$s_target = ((isset($value['properties']['target'])) AND ($value['properties']['target'] !== null)) ? $value['properties']['target'] : '';
					$s_btn_html .= anchor(site_url($value['target']).$s_param, $value['properties']['content'], array('class' => $value['properties']['class'], 'title' => $value['properties']['title'], 'target' => $s_target));
					break;
					
				case "modal":
					$s_btn_html .= form_button($value['properties']);
					break;
					
				case "action":
					break;
			}
		}
		return $s_btn_html;
	}
}