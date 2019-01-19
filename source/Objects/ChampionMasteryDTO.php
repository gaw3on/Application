<?php


namespace Application\Objects;


class ChampionMasteryDTO extends ObjectApi
{
    /** @var boolean $chestGranted */
   public $chestGranted;
    /** @var int $championLevel */
   public $championLevel;
    /** @var int $championPoints */
   public $championPoints;
    /** @var string $championId */
   public $championId;
    /** @var int $championPointsUntilNextLevel */
   public $championPointsUntilNextLevel;
    /** @var string $lastPlayTime */
   public $lastPlayTime;
    /** @var int $tokensEarned */
   public $tokensEarned;
    /** @var int $championPointsSinceLastLevel */
   public $championPointsSinceLastLevel;
    /** @var string $summonerId */
   public $summonerId;

}