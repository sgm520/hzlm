<?php


namespace app\admin\validate;

use think\Validate;

class Article
{
    /**
     * 验证规则
     */
    protected $rule = [
        'content'  => 'require',
        'title' => 'require',
    ];
    /**
     * 提示消息
     */
    protected $message = [
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];
}