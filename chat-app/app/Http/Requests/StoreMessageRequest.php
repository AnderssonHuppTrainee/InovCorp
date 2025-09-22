<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => [
                'required',
                'string',
                'min:1',
                'max:1000',
                'regex:/^[^<>]*$/', // previnir HTML/script injection
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.required' => 'Message body is required.',
            'body.min' => 'Message cannot be empty.',
            'body.max' => 'Message cannot exceed 1000 characters.',
            'body.regex' => 'Message contains invalid characters.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Rate limiting check
            $key = 'messages:' . auth()->id() . ':' . $this->route('room')->id;
            $maxAttempts = 10;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);
                $validator->errors()->add('rate_limit', "Too many messages sent. Try again in {$seconds} seconds.");
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        // If rate limit error, return 429 status
        if ($validator->errors()->has('rate_limit')) {
            $seconds = RateLimiter::availableIn('messages:' . auth()->id() . ':' . $this->route('room')->id);
            abort(429, "Too many messages sent. Try again in {$seconds} seconds.");
        }

        parent::failedValidation($validator);
    }
}

