<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Models\Role;
use App\Repositories\BaseRepository;
use App\Repositories\User\UserRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return User::class;
    }

    public function getAll()
    {
        $users = User::paginate(config('paginate.record_number'));

        return $users;
    }

    public function getDeletedUser()
    {
        $users = User::onlyTrashed()->paginate(config('paginate.record_number'));

        return $users;
    }

    public function restoreUser($id)
    {
        return User::withTrashed()->where('id', $id)->restore();
    }

    public function forceDeleteUser($id)
    {
        return User::withTrashed()->where('id', $id)->forceDelete();
    }

    public function create($data = [])
    {
        $user = User::create($data);

        return $user->roles()->attach($data['roles']);
    }

    public function updateUser($user, $data, $roles)
    {
        $user->update($data);

        return $user->roles()->sync($roles);
    }

    public function import($request)
    {
        return Excel::import(new UsersImport, $request->file('file'));
    }

    public function hasRole($roleName)
    {
        $user = auth()->user();
        $role = Role::where('slug', $roleName)->first();
        if ($role) {
            return $user->roles->contains($role);
        }

        return false;
    }

    public function getUsersNoGroup($userIds, $groupIds)
    {
        $users = User::whereIn('id', $userIds)
            ->whereNotIn('id', function ($query) use ($groupIds) {
                $query->select('user_id')->from('group_user')
                    ->whereIn('group_id', $groupIds);
            })->get();

        return $users;
    }

    public function getUsersToAddGroup($userIds)
    {
        $users = User::whereIn('id', $userIds)->get();

        return $users;
    }
}
