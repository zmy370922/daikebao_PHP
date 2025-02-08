<?php

namespace app\common\model;

class Device extends Base
{
    public function tearcher(){
        return $this->hasOne("TearcherBind",'did','id');

    }

}
