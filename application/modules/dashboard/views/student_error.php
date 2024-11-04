<?php
$page = (isset($page_error)) ? $page_error : '';
$s_header_message = (isset($s_header_message)) ? $s_header_message : 'Something went wrong';
$s_body_message = (isset($s_body_message)) ? $s_body_message : '<p>Sorry for the inconvenience, an error has occurred on the page you are visiting.</p><p>Please contact the IT team to immediately fix the error on the '.$page.' page</p>';
?>
<style>
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a.links { color: #dc8100; text-decoration: none; }
  a.links:hover { color: #333; text-decoration: none; }
</style>

<article>
    <h1>Oops..!<br><?=$s_header_message;?></h1>
    <div>
        <?=$s_body_message;?>
        <p><a href="mailto:employee@company.ac.id" class="links">&mdash; IULI Dev Team</a></p>
    </div>
</article>