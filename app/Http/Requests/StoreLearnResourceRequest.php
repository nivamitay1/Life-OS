<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLearnResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:book,course,article,podcast,other',
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'total_units' => 'nullable|integer|min:1',
            'unit_label' => 'nullable|in:page,chapter,lesson,module,episode,article,other',
        ];
    }
}
