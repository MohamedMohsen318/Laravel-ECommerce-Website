
<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CouponException extends Exception
{
    public function render(): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if (request()->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error'   => 'coupon_error',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return back()->withErrors(['coupon' => $this->getMessage()]);
    }
}
