    <?php

    require __DIR__ .'/vendor/autoload.php';
    use Application\RiotApi;
    use Application\Exceptions\GeneralException;

    try {
        if(!isset($_GET['summoner']) || $_GET['summoner'] == "") {
            throw new GeneralException("Summoner not defined!");
        } elseif(!isset($_GET['region']) || $_GET['region'] == "")  {
            throw new GeneralException("Region not defined!");
        }
    } catch (GeneralException $exc) {
        echo $exc->getMessage();
        die();
    }

    function showtierlogo($tier)
    {
        if (isset($tier)) {
            switch ($tier) {
                case "CHALLENGER":
                    return "<img src=\"graphics/emblems/Challenger_Emblem.png\" class=\"summoner_tier_icon\">";
                case "GRANDMASTER":
                    return "<img src=\"graphics/emblems/Grandmaster_Emblem.png\" class=\"summoner_tier_icon\">";
                case "MASTER":
                    return "<img src=\"graphics/emblems/Master_Emblem.png\" class=\"summoner_tier_icon\">";
                case "DIAMOND":
                    return "<img src=\"graphics/emblems/Diamond_Emblem.png\" class=\"summoner_tier_icon\">";
                case "PLATINUM":
                    return "<img src=\"graphics/emblems/Platinum_Emblem.png\" class=\"summoner_tier_icon\">";
                case "GOLD":
                    return "<img src=\"graphics/emblems/Gold_Emblem.png\" class=\"summoner_tier_icon\">";
                case "SILVER":
                    return "<img src=\"graphics/emblems/Silver_Emblem.png\" class=\"summoner_tier_icon\">";
                case "BRONZE":
                    return "<img src=\"graphics/emblems/Bronze_Emblem.png\" class=\"summoner_tier_icon\">";
                case "IRON":
                    return "<img src=\"graphics/emblems/Iron_Emblem.png\" class=\"summoner_tier_icon\">";
            }
        }
    }

    $nick = $_GET['summoner'];
    $region = $_GET['region'];

    $api = new RiotApi($region);
    $summoner = $api->getSummoner($nick);

    $position = $api->getPosition($summoner->id);

    foreach($position as $key => $value) {
        if($value->queueType == "RANKED_SOLO_5x5") {
            $ranked_solo = $value;
        }   elseif($value->queueType == "RANKED_FLEX_SR") {
            $ranked_flex_sr = $value;
        }   elseif($value->queueType == "RANKED_FLEX_TT") {
            $ranked_flex_tt = $value;
        }
    }

    $masteries = $api->getSummonerMasteries($summoner->id);

    $masteries_first = $masteries[0];
    $masteries_second = $masteries[1];
    $masteries_third = $masteries[2];

    $champions = $api->call_URL("http://ddragon.leagueoflegends.com/cdn/9.1.1/data/en_US/champion.json");
    $champions = $champions['data'];

    $matches = $api->getMatchList($summoner->accountId);

    $counter = array(
            "TOP"       => 0,
            "JUNGLE"    => 0,
            "MIDDLE"    => 0,
            "BOTTOM"    => 0,
    );

    foreach($matches->matches as $key => $value) {
        if($value['lane'] == "TOP") {
            $counter['TOP'] += 1;
        } elseif($value['lane'] == "JUNGLE") {
            $counter['JUNGLE'] += 1;
        } elseif($value['lane'] == "MID") {
            $counter['MIDDLE'] += 1;
        } elseif($value['lane'] == "BOTTOM") {
            $counter['BOTTOM'] += 1;
        } elseif($value['lane'] == "NONE") {
            $counter['BOTTOM'] += 1;
        }
    }

?>

<!DOCTYPE html>
<html >
<head>
    <title>Summoner details - <?php echo $summoner->name ?></title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="graphics/style.css">

