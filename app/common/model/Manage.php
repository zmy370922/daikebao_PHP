<?php

namespace app\common\model;

class Manage extends Base
{
    //角色
    public function role()
    {
        return $this->hasOne('SysRole', 'role_id', 'role_id')->field('role_id,role_name');
    }
}
