<?php

namespace Empathy\ELib\Storage;
use Empathy\MVC\Entity;
use Empathy\ELib\Model;
use Empathy\MVC\Validate;
use Empathy\ELib\Storage\UserItem;



class MobileUserItem extends UserItem
{
    const REG_LENGTH = 4;
    
    
    public function getInactiveByEmail($email, $reg)
    {
        $table = $this::TABLE;
        $params = [];
        $user_id = 0;
        $sql = "SELECT id FROM $table"
            .' WHERE (email = ? and reg_code like ?)'
            .' AND active = 0';
        $params[] = $email;
        $params[] = $reg . '%';
        $error = 'Could not inactive user by email.';
        $result = $this->query($sql, $error, $params);
        $rows = $result->rowCount();
        if ($rows == 1) {
            $row = $result->fetch();
            $user_id = $row['id'];
        }
        return $user_id ?? 0;
    }

    public function getUserByUsername($username)
    {
        $table = $this::TABLE;
        $params = [];
        $user_id = 0;
        $sql = "SELECT id FROM $table"
            .' WHERE (username = ? and active = 1)';
        $params[] = $username;
        $error = 'Could not get user by username.';
        $result = $this->query($sql, $error, $params);
        $rows = $result->rowCount();
        if ($rows == 1) {
            $row = $result->fetch();
            $user_id = $row['id'];
        }
        return $user_id ?? 0;
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
    
    public function validateConfirmReg()
    {
        $this->doValType(Validate::EMAIL, 'email', $this->email, false);
        if ($this->doValType(Validate::ALNUM, 'reg_code', $this->reg_code, false)) {
            if (strlen($this->reg_code) !== self::REG_LENGTH) {
                $this->addValError('Invalid code', 'reg');
            }
        }
    }
}
