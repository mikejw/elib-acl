<?php

namespace Empathy\ELib\Storage;
use Empathy\MVC\Entity as Entity;
use Empathy\ELib\Model;

class UserRole extends Entity
{
    const TABLE = 'role_user';

    public $id;
    public $role_id;
    public $user_id;


    public function getRoles($id)
    {
        $sql = 'select t1.name from ' . Model::getTable('Role') . ' t1,'
            . ' ' . Model::getTable('UserRole') . ' t2'
            . " where t2.user_id = '$id'"
            . ' and t1.id = t2.role_id';

        $error = "Could not get user roles.";
        $result = $this->query($sql, $error);
        $roles = array();
        foreach ($result as $index => $row) {
            $roles[$index] = $row['name'];
        }
        return $roles;
    }
}