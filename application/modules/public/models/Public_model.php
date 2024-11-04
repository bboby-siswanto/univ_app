<?php
class Public_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_category()
    {
        $s_dir = APPPATH."uploads/public/";

        return (file_exists($s_dir)) ? scandir($s_dir) : false;
    }

    public function get_personal_document($s_document_token)
    {
        $this->db->select('*, pdd.date_added');
        $this->db->from('dt_personal_document pdd');
        $this->db->join('dt_personal_data pd', 'pd.personal_data_id = pdd.personal_data_id_generated');
        $this->db->where('pdd.document_token', $s_document_token);

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->first_row() : false;
    }

    public function get_files($s_category = 'all')
    {
        $a_files= [];
        $s_main_dir = APPPATH."uploads/public/";

        if (file_exists($s_main_dir)) {
            if ($s_category == 'all') {
                $a_dir = scandir($s_main_dir);
                for ($i=0; $i < count($a_dir); $i++) { 
                    if (($a_dir[$i] != '.') AND ($a_dir[$i] != '..')) {
                        $s_sub_dir = $s_main_dir.$a_dir[$i].'/';
                        $a_sub_dir = scandir($s_sub_dir);
    
                        for ($x=0; $x < count($a_sub_dir); $x++) {
                            if (($a_sub_dir[$x] != '.') AND ($a_sub_dir[$x] != '..')) {
                                $s_files = $s_sub_dir.$a_sub_dir[$x];
                                if (file_exists($s_files)) {
                                    array_push($a_files, [
                                        'filename' => $a_sub_dir[$x],
                                        'lastmodified' => date ("d F Y H:i:s.", filemtime($s_files)),
                                        'filesize' => $this->_calculate_size(filesize($s_files))
                                    ]);
                                }
                            }
                        }
                    }
                }
            }else{
                $s_sub_dir = $s_main_dir.$s_category.'/';
                if (file_exists($s_sub_dir)) {
                    $a_sub_dir = scandir($s_sub_dir);
                    for ($x=0; $x < count($a_sub_dir); $x++) {
                        if (($a_sub_dir[$x] != '.') AND ($a_sub_dir[$x] != '..')) {
                            $s_files = $s_sub_dir.$a_sub_dir[$x];
                            if (file_exists($s_files)) {
                                array_push($a_files, [
                                    'filename' => $a_sub_dir[$x],
                                    'lastmodified' => date ("d F Y H:i:s.", filemtime($s_files)),
                                    'filesize' => $this->_calculate_size(filesize($s_files))
                                ]);
                            }
                        }
                    }
                }
            }
        }

        $a_files = array_values($a_files);
        return $a_files;
        // print('<pre>');
        // var_dump($a_files);exit;
    }

    private function _calculate_size($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
