<?php

namespace App\Http\Requests\Hr;

use Illuminate\Foundation\Http\FormRequest;

class BillAnnounceRequest extends FormRequest
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
        return [
            'unit' => 'required',
            'bill_type_id' => 'required'
        ];
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return array
     */
    public function store()
    {
        return BillSettings::create($this->validated());
    }
}
