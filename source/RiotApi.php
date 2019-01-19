<?php

namespace Application;

use Application\Objects;
use Application\Exceptions\GeneralException;

class RiotApi
{
    const
        API_KEY = 'RGAPI-18404b13-085c-44bb-a084-7a79c2b90ab9';

    private $data = array();
    protected $region;

    public function __construct($region)
    {
        $this->region = $region;
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

    public $endpoint;
    public function set_URL($version, $endpoint, $method, $parameter)
    {
        $this->version = $version;
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->parameter = $parameter;

        return (string) ('https://' . $this->region . $this->endpoint . $this->version . $this->method . $this->parameter);
    }

    /**
     * @param $name
     * @return Objects/SummonerDTO
     * /lol/summoner/v4/summoners/by-name/{summonerName}
     */

    const VERSION = "/v4/";
    const ENDPOINT_SUMMONER = '.api.riotgames.com/lol/summoner';
    const METHOD_SUMMONER = 'summoners/by-name/';

    public function getSummoner(string $name)
    {
        $name = str_replace(' ', '%20', $name);
        $link = $this->set_URL(self::VERSION, self:: ENDPOINT_SUMMONER, self:: METHOD_SUMMONER, $name);
        $result = $this->call_URL($link);

        return new Objects\SummonerDTO($result);
    }


    const ENDPOINT_LEAUGE = '.api.riotgames.com/lol/league';
    const METHOD_LEAUGE = 'positions/by-summoner/';

    public function getPosition(string $id)
    {
        $link = $this->set_URL(self::VERSION, self:: ENDPOINT_LEAUGE, self:: METHOD_LEAUGE, $id);

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

        $link = $this->set_URL(self::VERSION, self:: ENDPOINT_MASTERIES, self:: METHOD_MASTERIES, $id);

        $result = $this->call_URL($link);
        $results = [];
        foreach ($result as $object)
            $results[] = new Objects\ChampionMasteryDTO($object);

        return $results;

    }

    public $version;

    public function getProfileID (int $id){

        $result = $this->call_URL("https://ddragon.leagueoflegends.com/api/versions.json");
        $this->version = $result[0];

        return "<img src=\"http://ddragon.leagueoflegends.com/cdn/"
            . $this->version . "/img/profileicon/" . $id . ".png\" class=\"rounded border summoner_icon\">";

    }

    public function getMasteryChampion (string $id){

        return "<img class=\"rounded-circle masteries\" src=\"http://ddragon.leagueoflegends.com/cdn/" .
            $this->version . "/img/champion/" . $id . ".png\">";

    }

    const ENDPOINT_MATCHES = '.api.riotgames.com/lol/match';
    const METHOD_MATCHES= 'matchlists/by-account/';

    public function getMatchList (string $id) {

        $link = $this->set_URL(self::VERSION, self:: ENDPOINT_MATCHES, self:: METHOD_MATCHES, $id);

        $result = $this->call_URL($link);
        return new Objects\MatchlistDto($result);
    }



}


