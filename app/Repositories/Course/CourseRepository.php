<?php

namespace App\Repositories\Course;

use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Course;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function getModel()
    {
        return Course::class;
    }

    public function getLatestCourses()
    {
        $courses = Course::orderBy('updated_at', 'desc')->get();
        $courses->splice(config('app.display_limit'));

        return $courses;
    }

    public function getGroupIds($group)
    {
        return Course::findOrFail($group->course_id)->groups()->pluck('groups.id');
    }

    public function getUserIds($group)
    {
        return Course::findOrFail($group->course_id)->users()->pluck('users.id');
    }
}
