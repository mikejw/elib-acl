<?php

namespace Empathy\ELib\MVC\Plugin;

use Empathy\MVC\Plugin\PreEvent;
use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\DI;
use Empathy\ELib\Model;
use Empathy\MVC\Testable;
use Empathy\MVC\RequestException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;


/**
 * Empathy Acl Plugin
 * @file            Empathy/MVC/Plugin/Acl.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Acl extends Plugin implements PreEvent
{
    public function onPreEvent()
    {
        $controller = $this->bootstrap->getController();
        $class = get_class($controller);
        $allowed = false;

        // don't run when running internal action
        if ($class !== 'Empathy\MVC\Controller') {

            $r = new \ReflectionClass($class);
            if (in_array('Laminas\Permissions\Acl\Resource\ResourceInterface', $r->getInterfaceNames())) {
                $acl = DI::getContainer()->get('Acl');
                DI::getContainer()->get('CurrentUser')->detectUser();                
                if (DI::getContainer()->get('CurrentUser')->loggedIn()) {
                    $user = DI::getContainer()->get('CurrentUser')->getUser();                           
                    $r = Model::load('UserRole');
                    $roles = $r->getRoles($user->id);
                } else {
                    $roles = ['guest'];
                }
                    
                foreach ($roles as $role) {
                    $allowed = $acl->isAllowed($role, $controller->getResourceId());
                    if ($allowed) {
                        break;
                    }
                }

                AnnotationRegistry::registerLoader('class_exists');
                $reflectionClass = new \ReflectionClass($class);
                $property = $reflectionClass->getMethod($controller->getEvent());
                $reader = new AnnotationReader();
                $annotation = $reader->getMethodAnnotation(
                    $property,
                    AclAnnotation::class
                );

                if (!$allowed) {
                    // check individual permissions               
                    if ($annotation && sizeof($annotation->permissions)) {
                        foreach ($annotation->permissions as $perm) {
                            foreach ($roles as $role) {
                                if ($allowed = $acl->isAllowed($role, $controller->getResourceId(), $perm)) {
                                    $allowed = true;
                                    break;    
                                }
                            }                 
                        }    
                    }                    
                                        
                }
            }

            if ($annotation && isset($annotation->method)) {
                if ($_SERVER['REQUEST_METHOD'] !== $annotation->method) {
                    throw new RequestException('Method Not Allowed', RequestException::METHOD_NOT_ALLOWED);
                }
            }

            if (!$allowed) {
                throw new RequestException('Denied', RequestException::NOT_AUTHORIZED);
            }
        }
    }
}
