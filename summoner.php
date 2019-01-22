    <?php

    require __DIR__ .'/vendor/autoload.php';

    use Application\RiotApi;
    use Application\Viewer;
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

    $nick = $_GET['summoner'];
    $region = $_GET['region'];

    $api = new RiotApi();
    $api->setregion($region);

    $viewer = new Viewer\Viewer();

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

    $matches = $api->getMatchList($summoner->accountId);

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
                echo $viewer->preferedposition($matches);
                ?>
            </div>
            <div class="col-5 text-center bordersilver">
                Summoner:<br>
                <h1><strong><u><?php echo $summoner->name . "<br>" ?></u></strong></h1>
                Account level:
                <strong><?php echo $summoner->summonerLevel ?></strong><br>
                <?php echo $viewer->getProfileID($summoner->profileIconId) ?>
            </div>
            <div class="col-3 text-center">

            </div>
        </div>


        <div class="row justify-content-around">
            <div class="col-3 bordersilver">
                <u>LAST GAMES (details TBD)</u><br>
                <?php
                echo $viewer->showgame($api->champions, $matches, "0", $region);
                echo $viewer->showgame($api->champions, $matches, "1", $region);
                echo $viewer->showgame($api->champions, $matches, "2", $region);
                ?>

            </div>

            <div class="col-5 bordersilver">
                <table  class="table">
                    <tbody>
                    <tr>
                        <td style="text-center" colspan="2">RANKING SOLO
                            <?php
                            $tier1 = isset($ranked_solo) ? $viewer->showtierdetail($ranked_solo, true) : $viewer->showtierunranked();
                            echo $tier1;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>RANKING FLEX (5vs5)<br>
                            <?php
                            $tier2 = isset($ranked_flex_sr) ? $viewer->showtierdetail($ranked_flex_sr, false) : $viewer->showtierunranked();
                            echo $tier2;
                            ?>
                        </td>
                        <td>RANKING FLEX (3vs3)<br>
                            <?php
                            $tier3 = isset($ranked_flex_tt) ? $viewer->showtierdetail($ranked_flex_tt, false) : $viewer->showtierunranked();
                            echo $tier3;
                            ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-3 bordersilver">
                <u>CHAMPION MASTERIES</u><br>
                    <?php
                    echo $viewer->showmasteries($api->champions, $masteries, "0");
                    echo $viewer->showmasteries($api->champions, $masteries, "1");
                    echo $viewer->showmasteries($api->champions, $masteries, "2");
                    ?>

            </div>

        </div>
    </div>
    </div>

    </body>
    </html>




