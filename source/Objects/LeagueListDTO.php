<?php


namespace Application\Objects;


class LeagueListDTO extends ObjectApi
{

    /** @var string $leagueId */
	public $leagueId;
	/** @var string $tier */
	public $tier;
    /** @var LeagueItemDTO $LeagueItemDto*/
	public $entries;
    /** @var string $queue */
	public $queue;
    /** @var string $name */
	public $name;

}