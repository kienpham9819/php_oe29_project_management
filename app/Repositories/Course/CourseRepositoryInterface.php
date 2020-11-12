<?php

namespace App\Repositories\Course;

interface CourseRepositoryInterface
{
    public function getLatestCourses();

    public function getGroupIds($group);

    public function getUserIds($group);
}
