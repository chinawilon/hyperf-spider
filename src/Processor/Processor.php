<?php


namespace Spider\Processor;


use Spider\Request;
use Swoole\Coroutine\Http\Client;


class Processor implements ProcessorInterface
{
    public function process(Request $request)
    {
        $http = new Client($request->getHost(), $request->getPort());

        echo 'fetching url: ' . $request->getUrl() . PHP_EOL;

        defer(function ()use($http) {
            $http->close();
        });

        $parser = $request->getParser();
        return $parser->parse($http->get($request->getPath() . '?' . $request->getQuery()));
    }
}