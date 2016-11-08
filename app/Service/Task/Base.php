<?php
/**
 * Created by PhpStorm.
 * User: byco
 * Date: 2016/11/7
 * Time: 9:19
 */
namespace App\Service;

abstract class Task_base extends ServiceBase
{
    protected $_taskId = 0;
    protected $_data = [];
    public function __construct($id = 0)
    {
        if ((int)$id) {
            $this->_taskId = (int)$id;
            $this->_data = DB::table('oa_task')->where('task_id' , '=', $this->_taskId)->get();
        }
        parent::__construct();
    }

    abstract function getType();

    abstract function getStep();

    protected function goToNext()
    {
        return true;
    }

    public function create($data)
    {
        return $this->beginTran();
    }
    protected function _create($data)
    {
        if (! $data['user_id'] || ! $data['user_name']) {
            throw new \Exception('用户异常');
        }
        $in_data = [
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'],
            'task_type' => $this->getType(),
            'create_time' => $this->_dateTime,
            'create_date' => $this->_date,
            'status' => 100,
            'data' => json_encode($data['data']),
        ];

        $task_id = DB::table('oa_task')->insertGetId($in_data);

        if (! $task_id) {
            throw new \Exception('数据库操作错误');
        }
        return $task_id;
    }

    /**
     * 删除
     * @return bool|mixed
     */
    public function delete()
    {
        return $this->beginTran();
    }
    public function _delete()
    {
        if (! $this->_taskId) {
            throw new \Exception('无效ID');
        }
        $succ = DB::table('oa_task')->where(['task_id' => $this->_taskId])->update(['is_delete' => 1]);
        if ($succ === false) {
            throw new \Exception('数据库操作错误');
        }
        return true;
    }

    public function success()
    {
        return $this->beginTran();
    }
    public function _success()
    {
        $succ = $this->_changeStatus(200);
        if ($succ) {
            //todo success
        } else {
            return false;
        }
        return true;
    }

    public function close()
    {
        return $this->beginTran();
    }
    public function _close()
    {
        $succ = $this->_changeStatus(300);
        if ($succ) {
            //todo close
        } else {
            return false;
        }
        return true;
    }

    public function changeStatus($state)
    {
        return $this->beginTran();
    }
    public function _changeStatus($state)
    {
        if (! $this->_taskId) {
            throw new \Exception('无效ID');
        }
        if (! in_array($state, array(100,200,300))) {
            throw new \Exception('状态错误');
        }
        if ($this->_date['status'] == 200) {
            throw new \Exception('流程已完成，无法修改状态');
        }
        if ($this->_date['status'] == 300) {
            throw new \Exception('流程已关闭，无法修改状态');
        }

        $succ = DB::table('oa_task')->update(['status' => $state])->where('task_id', '=', $this->_taskId);
        if ($succ === false) {
            throw new \Exception('数据操作错误');
        }
        return true;
    }
}