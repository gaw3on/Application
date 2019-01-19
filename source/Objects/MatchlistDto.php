<?php


namespace Application\Objects;


class MatchlistDto extends ObjectApi
{
    /** @var MatchReferenceDto $matches */
    public $matches;
    /** @var int $totalGames */
    public $totalGames;
    /** @var int $startIndex */
    public $startIndex;
    /** @var int $endIndex */
    public $endIndex;
}