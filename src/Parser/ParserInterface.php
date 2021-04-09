<?php

namespace Spider\Parser;

interface ParserInterface
{
    public function parse(string $body): ParseResult;
}