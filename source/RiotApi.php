<?php

namespace Application;

use Application\Objects;
use Application\Exceptions\GeneralException;

class RiotApi
{
    const
        API_KEY = 'RGAPI-42cb330b-c3f4-434d-9b06-fb8d59874f9f';

    private $data = array();
    protected $region;
    public $game_version;
    public $champions;

    public function __construct($region)
    {
        $this->region = $region;

        $this->game_version = $this->call_URL("https://ddragon.leagueoflegends.com/api/versions.json");
        $this->game_version = $this->game_version[0];

        $champions_link= "http://ddragon.leagueoflegends.com/cdn/" . $this->game_version . "/data/en_US/champion.json";
        $this->champions = $this->call_URL($champions_link);
        $this->champions = $this->champions['data'];
    }

    public function call_URL(string $url)
    {

        $curl = curl_init();

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => ['X-Riot-Token: ' . self::API_KEY],
        );

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $this->data = $response;
        $this->data = json_decode($this->data, true);

        $processed = $this->process_URL($httpCode);

        curl_close($curl);

        if ($processed == true) {
            return $this->data;
        } else {
            die(PHP_EOL . " Data processing failed!");
        }
    }

    public function process_URL(int $httpCode)
    {
        $processed = true;

        try {
            if ($httpCode !== 200) {
                switch ($httpCode) {
                    case 504:
                        throw new GeneralException('Api: Gateway timeout - ' . $httpCode);
                    case 503:
                        throw new GeneralException('Api: Service unavailable - ' . $httpCode);
                    case 502:
                        throw new GeneralException('Api: Bad gateway - ' . $httpCode);
                    case 500:
                        throw new GeneralException('Api: Internal server error - ' . $httpCode);
                    case 429:
                        throw new GeneralException('Api: Rate limit exceeded - ' . $httpCode);
                    case 415:
                        throw new GeneralException('Api: Unsupported media type - ' . $httpCode);
                    case 405:
                        throw new GeneralException('Api: Method not allowed - ' . $httpCode);
                    case 404:
                        throw new GeneralException('Api: Data not found - ' . $httpCode);
                    case 403:
                        throw new GeneralException('Api: Forbidden - ' . $httpCode);
                    case 401:
                        throw new GeneralException('Api: Unauthorized - ' . $httpCode);
                    case 400:
                        throw new GeneralException('Api: Bad request - ' . $httpCode);
                    default:
                        if ($httpCode > 400) {
                            throw new GeneralException('Api: Unknown error - ' . $httpCode);
                        }
                }
            }
        } catch (GeneralException $exc) {
            echo $exc->getMessage();
            $processed = false;
        }

        return $processed;

    }

    public $url;

    public function return_URL(string $url)
    {
        $this->url = $url;
        return $this;
    }

    public $endpoint, $version, $method, $parameter;

    public function set_URL($version, $endpoint, $method, $parameter)
    {
        $this->version = $version;
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->parameter = $parameter;

        return (string) ('https://' . $this->region . $this->endpoint . $this->version . $this->method . $this->parameter);
    }

    public static function sortbypoints($var1, $var2) {
        if($var1['leaguePoints'] ==  $var2['leaguePoints']){return 0 ;}
        return ($var1['leaguePoints'] > $var2['leaguePoints']) ? -1 : 1;
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


    /**
     * @param $name
     * @return Objects/SummonerDTO
     * /lol/summoner/v4/summoners/by-name/{summonerName}
     */

    const VERSION_APP = "/v4/";
    const ENDPOINT_SUMMONER = '.api.riotgames.com/lol/summoner';
    const METHOD_SUMMONER = 'summoners/by-name/';

    public function getSummoner(string $name)
    {
        $name = str_replace(' ', '%20', $name);
        $link = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_SUMMONER, self:: METHOD_SUMMONER, $name);
        $result = $this->call_URL($link);

        return new Objects\SummonerDTO($result);
    }


    const ENDPOINT_LEAUGE = '.api.riotgames.com/lol/league';
    const METHOD_LEAUGE = 'positions/by-summoner/';

    public function getPosition(string $id)
    {
        $link = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_LEAUGE, self:: METHOD_LEAUGE, $id);

        $result = $this->call_URL($link);
        $results = [];
        foreach ($result as $object)
            $results[] = new Objects\LeaguePositionDTO($object);

        return $results;

    }

    public function challengerlist()
    {
        $link = "https://" . $this->region . ".api.riotgames.com/lol/league/v4/challengerleagues/by-queue/RANKED_SOLO_5x5";

        $result = $this->call_URL($link);
        return new Objects\LeagueListDTO($result);

    }

    const ENDPOINT_MASTERIES = '.api.riotgames.com/lol/champion-mastery';
    const METHOD_MASTERIES= 'champion-masteries/by-summoner/';

    public function getSummonerMasteries(string $id) {

        $link = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_MASTERIES, self:: METHOD_MASTERIES, $id);

        $result = $this->call_URL($link);
        $results = [];
        foreach ($result as $object)
            $results[] = new Objects\ChampionMasteryDTO($object);

        return $results;

    }



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

    const ENDPOINT_MATCHES = '.api.riotgames.com/lol/match';
    const METHOD_MATCHES= 'matchlists/by-account/';

    public function getMatchList (string $id) {

        $link = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_MATCHES, self:: METHOD_MATCHES, $id);

        $result = $this->call_URL($link);
        return new Objects\MatchlistDto($result);
    }



}


