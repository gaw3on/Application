<?php

namespace Application;

use Application\Objects;
use Application\Exceptions\GeneralException;

class RiotApi
{
    const
        API_KEY = 'RGAPI-5a775450-e3ff-400d-8ca5-2fbb0aad712a';

    private $data = array();
    protected $region;
    public $game_version;
    public $champions;

    public function __construct()
    {
        $this->game_version = $this->call_URL("https://ddragon.leagueoflegends.com/api/versions.json");
        $this->game_version = $this->game_version[0];

        $champions_link= "http://ddragon.leagueoflegends.com/cdn/" . $this->game_version . "/data/en_US/champion.json";
        $this->champions = $this->call_URL($champions_link);
        $this->champions = $this->champions['data'];
    }

    public function setregion($region) {

        $this->region = $region;
        return $this->region;

    }

    public function call_URL(string $url) {

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

    /**
     * @param $name
     * @return Objects/SummonerDTO
     * /lol/summoner/v4/summoners/by-name/{summonerName}
     */

    const VERSION_APP = "/v4/";
    const ENDPOINT_SUMMONER = '.api.riotgames.com/lol/summoner';
    const METHOD_SUMMONER = 'summoners/by-name/';

    public function getSummoner(string $summonerName)
    {
        $summonerName = str_replace(' ', '%20', $summonerName);
        $access = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_SUMMONER, self:: METHOD_SUMMONER, $summonerName);
        $result = $this->call_URL($access);

        return new Objects\SummonerDTO($result);
    }

    /**
     * @param $id
     * @return Objects/LeaguePositionDTO
     * /lol/summoner/v4/positions/by-summoner/{id}
     */

    const ENDPOINT_LEAUGE = '.api.riotgames.com/lol/league';
    const METHOD_LEAUGE = 'positions/by-summoner/';

    public function getPosition(string $id)
    {
        $access = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_LEAUGE, self:: METHOD_LEAUGE, $id);
        $result = $this->call_URL($access);

        $results = [];
        foreach ($result as $object)
            $results[] = new Objects\LeaguePositionDTO($object);

        return $results;

    }

    /**
     * @param
     * @return Objects/LeagueListDTO
     * /lol/league/v4/challengerleagues/by-queue/RANKED_SOLO_5x5
     */

    public function challengerlist()
    {
        $access = "https://" . $this->region . ".api.riotgames.com/lol/league/v4/challengerleagues/by-queue/RANKED_SOLO_5x5";
        $result = $this->call_URL($access);

        return new Objects\LeagueListDTO($result);

    }

    /**
     * @param $id
     * @return Objects/ChampionMasteryDTO
     * /lol/champion-mastery/v4/champion-masteries/by-summoner/{id}
     */

    const ENDPOINT_MASTERIES = '.api.riotgames.com/lol/champion-mastery';
    const METHOD_MASTERIES= 'champion-masteries/by-summoner/';

    public function getSummonerMasteries(string $id) {

        $access = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_MASTERIES, self:: METHOD_MASTERIES, $id);

        $result = $this->call_URL($access);
        $results = [];
        foreach ($result as $object)
            $results[] = new Objects\ChampionMasteryDTO($object);

        return $results;

    }

    /**
     * @param $id
     * @return Objects/MatchlistDto
     * /lol/match/v4/matchlists/by-account/{id}
     */

    const ENDPOINT_MATCHES = '.api.riotgames.com/lol/match';
    const METHOD_MATCHES= 'matchlists/by-account/';

    public function getMatchList (string $id) {

        $access = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_MATCHES, self:: METHOD_MATCHES, $id);

        $result = $this->call_URL($access);
        return new Objects\MatchlistDto($result);
    }

    /**
     * @param $id
     * @return Objects/MatchDto
     * /lol/match/v4/matches/{id}
     */

    const ENDPOINT_MATCHES_DETAIL = '.api.riotgames.com/lol/match';
    const METHOD_MATCHES_DETAIL = 'matches/';

    public function getMatchDetail (string $id) {

        $access = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_MATCHES_DETAIL, self:: METHOD_MATCHES_DETAIL, $id);

        $result = $this->call_URL($access);
        return new Objects\MatchDto($result);
    }

    /**
     * @param $id
     * @return Objects/MatchTimelineDto
     * /lol/match/v4/matches/{id}
     */


    const ENDPOINT_MATCHES_TIMELINE = '.api.riotgames.com/lol/match';
    const METHOD_MATCHES_TIMELINE = 'timelines/by-match/';

    public function getMatchTimeline (string $id) {

        $access = $this->set_URL(self::VERSION_APP, self:: ENDPOINT_MATCHES_TIMELINE, self:: METHOD_MATCHES_TIMELINE, $id);

        $result = $this->call_URL($access);
        return new Objects\MatchTimelineDto($result);
    }
}


