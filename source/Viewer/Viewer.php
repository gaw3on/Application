<?php

namespace Application\Viewer;

use Application\RiotApi;

Class Viewer extends RiotApi {

    const
        DataDragon = "http://ddragon.leagueoflegends.com/cdn/",
        img_DataDragon = ".png\"",

        class_imgProfile = "class=\"rounded border summoner_icon\"",
        img_Profile= "/img/profileicon/",

        class_imgChampion = "class=\"rounded-circle lastgame\"",
        img_Champion = "/img/champion/",

        class_imgLastGame = "class=\"rounded-circle lastgame\"",

        class_LoadingChampion = "class=\"loading_champion\"",
        img_LoadingChampion = "img/champion/loading/";

    public function getProfileID (int $id){

        $result = "<img src=\"" .  self::DataDragon . $this->game_version .
            self::img_Profile . $id . self::img_DataDragon . self::class_imgProfile . ">";

        return $result;
    }

    public function getMasteryChampion (string $id){

        $result = "<img src=\"" .  self::DataDragon . $this->game_version .
            self::img_Champion . $id . self::img_DataDragon . self::class_imgChampion . ">";

        return $result;
    }

    public function getLastGameChampion (string $id){

        $result = "<img src=\"" .  self::DataDragon . $this->game_version .
            self::img_Champion . $id . self::img_DataDragon . self::class_imgLastGame . ">";

        return $result;

    }

    public function getLoadingChampion (int $id, string $summoner, $champions) {

        foreach($champions as $key => $value) {
            if($value['key'] == $id) {
                $result = "<img src=\"" .  self::DataDragon . self::img_LoadingChampion . $value['id'] .
                    "_0.jpg\" alt=\"" . $value['name'] . "\" " . self::class_LoadingChampion . ">";
            }
        }
        $result .= "<br>Summoner";
        $result .= "<br><strong><u>" . $summoner . "</u></strong>";

        return $result;
    }

    public function isBlueTeam($team) {
        if($team['teamId'] == 100) {
            return true;
        } else {
            return false;
        }

    }

    public function teamStats($team) {

        $result = "<img src=\"graphics/tower_icon.png\" class=\"stats_icon\">" . " Towers: " . $team['towerKills'];
        $result .= " / <img src=\"graphics/dragon_icon.png\" class=\"stats_icon\">" . " Dragons: " . $team['dragonKills'];
        $result .= " / <img src=\"graphics/baron_icon.png\" class=\"stats_icon\">" . " Barons: " . $team['baronKills'];

        return (string) $result;

    }

    public function fullNameRegion ($region) {
        if($region == "eun1") {
            return "<strong>Europe Nordic and East</strong>";
        } elseif($region == "euw1") {
            return "<strong>Europe West</strong>";
        } elseif($region == "na1") {
            return "<strong>North America</strong>";
        }
    }

    public $line;

    public function preferedposition($object) {

        $this->object = $object;

        $counter = array(
            "TOP"       => 0,
            "JUNGLE"    => 0,
            "MIDDLE"    => 0,
            "BOTTOM"    => 0,
        );

        foreach($this->object->matches as $key => $value) {
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

        if(array_search(max($counter), $counter) == "TOP") {
            $line1 =  "<img class=\"positions\" src=\"graphics/positions/Top_icon.png\"><br>";
        } elseif(array_search(max($counter), $counter) == "JUNGLE") {
            $line1 =  "<img class=\"positions\" src=\"graphics/positions/Jungle_icon.png\"><br>";
        } elseif(array_search(max($counter), $counter) == "MIDDLE") {
            $line1 =  "<img class=\"positions\" src=\"graphics/positions/Middle_icon.png\"><br>";
        } elseif(array_search(max($counter), $counter) == "BOTTOM") {
            $line1 =  "<img class=\"positions\" src=\"graphics/positions/Bottom_icon.png\"><br>";
        }

        $line2 = array_search(max($counter), $counter) . "<br>";
        $line3 = round(max($counter)/$this->object->totalGames*100) . "% of played games<br>";

        $line = "$line1 $line2 $line3";
        return (string) $line;
    }

    public function showtierlogo($tier) {

        $this->tier = $tier;

        if (isset($this->tier)) {
            switch ($this->tier) {
                case "CHALLENGER":
                    return (string) "<img src=\"graphics/emblems/Challenger_Emblem.png\" class=\"summoner_tier_icon\">";
                case "GRANDMASTER":
                    return (string) "<img src=\"graphics/emblems/Grandmaster_Emblem.png\" class=\"summoner_tier_icon\">";
                case "MASTER":
                    return (string) "<img src=\"graphics/emblems/Master_Emblem.png\" class=\"summoner_tier_icon\">";
                case "DIAMOND":
                    return (string) "<img src=\"graphics/emblems/Diamond_Emblem.png\" class=\"summoner_tier_icon\">";
                case "PLATINUM":
                    return (string) "<img src=\"graphics/emblems/Platinum_Emblem.png\" class=\"summoner_tier_icon\">";
                case "GOLD":
                    return (string) "<img src=\"graphics/emblems/Gold_Emblem.png\" class=\"summoner_tier_icon\">";
                case "SILVER":
                    return (string) "<img src=\"graphics/emblems/Silver_Emblem.png\" class=\"summoner_tier_icon\">";
                case "BRONZE":
                    return (string) "<img src=\"graphics/emblems/Bronze_Emblem.png\" class=\"summoner_tier_icon\">";
                case "IRON":
                    return (string) "<img src=\"graphics/emblems/Iron_Emblem.png\" class=\"summoner_tier_icon\">";
            }
        }
    }

    public function showtierdetail($ranking, $long) {

        $this->ranking = $ranking;

        if(isset($this->ranking)) {
            $line2 =  "<strong><u>" . $this->ranking->tier . " " . $this->ranking->rank . "</u><br>";
            $line3 = " " . $this->ranking->leaguePoints . " LP</strong>";
            $line4 =  " / " . $this->ranking->wins . "W / " . $this->ranking->losses . "L<br>";
            $line5 =  "Leauge: " . $this->ranking->leagueName . "<br>";
            $winratio = round($this->ranking->wins/($this->ranking->wins+$this->ranking->losses)*100);
            $line6 =  "Win ratio: " . $winratio . "%<br>";
            $line7 =  $this->showtierlogo($this->ranking->tier);
            $line8 =  "<br>";

            if($long) {
                $line = "$line2 $line3 $line4 $line5 $line6 $line7 $line8";
            } else {
                $line = "$line2 $line7";
            }
        }
        else {
            $line1 =  "Unranked" . "<br>";
            $line2 =  "<img src=\"graphics/emblems/Unranked_Emblem.png\" class=\"summoner_tier_icon\"><br>";

            $line = "$line1 $line2";
        }
        return (string) $line;
    }



    public function showtierunranked() {

        $line1 =  "Unranked" . "<br>";
        $line2 =  "<img src=\"graphics/emblems/Unranked_Emblem.png\" class=\"summoner_tier_icon\"><br>";

        $line = "$line1 $line2";

        return (string) $line;
    }

    public function showmasteries($champions, $masteries, $id) {
        foreach($champions as $key => $value) {
            if($value['key'] == $masteries[$id]->championId) {
                $line1 =  $this->getMasteryChampion($value['id']);
                $line2 =  "<br>Name: <strong>" . $value['name'] . "</strong>";
                $line3 =  "<br>Mastery Points: " . $masteries[$id]->championPoints . "<br>";

                $line = "$line1 $line2 $line3";
            }
        }
        return (string) $line;
    }

    public function showgame($champions, $matches, $id, $region, $summoner) {
        foreach($champions as $key => $value) {
            if($value['key'] == $matches->matches[$id]['champion']) {
                $line1 =  $this->getLastGameChampion($value['id']);
                $line2 =  "<form action=\"gamedetail.php\" method=\"GET\">";
                $line3 =  "<input type=\"hidden\" name=\"gameId\" value=\"" . $matches->matches[$id]['gameId'] . "\"></input>";
                $line4 =  "<input type=\"hidden\" name=\"region\" value=\"" . $region . "\"></input>";
                $line5 =  "<input type=\"hidden\" name=\"summoner\" value=\"" . $summoner . "\"></input>";
                $line6 =  "<button type=\"submit\" class=\"btn btn-primary\">Game as " . $value['name'] . " - see details!</button>";
                $line7 =  "</form>";

                $line = "$line1 $line2 $line3 $line4 $line5 $line6 $line7";
            }
        }
        return (string) $line;
    }

    public function processGraph($player, $dataPoints, $id, $summoner, $y) {

        $idd= $id+1;

        $line1 = "name: \"" . $player['summonerName'][$id] . "\",";
        $line3 = "type: \"spline\",";
        if($player['summonerName'][$id] == $summoner) {
            $line4 = "visible: true,";
        } else {
            $line4 = "visible: false,";
        }
        $line5 = "markerSize: 0,";
        $line6 = "toolTipContent: \"{y} " . $y ." at {x} minute\",";

        $line8 = "showInLegend: true,";
        $line9 = "dataPoints: " . json_encode($dataPoints[$idd], JSON_NUMERIC_CHECK);

        $line = "$line1 $line3 $line4 $line5 $line6 $line8 $line9";

        return (string) $line;

    }



}