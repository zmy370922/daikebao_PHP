<?php

namespace app\common\model;

class Fankui extends Base
{

    public function user(){
        return $this->hasOne("User",'id','uid');
    }
    public function device(){
        return $this->hasOne("Device",'id','device_id');
    }
}
