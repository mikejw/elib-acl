<?php

namespace Empathy\ELib\Model;
use Empathy\MVC\Entity;
use Empathy\ELib\Model;
use Empathy\MVC\Validate;
use Empathy\ELib\Storage\UserItem;



class MobileUserItem extends UserItem
{
    const REG_LENGTH = 4;

    public function getInactiveByEmail($email, $reg)
    {
        if (strlen($reg) !== self::REG_LENGTH) {
            return 0;
        }
        $user = \R::findOne(
            'user',
            ' email = ? and active = ? and reg_code like ? ',
            array($email, 0, "$reg%")
        );
        return $user->id ?? 0;
    }

    public function validates($email_check=true)
    {
        if ($this->doValType(Validate::EMAIL, 'email', $this->email, false)) {
            if ($email_check) {
                if ($this->activeUser()) {
                    $this->addValError('That email address can\'t be used', 'email');
                }
            }
        }
        $this->validatePassword();
    }

    public function validateLogin()
    {
        $this->doValType(Validate::EMAIL, 'email', $this->username, false);
        $this->doValType(Validate::PASSWORD, 'password', $this->password, false);
    }


    public function validatePassword()
    {
        $this->doValType(
            Validate::PASSWORD, 'password', $this->password, false,
            'Password not strong. Include uppercase and lowercase letters,'
                .' numbers and special characters'
        );
    }
}
