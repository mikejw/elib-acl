<?php

namespace Empathy\ELib;

use Empathy\ELib\EController;
use Empathy\MVC\Testable;

class BaseApiController extends EController
{
    public function __construct($boot)
    {
        parent::__construct($boot, false);
        Testable::header('Access-Control-Allow-Headers: Origin,Content-Type,X-Auth-Token,Accept,Authorization,X-Requested-With');
        // @TODO: make configurable:
        // Testable::header('Access-Control-Allow-Origin: *');
    }
}
