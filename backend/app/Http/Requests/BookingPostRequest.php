<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\API\ParameterNotFoundException;
use App\Http\Responses\JsonApiResponse;

class BookingPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'room_id' => 'required|integer',
            'user_name' => 'required|string',
			'date' => 'required|date|date_format:Y-m-d|after:yesterday',
			'start_time' => 'required|string|date_format:H:i',
			'end_time' => 'required|string|date_format:H:i'
        ];
    }

    public function messages()
    {
        return [
            'date.date_format' => 'Use a valid date format YYYY-MM-DD',
			'date.after' => 'Only dates in the future',
            'start_time.date_format' => 'Use a valid time format hh:mm',
			'end_time.date_format' => 'Use a valid time format hh:mm'
        ];
    }	

	public function failedValidation(Validator $validator)
	{
		throw new HttpResponseException(
			JsonApiResponse::unprocessable('Validation errors', $validator->errors()->toArray())->toResponse(null));
	}	
}
