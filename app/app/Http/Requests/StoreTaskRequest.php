<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'=>'required|max:255',
            'content'=>'required|max:255',
            'user_id'=>'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'attached_file'=>'image|max:1024',
        ];
    }
}
