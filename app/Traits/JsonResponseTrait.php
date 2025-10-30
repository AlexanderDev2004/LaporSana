<?php

namespace App\Traits;

trait JsonResponseTrait
{
    /**
     * Return a success JSON response
     *
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonSuccess(string $message, $data = null)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    /**
     * Return an error JSON response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonError(string $message, array $msgField = [])
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'msgField' => $msgField,
        ]);
    }

    /**
     * Return a validation error JSON response
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonValidationError($validator)
    {
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal',
            'msgField' => $validator->errors(),
        ]);
    }
}
