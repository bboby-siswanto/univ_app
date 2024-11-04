<script src="<?= base_url() ?>assets/vendors/chart.js/js/Chart.min.js"></script>
<!-- <script src="<?= base_url() ?>assets/vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.6.0"></script> -->
<script src="<?=base_url()?>assets/vendors/chart.js/js/chartjsplugin/chartjs-plugin-datalabels.min.js"></script>

<?php
$a_question_description = [
    'f5' => 'Masa Tunggu Kerja (Setelah Lulus)',
    'f12' => 'Sumber Dana Kuliah',
    'f8' => 'Posisi bekerja pada saat ini',
    'f14' => 'Keeratan Prodi dengan pekerjaan',
    'f15' => 'Tingkat Pendidikan sesuai pekerjaan',
    'f13' => 'Pendapatan Bekerja',
    'f21' => 'Survey Metode Pembelajaran - Perkuliahan',
    'f22' => 'Survey Metode Pembelajaran - Demonstrasi',
    'f23' => 'Survey Metode Pembelajaran - Partisipasi dalam proyek riset',
    'f24' => 'Survey Metode Pembelajaran - Magang',
    'f25' => 'Survey Metode Pembelajaran - Praktikum',
    'f26' => 'Survey Metode Pembelajaran - Kerja Lapangan',
    'f27' => 'Survey Metode Pembelajaran - Diskusi',
    'f3' => 'Besaran Waktu Mencari Kerja',
    'f4' => 'Metode Mencari Pekerjaan',
    'f6' => 'Jumlah Perusahaan Dilamar',
    'f7' => 'Jumlah Perusahaan Dilamar Merespon',
    'f7a' => 'Jumlah Perusahaan Dilamar Mengundang Interview',
    'f9' => 'Gambaran Opini  Alumni Saat ini',
    'f10' => 'Status Aktif Mencari Pekerjaan',
    'f11' => 'Jenis Perusahaan Tempat Kerja',
    'iuli1' => 'Lokasi Perusahaan Tempat Kerja',
    'f16' => 'Kesesuaian Bidang Pekerjaan',
    'f1701' => 'Kompetensi yang dikuasai - Pengetahuan disiplin ilmu',
    'f1703' => 'Kompetensi yang dikuasai - Pengetahuan diluar bidang ilmu',
    'f1705' => 'Kompetensi yang dikuasai - Pengetahuan umum',
    'f1705a' => 'Kompetensi yang dikuasai - Bahasa Ingris',
    'f1707' => 'Kompetensi yang dikuasai - Keterampilan Internet',
    'f1709' => 'Kompetensi yang dikuasai - Keterampilan Komputer',
    'f1711' => 'Kompetensi yang dikuasai - Berfikir Kritis',
    'f1713' => 'Kompetensi yang dikuasai - Keterampilan Riset',
    'f1715' => 'Kompetensi yang dikuasai - Kemampuan Belajar',
    'f1717' => 'Kompetensi yang dikuasai - Kemampuan Berkomunikasi',
    'f1719' => 'Kompetensi yang dikuasai - Bekerja dibawah tekanan',
    'f1721' => 'Kompetensi yang dikuasai - Manajemen waktu',
    'f1723' => 'Kompetensi yang dikuasai - Bekerja secara mandiri',
    'f1725' => 'Kompetensi yang dikuasai - Bekerja dalam tim',
    'f1727' => 'Kompetensi yang dikuasai - Kemampuan memecahkan masalah',
    'f1729' => 'Kompetensi yang dikuasai - Negosiasi',
    'f1731' => 'Kompetensi yang dikuasai - Kemampuan analisis',
    'f1733' => 'Kompetensi yang dikuasai - Toleransi',
    'f1735' => 'Kompetensi yang dikuasai - Kemampuan adaptasi',
    'f1737' => 'Kompetensi yang dikuasai - Loyalitas',
    'f1737a' => 'Kompetensi yang dikuasai - Integritas',
    'f1739' => 'Kompetensi yang dikuasai - Bekerja dengan orang berbeda budaya',
    'f1741' => 'Kompetensi yang dikuasai - Kepemimpinan',
    'f1743' => 'Kompetensi yang dikuasai - Kemampuan memegang tanggung jawab',
    'f1745' => 'Kompetensi yang dikuasai - Inisiatif',
    'f1747' => 'Kompetensi yang dikuasai - Manajemen proyek / program',
    'f1749' => 'Kompetensi yang dikuasai - Kemampuan mempresentasikan produk/ide/laporan',
    'f1751' => 'Kompetensi yang dikuasai - Kemampuan dalam menulis laporan, memo, dan dokumen',
    'f1753' => 'Kompetensi yang dikuasai - Kemampuan untuk terus belajar sepanjang hayat',
    'f1702b' => 'Kompetensi yang diperlukan dalam pekerjaan - Pengetahuan disiplin ilmu',
    'f1704b' => 'Kompetensi yang diperlukan dalam pekerjaan - Pengetahuan diluar bidang ilmu',
    'f1706b' => 'Kompetensi yang diperlukan dalam pekerjaan - Pengetahuan umum',
    'f1706ba' => 'Kompetensi yang diperlukan dalam pekerjaan - Bahasa Ingris',
    'f1708b' => 'Kompetensi yang diperlukan dalam pekerjaan - Keterampilan Internet',
    'f1710b' => 'Kompetensi yang diperlukan dalam pekerjaan - Keterampilan Komputer',
    'f1712b' => 'Kompetensi yang diperlukan dalam pekerjaan - Berfikir Kritis',
    'f1714b' => 'Kompetensi yang diperlukan dalam pekerjaan - Keterampilan Riset',
    'f1716b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan Belajar',
    'f1718b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan Berkomunikasi',
    'f1720b' => 'Kompetensi yang diperlukan dalam pekerjaan - Bekerja dibawah tekanan',
    'f1722b' => 'Kompetensi yang diperlukan dalam pekerjaan - Manajemen waktu',
    'f1724b' => 'Kompetensi yang diperlukan dalam pekerjaan - Bekerja secara mandiri',
    'f1726b' => 'Kompetensi yang diperlukan dalam pekerjaan - Bekerja dalam tim',
    'f1728b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan memecahkan masalah',
    'f1730b' => 'Kompetensi yang diperlukan dalam pekerjaan - Negosiasi',
    'f1732b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan analisis',
    'f1734b' => 'Kompetensi yang diperlukan dalam pekerjaan - Toleransi',
    'f1736b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan adaptasi',
    'f1738b' => 'Kompetensi yang diperlukan dalam pekerjaan - Loyalitas',
    'f1738ba' => 'Kompetensi yang diperlukan dalam pekerjaan - Integritas',
    'f1740b' => 'Kompetensi yang diperlukan dalam pekerjaan - Bekerja dengan orang berbeda budaya',
    'f1742b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kepemimpinan',
    'f1744b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan memegang tanggung jawab',
    'f1746b' => 'Kompetensi yang diperlukan dalam pekerjaan - Inisiatif',
    'f1748b' => 'Kompetensi yang diperlukan dalam pekerjaan - Manajemen proyek / program',
    'f1750b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan mempresentasikan produk/ide/laporan',
    'f1752b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan dalam menulis laporan, memo, dan dokumen',
    'f1754b' => 'Kompetensi yang diperlukan dalam pekerjaan - Kemampuan untuk terus belajar sepanjang hayat',
];

