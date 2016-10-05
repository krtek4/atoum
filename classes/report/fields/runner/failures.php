<?php

namespace mageekguy\atoum\report\fields\runner;

use mageekguy\atoum\locale;
use mageekguy\atoum\runner;
use mageekguy\atoum\report;
use mageekguy\atoum\observable;

abstract class failures extends report\field
{
    protected $runner = null;

    public function __construct()
    {
        parent::__construct(array(runner::runStop));
    }

    public function getRunner()
    {
        return $this->runner;
    }

    public function handleEvent($event, observable $observable)
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $this->runner = $observable;

            return true;
        }
    }
}
