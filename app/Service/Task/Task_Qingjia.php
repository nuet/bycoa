<?php
/**
 * Created by PhpStorm.
 * User: byco
 * Date: 2016/11/7
 * Time: 10:03
 */
namespace App\Service;

class Task_Qingjia extends Task_base 
{
    public function getType()
    {
        return Task::TYPE_QINGJIA;
    }
    
    public function getStep($step = 0)
    {
        $steps = array(
            1 => '创建',
            2 => '团队负责人审批',
            3 => '管理员审批',
            4 => '本人确认',
            5 => '完成',
        );
        if ($step && $steps[$step]) {
            return $steps[$step];
        }
        return $steps;
    }

    public function create($data)
    {
        return $this->beginTran();
    }
    public function _create($data)
    {
        if (! $data['start_time']) {
            throw new \Exception('请选择开始时间');
        }
        if (! $data['end_time']) {
            throw new \Exception('请选择结束时间');
        }
        $base_data = [
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'],
            'data' => [
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'reason' => $data['reason'],
                'step' => 1,
            ],
        ];
        $task_id = parent::_create($base_data);
        if (! $task_id) {
            throw new \Exception('数据库操作错误');
        }
        return true;
    }
    
    public function goToNext()
    {
        
    }
}