</head>

    <body class="summoner_body">

    RiotApi Application / Gawron / 2019

    <div class="container">

        <div class="row justify-content-around">
            <div class="col-3 text-center bordersilver">
                <u>PREFERRED POSITION</u><br>
                <?php
                if(array_search(max($counter), $counter) == "TOP") {
                    echo "<img class=\"positions\" src=\"graphics/positions/Top_icon.png\">";
                } elseif(array_search(max($counter), $counter) == "JUNGLE") {
                    echo "<img class=\"positions\" src=\"graphics/positions/Jungle_icon.png\">";
                } elseif(array_search(max($counter), $counter) == "MIDDLE") {
                    echo "<img class=\"positions\" src=\"graphics/positions/Middle_icon.png\">";
                } elseif(array_search(max($counter), $counter) == "BOTTOM") {
                    echo "<img class=\"positions\" src=\"graphics/positions/Bottom_icon.png\">";
                }
                echo "<br>" . array_search(max($counter), $counter);
                echo "<br>";
                echo round(max($counter)/$matches->totalGames*100) . "% of played games<br>";
                ?>
            </div>
            <div class="col-5 text-center bordersilver">
                Summoner:<br>
                <h1><strong><u><?php echo $summoner->name . "<br>" ?></u></strong></h1>
                Account level:
                <strong><?php echo $summoner->summonerLevel ?></strong><br>
                <?php echo $api->getProfileID($summoner->profileIconId) ?>
            </div>
            <div class="col-3 text-center">

            </div>


        </div>


        <div class="row justify-content-around">
            <div class="col-3 bordersilver">
                <u>LAST GAMES (details TBD)</u><br>
                <?php
                foreach($champions as $key => $value) {
                    if($value['key'] == $matches->matches[0]['champion']) {
                        echo $api->getLastGameChampion($value['id']);
                        echo "<form action=\"gamedetail.php\" method=\"GET\">";
                        echo "<input type=\"hidden\" name=\"gameId\" value=\"" . $matches->matches[0]['gameId'] . "\"></input>";
                        echo "<input type=\"hidden\" name=\"region\" value=\"" . $region . "\"></input>";
                        echo "<button type=\"submit\" class=\"btn btn-primary\">Game as " . $value['name'] . "- see details!</button>";
                        echo "</form>";
                    }
                }
                foreach($champions as $key => $value) {
                    if($value['key'] == $matches->matches[1]['champion']) {
                        echo $api->getLastGameChampion($value['id']);
                        echo "<form action=\"gamedetail.php\" method=\"GET\">";
                        echo "<input type=\"hidden\" name=\"gameId\" value=\"" . $matches->matches[1]['gameId'] . "\"></input>";
                        echo "<input type=\"hidden\" name=\"region\" value=\"" . $region . "\"></input>";
                        echo "<button type=\"submit\" class=\"btn btn-primary\">Game as " . $value['name'] . "- see details!</button>";
                        echo "</form>";
                    }
                }
                foreach($champions as $key => $value) {
                    if($value['key'] == $matches->matches[2]['champion']) {
                        echo $api->getLastGameChampion($value['id']);
                        echo "<form action=\"gamedetail.php\" method=\"GET\">";
                        echo "<input type=\"hidden\" name=\"gameId\" value=\"" . $matches->matches[2]['gameId'] . "\"></input>";
                        echo "<input type=\"hidden\" name=\"region\" value=\"" . $region . "\"></input>";
                        echo "<button type=\"submit\" class=\"btn btn-primary\">Game as " . $value['name'] . "- see details!</button>";
                        echo "</form>";
                    }
                }
                ?>

            </div>

            <div class="col-5 bordersilver">
                <table  class="table">
                    <tbody>
                    <tr>
                        <td style="text-center" colspan="2">
                            <?php
                            if(isset($ranked_solo)) {
                                echo "RANKING SOLO" . "<br>";
                                echo "<strong><u>" . $ranked_solo->tier . " " . $ranked_solo->rank . "</u><br>";
                                echo " " . $ranked_solo->leaguePoints . " LP</strong>";
                                echo " / " . $ranked_solo->wins . "W / " . $ranked_solo->losses . "L<br>";
                                echo "Leauge: " . $ranked_solo->leagueName . "<br>";
                                $winratio = round($ranked_solo->wins/($ranked_solo->wins+$ranked_solo->losses)*100);
                                echo "Win ratio: " . $winratio . "%<br>";
                                echo showtierlogo($ranked_solo->tier);
                                echo "<br>";
                            }
                               else {
                                echo "Unranked" . "<br>";
                                echo "<img src=\"graphics/emblems/Unranked_Emblem.png\" class=\"summoner_tier_icon\"><br>";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>RANKING FLEX (5vs5)<br>
                            <?php
                            if(isset($ranked_flex_sr)) {
                                echo $ranked_flex_sr->tier . "<br>";
                                echo showtierlogo($ranked_flex_sr->tier);
                                echo "<br>";
                            }   else {
                                echo "Unranked" . "<br>";
                                echo "<img src=\"graphics/emblems/Unranked_Emblem.png\" class=\"summoner_tier_icon\"><br>";
                            }

                            ?>
                        </td>
                        <td>RANKING FLEX (3vs3)<br>
                            <?php
                            if(isset($ranked_flex_tt)) {
                                echo $ranked_flex_tt->tier . "<br>";
                                echo showtierlogo($ranked_flex_tt->tier);
                                echo "<br>";
                            }   else {
                                echo "Unranked" . "<br>";
                                echo "<img src=\"graphics/emblems/Unranked_Emblem.png\" class=\"summoner_tier_icon\"><br>";
                            }
                            ?>

                        </td>
                    </tr>
                    </tbody>
                </table>




            </div>
            <div class="col-3 bordersilver">
                <u>CHAMPION MASTERIES</u><br>
                    <?php
                    foreach($champions as $key => $value) {
                        if($value['key'] == $masteries_first->championId) {
                            echo $api->getMasteryChampion($value['id']);
                            echo "<br>Name: <strong>" . $value['name'] . "</strong>";
                            echo "<br>Mastery Points: " . $masteries_first->championPoints . "<br>";
                        }
                    }
                    foreach($champions as $key => $value) {
                        if($value['key'] == $masteries_second->championId) {
                            echo $api->getMasteryChampion($value['id']);
                            echo "<br>Name: <strong>" . $value['name'] . "</strong>";
                            echo "<br>Mastery Points: " . $masteries_second->championPoints . "<br>";
                        }
                    }
                    foreach($champions as $key => $value) {
                        if($value['key'] == $masteries_third->championId) {
                            echo $api->getMasteryChampion($value['id']);
                            echo "<br>Name: <strong>" . $value['name'] . "</strong>";
                            echo "<br>Mastery Points: " . $masteries_third->championPoints . "<br>";
                        }
                    }
                    ?>

            </div>

        </div>






    </div>

    </div>

    </body>
    </html>




