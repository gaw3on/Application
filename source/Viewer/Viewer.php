<?php

namespace Application\Viewer;

use Application\RiotApi;

Class Viewer extends RiotApi {

    public function getProfileID (int $id){

        return "<img src=\"http://ddragon.leagueoflegends.com/cdn/"
            . $this->game_version . "/img/profileicon/" . $id . ".png\" class=\"rounded border summoner_icon\">";

    }

    public function getMasteryChampion (string $id){

        return "<img class=\"rounded-circle masteries\" src=\"http://ddragon.leagueoflegends.com/cdn/" .
            $this->game_version . "/img/champion/" . $id . ".png\">";

    }

    public function getLastGameChampion (string $id){

        return "<img class=\"rounded-circle lastgame\" src=\"http://ddragon.leagueoflegends.com/cdn/" .
            $this->game_version . "/img/champion/" . $id . ".png\">";

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

    public function tierdetail($ranking, $long) {

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



    public function tierunranked() {

        $line1 =  "Unranked" . "<br>";
        $line2 =  "<img src=\"graphics/emblems/Unranked_Emblem.png\" class=\"summoner_tier_icon\"><br>";

        $line = "$line1 $line2";

        return (string) $line;
    }

    public function masteries($champions, $masteries, $id) {
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

    public function game($champions, $matches, $id) {
        foreach($champions as $key => $value) {
            if($value['key'] == $matches->matches[$id]['champion']) {
                $line1 =  $this->getLastGameChampion($value['id']);
                $line2 =  "<form action=\"gamedetail.php\" method=\"GET\">";
                $line3 =  "<input type=\"hidden\" name=\"gameId\" value=\"" . $matches->matches[$id]['gameId'] . "\"></input>";
                $line4 = "<input type=\"hidden\" name=\"region\" value=\"" . $this->region . "\"></input>";
                $line5 =  "<button type=\"submit\" class=\"btn btn-primary\">Game as " . $value['name'] . "- see details!</button>";
                $line6 =  "</form>";

                $line = "$line1 $line2 $line3 $line4 $line5 $line6";
            }
        }
        return (string) $line;
    }

}