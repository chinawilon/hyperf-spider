<?php

namespace Spider\Processor;

use Spider\Request;

interface ProcessorInterface
{
    public function process(Request $request);
}