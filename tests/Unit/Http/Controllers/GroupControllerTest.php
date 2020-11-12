<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use App\Models\Course;
use App\Http\Requests\GroupRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\Group;
use App\Models\User;
use App\Repositories\Group\GroupRepositoryInterface;
use App\Http\Controllers\GroupController;
use App\Repositories\Group\GroupRepository;
use App\Repositories\Course\CourseRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;

class GroupControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    protected $groupMock;
    protected $userMock;
    protected $courseMock;
    protected $groupController;

    protected function setUp() : void
    {
        parent::setUp();
        $this->groupMock = Mockery::mock(GroupRepositoryInterface::class)->makePartial();
        $this->userMock = Mockery::mock(UserRepositoryInterface::class)->makePartial();
        $this->courseMock = Mockery::mock(CourseRepositoryInterface::class)->makePartial();
        $this->groupController = new GroupController($this->groupMock, $this->userMock, $this->courseMock);
    }

    public function tearDown() : void
    {
        Mockery::close();
        unset($this->groupController);
        parent::tearDown();
    }

    public function test_create_group_when_is_not_admin()
    {
        $id = 1;
        $request = new GroupRequest();
        $data = [
            'name' => $request->name_group,
            'course_id' => $id,
        ];
        $this->groupMock->shouldReceive('create')->with($data);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(false);
        $response = $this->groupController->store($id, $request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('lecturers.courseDetail', $id), $response->headers->get('Location'));
        $this->assertEquals(trans('group.add_noti'), $response->getSession()->get('message'));
    }

    public function test_create_group_when_is_admin()
    {
        $id = 1;
        $request = new GroupRequest();
        $data = [
            'name' => $request->name_group,
            'course_id' => $id,
        ];
        $this->groupMock->shouldReceive('create')->with($data);
        $this->userMock->shouldReceive('hasRole')->with('admin')->once()->andReturn(true);
        $response = $this->groupController->store($id, $request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('courses.show', $id), $response->headers->get('Location'));
        $this->assertEquals(trans('group.add_noti'), $response->getSession()->get('message'));
    }

    public function test_get_users_has_no_group()
    {
        $idGroup = 2;
        $group = new Group();
        $group->id = $idGroup;
        $group->name = 'g1';
        $group->course_id = $idGroup;
        $this->groupMock->shouldReceive('find')->with($idGroup)->andReturn($group);
        $groupIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getGroupIds')->with($group)->andReturn($groupIds);
        $userIds = new Collection([
            1,
            2,
        ]);
        $this->courseMock->shouldReceive('getUserIds')->with($group)->andReturn($userIds);
        $this->userMock->shouldReceive('getUsersNoGroup')->with($userIds, $groupIds)->andReturn(new Collection);

        $result = $this->groupController->getUsersHasNoGroup($idGroup);
        $this->assertInstanceOf(Collection::class, $result);
    }

    // public function test_
}
