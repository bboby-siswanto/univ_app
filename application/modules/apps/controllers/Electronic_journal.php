<?php
class Electronic_journal extends App_core
{
    function __construct()
	{
		parent::__construct();
		// $this->main_menu = $this->config->item('main');
		// $this->page_number = $this->config->item('page_number');
		// $this->migration = $this->config->item('migration');
		// $this->breadcrumbs = $this->config->item('breadcrumbs');
	}

    public function ejournal($lib2 = false)
    {
		$page = '';
		if ($lib2 == '1') {
			// print('ada');exit;
			// $page = 'https://rzblx1.uni-regensburg.de/ezeit/fl.phtml?bibid=UBIL&colors=7&lang=en';
			$page = 'https://ezb.ur.de/ezeit/fl.phtml?bibid=UBIL&colors=7&lang=en';
		}
		else if ($lib2 == '2') {
			// $page = 'https://rzblx10.uni-regensburg.de/dbinfo/fachliste.php?bib_id=ubil&lett=l&colors=&ocolors=';
			$page = 'https://dbis.ur.de/dbinfo/fachliste.php?bib_id=ubil&lett=l&colors=&ocolors=';
		}

		if (empty($page)) {
			$a_data['page'] = $this->load->view('electronic-journal/journal_menu');
			$a_data['allow_iframe'] = false;
			// print('ada');exit;
		}
		else {
			$a_data['page'] = $page;
			$a_data['allow_iframe'] = true;
		}
        $this->load->view('electronic-journal/ejournal', $a_data);
    }

	public function redirect()
	{
		$db_id = $this->uri->segment(4);
		// print($db_id);exit;
		if($db_id == 1)
		{
			$url = "";
		}

		if ($db_id == 2)
		{
			$url = "";
		}
		$text = <<<HTML
<script type="text/javascript">
document.location.href = "{$url}";
</script>
HTML;
		print $text;
		exit();
	}
}
