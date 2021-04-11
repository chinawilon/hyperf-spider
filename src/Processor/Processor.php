<?php


namespace Spider\Processor;


use Spider\Request;
use Swoole\Coroutine\Http\Client;


class Processor implements ProcessorInterface
{
    public function process(Request $request)
    {
        $http = new Client($request->getHost(), $request->getPort(), true);

        echo 'fetching url: ' . $request->getUrl() . PHP_EOL;

        defer(function ()use($http) {
            $http->close();
        });

        $parser = $request->getParser();
        $http->get($request->getPath() . '?' . $request->getQuery());
        return $parser->parse($http->body);
    }
}