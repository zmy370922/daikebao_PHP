<?php

namespace app\common\model;

class RechargeOrder extends Base
{
    //用户
    public function user()
    {
        return $this->hasOne('User', 'id', 'uid')->field('id,nickname,mobile,avatar');
    }
}
