<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant\Item;
use Modules\MobileApp\Http\Requests\Api\ItemRequest;
use Modules\Finance\Helpers\UploadFileHelper;
use Modules\MobileApp\Http\Resources\Api\{
    ItemCollection,
    ItemResource,
    ItemSaleCollection
};
use Modules\Item\Models\{
    Category
};
use App\Http\Controllers\Tenant\ItemController as ItemControllerWeb;
use App\Models\Tenant\Catalogs\AffectationIgvType;


class ItemController extends Controller
{
          
    /**
     * 
     * Obtener tablas relacionadas
     *
     * @return array
     */
    public function tables()
    {
        return [
            'categories' => $this->table('categories')
        ];
    } 
    

    /**
     * 
     * @return array
     */
    public function table($table)
    {
        $data = [];

        switch ($table) {
            case 'categories':
                $data = Category::filterForTables()->get()->transform(function($row){
                    return $row->getRowResourceApi();
                });
                break;
            case 'affectation_igv_types':
                $data = AffectationIgvType::whereActive()->get();
                break;
        }

        return $data;
    } 
    

    /**
     * 
     * Obtener registros paginados
     * Mantenimiento items - App
     *
     * @param  Request $request
     * @return array
     */
    public function records(Request $request)
    {
        $records = Item::whereFilterRecordsApi($request->input, $request->search_by_barcode);
        
        return new ItemCollection($records->paginate(config('tenant.items_per_page')));
    }

    
    /**
     * 
     * Obtener registros paginados para ventas - Modo POS App
     *
     * @param  Request $request
     * @return array
     */
    public function recordsSale(Request $request)
    {
        $records = Item::filterRecordsSaleApi($request);

        return new ItemSaleCollection($records->paginate(config('tenant.items_per_page')));
    }
    
 
    
    /**
     * obtener registro
     *
     * @param  int $id
     * @return ItemResource
     * 
     */
    public function record($id)
    {
        return new ItemResource(Item::findOrFail($id));
    }
    

    /**
     * 
     * Actualizar item
     *
     * @param  ItemRequest $request
     * @return array
     */
    public function update(ItemRequest $request)
    {

        $item = Item::findOrFail($request->id);
        $item->fill($request->all());
        $this->saveImage($item, $request);
        $item->update();

        return [
            'success' => true,
            'message' => 'Producto actualizado',
            'data' => [
                'id' => $item->id,
                'internal_id' => $item->internal_id,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'name' => $item->name,
                'second_name' => $item->second_name,
                'unit_type_id' => $item->unit_type_id,
                'currency_type_id' => $item->currency_type_id,
                'sale_unit_price' => $item->sale_unit_price,
                'purchase_unit_price' => $item->purchase_unit_price,
                'has_isc' => $item->has_isc,
                'system_isc_type_id' => $item->system_isc_type_id,
                'percentage_isc' => $item->percentage_isc,
                'sale_affectation_igv_type_id' => $item->sale_affectation_igv_type_id,
                'purchase_affectation_igv_type_id' => $item->purchase_affectation_igv_type_id,
                'calculate_quantity' => $item->calculate_quantity,
                'has_igv' => $item->has_igv,
                'has_perception' => $item->has_perception,
                'percentage_of_profit' => $item->percentage_of_profit,
                'percentage_perception' => $item->percentage_perception,
                'category_id' => $item->category_id,
                'brand_id' => $item->brand_id,
                'barcode' => $item->barcode,
            ]
        ];
    }

        
    /**
     * 
     * Eliminar item, usa método del proceso por web
     *
     * @param  int $id
     * @return array
     */
    public function destroy($id)
    {
        return app(ItemControllerWeb::class)->destroy($id);
    }

        
    /**
     * 
     * Activar/Desactivar producto
     *
     * @param  int $id
     * @param  bool $active
     * @return array
     */
    public function changeActive($id, $active)
    {
        $record = Item::findOrFail($id);
        $record->active = $active;
        $record->save();
        
        return [
            'success' => true,
            'message' => $active ? 'Producto habilitado con éxito' : 'Producto inhabilitado con éxito'
        ];
    }

     
    /**
     * 
     * Activar/Desactivar favorito
     *
     * @param  int $id
     * @param  bool $favorite
     * @return array
     */
    public function changeFavorite($id, $favorite)
    {
        $record = Item::findOrFail($id);
        $record->favorite = $favorite;
        $record->save();
        
        return [
            'success' => true,
            'message' => $favorite ? 'Agregado a favoritos' : 'Eliminado de favoritos'
        ];
    }

    
    /**
     * 
     * Guardar imágen de diferentes tamaños
     *
     * @param  Item $item
     * @param  ItemRequest $request
     * @return void
     */
    public function saveImage(&$item, $request)
    {
        $temp_path = $request->temp_path;

        if($temp_path) 
        {
            $old_filename = $request->image;
            $folder = 'items';

            $item->image = UploadFileHelper:: uploadImageFromTempFile($folder, $old_filename, $temp_path, "{$item->description}-{$item->id}", true);

            //size medium
            $image_medium = $item->getImageResize($temp_path, 512);
            $item->image_medium = UploadFileHelper:: uploadImageFromTempFile($folder, $old_filename, (string) $image_medium->encode('jpg', 30), "{$item->description}-{$item->id}", false, 'medium');

              //size small
            $image_small = $item->getImageResize($temp_path, 256);
            $item->image_small = UploadFileHelper:: uploadImageFromTempFile($folder, $old_filename, (string) $image_small->encode('jpg', 20), "{$item->description}-{$item->id}", false, 'small');
        }
        else if(!$request->image && !$request->temp_path && !$request->image_url)
        {
            $description = 'imagen-no-disponible.jpg';
            $item->image = $description;
            $item->image_medium = $description;
            $item->image_small = $description;
        }

    }

    
    /**
     * 
     * Cargar imágen desde app
     *
     * @param  Request $request
     * @return array
     */
    public function uploadTempImage(Request $request)
    {

        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file', 'jpg,jpeg,png,svg');
        if(!$validate_upload['success']) return $validate_upload;


        if ($request->hasFile('file'))
        {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type') ?? null,
            ];

            return UploadFileHelper::getTempFile($new_request);
        }

        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

}
