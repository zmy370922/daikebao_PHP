<?php

namespace app\common\model;

class Tearcher extends Base
{
    public function children(){
        return $this->hasMany("Tearcher",'pid',"id");
    }
    
}
