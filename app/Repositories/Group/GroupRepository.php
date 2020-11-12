<?php

namespace App\Repositories\Group;

use App\Repositories\Group\GroupRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Group;

class GroupRepository extends BaseRepository implements GroupRepositoryInterface
{
    public function getModel()
    {
        return Group::class;
    }
}
