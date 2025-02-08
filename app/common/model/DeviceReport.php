<?php

namespace app\common\model;

class DeviceReport extends Base
{
    public function user(){
        return $this->hasOne('user','id','uid');

    }
    //设备信息
    public function device()
    {
        return $this->hasOne('device', 'id', 'device_id');
    }
}
