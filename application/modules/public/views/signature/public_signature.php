<style>
body {
    background-image: url("https://www.iuli.ac.id/upload/landing/983/qoaahlg1d7j3qjryoqs8edtx3897cqi8/Untitled_1sdssd@1x.jpg");
    /* filter: blur(8px) !important;
    -webkit-filter: blur(8px) !important; */
    /* backdrop-filter: blur(8px); */
    background-repeat: no-repeat;
    background-size: cover;
    /* position: absolute; */
}
.header{
    border-top: 3px solid #001489;
    border-bottom: 3px solid #001489;
    background: #ffffffdb;
    color: #fff;
    padding: 2%;
}
.footer{
    /* border-top: 3px solid #001489; */
    border-bottom: 3px solid #001489;
    background: #ffffffdb;
    color: #fff;
    padding: 2%;
}
.content{
    background: #ffffffdb;
    padding: 2%;
    border-bottom: 3px solid #001489;
    /* border-left: 3px solid #001489;
    border-right: 3px solid #001489; */
}
table, .text-content {
    font-size: 16px;
}
hr {
    border-color: #001489 !important;
}
.img-owl {
    max-width: 50px;
}
.img-iuli {
    max-width: 80%;
}
.bg-white {
    background-color: #ffffff !important;
}
</style>
<div class="container mt-5">
    <div class="row align-items-center justify-content-center">
        <div class="col-sm-8 align-self-center">
            <div class="header bg-white">
                <div class="row">
                    <div class="col-12">
                        <img src="<?=base_url()?>assets/img/header_of_file.png" alt="IULI" class="img-fluid" width="100%">
                    </div>
                    <!-- <div class="col-sm-4 text-center text-sm-left">
                    
                    </div> -->
                </div>
            </div>
            <div class="content bg-white">
                <h2><img src="<?=base_url()?>assets/img/owl_pen.png" alt="IULI" class="img-owl"> Proof of Digital Letter</h2>
                <hr>
                <div class="text-content">
                    <div class="row p-2">
                        <div class="col-sm-3">
                            Letter Number
                        </div>
                        <div class="col-sm-1 col-8 d-none d-sm-block float-right">:</div>
                        <div class="col-sm-7">
                            <strong><?=$document['letter_number'];?></strong>
                        </div>
                    </div>
                    <div class="row p-2">
                        <div class="col-sm-3">
                            Action Datetime
                        </div>
                        <div class="col-sm-1 col-8 d-none d-sm-block float-right">:</div>
                        <div class="col-sm-7">
                            <strong><?=$document['action_datetime'];?></strong>
                        </div>
                    </div>
                    <div class="row p-2">
                        <div class="col-sm-3">
                            Action By
                        </div>
                        <div class="col-sm-1 col-8 d-none d-sm-block float-right">:</div>
                        <div class="col-sm-7">
                            <strong><?=$document['user_sign'];?></strong>
                        </div>
                    </div>
                    <div class="row p-2">
                        <div class="col-sm-3">
                            About
                        </div>
                        <div class="col-sm-1 col-8 d-none d-sm-block float-right">:</div>
                        <div class="col-sm-7">
                            <strong><?=$document['about_letter'];?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer bg-white">
                <div class="row">
                    <div class="col-12">
                        <img src="<?=base_url()?>assets/img/footer_of_letter.png" alt="IULI" class="img-fluid" width="100%">
                    </div>
                    <!-- <div class="col-sm-4 text-center text-sm-left">
                    
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>