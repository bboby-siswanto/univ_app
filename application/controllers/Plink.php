<?php
class Plink extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    // public function test()
    // {
    //     $s_string = 'cHVibGljL2NsYXNzbGlzdC9jbGFzcy82YzA1NGUwZS02Y2NhLTRkNDMtYjQ3Ny0zZjhlYzc2YTVhNTAvMjAyMjI%3D';
    //     $s_result = base64_decode(urldecode($s_string));
    //     print($s_result);exit;
    // }

    public function goto($s_pathtarget = false)
    {
        if ($s_pathtarget) {
            $s_linktarget = base64_decode(urldecode($s_pathtarget));
            redirect($s_linktarget);
        }
        else {
            show_404();
        }
    }
}
