<?php

namespace Empathy\ELib;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Empathy\MVC\DI;


class ApiController extends BaseApiController implements ResourceInterface
{
    public function default_event()
    {
        $this->assign('default', [1, 2, 3, 4, 5], true);
    }

    public function login()
    {
        DI::getContainer()->get('CurrentUser')->apiLogin();
    }


    public function signup()
    {
       DI::getContainer()->get('CurrentUser')->apiSignup();
    }

    public function confirm_signup()
    {
        DI::getContainer()->get('CurrentUser')->apiConfirmSignup();
    }

    public function getResourceId()
    {
        return 'public-api';
    }
}
