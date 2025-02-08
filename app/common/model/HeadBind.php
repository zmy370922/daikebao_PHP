<?php

namespace app\common\model;

class HeadBind extends Base
{
    public function device(){
        return $this->hasOne("Device",'id','did');
    }
    
}
