<?php

require __DIR__ .'/vendor/autoload.php';
use Application\RiotApi;
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

$getmatch = json_decode(file_get_contents("getmatch.json"), true);

echo "<br>";
$gettimeline = json_decode(file_get_contents("getmatchtimeline.json"), true);

foreach($getmatch['participantIdentities'] as $key => $value) {
    $player['summonerName'][$key] = $value['player']['summonerName'];
}

foreach($getmatch['participants'] as $key => $value) {
    $player['details'][$key] = $value;
}



for($i=0; $i<10; $i++) {
    for($j=0; $j<sizeof($gettimeline['frames']); $j++) {
        $goldchart[$i][$j] = $gettimeline['frames'][$j]['participantFrames'][$i+1]['totalGold'];
    }
}

?>
<?php

$dataPoints1 = array();
$dataPoints2 = array();
$dataPoints3 = array();

for($i=0; $i<count($goldchart[0]); $i++) {
    $temp = array("x" => $i, "y" => $goldchart[0][$i]);
    $dataPoints1[] = $temp;
}

for($i=0; $i<count($goldchart[1]); $i++) {
    $temp = array("x" => $i, "y" => $goldchart[1][$i]);
    $dataPoints2[] = $temp;
}

for($i=0; $i<count($goldchart[2]); $i++) {
    $temp = array("x" => $i, "y" => $goldchart[2][$i]);
    $dataPoints3[] = $temp;
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
                    title: "Frames",
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
                    name: "Player 1",
                    type: "spline",
                    markerSize: 2,
                    toolTipContent: "{y} gold",
                    showInLegend: true,
                    dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
                }, {
                    type: "spline",
                    markerSize: 2,
                    toolTipContent: "{y} gold",
                    showInLegend: true,
                    dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
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