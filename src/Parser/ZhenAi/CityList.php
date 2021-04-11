<?php


namespace Spider\Parser\ZhenAi;


use Spider\Parser\ParseResult;
use Spider\Parser\ParserInterface;

class CityList implements ParserInterface
{
    public function parse(string $body): ParseResult
    {

        return new ParseResult();
    }
}