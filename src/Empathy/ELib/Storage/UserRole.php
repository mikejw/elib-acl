<?php

namespace Empathy\ELib\Storage;
use Empathy\MVC\Entity as Entity;
use Empathy\MVC\Model;
use Empathy\ELib\Storage\Role;

class UserRole extends Entity
{
    const TABLE = 'role_user';

    public $id;
    public $role_id;
    public $user_id;

    public function getRoles($id)
    {
        $table = $this::TABLE;
        $params = [];
        $sql = 'select t1.name from ' . Model::getTable(Role::class) . ' t1,'
            . " $table t2"
            . ' where t2.user_id = ?'
            . ' and t1.id = t2.role_id';
        $params[] = $id;

        $error = 'Could not get user roles.';
        $result = $this->query($sql, $error, $params);
        $roles = array();
        foreach ($result as $index => $row) {
            $roles[$index] = $row['name'];
        }
        return $roles;
    }
}
