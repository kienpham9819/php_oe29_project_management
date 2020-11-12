<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Group;
use App\Models\Course;
use DB;

class GroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $q = (int)explode("/", $this::url())[4];
        $groupNames = DB::table('groups')->select('name')->where('course_id', $q)->get()->toArray();
        $data = json_decode(json_encode($groupNames), true);
        foreach ($data as $key => $value) {
            $data[$key] = $value['name'];
        }

        return [
            'name_group' => [
                'required',
                'string',
                'min:1',
                'max:50',
                Rule::notIn($data),
            ],
        ];
    }
}
