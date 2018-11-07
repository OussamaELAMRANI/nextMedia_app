<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignUpClientRequest extends FormRequest
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
			'firstname' => 'required|string',
			'lastname' => 'required|string',
			'address' => 'required|string',
			'country' => 'required|string',
			'phone' => 'required|string',
			'client_type' => [
				'required',
				Rule::in(['SELLER', 'COSTUMER']),
			],
			'email' => 'required|string|email|unique:users',
			'password' => 'required|string|confirmed',
		];
	}

	public function messages()
	{
		return array_merge(
			parent::messages(),
			[
				'client_type.in' => 'Client type must be one of those values (SELLER Or CUSTOMER)',
			]
		);
	}
}
