<?php

namespace Modules\LevelAccess\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Modules\LevelAccess\Models\AuthorizedDiscountUser;


class AuthorizedDiscountUserController extends Controller
{
    
    /**
     * 
     * Validar token para autorizar descuento
     *
     * @param  Request $request
     * @return array
     */
    public function validateToken(Request $request)
    {
        $authorized_discount_user = AuthorizedDiscountUser::findTokenAvailable($request->token)->first();

        if($authorized_discount_user)
        {
            $authorized_discount_user->update([
                'seller_id' => auth()->id(),
                'active' => false,
            ]);

            return $this->generalResponse(true, 'Token validado correctamente.');
        }

        return $this->generalResponse(false, 'El token no existe o no se encuentra disponible.');
    }


    /**
     * 
     * Generar token para autorizar descuento a vendedor
     *
     * @param  Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if($user->type === 'admin')
        {
            $authorized_discount_user = $user->authorized_discount_users()->create([
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'token' => Str::random(60),
            ]);

            return [
                'success' => true,
                'message' => 'Token generado correctamente',
                'data' => [
                    'token' => $authorized_discount_user->token
                ]
            ];
        }

        return $this->generalResponse(false, 'No tiene permisos para realizar esta acción');
    }

}
