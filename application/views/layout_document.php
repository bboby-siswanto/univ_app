<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        /* @page {
            margin: 0mm;
            margin-header: 0mm !important;
            margin-footer: 0mm;
        } */
        .fn-9 {
            font-size: 9px !important;
        }
        .p-10 {
            padding: 10px !important;
        }
        .p-15 {
            padding: 15px !important;
        }
        html {
            background-color: #ffffff;
        }
		.pt25 {
			padding-top: 2.5mm !important;
		}
        .table {
            width: 100%;
            /* padding-top: 1mm;
            padding-bottom: 1mm;
            padding-right: 1mm;
            padding-left: 4.5mm; */
            text-align:left;
		}
        .table-border {
            border: 1px solid #000;
        }
        .bt {
            border-top: 1px solid #000;
        }
        .bb {
            border-bottom: 1px solid #000;
        }
        th, td {
            padding: 1mm 2mm 0.5mm 0.5mm;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
    </style>
</head>
<body>
    <!-- <div style="padding-top: 5px; padding-left: 15px; padding-right: 15px;">
    <img src="./assets/img/header_of_file.jpg" alt="">
    </div> -->
    <?=(isset($body)) ? $body : '';?>
    <!-- <img src="./assets/img/footer_of_letter.png" alt=""> -->
</body>
</html>