$s_title = (array_key_exists($question_id, $a_question_description)) ? $a_question_description[$question_id] : '';
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="chart-wrapper-tracer w-100">
                    <canvas class="chart-tracer" id="chartbar_question"></canvas>
                </div>
                <div class="chart-wrapper-tracer w-100 mt-5">
                    <canvas class="chart-tracer" id="chartpie_question"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(e) {
    var data_chart = JSON.parse( '<?=($data_result) ? json_encode($data_result) : "[]";?>');
    var ctxbar = document.getElementById("chartbar_question").getContext("2d");
    var ctxpie = document.getElementById("chartpie_question").getContext("2d");
    var data_option = [];
    var dataresult = [];
    var coloR = [];
    var total_data = 0;

    var dynamicColors = function() {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgb(" + r + "," + g + "," + b + ")";
    };

    $.each(data_chart, function(i, v) {
        data_option.push(v.key_option);
        dataresult.push(v.result);
        coloR.push(dynamicColors());
        total_data += parseFloat(v.result) || 0;
    });
    // console.log(total_data);

    var data_resultbar = [{
        'label': 'Jumlah Responden',
        'backgroundColor': '#0014897d',
        'borderColor': '#001489',
        'pointHoverBackgroundColor': '#fff',
        'borderWidth': 2,
        'data': dataresult
    }];
    var data_resultpie = [{
        'label': 'Jumlah Responden',
        'backgroundColor': coloR,
        'data': dataresult,
        'hoverOffset': 4
    }];

    let mainChart = new Chart(ctxbar, {
        plugins: [ChartDataLabels],
        type: 'bar',
        data: {
            labels: data_option,
            datasets: data_resultbar
        },
        options: {
            plugins: {
                datalabels: {
                    // display: function(context) {
                    //     return context.dataset.data[context.dataIndex] > 15;
                    // },
                    display: true,
                    align: 'end',
                    anchor: 'end',
                    font: {
                        size: 20
                    },
                    formatter: Math.round
                },
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        min: 0
                    },
                    scaleLabel: {
                        display: true,
                        fontSize: 17,
                        labelString: 'Jumlah Responden'
                    }
                }],
            },
            legend: {
                display: false,
                // position: 'right',
                // align: 'start',
                // labels: {
                //     fontSize: 14
                // }
            },
            title: {
                display: true,
                text: '<?=$s_title;?>',
                fontSize: 29,
                padding: 40
            }
        },
    });

    let myPieChart = new Chart(ctxpie, {
        plugins: [ChartDataLabels],
        type: 'pie',
        data: {
            labels: data_option,
            datasets: data_resultpie
        },
        options: {
            plugins: {
                datalabels: {
                    // display: function(context) {
                    //     return context.dataset.data[context.dataIndex] > 15;
                    // },
                    color: 'white',
                    display: true,
                    font: {
                        size: 20
                    },
                    formatter: function(value, context) {
                        return Math.round((value / total_data) * 100) + '%';
                    }
                },
            },
            legend: {
                // display: false,
                position: 'right',
                align: 'start',
                labels: {
                    fontSize: 14
                }
            },
            title: {
                display: true,
                text: 'Persentase <?=$s_title;?>',
                fontSize: 29,
                padding: 40
            }
        },
    });
})
</script>