<?php

require __DIR__ .'/vendor/autoload.php';
use Application\RiotApi;
use Application\Viewer;
use Application\Exceptions\GeneralException;

try {
    if(!isset($_GET['gameId']) || $_GET['gameId'] == "") {
        throw new GeneralException("GameId not defined!");
    } elseif(!isset($_GET['region']) || $_GET['region'] == "")  {
        throw new GeneralException("Region not defined!");
    }
} catch (GeneralException $exc) {
    echo $exc->getMessage();
    die();
}

/** @var team blue 100, red 200  */

$region = $_GET['region'];
$gameId = $_GET['gameId'];

$api = new RiotApi();
$viewer = new Viewer\Viewer();

$api->setregion($region);

$getmatch = $api->getMatchDetail($gameId);

$gettimeline = $api->getMatchTimeline($gameId);

foreach($getmatch->participantIdentities as $key => $value) {
    $player['summonerName'][$key] = $value['player']['summonerName'];
}

foreach($getmatch->participants as $key => $value) {
    $player['details'][$key] = $value;
}


for($i=0; $i<10; $i++) {
    for($j=0; $j<sizeof($gettimeline->frames); $j++) {
        $goldchart[$i][$j] = $gettimeline->frames[$j]['participantFrames'][$i+1]['totalGold'];
    }
}

?>

<?php





for($k=0; $k<10; $k++) {;
    for($i=0; $i<count($goldchart[$k]); $i++) {
        $temp = array("x" => $i, "y" => $goldchart[$k][$i]);
        $dataPoints[$k+1][] = $temp;
    }
}






?>


<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">


    <script>
        window.onload = function () {

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                title:{
                    text: "Player gold difference in time"
                },
                axisX: {
                    title: "Game time (minutes)",
                },

                axisY: {
                    title: "Gold",
                },
                legend:{
                    cursor: "pointer",
                    dockInsidePlotArea: true,
                    itemclick: toggleDataSeries
                },

                data: [{
                    name: "<?php echo $player['summonerName'][0] ?>",
                    type: "spline",
                    visible: false,
                    markerSize: 2,
                    toolTipContent: "{y} gold at {x} minute",
                    showInLegend: true,
                    dataPoints: <?php echo json_encode($dataPoints[1], JSON_NUMERIC_CHECK); ?>
                }, {
                    name: "<?php echo $player['summonerName'][1] ?>",
                    type: "spline",
                    markerSize: 2,
                    toolTipContent: "{y} gold",
                    showInLegend: true,
                    dataPoints: <?php echo json_encode($dataPoints[2], JSON_NUMERIC_CHECK); ?>
                }, {
                    name: "<?php echo $player['summonerName'][2] ?>",
                    type: "spline",
                    markerSize: 2,
                    toolTipContent: "{y} gold",
                    showInLegend: true,
                    dataPoints: <?php echo json_encode($dataPoints[3], JSON_NUMERIC_CHECK); ?>
                }
                ]
            });

            chart.render();

            function toggleDataSeries(e){
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                }
                else{
                    e.dataSeries.visible = true;
                }
                chart.render();
            }

        }




    </script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 60%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

</body>
</html>