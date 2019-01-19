<?php

    require __DIR__ .'/vendor/autoload.php';
    use Application\RiotApi;
    use Application\Exceptions\GeneralException;

    /**
     *  Get Challenger Player list (by region) and sort it (by points);
     */

    try {
        if(!isset($_GET['region']) || $_GET['region'] == "") {
            throw new GeneralException("Region not defined!");
        }
    } catch (GeneralException $exc) {
        echo $exc->getMessage();
        die();
    }

    $region = $_GET['region'];

    $api = new RiotApi($region);
    $data = $api->challengerlist($region);
    $entries = $data->entries;

    function cmp( $a, $b )
    {
        if(  $a['leaguePoints'] ==  $b['leaguePoints'] ){ return 0 ; }
        return ($a['leaguePoints'] > $b['leaguePoints']) ? -1 : 1;
    }

    usort($entries,'cmp');
    $data->entries = $entries;

?>

<!DOCTYPE html>
<html>
<head>
<title>Challenger Ranking</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="graphics/style.css">

</head>
    <body class="challenger_body">

    <div class="container_challenger">
        <div class="row">
            <div class="col">
                <p class="tier_details"><?php echo $data->tier ?>
                    <br />
                    <?php
                    if($region == "eun1") {
                        echo "<strong>Europe Nordic and East</strong>";
                    } elseif($region == "euw1") {
                        echo "<strong>Europe West</strong>";
                    } elseif($region == "na1") {
                        echo "<strong>North America</strong>";
                    } else {
                        return false;
                    }
                    ?>
                    <p>League name: <i><u><?php echo $data->name ?></u></i>
                    <br><?php echo date("F j, Y, g:i a") ?>
                    </p>
                </p>

                <img src="graphics\emblems\Challenger_Emblem.png" class="tier_icon"/>
                <br />
                <table class="table text-center table-striped">

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

    </body>
</html>




