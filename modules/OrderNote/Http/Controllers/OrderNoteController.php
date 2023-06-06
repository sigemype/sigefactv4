<?php

namespace Modules\OrderNote\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\OrderNote;

class OrderNoteController extends Controller
{
    public function updateState(Request $request)
    {
        DB::connection('tenant')->transaction(function () use ($request) {
            $order_note = OrderNote::find($request->id);
            $order_note->state_type_id = $request->state_type_id;
            $order_note->save();
        });

        return [
            'success' => true,
            'message' => 'Pedido actualizado con éxito'
        ];
    }
}
