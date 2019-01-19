<?php


namespace Application\Objects;


class LeaguePositionDTO extends ObjectApi
{

    /** @var string $queueType */
	public $queueType;
	/** @var string $summonerName */
	public $summonerName;
	/** @var bool $hotStreak */
	public $hotStreak;
	/** @var MiniSeriesDto $miniSeries */
	public $miniSeries;
	/** @var int $wins */
	public $wins;
	/** @var bool $veteran */
	public $veteran;
	/** @var int $losses */
	public $losses;
	/** @var bool $freshBlood */
	public $freshBlood;
	/** @var string $leagueId */
	public $leagueId;
	/** @var bool $inactive */
	public $inactive;
	/** @var string $rank */
	public $rank;
	/** @var string $leagueName */
	public $leagueName;
	/** @var string $tier */
	public $tier;
	/** @var string $summonerId */
	public $summonerId;
	/** @var int $leaguePoints */
	public $leaguePoints;
}