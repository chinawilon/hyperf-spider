<?php


namespace Spider\Scheduler;


use Spider\Request;
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

        go(function () use(&$workerQ, &$requestQ) {
            for (;;) {
                if (count($workerQ) > 0 && count($requestQ) > 0 ) {
                    $worker = array_shift($workerQ); // worker
                    $request = array_shift($requestQ); // request
                    $worker->push($request);
                }
                usleep(1); // yield
            }
        });

        go(function () use(&$workerQ) {
            for (;;) {
                $workerQ[] = $this->workerChan->pop();
            }
        });

        go(function () use(&$requestQ){
            for (;;) {
                $requestQ[] = $this->requestChan->pop();
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