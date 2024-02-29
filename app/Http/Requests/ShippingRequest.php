<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ShippingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $toValidate = $this->request->all();

        $addFialds = [
            'countryCode' => 'BG',
            'countryName' => 'BULGARIA'
        ];

        $toValidate['receiverDetails'] = array_merge($toValidate['receiverDetails'], $addFialds);
        $toValidate['shipperDetails'] = array_merge($toValidate['shipperDetails'], $addFialds);

        $this->merge($toValidate);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'plannedShippingDateAndTime' => ['required'],
            'packages' => ['filled'],

            '*.countryCode' => ['filled'],
            '*.countryName' => ['filled'],
            '*.cityName' => ['filled'],

            'shipperDetails.postalCode' => ['required'],
            'receiverDetails.postalCode' => ['required'],

            'shipperDetails.mobilePhone' => ['required', 'digits_between:10,10'],
            'receiverDetails.mobilePhone' => ['required', 'digits_between:10,10'],

            'shipperDetails.phone' => ['required', 'digits_between:7,7'],
            'receiverDetails.phone' => ['required','digits_between:7,7'],

            'receiverDetails.email' => ['required', 'email'],
            'shipperDetails.email' => ['required', 'email'],

            'shipperDetails.addressLine1' => ['required', 'string', 'max:255'],
            'receiverDetails.addressLine1' => ['required', 'string', 'max:255'],

            'shipperDetails.fullName' => ['required', 'string', 'max:100'],
            'receiverDetails.fullName' => ['required', 'string', 'max:100'],

            'shipperDetails.companyName' => ['required', 'string', 'max:100'],
            'receiverDetails.companyName' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages()
    {
        return [
            'plannedShippingDateAndTime' => 'Shipping Date is required',
            'shipperDetails.mobilePhone' => [
                'required' => 'Shipper Mobile Phone is Required.',
                'digits_between' => 'Shipper Mobile Phone can contain 10 digits only.'

            ],
            'receiverDetails.mobilePhone' => [
                'required' => 'Receiver Mobile Phone is Required',
                'digits_between' => 'Receiver Mobile Phone can contain 10 digits only.'
            ],
            'receiverDetails.phone' => [
                'required' => 'Receiver Phone is Required',
                'digits_between' => 'Receiver Phone can contain 7 digits only.'
            ],
            'shipperDetails.phone' => [
                'required' => 'Shipper Phone is Required',
                'digits_between' => 'Shipper Phone can contain 7 digits only.'
            ],
            'shipperDetails.email' => [
                'required' => 'Shipper Email is Required',
                'email' => 'Incorrect Shipper Email'
            ],
            'receiverDetails.email' => [
                'required' => 'Receiver Email is Required',
                'email' => 'Incorrect Receiver Email'
            ],

            'receiverDetails.addressLine1' => [
                'required' => 'Receiver Address is Required',
                'max' => 'Receiver Address is too long'
            ],
            'shipperDetails.addressLine1' => [
                'required' => 'Shipper Address is Required',
                'max' => 'Shipper Address is too long'
            ],

            'shipperDetails.postalCode' => [
                'required' => 'Shipper City is Required',
            ],
            'receiverDetails.postalCode' => [
                'required' => 'Receiver City is Required',
            ],

            'receiverDetails.fullName' => [
                'required' => 'Receiver Full Name is Required',
                'max' => 'Receiver Full Name is too long'
            ],
            'shipperDetails.fullName' => [
                'required' => 'Shipper Full Name is Required',
                'max' => 'Shipper Full Name is too long'
            ],

            'receiverDetails.companyName' => [
                'required' => 'Receiver Company Name is Required',
                'max' => 'Receiver Company Name is too long'
            ],
            'shipperDetails.companyName' => [
                'required' => 'Shipper Company Name is Required',
                'max' => 'Shipper Company Name is too long'
            ],
        ];
    }

    public function failedValidation(Validator $validator) {
        $messages = $validator->errors()->messages();

        $errors = array_reduce(array_keys($messages), function ($carry, $key) use ($messages) {
            Arr::set($carry, $key, $messages[$key][0]);
            return $carry;
        }, []);

        $response = response()->json(
            [
                'success' => false,
                'errors' => $errors
            ], 422);

        throw new HttpResponseException($response);
    }
}
