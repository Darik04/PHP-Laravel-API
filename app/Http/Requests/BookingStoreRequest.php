<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
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
            "reservation_token" => "required|max:255",
            "name" => "required|string|max:255",
            "address" => "required|string|max:255",
            "city" => "required|string|max:255",
            "zip" => "required|string|max:255",
            "country" => "required|string|max:255"
        ];
    }
}
