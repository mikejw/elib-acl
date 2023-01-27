<?php

namespace Empathy\ELib;

use Empathy\MVC\Controller\CustomController;
use Empathy\MVC\Testable;

class BaseApiController extends CustomController
{
    public function __construct($boot)
    {
        parent::__construct($boot, false);
        Testable::header('Access-Control-Allow-Headers: Origin,Content-Type,X-Auth-Token,Accept,Authorization,X-Request-With');
    }
}