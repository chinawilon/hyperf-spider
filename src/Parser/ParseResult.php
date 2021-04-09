<?php


namespace Spider\Parser;


use Spider\Request;

class ParseResult
{
    /**
     * @var array
     */
    private $requests;

    /**
     * @var array
     */
    private $items;

    public function __construct(array $items = [], array $requests = [])
    {
        $this->items = $items;
        $this->requests = $requests;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getRequests(): array
    {
        return $this->requests;
    }

}