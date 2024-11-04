<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" xml:lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <title>International University Liaison Indonesia | Electronic Scientific Journals</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="language" content="English">
    <meta http-equiv="Content-Language" content="en">
    <meta content="Spirit of Science | International University Liaison Indonesia - IULI" name="description" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url()?>assets/img/iuli-owl.png">

    <meta name="description" content="International University Liaison Indonesia (IULI) operates in Indonesia as the representative of the European Universities Consortium - EUC coordinated by TU-Ilmenau, Germany" />
    <meta name="keywords" content="iuli, international university liaison indonesia, campus, international university, european universities consortium, university in jakarta, euc" />
    <meta name="author" content="International University Liaison Indonesia - IULI" />
    <meta name="robots" content="index, follow">
	<meta name="revisit-after" content="14 days">
	<meta name="google-site-verification" content="VXMpztHR44bW3csLK6iZm2yy5juy_qByp_5LW2dQgaA" />
    <link rel="canonical" href="http://www.iuli.ac.id/" title="IULI Canonical link" />

   <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="<?=base_url()?>assets/vendors/fontawesome/css/all.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/vendors/pace-progress/css/pace.min.css" rel="stylesheet">
    <!-- <link href="<?=base_url()?>assets/vendors/toastr/css/toastr.min.css" rel="stylesheet"> -->
    <link href="<?=base_url()?>assets/css/iuli.css" rel="stylesheet">

    <style>
        .header-nav {
            /* height: 100px !important; */
            background-color: #001489 !important;
            border-bottom: #000 !important;
            /* color: #fff !important; */
        }
        .icon-link-journal {
            width: 80px;
            height: 80px;
            border-radius: 40px;
            background-repeat: no-repeat;
            background-position-y: -20px;
            background-position-x: 10px;
            background-color: #14509D;
            background-size: cover;
            color: white;
            font-weight: bold;
        }
        .icon-link-journal-bottom {
            width: 140px;
            height: 140px;
            border-radius: 70px;
            background-repeat: no-repeat;
            background-position-y: -40px;
            background-position-x: 30px;
            background-color: #14509D;
            background-size: cover;
            color: white;
            font-weight: bold;
        }
        .text-link-journal {
            position: relative;
            left: 20px;
            bottom: 25px;
            font-weight: bold;
        }
        .text-link-journal-bottom {
            position: relative;
            left: 40px;
            bottom: 45px;
            font-weight: bold;
            font-size: 20px;
        }
    </style>
    
    <script src="<?=base_url()?>assets/vendors/jquery/js/jquery.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/moment/js/moment.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/daterangepicker/daterangepicker.js"></script>
    <script src="<?=base_url()?>assets/vendors/jquery-ui/jquery-ui.bundle.js"></script>
    <script src="<?=base_url()?>assets/vendors/popper.js/js/popper.min.js"></script>
    <script src="<?=base_url()?>assets/vendors/bootstrap/js/bootstrap.min.js"></script>

    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script> -->
   <!-- BEGIN THEME STYLES -->
   <!-- END THEME STYLES -->
	<!-- <script type="text/javascript" src="<?=base_url();?>assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script> -->
</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="app">
    <nav class="navbar navbar-expand-lg header-nav">
        <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <!-- <span class="navbar-toggler-icon"></span> -->
            <span class="fas fa-grip-lines"></span>
        </button>

        <div class="collapse navbar-collapse ml-5" id="navbarSupportedContent">
<?php
if ((isset($allow_iframe)) AND ($allow_iframe)) {
?>
            <ul class="navbar-nav mr-auto">
                <li class="nav-item pr-3">
                    <a class="nav-link text-white" href="<?=base_url()?>apps/electronic_journal/ejournal/1">
                        <div class="icon-link-journal" style="background-image: url('https://rzblx1.uni-regensburg.de/ezeit/img/GettyImages_90309427_montage_255x130px.png');"></div>
                        <span class="text-link-journal">Electronic Journal Library</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?=base_url()?>apps/electronic_journal/ejournal/2">
                        <div class="icon-link-journal" style="background-image: url('https://rzblx10.uni-regensburg.de/dbinfo/icons/logoubil.gif');"></div>
                        <span class="text-link-journal">Datenbank-Infosystem (DBIS)</span>
                    </a>
                </li>
            </ul>
<?php
}
?>
        </div>
        <a class="navbar-brand" href="<?= base_url()?>apps/electronic_journal/ejournal">
            <img class="navbar-brand-full" src="<?=base_url()?>assets/img/iuli.png" height="<?= ((isset($allow_iframe)) AND ($allow_iframe)) ? '100' : '75' ?>" alt="IULI">
        </a>
    </nav>
    <div class="app-body">
			<!-- <div class="sidebar text-uppercase">
				<nav class="sidebar-nav">
					<ul class="nav">
					</ul>
				</nav>
			</div> -->

			<!-- <main> -->
				<!-- <div class="container"> -->
					<!-- <div class="pb-5"></div> -->
					<?php
                    // var_dump($allow_iframe);exit;
                    if ((isset($allow_iframe)) AND ($allow_iframe)) {
                    ?>
                        <div class="embed-responsive">
                            <iframe class="embed-responsive-item embed-responsive-16by9" src="<?=$page;?>"></iframe>
                        </div>
                    <?php
                    }
                    else {
                    ?>
                        <div class="container-fluid">
                            <div class='row'>
                                <div class='col-12'>
                                    <!-- <h2 class="text-center" style="color: #14509D;"></h2> -->
                                    <div class="jumbotron text-center mt-5" style="padding-top: 1px !important;">
                                        <!-- <h1 class="display-4">Hello,</h1> -->
                                        <p class="h2">In IULI library you can access to<br />over than <strong>70.000</strong><br />electronic scientific journals.</p>
                                        <!-- <hr class="my-4"> -->
                                    <!-- <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
                                    <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a> -->
                                        <div class="row justify-content-center mt-5">
                                            <div class="col-sm-4">
                                                <a class="nav-link text-left" href="<?=base_url()?>apps/electronic_journal/ejournal/1">
                                                    <div class="icon-link-journal-bottom" style="background-image: url('https://rzblx1.uni-regensburg.de/ezeit/img/GettyImages_90309427_montage_255x130px.png');"></div>
                                                    <span class="text-link-journal-bottom">Electronic Journal Library</span>
                                                </a>
                                            </div>
                                            <div class="col-sm-4">
                                                <a class="nav-link text-left" href="<?=base_url()?>apps/electronic_journal/ejournal/2">
                                                    <div class="icon-link-journal-bottom" style="background-image: url('https://rzblx10.uni-regensburg.de/dbinfo/icons/logoubil.gif');"></div>
                                                    <span class="text-link-journal-bottom">Datenbank-Infosystem (DBIS)</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
				<!-- </div> -->
			<!-- </main> -->
		</div>
</body>
</html>
<!-- END BODY -->
