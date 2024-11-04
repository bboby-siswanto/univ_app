<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Score_reader extends App_core
{
    public $connect;
    private $inbox;
    private $msg_cnt;

    private $server = 'mail.iuli.ac.id';
    private $user   = 'employee@company.ac.id';
    private $pass   = '';
    private $port   = 0;

    // connect to the server and get the inbox emails
    public function __construct() {
        $this->connect();
        $this->inbox();
    }

    public function show_inbox()
    {
        print('<pre>');
        $s_upload_directory = APPPATH.'uploads/score_uploads/tmp/';
        if(!file_exists($s_upload_directory)){
	        mkdir($s_upload_directory, 0755, true);
        }
        
        foreach($this->inbox as $key => $email){
	        
	        $attachments = [];
	        
	        if(isset($email['structure']->parts) && count($email['structure']->parts)){
		        for($i = 0; $i < count($email['structure']->parts); $i++){
			        $attachments[$i] = [
			        	'is_attachment' => FALSE,
			        	'filename' => '',
			        	'name' => '',
			        	'attachment' => ''
					];
					
					if($email['structure']->parts[$i]->ifdparameters){
						foreach($email['structure']->parts[$i]->dparameters as $object){
							// if this attachment is a file, mark the attachment and filename
							if(strtolower($object->attribute) == 'filename'){
								$attachments[$i]['is_attachment'] = TRUE;
								$attachments[$i]['filename'] = $object->value;
							}
						}
					}

					if($email['structure']->parts[$i]->ifparameters){
						foreach($email['structure']->parts[$i]->parameters as $object){
							if(strtolower($object->attribute) == 'name'){
								$attachments[$i]['is_attachment'] = TRUE;
								$attachments[$i]['name'] = $object->value;
							}
						}
					}
					
					if($attachments[$i]['is_attachment']){
						$attachments[$i]['attachment'] = imap_fetchbody($this->conn, $email['index'], $i+1);
						
						if($email['structure']->parts[$i]->encoding == 3){ // 3 = BASE64
							$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
						}
						
						elseif($email['structure']->parts[$i]->encoding == 4){ // 4 = QUOTED-PRINTABLE
							$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
						}
					}
					
					// var_dump($attachments);
		        }
	        }
	        
	        foreach($attachments as $attachment){
		        if($attachment['is_attachment']){
			        // get information on the file
			        $finfo = pathinfo($attachment['filename']);
			        
			        if(
			        	!array_key_exists('extension', $finfo) OR 
			        	!isset($finfo['extension']) OR 
			        	$finfo['extension'] != 'xls'
			        ){
				        // handle rejection here
			        }
			        else{
				        $s_token = md5(time());
				        $s_file = $s_upload_directory.implode('.', [$s_token, $finfo['extension']]);
				        if(@file_put_contents($s_file, $attachment['attachment'])){
					        $this->read_score_file($s_file);
				        }
				        else{
					        var_dump('failed');
				        }
			        }
				}
			}
        }
    }
    
    public function read_score_file($s_file_path)
    {
		$o_spreadsheet = IOFactory::load($s_file_path);
		$sheet = $o_spreadsheet->setActiveSheetIndexByName("Score");
		$s_score_token = $sheet->getCell('C6')->getValue();
		
		// get_class_master_group
		
		$i_score_start = 12;
		
		while($sheet->getCell('B'.$i_score_start)->getValue() != NULL){
			
		}
    }

    // close the server connection
    public function close() {
        $this->inbox = array();
        $this->msg_cnt = 0;
        imap_close($this->conn);
    }
    // open the server connection
    // the imap_open function parameters will need to be changed for the particular server
    // these are laid out to connect to a Dreamhost IMAP server
    public function connect() {
        $this->conn = imap_open('{'.$this->server.'/tls}', $this->user, $this->pass);
    }

    // move the message to a new folder
    public function move($msg_index, $folder='INBOX.Processed') {
        // move on server
        imap_mail_move($this->conn, $msg_index, $folder);
        imap_expunge($this->conn);
        // re-read the inbox
        $this->inbox();
    }
    // get a specific message (1 = first email, 2 = second email, etc.)
    public function get($msg_index=NULL) {
        if (count($this->inbox) <= 0) {
            return array();
        }
        elseif ( ! is_null($msg_index) && isset($this->inbox[$msg_index])) 
        {
            return $this->inbox[$msg_index];
        }
        return $this->inbox[0];
    }

    // read the inbox
    public function inbox() {
        $this->msg_cnt = imap_num_msg($this->conn);
        $in = array();
        for($i = 1; $i <= $this->msg_cnt; $i++) {
            $in[] = array(
                'index'     => $i,
                'header'    => imap_headerinfo($this->conn, $i),
                'body'      => imap_body($this->conn, $i),
                'structure' => imap_fetchstructure($this->conn, $i)
            );
        }
        $this->inbox = $in;
    }
}