<?php


namespace Spider\Engine;


use Spider\Parser\ParseResult;
use Spider\Processor\ProcessorInterface;
use Spider\Request;
use Spider\Scheduler\SchedulerInterface;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;

class ConcurrentEngine
{
    /**
     * @var int
     */
    private $workerCount;

    /**
     * @var SchedulerInterface
     */
    private $scheduler;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @var array
     */
    private $visitedUrls;

    /**
     * @var Channel
     */
    private $itemChan;

    public function __construct(SchedulerInterface $scheduler, ProcessorInterface $processor, Channel $itemChan, $workerCount)
    {
        $this->scheduler = $scheduler;
        $this->workerCount = $workerCount;
        $this->processor = $processor;
        $this->itemChan = $itemChan;
    }

    /**
     * @param $seeds array
     */
    public function run(...$seeds): void
    {
        $out = new Channel();
        $this->scheduler->run();

        for ($i = 0; $i < $this->workerCount; $i++) {
            $this->createWorker($this->scheduler->workerChan(), $out);
        }

        foreach ($seeds as $seed) {
            if ( $this->isDuplicate($seed) ) {
                continue;
            }
            $this->scheduler->submit($seed);
        }

        for (;;) {
            /**@var $result ParseResult**/
            $result = $out->pop();
            foreach($result->getItems() as $item) {
                go(function () use($item) {
                    $this->itemChan->push($item);
                });
            }

            foreach($result->getRequests() as $request) {
                if ( $this->isDuplicate($request)) {
                    continue;
                }
                $this->scheduler->submit($request);
            }
        }
    }

    public function createWorker(Channel $in, Channel $out): void
    {
        go(function () use($in, $out) {
            for (;;) {
                $this->scheduler->workerReady($in);
                $request = $in->pop();
                $result = $this->processor->process($request);
                $out->push($result);
            }
        });
    }

    public function isDuplicate(Request $request): bool
    {
        if( isset($this->visitedUrls[$request->getUrl()])) {
            return true;
        }
        $this->visitedUrls[$request->getUrl()] = true;
        return false;
    }
}