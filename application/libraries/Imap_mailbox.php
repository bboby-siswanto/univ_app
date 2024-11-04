<?php
class Imap_mailbox
{
	private $o_connection;
	private $s_server;
	private $s_user;
	private $s_pass;
	private $i_port;
	private $a_inbox_items;
	
	public function __construct($a_params)
	{
		$this->_handle_params($a_params);
		$this->_connect();
		$this->_read_inbox();
	}
	
	private function _handle_params($a_params)
	{
		$a_required_params = ['s_server', 's_user', 's_pass', 'i_port'];
		foreach($a_params as $key => $value){
			if(in_array($key, $a_required_params)){
				$this->{$key} = $value;
			}
			else{
				throw new Exception('Invalid parameter key: '.$key);
			}
		}
	}
	
	private function _connect(){
		$this->o_connection = imap_open('{'.$this->s_server.'/tls}', $this->s_user, $this->s_pass);
	}
	
	private function _read_inbox(){
		$i_count_messages = imap_num_msg($this->o_connection);
		$a_inbox_items = [];
		for($i = 1; $i <= $i_count_messages; $i++){
			$o_structure = imap_fetchstructure($this->o_connection, $i);
			array_push($a_inbox_items, [
				'index' => $i,
				'header' => imap_headerinfo($this->o_connection, $i),
				'body' => imap_body($this->o_connection, $i),
				'structure' => $o_structure,
				'attachments' => $this->get_attachments($i)
			]);
        }
        $this->a_inbox_items = $a_inbox_items;
    }
    
    public function get_attachments($i_email_index)
    {
	    $o_structure = imap_fetchstructure($this->o_connection, $i_email_index);
	    $a_attachments = [];
	    if(isset($o_structure->parts)){
		    for($i = 0; $i < count($o_structure->parts); $i++){
				$b_is_attachment = false;
				$a_attachment_items = [];
				
				if($o_structure->parts[$i]->ifdparameters){
					foreach($o_structure->parts[$i]->dparameters as $object){
						// if this attachment is a file, mark the attachment and filename
						if(strtolower($object->attribute) == 'filename'){
							$b_is_attachment = true;
							$a_attachment_items['filename'] = imap_utf8($object->value);
						}
					}
				}
	
				if($o_structure->parts[$i]->ifparameters){
					foreach($o_structure->parts[$i]->parameters as $object){
						if(strtolower($object->attribute) == 'name'){
							$b_is_attachment = true;
							$a_attachment_items['name'] = imap_utf8($object->value);
						}
					}
				}
				
				if($b_is_attachment){
					// $a_attachment_items['attachment'] = imap_fetchbody($this->o_connection, $i_email_index, 2);
					$s_email_body = imap_fetchbody($this->o_connection, $i_email_index, 2);
					$i_encoding = $o_structure->parts[$i]->encoding;
					
					$a_attachment_items['encoding'] = $i_encoding;
					$a_attachment_items['attachment_header'] = imap_mime_header_decode($s_email_body);
					switch($i_encoding)
					{
/*
						case 0:
							$a_attachment_items['attachment'] = imap_7bit($s_email_body);
							break;
*/
							
						case 1:
							$a_attachment_items['attachment'] = imap_8bit($s_email_body);
							break;
							
						case 2:
							$a_attachment_items['attachment'] = imap_binary($s_email_body);
							break;
						
						case 3:
							$a_attachment_items['attachment'] = imap_base64($a_attachment_items['attachment_header'][0]->text);
							break;
							
						default:
							$a_attachment_items['attachment'] = imap_qprint($s_email_body);
							break;
					}
					array_push($a_attachments, $a_attachment_items);
				}
			}
			return $a_attachments;
	    }
    }
    
    public function move_message($i_message_index, $s_folder='INBOX.Processed') {
	    imap_mail_move($this->o_connection, $i_message_index, $s_folder, CP_UID);
	    imap_expunge($this->o_connection);
	    $this->_read_inbox();
    }
	
	public function get_inbox_items()
	{
		return $this->a_inbox_items;
	}
    
    public function get_message($i_message_index = NULL){
	    if(count($this->a_inbox_items) <= 0){
		    return [];
        }
        else{
	        if(!is_null($i_message_index) && isset($this->a_inbox_items[$i_message_index])){
	            return $this->a_inbox_items[$i_message_index];
	        }
        }
        return $this->a_inbox_items[0];
    }
}