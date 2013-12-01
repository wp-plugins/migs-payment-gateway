<?php
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}

$paidList = MigsPaymentGatewayPaymentLogs::getPaymentLogsCharts(1);
$month = array();
$month_sum = array();
foreach ($paidList as $p) {
    array_push($month, $p->month);
    array_push($month_sum, MigsUtilities::filterNumberForChart($p->monthsum));
}
$month_sum_negative = array();
$notPaidList = MigsPaymentGatewayPaymentLogs::getPaymentLogsCharts(0);
foreach ($notPaidList as $p) {
    array_push($month_sum_negative, MigsUtilities::filterNumberForChart($p->monthsum));
}

?>

<script type="text/javascript" charset="utf-8" src="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/js/chart.min.js"></script>
<meta name = "viewport" content = "initial-scale = 1, user-scalable = no">
<table class="ui-widget ui-widget-content migstable">
    <thead>
        <tr class="ui-widget-header charthead">
            <th>Chart</th>
            <th>Accepted Payment details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><canvas id="canvas" height="200" width="800"></canvas></td>
            <td>
                <table class="ui-widget ui-widget-content">
                    <thead>
                        <tr class="ui-widget-header ">
                            <th>Month</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paidList as $p) { ?>
                        <tr>
                            <td><?php echo $p->month; ?></td>
                            <td><?php echo $p->monthsum; ?></td>
                        </tr>
                         <?php } ?>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<div class="bold">
    Last 12 months activity.<br/>
    - Blue color indicates the accepted payments.<br/>
    - Grey color indicates the failed payments.<br/>
</div>
<script type="text/javascript">

    var lineChartData = {
        labels: [<?php echo '"' . implode('","', $month) . '"'; ?>],
        datasets: [
            {
                fillColor: "rgba(220,220,220,0.5)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#D6473C",
                data: [<?php echo implode(",", $month_sum_negative); ?>]
            },
            {
                fillColor: "rgba(151,187,205,0.5)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                data: [<?php echo implode(",", $month_sum); ?>]
            }
        ]

    }

    var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData);
</script>
<hr />
<?php 
unset($notPaidList);
unset($paidList);
?>