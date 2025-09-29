<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Database\QueryException;
use BadMethodCallException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Handle database constraint violations
        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1] ?? null;

            if ($errorCode === 1451) { // Foreign key constraint
                return $this->handleConstraintViolation($request, 'Cannot delete this record because it has related data.');
            }
        }

        // Handle undefined method calls (like missing relationships)
        if ($exception instanceof BadMethodCallException) {
            $message = $exception->getMessage();

            if (strpos($message, 'payments()') !== false) {
                return $this->handleConstraintViolation($request, 'Cannot delete tenant: This tenant has payment records.');
            }

            return $this->handleConstraintViolation($request, 'Cannot complete this action due to a system error.');
        }

        // Handle specific exceptions with user-friendly messages
        if ($exception instanceof ModelNotFoundException) {
            return response()->view('errors.404', [
                'message' => 'The requested resource was not found.'
            ], 404);
        }

        if ($exception instanceof AuthorizationException || $exception instanceof AccessDeniedHttpException) {
            return response()->view('errors.403', [
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [
                'message' => 'Page not found.'
            ], 404);
        }

        // For AJAX requests, return JSON error responses
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    protected function handleConstraintViolation($request, $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => true,
                'message' => $message
            ], 400);
        }

        return redirect()->back()
            ->with('error', $message)
            ->withInput();
    }

    protected function handleApiException($request, Throwable $exception)
    {
        $status = 500;
        $message = 'An error occurred.';

        if ($exception instanceof ModelNotFoundException) {
            $status = 404;
            $message = 'Resource not found.';
        } elseif ($exception instanceof AuthorizationException) {
            $status = 403;
            $message = 'Unauthorized access.';
        } elseif ($exception instanceof ValidationException) {
            $status = 422;
            $message = 'Validation failed.';
        } elseif ($exception instanceof QueryException) {
            $status = 400;
            $message = 'Cannot complete this action due to related data.';
        } elseif ($exception instanceof BadMethodCallException) {
            $status = 400;
            $message = 'Cannot complete this action due to a system error.';
        }

        return response()->json([
            'error' => true,
            'message' => $message,
            'status' => $status
        ], $status);
    }
}