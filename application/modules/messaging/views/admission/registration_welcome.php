<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IULI</title>
    <style>
    .canvas {
        margin: 0px auto;
        max-width: 700px !important;
        text-align: justify;
        text-justify: inter-word;
    }
    .canvas img {
        width: 100% !important;
    }
    </style>
</head>
<body>
    <div class="canvas">
        <img src="<?=base_url()?>assets/img/pmb/email_image/profile_campus.jpg" alt="">
        <p></p>
        <h3>Dear Candidate,</h3>
    <?php
    if ((isset($enrollment_fee)) AND ($enrollment_fee['code'] == 0)) {
    ?>
        <!-- <p>Thank you for interesting with IULI University and please <a href="https://pmb.iuli.ac.id/" target="_blank" class="text-white">complete your documents</a> to continue the next step process to join IULI University.</p> -->
        <p>Thank you for interesting with IULI University.</p>
        <p>Please transfer the enrollment fee to continue the next step process to join IULI University.</p>
        <ul style="text-align: left; list-style-type: none;">
            <li>Fee Amount: <?=$enrollment_fee['amount'];?></li>
            <li>Beneficiary Name: <?=$personal_data['personal_data_name'];?></li>
            <li>Bank: BNI 46</li>
            <li>Virtual Account Number: <?=$enrollment_fee['va_number'];?></li>
        </ul>
        <!-- <p style="text-align: left;"> -->
            <ol style="text-align: left;">
                Terms and Conditions Payment:
                <li>Please transfer the exact amount stated above.</li>
                <li>Unmatched payment will be rejected by BNI.</li>
            </ol>
        <!-- </p> -->
    <?php
    }
    ?>
        <p><br></p>
        <p>Please click <a href="<?=$confirmation_link;?>">the link</a> below to confirm your email address and complete your documents.</p>
        <p><a href="<?=$confirmation_link;?>"><?=$confirmation_link;?></a></p>
        <p><br></p>
        <p>If any problem issue or for further information please don't hesitate to phone or WhatsApp us on 
        <a href="https://wa.me/+6287844033007" target="_blank" class="text-white"><strong>+6287844033007</strong></a>
        or 
        <a href="https://wa.me/+6285212318000" target="_blank" class="text-white"><strong>+6285212318000</strong></a>
        </p>
        <p>This is a one-time email. You have received it because you signed up for an admission account in International University Liaison Indonesia - IULI.</p>
        <p><br></p>
        <p>
        <!-- <br><br> -->
        Hotline : 085212318000 <br> 
        Email : employee@company.ac.id <br>
        Registration: pmb.iuli.ac.id <br>
        Website : www.iuli.ac.id  <br><br>
        <strong>Office Hours</strong> : Monday - Friday: 08.00 - 17.00 WIB <br>
        <strong>Closed</strong>: Saturday, Sunday and Indonesia National Holiday. 
        </p>
        <p></p>
        <img src="<?=base_url()?>assets/img/pmb/email_image/footer.jpg" alt="">
    </div>
</body>
</html>