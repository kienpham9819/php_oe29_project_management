<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\AddUserRequest;
use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use App\Models\Role;
use App\Repositories\Group\GroupRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Course\CourseRepositoryInterface;

class GroupController extends Controller
{
    protected $groupRepository;
    protected $userRepository;
    protected $courseRepository;

    public function __construct(GroupRepositoryInterface $groupRepository,
        UserRepositoryInterface $userRepository,
        CourseRepositoryInterface $courseRepository)
    {
        $this->middleware('auth');
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, GroupRequest $request)
    {
        $data = [
            'name' => $request->name_group,
            'course_id' => $id,
        ];
        $this->groupRepository->create($data);
        if ($this->userRepository->hasRole('admin')) {
            return redirect()->route('courses.show', $id)->with('message', trans('group.add_noti'));
        }

        return redirect()->route('lecturers.courseDetail', $id)->with('message', trans('group.add_noti'));
    }

    public function getUsersHasNoGroup($id)
    {
        $group = $this->groupRepository->find($id);
        $groupIds = $this->courseRepository->getGroupIds($group);
        $userIds = $this->courseRepository->getUserIds($group);
        // danh sách các user ko thuộc 1 group nào trong 1 class cụ thể
        $users = $this->userRepository->getUsersNoGroup($userIds, $groupIds);

        return $users;
    }

    public function addUserToGroup(AddUserRequest $request, $id)
    {
        $userIds = $request->user_id;

        $users = $this->userRepository->getUsersToAddGroup($userIds);
        dd($users);
        $group = $this->groupRepository->find($id);
        foreach ($users as $user) {
            $nonGroupedUsers = $this->getUsersHasNoGroup($group);
            if ($this->userRepository->hasRole('lecturer') || $this->userRepository->hasRole('admin')) {
                return redirect()->back()->withErrors(['user_id' => trans('course.permission_student')]);
            } elseif (!$nonGroupedUsers->contains($user)) {
                return redirect()->back()->withErrors(['user_id' => trans('course.invalid')]);
            }
        }

        $group->users()->attach($request->user_id);

        return redirect()->back()->with('message', trans('group.noti_addUser'));
    }

    public function addLeaderToGroup(Request $request, Group $group)
    {
        foreach ($group->users as $user) {
            $group->users()->updateExistingPivot($user->id, ['is_leader' => config('admin.isNotLeader')]);
        }
        $group->users()->updateExistingPivot($request->leader, ['is_leader' => config('admin.isLeader')]);
        Role::findOrFail(config('admin.leader'))->users()->syncWithoutDetaching($request->leader);

        return redirect()->back()->with('message', trans('group.noti_addLeader'));
    }

    public function deleteUserFromGroup(Group $group, User $user)
    {
        $group->users()->detach($user);

        return redirect()->back()->with('message', trans('group.noti_deleteUser'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = $this->groupRepository->find($id);
        $newCourses = getLatestCourses();
        $leader = User::whereIn('id', function ($query) use ($id) {
            $query->select('user_id')->from('group_user')->where('is_leader', config('admin.isLeader'))
                ->where('group_id', $id);
        })->first();
        $users = $this->getUsersHasNoGroup($id);

        return view('users.admin.group_detail', compact(['group', 'newCourses', 'users', 'leader']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $newCourses = getLatestCourses();

        return view('users.admin.group_edit', compact(['group', 'newCourses']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GroupRequest $request, Group $group)
    {
        $course = Course::findOrFail($group->course_id);
        $groups = $course->groups()->where('name', '!=', $group->name)->get();
        if ($groups->contains('name', $request->name_group)) {
            return redirect()->back()
                ->withErrors(['name_group' => trans('group.unique')])
                ->withInput($request->all());
        } else {
            $group->update([
                'name' => $request->name_group,
            ]);

            return redirect()->route('courses.show', $group->course_id)->with('message', trans('group.edit_noti'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $users = $group->users;
        $project = $group->project;
        if ($project) {
            $project->delete();
        }
        if ($users) {
            $group->users()->detach($users);
        }
        $group->delete();

        return redirect()->back()->with('message', trans('group.delete_noti'));
    }
}
