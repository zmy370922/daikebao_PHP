<?php

namespace app\common\model;

class ActionLog extends Base
{
    //管理员
    public function manage()
    {
        return $this->hasOne('Manage', 'id', 'mid')->field('id,account,username');
    }
}
