<?php


namespace Spider\Scheduler;


use Spider\Request;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;


class QueueScheduler implements SchedulerInterface
{

    /**
     * @var Channel
     */
    private $requestChan;

    /**
     * @var Channel
     */
    private $workerChan;

    public function __construct()
    {
        $this->requestChan = new Channel();
        $this->workerChan = new Channel();
    }

    public function run(): void
    {
        $workerQ  = [];
        $requestQ = [];

        $cid = go(function () use(&$workerQ, &$requestQ) {
            for (;;) {
                if (count($workerQ) > 0 && count($requestQ) > 0 ) {
                    $worker = array_shift($workerQ); // worker
                    $request = array_shift($requestQ); // request
                    $worker->push($request);
                }
                Coroutine::yield();
            }
        });

        go(function () use(&$workerQ, $cid) {
            for (;;) {
                $workerQ[] = $this->workerChan->pop();
                Coroutine::resume($cid);
            }
        });

        go(function () use(&$requestQ, $cid){
            for (;;) {
                $requestQ[] = $this->requestChan->pop();
                Coroutine::resume($cid);
            }
        });
    }

    public function workerReady(Channel $worker): void
    {
        $this->workerChan->push($worker);
    }

    public function submit(Request $request): void
    {
        $this->requestChan->push($request);
    }

    public function workerChan(): Channel
    {
        return new Channel();
    }
}