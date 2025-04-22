<?php

namespace App\Exceptions;


use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Resources\BaseResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
class Handler extends ExceptionHandler
{
    protected $dontReport = [];



public function render($request, Throwable $exception)
{
    // Handle Validation Exception
    if ($exception instanceof ValidationException) {
        return BaseResource::respond(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Validation Error',
            $exception->errors()
        );
    }

    // Handle Authentication Exception
    if ($exception instanceof AuthenticationException) {
        return BaseResource::respond(
            Response::HTTP_UNAUTHORIZED,
            'Unauthenticated'
        );
    }

    // Handle Not Found (Model)
    if ($exception instanceof ModelNotFoundException) {
        return BaseResource::respond(
            Response::HTTP_NOT_FOUND,
            'Data not found'
        );
    }

    // Handle Http Exception
    if ($exception instanceof HttpException) {
        return BaseResource::respond(
            $exception->getStatusCode(),
            $exception->getMessage() ?: Response::$statusTexts[$exception->getStatusCode()]
        );
    }
    if ($exception instanceof JWTException){
        return BaseResource::respond(
            Response::HTTP_UNAUTHORIZED,
            'Unauthenticated'
        );
    }
    if ($exception instanceof TokenExpiredException) {
        return BaseResource::respond(Response::HTTP_UNAUTHORIZED, 'Token expired');
    }

    if ($exception instanceof TokenInvalidException) {
        return BaseResource::respond(Response::HTTP_UNAUTHORIZED, 'Token invalid');
    }


    // Default exception handler (fallback)
    return BaseResource::respond(
        Response::HTTP_INTERNAL_SERVER_ERROR,
        $exception->getMessage() ?: 'Internal server error'
    );
}

}
