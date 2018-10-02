<?php
namespace core;

class CliHelp
{
    public function newCtrl($module,$file)
    {
        return '<?php
namespace app\\'.$module.'\\ctrl;

class '.$file.'Ctrl 
{
    public function index()
    {
        //put some
    }
}
';
    }
}