<?php
/**
 * Created by PhpStorm.
 * User: byco
 * Date: 2016/11/4
 * Time: 16:48
 */
namespace App\Service;

class Task extends ServiceBase
{
    const TYPE_QINGJIA = 1;
    const TYPE_BUKA = 2;
    const TYPE_JIABAN = 3;
    public function __construct()
    {

    }

    public static function make($id = 0, $type = 0)
    {
        //
        if (! $type && $id) {
            $type = 1;//getTypeById()
        }
        //参数错误，构造失败
        if (! $type && ! $id) {
            return null;
        }

        switch ($type) {
            case self::TYPE_QINGJIA:
                return new Task_Qingjia($id);
                break;
            case self::TYPE_BUKA:
                break;
            case self::TYPE_JIABAN:
                break;
            default:
                break;
        }
        return null;
    }
}