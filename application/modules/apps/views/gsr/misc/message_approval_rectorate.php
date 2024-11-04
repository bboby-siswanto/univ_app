<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSR - IULI</title>
    <style>
    .btn {
        font-size: 14px;
        padding: 6px 12px;
        margin-bottom: 0;
        display: inline-block;
        text-decoration: none;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: none;
        border: 1px solid transparent;
    }
    .btn:focus,
    .btn:active:focus {
        outline: thin dotted;
        outline: 5px auto -webkit-focus-ring-color;
        outline-offset: -2px;
    }
    .btn:hover,
    .btn:focus {
        color: #333;
        text-decoration: none;
    }
    .btn:active {
        background-image: none;
        outline: 0;
        -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
        box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
    }

    /* default
    ---------------------------- */
    .btn-default {
        color: #333;
        background-color: #fff;
        border-color: #ccc;
    }
    .btn-default:focus {
        color: #333;
        background-color: #e6e6e6;
        border-color: #8c8c8c;
    }
    .btn-default:hover {
        color: #333;
        background-color: #e6e6e6;
        border-color: #adadad;
    }
    .btn-default:active {
        color: #333;
        background-color: #e6e6e6;
        border-color: #adadad;
    }
    .btn-danger {
        color: #fff;
        background-color: #f86c6b;
        border-color: #f86c6b;
    }
    .btn-danger:focus {
        /* color: #333;
        background-color: #e6e6e6;
        border-color: #8c8c8c; */
        box-shadow: 0 0 0 0.2rem rgba(249, 130, 129, 0.5)
    }
    .btn-danger:hover {
        color: #fff;
        background-color: #f64846;
        border-color: #f63c3a;
    }
    .btn-danger:active {
        color: #fff;
        background-color: #f63c3a;
        border-color: #f5302e;
    }
    .btn-success {
        color: #fff;
        background-color: #4dbd74;
        border-color: #4dbd74;
    }
    .btn-success:focus {
        /* color: #fff;
        background-color: #e6e6e6;
        border-color: #8c8c8c; */
        box-shadow: 0 0 0 0.2rem rgba(104, 199, 137, 0.5)
    }
    .btn-success:hover {
        color: #fff;
        background-color: #3ea662;
        border-color: #3a9d5d;
    }
    .btn-success:active {
        color: #fff;
        background-color: #3a9d5d;
        border-color: #379457;
    }
    </style>
</head>
<body>
<p>Kepada: <?=$s_approve_name;?></p>
<p><br></p>
<p><?= ucwords(strtolower($s_user_request));?> telah membuat gsr dengan nomor <?=$gsr_code;?>, mohon ditindak lanjuti apakah disetujui atau perlu dilakukan perbaikan melalui link berikut:</p>
<p><a href="<?=base_url()?>apps/gsr/submit_action/<?=$param_link;?>"><?=base_url()?>apps/gsr/submit_action/<?=$param_link;?></a></p>
<p></p>
<p>atau bisa langsung klik tombol dibawah untuk action.</p>
<a href="<?=base_url()?>apps/gsr/submit_action/<?=$param_link;?>/approve" class="btn btn-success">Approve</a>
or
<a href="<?=base_url()?>apps/gsr/submit_action/<?=$param_link;?>/reject" class="btn btn-danger">Reject</a>
<p></p>
<hr>
Nb.
<ul>
<li>File GSR dan Attachment lainnya terlampir.</li>
<li>Pastikan sudah login ke sistem portal sebelumnya.</li>
<li>Pesan ini digenerate oleh sistem.</li>
</ul>
</body>
</html>