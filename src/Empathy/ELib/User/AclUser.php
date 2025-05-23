<?php

namespace Empathy\ELib\User;
use Empathy\ELib\User\CurrentUser as ECurrentUser;
use Empathy\MVC\Model;
use Empathy\ELib\Storage\MobileUserItem;
use Empathy\ELib\Storage\Role;
use Empathy\ELib\Storage\UserRole;
use Empathy\MVC\DI;
use Empathy\MVC\Entity;
use Empathy\MVC\Config;
use Empathy\ELib\Config as ELibConfig;
use Empathy\MVC\Plugin\JSONView\EROb;
use Empathy\MVC\Plugin\JSONView\ReturnCodes;
use Empathy\MVC\Plugin\JSONView\ROb;
use Empathy\MVC\RequestException;
use Empathy\MVC\Session;


class AclUser extends ECurrentUser {

    public function apiConfirmSignup() 
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['email']) && isset($data['reg'])) {
            list($errors, $token) = $this->doConfirmMobileSignup($data['email'], $data['reg']);

            if (sizeof($errors)) {
                $rob = new EROb(ReturnCodes::Bad_Request, $errors);
            } else {
                $rob = new ROb();
                $data = new \stdClass();
                $data->token = $token;
                $rob->setData($data);
            }
            DI::getContainer()->get('Controller')->assign('default', $rob, true);
        } else {
            throw new RequestException('Cannot confirm signup. Missing info?', RequestException::NOT_AUTHENTICATED);
        }
    }

    public function apiSignup()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['email']) && isset($data['password'])) {
            list($errors, $user) = $this->doMobileSignup($data['email'], $data['password']);

            if (sizeof($errors)) {
                $rob = new EROb(ReturnCodes::Bad_Request, $errors);
            } else {
                $rob = new ROb();
                $rob->setData("Ok");
            }
            DI::getContainer()->get('Controller')->assign('default', $rob, true);
        } else {
            throw new RequestException('Cannot signup. Missing creds?', RequestException::NOT_AUTHENTICATED);
        }
    }

    public function apiLogin()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['password']) && isset($data['username'])) {

            list($errors, $user) = DI::getContainer()
                ->get('CurrentUser')
                ->doLogin($data['username'], $data['password'], false);

            if (
                isset($errors['email']) &&
                $errors['email'] == 'Invalid email' &&
                preg_match('/^\w{1,15}$/', $data['username'])
            ) {
                $legacyUser = Model::load(MobileUserItem::class);
                $legacyUser->id = $legacyUser->getUserByUsername($data['username']);

                if ($legacyUser->id > 0) {
                    $legacyUser->load($legacyUser->id);
                    list($errors, $user) = DI::getContainer()
                        ->get('CurrentUser')
                        ->doLogin($legacyUser->email, $data['password'], false);
                } else {
                    $errors = array('general' => 'Wrong username/password combination');
                }
            }

            if (sizeof($errors)) {
                $rob = new EROb(ReturnCodes::Unauthorized, $errors);
            } else {
                $rob = new ROb();
                $data = new \stdClass();
                $data->token = DI::getContainer()->get('JWT')->generate();
                $rob->setData($data);
            }
            DI::getContainer()->get('Controller')->assign('default', $rob, true);
        } else {
            throw new RequestException('Cannot authenticate. Missing creds?', RequestException::NOT_AUTHENTICATED);
        }
    }

    protected function postRegister($u)
    {
        try {
            $r = Model::load(Role::class);
            $ur = Model::load(UserRole::class);
            $ur->role_id = $r->getIdByName('free');
            $ur->user_id = $u->id;
            $ur->insert();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return true;
    }

    public function isAdmin($user)
    {
        $r = Model::load(UserRole::class);
        $roles = $r->getRoles($user->id);
        if (in_array('admin', $roles)) {
            return true;
        } else {
            return false;
        }
    }

    public function detectUser($c = NULL, $store_active = false, $user_id = null)
    {
        parent::detectUser($c, $store_active, $user_id);
        if (!$this->loggedIn()) {
            $token = DI::getContainer()->get('JWT')->tryAuthenticate();
            if ($token !== null && is_object($token) && isset($token->user_id)) {
                Session::set('user_id', $token->user_id);
                parent::detectUser($c);
            }
        }
    }

    public function doConfirmMobileSignup($email, $reg)
    {
        $errors = array();
        $token = '';
        $user = Model::load(MobileUserItem::class);

        $user->email = $email;
        $user->reg_code = $reg;
        $user->validateConfirmReg();

        if ($user->getValErrors()) {
            $errors = $user->getValErrors();
            $user->id = 0;
        } else {
            $user->id = $user->getInactiveByEmail($user->email, $user->reg_code);
        }

        if (count($errors) === 0 && $user->id === 0) {
            $errors['reg'] = 'Could not find user';
        } elseif (count($errors) < 1) {
            $user->load($user->id);
            $user->active = 1;
            $user->activated = 'MYSQLTIME';
            $user->reg_code = null;
            $user->save();
            if ($this->postRegister($user)) {
                $this->setUser($user);
                $token = DI::getContainer()->get('JWT')->generate();
            }
        }
        return [
            $errors,
            $token
        ];
    }


    public function doMobileSignup($email, $password)
    {
        $user = Model::load(MobileUserItem::class);
        $user->email = $email;
        $user->username = $email;
        $user->password = $password;
        $user->fullname = 'Not provided Not provided';
        $user->username = $email;
        $user->validates();

        if (!sizeof($user->getValErrors())) {
            $user->reg_code = exec(MAKEPASSWD . ' --chars=16');
            $user->password = password_hash($user->password, PASSWORD_DEFAULT);
            $user->registered = 'MYSQLTIME';
            $user->auth = 0;
            $user->active = 0;
            $user->id = $user->insert();
            if (
                ELibConfig::get('EMAIL_ORGANISATION') &&
                ELibConfig::get('EMAIL_FROM')
            ) {
                $_POST['body'] = "\nHi ___,\n\n"
                    . "Thanks for registering with " . ELibConfig::get('EMAIL_ORGANISATION') . "\n\nBefore you"
                    . " can log in, please confirm your email address by entering the following code into the app."
                    . "\n\nCheers\n\n";
                if ($user->fullname === 'Not provided Not provided') {
                    $_POST['body'] = str_replace('Hi ___,', 'Hi,', $_POST['body']);
                    $user->fullname = $user->email;
                }

                $_POST['subject'] = "Registration with ".ELibConfig::get('EMAIL_ORGANISATION');

                $smartyPlugin = DI::getContainer()->get('PluginManager')->find(['SmartySSL', 'Smarty']);
                $smarty = $smartyPlugin->getSmarty();

                $smarty->assign('WEB_ROOT', Config::get('WEB_ROOT'));
                $smarty->assign('PUBLIC_DIR', Config::get('PUBLIC_DIR'));
                $smarty->assign('body', $_POST['body']);
                $smarty->assign('reg', substr($user->reg_code, 0, 4));
                $_POST['body'] = $smarty->fetch('elib://email/confirm.tpl');

                $_POST['first_name'] = 'Not provided';
                $_POST['last_name'] = 'Not provided';
                $_POST['email'] = $user->email;
                $service =  DI::getContainer()->get('Contact');
                $service->prepareDispatch($user->id, true);
                $service->dispatchEmail($user->fullname, true);
                $service->persist();
            }
        }

        return [
            $user->getValErrors(),
            $user
        ];
    }

}
