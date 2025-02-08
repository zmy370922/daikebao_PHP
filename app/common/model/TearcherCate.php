<?php

namespace app\common\model;

class TearcherCate extends Base
{

    public function tearcher(){
        return $this->hasOne("Tearcher",'id','tid');
    }
}
