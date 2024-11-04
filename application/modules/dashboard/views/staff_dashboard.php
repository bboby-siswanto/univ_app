<style>
.emp-profile{
    padding: 3%;
    margin-bottom: 3%;
    border-radius: 0.5rem;
    background: #fff;
}
.profile-img{
    text-align: center;
}
.btn-profile-file {
    width: 70%;
}
.profile-img img{
    width: 70%;
    height: 100%;
}
.profile-img .file {
    position: relative;
    overflow: hidden;
    margin-top: -20%;
    width: 70%;
    border: none;
    border-radius: 0;
    font-size: 15px;
    background: #212529b8;
}
.profile-img .file input {
    position: absolute;
    opacity: 0;
    right: 0;
    top: 0;
}
.profile-head{
    padding-top: 20px;
}
.profile-head h5, .profile-head small{
    color: #333;
    text-align: center;
}
.profile-head h6{
    color: #0062cc;
}
.profile-edit-btn{
    border-radius: 1.5rem;
    width: 70%;
    padding: 2%;
    font-weight: 600;
    color: #6c757d !important;
    cursor: pointer;
}
.proile-rating{
    font-size: 12px;
    color: #818182;
    margin-top: 5%;
}
.proile-rating span{
    color: #495057;
    font-size: 15px;
    font-weight: 600;
}
.profile-head .nav-tabs{
    margin-bottom:5%;
}
.profile-head .nav-tabs .nav-link{
    font-weight:600;
    border: none;
}
.profile-head .nav-tabs .nav-link.active{
    border: none;
    border-bottom:2px solid #0062cc;
}
.profile-work{
    padding: 14%;
    margin-top: -15%;
}
.profile-work p{
    font-size: 12px;
    color: #818182;
    font-weight: 600;
    margin-top: 10%;
}
.profile-work a{
    text-decoration: none;
    color: #495057;
    font-weight: 600;
    font-size: 14px;
}
.profile-work ul{
    list-style: none;
}
.profile-tab label{
    font-weight: 600;
}
.profile-tab p{
    font-weight: 600;
    color: #0062cc;
}
.orange-circle-button {
	box-shadow: 2px 4px 0 2px rgba(0,0,0,0.1);
	border: .5em solid #E84D0E;
	font-size: 1em;
	line-height: 1.1em;
	color: #ffffff;
	background-color: #e84d0e;
	margin: auto;
	border-radius: 50%;
	height: 7em;
	width: 7em;
	position: relative;
}
.orange-circle-button:hover {
	color:#ffffff;
    background-color: #e84d0e;
	text-decoration: none;
	border-color: #ff7536;
	
}
.orange-circle-button:visited {
	color:#ffffff;
    background-color: #e84d0e;
	text-decoration: none;
	
}
.orange-circle-link-greater-than {
    font-size: 38px;
}
</style>
<div class="container emp-profile">
    <form method="post">
        <div class="row">
            <div class="col-md-4">
                <div class="profile-img">
                    <img src="<?=site_url('file_manager/view/0bde3152-5442-467a-b080-3bb0088f6bac/'.$this->session->userdata('user'))?>" class="img-fluid">
                </div>
                <div class="profile-head">
                    <div class="text-center">
                        <h5><?=$this->session->userdata('name')?></h5>
                        <a href="<?=base_url()?>user/profile" class="btn btn-secondary profile-edit-btn mt-3">Edit Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="tab-content profile-tab" id="myTabContent">
                    <div class="tab-pane fade show active text-center" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <h5>Select an Module</h5>
                        <hr>
                        <div class="row">
                            <div class="col-sm-4 p-3">
                                <a href="<?=base_url()?>module/set/admission" class="btn orange-circle-button"><i class="orange-circle-link-greater-than fas fa-pager"></i></a>
                                <!-- <button type="button" class="btn orange-circle-button"><i class="orange-circle-link-greater-than fas fa-pager"></i></button> -->
                                <h6>Admission</h6>
                            </div>
                            <div class="col-sm-4 p-3">
                                <button type="button" class="btn orange-circle-button"><i class="orange-circle-link-greater-than fas fa-graduation-cap"></i></button>
                                <h6>Academic</h6>
                            </div>
                            <div class="col-sm-4 p-3">
                                <button type="button" class="btn orange-circle-button"><i class="orange-circle-link-greater-than fas fa-donate"></i></button>
                                <h6>Finance</h6>
                            </div>
                            <div class="col-sm-4 p-3">
                                <button type="button" class="btn orange-circle-button"><i class="orange-circle-link-greater-than fas fa-user-graduate"></i></button>
                                <h6>Alumni</h6>
                            </div>
                            <div class="col-sm-4 p-3">
                                <button type="button" class="btn orange-circle-button"><i class="orange-circle-link-greater-than fas fa-user-tie"></i></button>
                                <h6>HRIS</h6>
                            </div>
                            <div class="col-sm-4 p-3">
                                <button type="button" class="btn orange-circle-button"><i class="orange-circle-link-greater-than fas fa-file-archive"></i></button>
                                <h6>Archive</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>           
</div>