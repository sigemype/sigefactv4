<?php

namespace Modules\Ecommerce\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant\User;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // se requiere consultar el primer usuario (administrador) para conocer los permisos
        $modules = User::first()->getModules();
        $access_modules = $modules->filter(function ($module, $key) {
            return $module->value === 'ecommerce';
        });
        if($access_modules->count() == 0){
            abort(404);
        };

        return $next($request);
    }
}
