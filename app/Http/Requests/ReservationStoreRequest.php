<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationStoreRequest extends FormRequest
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
            'reservation_token' => 'required|max:255',
            'duration' => 'required|numeric|min:1|max:300',
            'reservations' => 'required|array|min:1',
            'reservations.*.row' => 'required|numeric|min:1|max:8',
            'reservations.*.seat' => 'required|numeric|min:1|max:50',
        ];
    }
}
