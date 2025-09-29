<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;

class HandleErrors
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Resource not found'
                ], 404);
            }

            return redirect()->back()->with('error', 'The requested item was not found.');
        } catch (AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            return redirect()->back()->with('error', 'You do not have permission to perform this action.');
        }
    }
}