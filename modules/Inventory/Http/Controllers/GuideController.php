<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Helpers\GuideStore;
use Modules\Inventory\Http\Requests\GuideRequest;

class GuideController extends Controller
{
    public function store(GuideRequest $request)
    {
            return $this->storeWithData($request->all());
    }

    public function storeWithData($inputs)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            $guide_store = new GuideStore();
            $record = $guide_store->save($inputs);
            $guide_store->setData($record->id);
//            $guide_store->createPdf();
            $message = 'Guía registrada con éxito.';

            DB::connection('tenant')->commit();

            return [
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $record->id
                ]
            ];
        } catch (\Exception $e) {
            DB::connection('tenant')->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }
}
