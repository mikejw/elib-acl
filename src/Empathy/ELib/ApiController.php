<?php

namespace Empathy\ELib;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Empathy\MVC\DI;
use Empathy\ELib\MVC\Plugin\AclAnnotation;



class ApiController extends BaseApiController implements ResourceInterface
{

    /**
     * @AclAnnotation(method="GET")
     *
     */
    public function default_event()
    {
        $this->assign('default', [1, 2, 3, 4, 5], true);
    }

    /**
     * @AclAnnotation(method="POST")
     *
     */
    public function login()
    {
        DI::getContainer()->get('CurrentUser')->apiLogin();
    }

    /**
     * @AclAnnotation(method="POST")
     *
     */
    public function signup()
    {
       DI::getContainer()->get('CurrentUser')->apiSignup();
    }

    /**
     * @AclAnnotation(method="POST")
     *
     */
    public function confirm_signup()
    {
        DI::getContainer()->get('CurrentUser')->apiConfirmSignup();
    }

    public function getResourceId()
    {
        return 'public-api';
    }
}
