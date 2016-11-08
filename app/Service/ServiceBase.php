<?php
/**
 * Created by PhpStorm.
 * User: byco
 * Date: 2016/11/4
 * Time: 16:03
 */
namespace App\Service;
use DB;

class ServiceBase
{
    protected $_msg = '';
    protected $_code = 0;

    protected $_time = 0;
    protected $_date = '';
    protected $_dateTime = '';

    public function __construct()
    {
        $this->_time        = time();
        $this->_date        = date('Y-m-d', $this->_time);
        $this->_dateTime   = date('Y-m-d H:i:s', $this->_time);
    }
    
    protected function beginTran() {
        $backtrace = debug_backtrace();
        return $this->_beginTran(array(
            $this,
            '_' . $backtrace[1]['function']
        ), $backtrace[1]['args']);
    }

    /**
     * 发起闭包事务调用
     *
     * @param callable $invoke
     * @param array $params
     * @return boolean|mixed
     */
    protected function _beginTran($invoke, array $params) {
        if (is_callable($invoke) == false) {
            $this->setError(-1, 'transaction unable to start, invoke not callable');
            return false;
        }

        try {
            DB::beginTransaction();

            $return = call_user_func_array($invoke, $params);

            DB::commit();
            return $return;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->setError(-1, $e->getMessage());
            return false;
        }
    }

    public function setError($code = 0, $msg = '')
    {
        $this->_code = $code;
        $this->_msg = $msg;
        return true;
    }

    public function getMsg()
    {
        return $this->_msg;
    }
}