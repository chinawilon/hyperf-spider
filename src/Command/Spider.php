<?php


namespace Spider\Command;

use Hyperf\Command\Annotation\Command;
use Spider\Engine\ConcurrentEngine;
use Spider\Parser\ZhenAi\CityList;
use Spider\Persistent\ItemSaver;
use Spider\Processor\Processor;
use Spider\Request;
use Spider\Scheduler\QueueScheduler;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

/**
 * Class Spider
 * @Command()
 * @package Spider\Command
 */
class Spider extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('spider:run');
    }

    public function configure() : void
    {
        parent::configure();
        $this->setDescription('Spider!!');
    }

    public function handle() : void
    {
        $workerCount = 1;
        $engine = new ConcurrentEngine(
            new QueueScheduler(),
            new Processor(),
            //ItemSaver::getItemChan(),
            null,
            $workerCount,
        );
        $engine->run(new Request("https://www.zhenai.com/zhenghun", new CityList()));
    }
}