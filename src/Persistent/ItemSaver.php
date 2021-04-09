<?php


namespace Spider\Persistent;


use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class ItemSaver
{
    public static function getItemChan(): Channel
    {
        $out = new Channel();
        go(function () use($out) {
            for (;;) {
                $item = $out->pop();
                var_dump($item);
            }
        });
        return $out;
    }
}