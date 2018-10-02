<?php
namespace shell\helper;
use shell\BaseShell;
class Help extends BaseShell
{
    public $param;

    public function __construct($param)
    {
        $this->param = $param;
        parent::__construct();
    }

    public function start()
    {
        $this->showCommand();
        $this->goodbye();
    }
    protected function outPut($msg, $important){
        echo $msg;
    }
}