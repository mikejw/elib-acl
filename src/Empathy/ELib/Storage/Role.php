<?php

namespace Empathy\ELib\Storage;
use Empathy\MVC\Entity as Entity;
use Empathy\ELib\Model;

class Role extends Entity
{
    const TABLE = 'role';

    public $id;
    public $name;

    public function getIdByName($name)
    {
        $id = 0;
        $sql = 'select id from ' . Model::getTable('Role') . ' t1'
            . " where name = '$name'";

        $error = "Could not get role by name.";
        $result = $this->query($sql, $error)->fetch();
        if (isset($result['id']) && is_numeric($result['id'])) {
            $id = $result['id'];
        }
        return $id;
    }
}