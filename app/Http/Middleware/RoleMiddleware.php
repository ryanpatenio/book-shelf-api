<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

      /**
     * Role mapping.
     *
     * @var array
     */
    private $roleMap = [
        'user' => 0,
        'admin' => 1,
        'super_admin' => 2,
    ];

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Convert role names to their respective numeric values
        $roleIds = array_map(function ($role) {
            return $this->roleMap[$role] ?? null;
        }, $roles);

        // Check if the user is authenticated and has the required role
        if (!$user || !in_array($user->role, $roleIds)) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
