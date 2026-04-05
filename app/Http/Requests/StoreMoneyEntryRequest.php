<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\EntryTypeEnum;

class StoreMoneyEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
            'type' => ['required', new Enum(EntryTypeEnum::class)],
            'date' => 'required|date'
        ];
    }
}
