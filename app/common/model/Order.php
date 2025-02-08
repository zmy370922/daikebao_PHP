<?php

namespace app\common\model;

class Order extends Base
{
    //用户
    public function user()
    {
        return $this->hasOne('User', 'id', 'uid')->field('id,nickname,mobile,avatar');
    }

    //商品
    public function goods()
    {
        return $this->hasOne('Goods', 'id', 'goods_id');
    }
}
