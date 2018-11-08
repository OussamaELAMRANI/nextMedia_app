<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        $rules= [
            "date" => "required|date",
        ];

		foreach($this->request->get('items') as $key => $val)
		{
			$rules['items.'.$key] = 'required|integer|min:1';
		}
    }
}
