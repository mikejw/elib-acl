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
        $table = $this::TABLE;
        $params = [];
        $id = 0;
        $sql = "select id from $table t1"
            . ' where name = ?';
        $params[] = $name;
        $error = 'Could not get role by name.';
        $result = $this->query($sql, $error, $params)->fetch();
        if (isset($result['id']) && is_numeric($result['id'])) {
            $id = $result['id'];
        }
        return $id;
    }
}