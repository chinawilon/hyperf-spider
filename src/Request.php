<?php


namespace Spider;


use Spider\Parser\ParserInterface;
use Hyperf\Utils\Arr;

class Request
{

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var array|false|int|string|null
     */
    private $urlInfo;

    /**
     * @var string
     */
    private $url;

    public function __construct(string $url, ParserInterface $parser)
    {
        $this->url = $url;
        $this->urlInfo = parse_url($url);
        $this->parser = $parser;
    }

    public function getHost()
    {
        return Arr::get($this->urlInfo, 'host');
    }

    public function getPort()
    {
        return Arr::get($this->urlInfo, 'port');
    }

    public function getPath()
    {
        return Arr::get($this->urlInfo, 'path');
    }

    public function getQuery()
    {
        return Arr::get($this->urlInfo, 'query');
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        return $this->parser;
    }

}