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
    } elseif(!isset($_GET['summoner']) || $_GET['summoner'] == "")  {
        throw new GeneralException("Summoner not defined!");
    }
} catch (GeneralException $exc) {
    echo $exc->getMessage();
    die();
}

$region = $_GET['region'];
$gameId = $_GET['gameId'];
$summoner = $_GET['summoner'];

$api = new RiotApi();
$viewer = new Viewer\Viewer();

$api->setregion($region);

$getmatch = $api->getMatchDetail($gameId);

$gettimeline = $api->getMatchTimeline($gameId);

$team_1 = $getmatch->teams[0];
$team_2 = $getmatch->teams[1];

foreach($getmatch->participantIdentities as $key => $value) {
    $players_data['summonerName'][$key] = $value['player']['summonerName'];
}

foreach($getmatch->participants as $key => $value) {
    $players_data['championId'][$key] = $value['championId'];
    $players_data['stats'][$key] = $value['stats'];
}


/**
 Gold chart
 */

for($i=0; $i<10; $i++) {
    for($j=0; $j<sizeof($gettimeline->frames); $j++) {
        $goldchart[$i][$j] = $gettimeline->frames[$j]['participantFrames'][$i+1]['totalGold'];
    }
}
for($k=0; $k<10; $k++) {;
    for($i=0; $i<count($goldchart[$k]); $i++) {
        $temp = array("x" => $i, "y" => $goldchart[$k][$i]);
        $dataPoints[$k+1][] = $temp;
    }
}

/**
Minions chart
 */

for($i=0; $i<10; $i++) {
    for($j=0; $j<sizeof($gettimeline->frames); $j++) {
        $minionschart[$i][$j] = $gettimeline->frames[$j]['participantFrames'][$i+1]['minionsKilled'];
    }
}
for($k=0; $k<10; $k++) {;
    for($i=0; $i<count($minionschart[$k]); $i++) {
        $temp = array("x" => $i, "y" => $minionschart[$k][$i]);
        $dataPoints2[$k+1][] = $temp;
    }
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="graphics/style.css">
    <script type="text/javascript" src="//code.jquery.com/jquery-git.js"></script>
    <title>Game details</title>

    <script>

        window.onload = function () {

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                title:{
                    text: "Player gold difference in time"
                },
                axisX: {
                    title: "Game time (minutes)",
                    interval: 1
                },
                axisY: {
                    title: "Gold",
                    interval: 2500
                },
                legend:{
                    cursor: "pointer",
                    horizontalAlign: "right",
                    verticalAlign: "center",
                    itemclick: toggleDataSeries

                },
                data: [
                    <?php
                    for($i=0; $i<10; $i++) {
                        echo "{";
                        echo $viewer->processGraph($players_data, $dataPoints, $i, $summoner, "Gold");
                        echo "},";
                    }
                    ?>
                ]
            });

            var chart2 = new CanvasJS.Chart("chartContainer2", {
                animationEnabled: true,
                title:{
                    text: "Mionions killed  in time"
                },
                axisX: {
                    title: "Game time (minutes)",
                    interval: 1
                },
                axisY: {
                    title: "Minions",
                    interval: 25
                },
                legend:{
                    cursor: "pointer",
                    horizontalAlign: "right",
                    verticalAlign: "center",
                    itemclick: toggleDataSeries2

                },
                data: [ <?php
                    for($i=0; $i<10; $i++) {
                        echo "{";
                        echo $viewer->processGraph($players_data, $dataPoints2, $i, $summoner, "Minions");
                        echo "},";
                    }
                    ?>
                ]
            });

            chart.render();
            chart2.render();

            function toggleDataSeries(e){
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                }
                else{
                    e.dataSeries.visible = true;
                }
                chart.render();
            }

            function toggleDataSeries2(e){
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                }
                else{
                    e.dataSeries.visible = true;
                }
                chart2.render();
            }
        }
    </script>

</head>
<body class="challenger_body">
<div class="container_gamedetails">
    <div class="title">GAME OVERVIEW</div>
<table class="table">
    <tr>
        <?php
            for($i=0; $i<5; $i++) {
                echo "<td>";
                echo $viewer->getLoadingChampion($players_data['championId'][$i], $players_data['summonerName'][$i], $api->champions);
                echo "</td>";
            }
        ?>
    </tr>
    <tr>
        <td colspan="5" class="<?php echo $var = $viewer->isBlueTeam($team_1) ? "blueteam" : "redteam" ?>">
            <span class="left"><img src="graphics/team_icon.png" class="stats_icon"> Team 1 /
            <?php echo $var = $viewer->isBlueTeam($team_1) ? "Blue team / " : "Red team / " ?>
            <?php echo $var = ($team_1['win'] == "Win") ? "WIN" : "LOST" ?>
            </span>
            <span class="right">
                <?php echo $viewer->teamStats($team_1) ?>
            </span>
        </td>
    </tr>
    <tr>
        <td colspan="5"><strong><u>VS.</u></strong></td>
    </tr>
    <tr>
        <td colspan="5" class="<?php echo $var = $viewer->isBlueTeam($team_2) ? "blueteam" : "redteam" ?>">
            <span class="left"><img src="graphics/team_icon.png" class="stats_icon"> Team 2 /
            <?php echo $var = $viewer->isBlueTeam($team_2) ? "Blue team / " : "Red team / " ?>
                <?php echo $var = ($team_2['win'] == "Win") ? "WIN" : "LOST" ?>
            </span>
            <span class="right">
                <?php echo $viewer->teamStats($team_2) ?>
            </span>
        </td>
    </tr>
    <tr>
        <?php
        for($i=5; $i<10; $i++) {
            echo "<td>";
            echo $viewer->getLoadingChampion($players_data['championId'][$i], $players_data['summonerName'][$i], $api->champions);
            echo "</td>";
            }
        ?></tr>
</table>
    <div class="title">FINAL BUILD</div>
    <div class="title">STATISTICS</div>
    <div class="title">GRAPHICS PRESENTATION</div>

                <div id="chartContainer" class="panel" style="height: 400px; width: 800px; margin: auto"></div>

                <div id="chartContainer2" class="panel" style="height: 400px; width: 800px; margin: auto"></div>

</div>

<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

</body>
</html>