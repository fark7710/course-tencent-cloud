<?php

namespace App\Models;

class CourseTopic extends Model
{

    /**
     * 主键编号
     *
     * @var int
     */
    public $id = 0;

    /**
     * 课程编号
     *
     * @var int
     */
    public $course_id = 0;

    /**
     * 话题编号
     *
     * @var int
     */
    public $topic_id = 0;

    /**
     * 创建时间
     *
     * @var int
     */
    public $create_time = 0;

    public function getSource(): string
    {
        return 'kg_course_topic';
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

}