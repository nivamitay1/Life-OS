<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMoneyGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // We check ownership in controller/action
    }

    public function rules(): array
    {
        return [
            'add_amount' => 'required|numeric|min:1'
        ];
    }
}
