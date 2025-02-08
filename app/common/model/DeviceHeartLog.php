<?php

namespace app\common\model;

class DeviceHeartLog extends Base
{
    //设备信息
    public function device()
    {
        return $this->hasOne('device', 'id', 'device_id');
    }
}
