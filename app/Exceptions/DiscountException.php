<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DiscountException extends Exception
{
    public function render(): JsonResponse|RedirectResponse
    {
        if (request()->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error'   => 'discount_error',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return back()->withErrors([
            'discount' => $this->getMessage(),
        ]);
    }
}
