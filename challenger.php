<?php

    require __DIR__ .'/vendor/autoload.php';
    use Application\RiotApi;
    use Application\Viewer;
    use Application\Exceptions\GeneralException;

    try {
        if(!isset($_GET['region']) || $_GET['region'] == "") {
            throw new GeneralException("Region not defined!");
        }
    } catch (GeneralException $exc) {
        echo $exc->getMessage();
        die();
    }

    $region = $_GET['region'];

    $api = new RiotApi();
    $api->setregion($region);

    $viewer = new Viewer\Viewer();

    $data = $api->challengerlist();

    usort($data->entries, array('Application\RiotApi', 'sortbypoints'));


?>

<!DOCTYPE html>
<html>
<head>
<title>Challenger Ranking</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="graphics/style.css">



</head>
    <body class="gamedetails_body">

    <div class="container_challenger">
        <div class="row">
            <div class="col">
                <p class="tier_details"><?php echo $data->tier ?>
                    <br />
                    <?php
                    echo $viewer->fullNameRegion($region);
                    ?>
                    <p>League name: <i><u><?php echo $data->name ?></u></i>
                    <br><?php echo date("F j, Y, g:i a") ?>
                    </p>
                </p>

                <img src="graphics\emblems\Challenger_Emblem.png" class="tier_icon"/>
                <br />
                <table class="table text-center table-striped tablesorter" id="table">

                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Summoner Name</th>
                        <th scope="col">Wins</th>
                        <th scope="col">Looses</th>
                        <th scole="col">Winning ratio</th>
                        <th scope="col">Points</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    foreach ($data->entries as $key => $value) {
                        $winratio = round(($value['wins']/($value['wins']+$value['losses'])*100));
                        $number = $key+1;
                        echo "<tr onclick=\"window.location='summoner.php?summoner=" . $value['summonerName'] . "&region=" . $region . "';\" class=\"summoner\">";
                        echo "<th scope=\"row\">" . $number . "</th>";
                        echo "<td><u>" . $value['summonerName'] . "</u></td>";
                        echo "<td>" . $value['wins'] . "</td>";
                        echo "<td>" . $value['losses'] . "</td>";
                        echo "<td class=\"align-middle\">" ;
                        echo "<div class=\"progress\">";
                        echo "<div class=\"progress-bar bg-success\" style=\"width:" . $winratio . "%\">" . $winratio . "%</div>";
                        echo "</div>";
                        echo "</td>";
                        echo "<td>" . $value['leaguePoints'] . " LP</td>";
                        echo "</tr></a>\n";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="//code.jquery.com/jquery-git.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.js"></script>
    <script type="text/javascript" src="https://mottie.github.io/tablesorter/dist/js/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" src="https://mottie.github.io/tablesorter/js/jquery.tablesorter.widgets.js"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            var $table = $('#table').tablesorter({
                theme: 'blue',
                widthFixed : true,
                sortLocaleCompare: true,
                widgets: ['zebra'],
                sortList: [
                    [5, 1],
                ]
            });
        });
    </script>
    </body>
</html>




