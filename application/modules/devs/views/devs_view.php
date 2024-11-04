<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resume</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<meta name="keywords" content="" />
	<meta name="description" content="" />

	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" media="all" /> 
	<!-- <link rel="stylesheet" type="text/css" href="resume.css" media="all" /> -->
  <style>
    /*
---------------------------------------------------------------------------------
	STRIPPED DOWN RESUME TEMPLATE
    html resume

    v0.9: 5/28/09

    design and code by: thingsthatarebrown.com 
                        (matt brown)
---------------------------------------------------------------------------------
*/

/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.7.0
*/
html{color:#000;background:#FFF;}body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,button,textarea,p,blockquote,th,td{margin:0;padding:0;}table{border-collapse:collapse;border-spacing:0;}fieldset,img{border:0;}address,caption,cite,code,dfn,em,strong,th,var,optgroup{font-style:inherit;font-weight:inherit;}del,ins{text-decoration:none;}li{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:normal;}q:before,q:after{content:'';}abbr,acronym{border:0;font-variant:normal;}sup{vertical-align:baseline;}sub{vertical-align:baseline;}legend{color:#000;}input,button,textarea,select,optgroup,option{font-family:inherit;font-size:inherit;font-style:inherit;font-weight:inherit;}input,button,textarea,select{*font-size:100%;}body{font:13px/1.231 arial,helvetica,clean,sans-serif;*font-size:small;*font:x-small;}select,input,button,textarea,button{font:99% arial,helvetica,clean,sans-serif;}table{font-size:inherit;font:100%;}pre,code,kbd,samp,tt{font-family:monospace;*font-size:108%;line-height:100%;}body{text-align:center;}#doc,#doc2,#doc3,#doc4,.yui-t1,.yui-t2,.yui-t3,.yui-t4,.yui-t5,.yui-t6,.yui-t7{margin:auto;text-align:left;width:57.69em;*width:56.25em;}#doc2{width:100%;*width:71.25em;}#doc3{margin:auto 10px;width:auto;}#doc4{width:74.923em;*width:73.05em;}.yui-b{position:relative;}.yui-b{_position:static;}#yui-main .yui-b{position:static;}#yui-main,.yui-g .yui-u .yui-g{width:100%;}.yui-t1 #yui-main,.yui-t2 #yui-main,.yui-t3 #yui-main{float:right;margin-left:-25em;}.yui-t4 #yui-main,.yui-t5 #yui-main,.yui-t6 #yui-main{float:left;margin-right:-25em;}.yui-t1 .yui-b{float:left;width:12.30769em;*width:12.00em;}.yui-t1 #yui-main .yui-b{margin-left:13.30769em;*margin-left:13.05em;}.yui-t2 .yui-b{float:left;width:13.8461em;*width:13.50em;}.yui-t2 #yui-main .yui-b{margin-left:14.8461em;*margin-left:14.55em;}.yui-t3 .yui-b{float:left;width:23.0769em;*width:22.50em;}.yui-t3 #yui-main .yui-b{margin-left:24.0769em;*margin-left:23.62em;}.yui-t4 .yui-b{float:right;width:13.8456em;*width:13.50em;}.yui-t4 #yui-main .yui-b{margin-right:14.8456em;*margin-right:14.55em;}.yui-t5 .yui-b{float:right;width:18.4615em;*width:18.00em;}.yui-t5 #yui-main .yui-b{margin-right:19.4615em;*margin-right:19.125em;}.yui-t6 .yui-b{float:right;width:23.0769em;*width:22.50em;}.yui-t6 #yui-main .yui-b{margin-right:24.0769em;*margin-right:23.62em;}.yui-t7 #yui-main .yui-b{display:block;margin:0 0 1em 0;}#yui-main .yui-b{float:none;width:auto;}.yui-gb .yui-u,.yui-g .yui-gb .yui-u,.yui-gb .yui-g,.yui-gb .yui-gb,.yui-gb .yui-gc,.yui-gb .yui-gd,.yui-gb .yui-ge,.yui-gb .yui-gf,.yui-gc .yui-u,.yui-gc .yui-g,.yui-gd .yui-u{float:left;}.yui-g .yui-u,.yui-g .yui-g,.yui-g .yui-gb,.yui-g .yui-gc,.yui-g .yui-gd,.yui-g .yui-ge,.yui-g .yui-gf,.yui-gc .yui-u,.yui-gd .yui-g,.yui-g .yui-gc .yui-u,.yui-ge .yui-u,.yui-ge .yui-g,.yui-gf .yui-g,.yui-gf .yui-u{float:right;}.yui-g div.first,.yui-gb div.first,.yui-gc div.first,.yui-gd div.first,.yui-ge div.first,.yui-gf div.first,.yui-g .yui-gc div.first,.yui-g .yui-ge div.first,.yui-gc div.first div.first{float:left;}.yui-g .yui-u,.yui-g .yui-g,.yui-g .yui-gb,.yui-g .yui-gc,.yui-g .yui-gd,.yui-g .yui-ge,.yui-g .yui-gf{width:49.1%;}.yui-gb .yui-u,.yui-g .yui-gb .yui-u,.yui-gb .yui-g,.yui-gb .yui-gb,.yui-gb .yui-gc,.yui-gb .yui-gd,.yui-gb .yui-ge,.yui-gb .yui-gf,.yui-gc .yui-u,.yui-gc .yui-g,.yui-gd .yui-u{width:32%;margin-left:1.99%;}.yui-gb .yui-u{*margin-left:1.9%;*width:31.9%;}.yui-gc div.first,.yui-gd .yui-u{width:66%;}.yui-gd div.first{width:32%;}.yui-ge div.first,.yui-gf .yui-u{width:74.2%;}.yui-ge .yui-u,.yui-gf div.first{width:24%;}.yui-g .yui-gb div.first,.yui-gb div.first,.yui-gc div.first,.yui-gd div.first{margin-left:0;}.yui-g .yui-g .yui-u,.yui-gb .yui-g .yui-u,.yui-gc .yui-g .yui-u,.yui-gd .yui-g .yui-u,.yui-ge .yui-g .yui-u,.yui-gf .yui-g .yui-u{width:49%;*width:48.1%;*margin-left:0;}.yui-g .yui-g .yui-u{width:48.1%;}.yui-g .yui-gb div.first,.yui-gb .yui-gb div.first{*margin-right:0;*width:32%;_width:31.7%;}.yui-g .yui-gc div.first,.yui-gd .yui-g{width:66%;}.yui-gb .yui-g div.first{*margin-right:4%;_margin-right:1.3%;}.yui-gb .yui-gc div.first,.yui-gb .yui-gd div.first{*margin-right:0;}.yui-gb .yui-gb .yui-u,.yui-gb .yui-gc .yui-u{*margin-left:1.8%;_margin-left:4%;}.yui-g .yui-gb .yui-u{_margin-left:1.0%;}.yui-gb .yui-gd .yui-u{*width:66%;_width:61.2%;}.yui-gb .yui-gd div.first{*width:31%;_width:29.5%;}.yui-g .yui-gc .yui-u,.yui-gb .yui-gc .yui-u{width:32%;_float:right;margin-right:0;_margin-left:0;}.yui-gb .yui-gc div.first{width:66%;*float:left;*margin-left:0;}.yui-gb .yui-ge .yui-u,.yui-gb .yui-gf .yui-u{margin:0;}.yui-gb .yui-gb .yui-u{_margin-left:.7%;}.yui-gb .yui-g div.first,.yui-gb .yui-gb div.first{*margin-left:0;}.yui-gc .yui-g .yui-u,.yui-gd .yui-g .yui-u{*width:48.1%;*margin-left:0;}.yui-gb .yui-gd div.first{width:32%;}.yui-g .yui-gd div.first{_width:29.9%;}.yui-ge .yui-g{width:24%;}.yui-gf .yui-g{width:74.2%;}.yui-gb .yui-ge div.yui-u,.yui-gb .yui-gf div.yui-u{float:right;}.yui-gb .yui-ge div.first,.yui-gb .yui-gf div.first{float:left;}.yui-gb .yui-ge .yui-u,.yui-gb .yui-gf div.first{*width:24%;_width:20%;}.yui-gb .yui-ge div.first,.yui-gb .yui-gf .yui-u{*width:73.5%;_width:65.5%;}.yui-ge div.first .yui-gd .yui-u{width:65%;}.yui-ge div.first .yui-gd div.first{width:32%;}#hd:after,#bd:after,#ft:after,.yui-g:after,.yui-gb:after,.yui-gc:after,.yui-gd:after,.yui-ge:after,.yui-gf:after{content:".";display:block;height:0;clear:both;visibility:hidden;}#hd,#bd,#ft,.yui-g,.yui-gb,.yui-gc,.yui-gd,.yui-ge,.yui-gf{zoom:1;}
.msg { padding: 10px; background: #222; position: relative; }
.msg h1 { color: #fff;  }
.msg a { margin-left: 20px; background: #408814; color: white; padding: 4px 8px; text-decoration: none; }
.msg a:hover { background: #266400; }

/* //-- yui-grids style overrides -- */
body { font-family: Georgia; color: #444; }
#inner { padding: 10px 80px; background: #f5f5f5; border: solid #666; border-width: 8px 0 2px 0; }
.yui-gf { margin-bottom: 2em; padding-bottom: 2em; border-bottom: 1px solid #ccc; }

/* //-- header, body, footer -- */
#hd { margin: 2.5em 0 3em 0; padding-bottom: 1.5em; border-bottom: 1px solid #ccc }
#hd h2 { text-transform: uppercase; letter-spacing: 2px; }
#bd, #ft { margin-bottom: 2em; }

/* //-- footer -- */
#ft { padding: 1em 0 5em 0; font-size: 92%; border-top: 1px solid #ccc; text-align: center; }
#ft p { margin-bottom: 0; text-align: center;   }

/* //-- core typography and style -- */
#hd h1 { font-size: 48px; text-transform: uppercase; letter-spacing: 3px; }
h2 { font-size: 152% }
h3, h4 { font-size: 122%; }
h1, h2, h3, h4 { color: #333; }
p { line-height: 18px; padding-right: 3em; }
a { color: #990003 }
a:hover { text-decoration: none; }
strong { font-weight: bold; }
li { line-height: 24px; border-bottom: 1px solid #ccc; }
p.enlarge { font-size: 144%; padding-right: 6.5em; line-height: 24px; }
p.enlarge span { color: #000 }
.contact-info { margin-top: 7px; }
.first h2 { font-style: italic; }
.last { border-bottom: 0 }


/* //-- section styles -- */

a#pdf { display: block; float: left; background: #666; color: white; padding: 6px 50px 6px 12px; margin-bottom: 6px; text-decoration: none;  }
a#pdf:hover { background: #222; }

.job { position: relative; margin-bottom: 1em; border-bottom: 1px solid #ccc; }
.job h4 { position: absolute; top: 0.35em; right: 0 }
.job p { margin: 0.75em 0 3em 0; }

.last { border: none; }
.skills-list {  }
.skills-list ul { margin: 0; }
.skills-list li { margin: 3px 0; padding: 3px 0; }
.skills-list li span { font-size: 152%; display: block; margin-bottom: -2px; padding: 0 }
.talent { width: 32%; float: left }
.talent h2 { margin-bottom: 6px; }

#srt-ttab { margin-bottom: 100px; text-align: center;  }
#srt-ttab img.last { margin-top: 20px }

/* --// override to force 1/8th width grids -- */
.yui-gf .yui-u{width:80.2%;}
.yui-gf div.first{width:12.3%;}



  </style>
</head>
<body>
  
<div id="doc2" class="yui-t7">
	<div id="inner">
	
		<div id="hd">
			<div class="yui-gc">
				<div class="yui-u first">
					<h1>Budi Siswanto</h1>
					<h2>Web Developer</h2>
				</div>

				<div class="yui-u">
					<div class="contact-info">
						<!-- <h3 id="download_pdf"><a id="pdf" href="#">Download PDF</a></h3> -->
						<h3 id="download_pdf">&nbsp;</h3>
						<h3><a href="mailto:budi.siswanto1450@gmail.com">budi.siswanto1450@gmail.com</a></h3>
						<h3>0818-557-441</h3>
					</div><!--// .contact-info -->
				</div>
			</div><!--// .yui-gc -->
		</div><!--// hd -->

		<div id="bd">
			<div id="yui-main">
				<div class="yui-b">

					<div class="yui-gf">
						<div class="yui-u first">
							<h2>Profile</h2>
						</div>
						<div class="yui-u">
							<p class="enlarge" style="text-align: justify;">
								Karyawan pada International University Liaison Indonesia sebagai IT Staff yang mensupport data dan pengembangan sistem selama 4 tahun terakhir. Sistem akademik dibangun dengan bahasa program PHP dan database MySQL sudah berjalan selama 4 tahun terakhir. Beberapa aplikasi juga sudah dibuat sebelum bergabung pada perusahaan tersebut.
							</p>
						</div>
					</div><!--// .yui-gf -->

					<!-- <div class="yui-gf">
						<div class="yui-u first">
							<h2>Skills</h2>
						</div>
						<div class="yui-u">

								<div class="talent">
									<h2>Web Design</h2>
									<p>Bootstrap, </p>
								</div>

								<div class="talent">
									<h2>Interface Design</h2>
									<p>Credibly streamline mission-critical value with multifunctional functionalities.	 </p>
								</div>

								<div class="talent">
									<h2>Project Direction</h2>
									<p>Proven ability to lead and manage a wide variety of design and development projects in team and independent situations.</p>
								</div>
						</div>
					</div> -->
          <!--// .yui-gf -->

					<div class="yui-gf">
						<div class="yui-u first">
							<h2>Skills</h2>
						</div>
						<div class="yui-u">
							<!-- <ul class="talent">
								<li>PHP</li>
								<li>CSS</li>
								<li class="last">Javascript</li>
							</ul> -->

							<ul class="talent">
								<li>PHP</li>
								<li>Jquery</li>
								<li class="last">MySQL</li>
							</ul>

							<ul class="talent">
								<li>Ms. Office</li>
								<!-- <li>Office Excel</li> -->
								<!-- <li class="last">Office Excel</li> -->
							</ul>
						</div>
					</div><!--// .yui-gf-->

					<div class="yui-gf">
	
						<div class="yui-u first">
							<h2>Experience</h2>
						</div><!--// .yui-u -->

						<div class="yui-u">

							<div class="job">
								<h2>International University Liaison Indonesia</h2>
								<h3>IT Staff Programer</h3>
								<h4>2019 - Sekarang</h4>
								<p>Menjaga dan mengembangkan sistem baru guna mempermudah mahasiswa, dosen dan staff lainnya dalam belajar maupun proses bekerja. </p>
							</div>

							<div class="job">
								<h2>PT. Restu Mahkota Karya</h2>
								<h3>Mekanik</h3>
								<h4>2012 - 2016</h4>
								<p>Memperbaiki dan melakukan servis kendaraan roda 4 khusus untuk mobil suzuki. </p>
							</div>

							<div class="job last">
								<h2>PT. Panca Budi Idaman</h2>
								<h3>Staff Gudang</h3>
								<h4>2010 - 2011</h4>
								<p>Melakukan administrasi data stok barang pada gudang dan juga bertanggung jawab terhadap stok dan kualitas barang gudang dan juga melakukan pengiriman dan penerimaan barang.</p>
							</div>

						</div><!--// .yui-u -->
					</div><!--// .yui-gf -->


					<div class="yui-gf last">
						<div class="yui-u first">
							<h2>Education</h2>
						</div>
						<div class="yui-u">
							<h2>UNIVERSITAS PAMULANG</h2>
							<h3>Teknik Informatika &mdash; 2013 - 2017 </h3>
						</div>
						<div class="yui-u last">
							<h2>SMK Negeri 2 Kota Tangerang</h2>
							<h3>Teknik Mekanik Otomotif &mdash; 2007 - 2010 </h3>
						</div>
					</div><!--// .yui-gf -->

					<!-- <div class="yui-gf">
						<div class="yui-u first">
							<h2>Portfolio</h2>
						</div>
						<div class="yui-u">
							<ul class="talent">
								<li>PHP</li>
								<li>Jquery</li>
								<li class="last">MySQL</li>
							</ul>

							<ul class="talent">
								<li>Ms. Office</li>
							</ul>
						</div>
					</div> -->


				</div><!--// .yui-b -->
			</div><!--// yui-main -->
		</div><!--// bd -->

		<div id="ft">
			<p>Budi Siswanto &mdash; <a href="mailto:budi.siswanto1450@gmail.com">budi.siswanto1450@gmail.com</a> &mdash; 0818-557-441</p>
		</div><!--// footer -->

	</div><!-- // inner -->


</div><!--// doc -->


</body>
</html>