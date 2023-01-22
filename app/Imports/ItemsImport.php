<?php

namespace App\Imports;

use App\Models\Tenant\Item;
use App\Models\Tenant\Warehouse;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Modules\Item\Models\Category;
use Modules\Item\Models\Brand;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Modules\Inventory\Models\InventoryTransaction;
use Modules\Inventory\Models\Inventory;
use Exception;
use Modules\Item\Models\ItemLotsGroup;
use Modules\Finance\Helpers\UploadFileHelper;


class ItemsImport implements ToCollection
{
    use Importable;

    protected $data;

    public function collection(Collection $rows)
    {
            $total = count($rows);
            $warehouse_id_de = request('warehouse_id');
            $registered = 0;
            unset($rows[0]);
            foreach ($rows as $row) {
                $description = $row[0];
                $item_type_id = '01';
                $internal_id = ($row[1])?:null;
                $model = ($row[2]) ? : null;
                $item_code = ($row[3])?:null;
                $unit_type_id = $row[4];
                $currency_type_id = $row[5];
                $sale_unit_price = $row[6];
                $sale_affectation_igv_type_id = $row[7];
                // $has_igv = (strtoupper($row[7]) === 'SI')?true:false;

                $affectation_igv_types_exonerated_unaffected = ['20','21','30','31','32','33','34','35','36','37'];

                if(in_array($sale_affectation_igv_type_id, $affectation_igv_types_exonerated_unaffected)) {

                    $has_igv = true;

                }else{

                    $has_igv = (strtoupper($row[8]) === 'SI')?true:false;

                }

                $purchase_unit_price = ($row[9])?:0;
                $purchase_affectation_igv_type_id = ($row[10])?:null;
                $stock = $row[11];
                $stock_min = $row[12];
                $category_name = $row[13];
                $brand_name = $row[14];

                $name = $row[15];
                $second_name = $row[16];

                $lot_code = $row[17];
                $date_of_due = $row[18];
                $barcode = $row[19] ?? null;
                $image_url = $row[20] ?? null;

                // image names
                $file_name = 'imagen-no-disponible.jpg';
                $file_name_medium = 'imagen-no-disponible.jpg';
                $file_name_small = 'imagen-no-disponible.jpg';



                // verifica el campo url y valida si es una url correcta
                if($image_url && filter_var($image_url, FILTER_VALIDATE_URL)) {
                    // verifica si la url no obtiene errores
                    if(strpos(get_headers($image_url, 1)[0],'200') != false){
                        // dd(stripos(getimagesize($image_url)['mime'], 'image') === 0? 'no':'si');
                        $image_type = exif_imagetype($image_url);
                        // verifica si lo que obtiene de la url es una imagen
                        if($image_type > 0 || $image_type < 19) {
                            $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'items'.DIRECTORY_SEPARATOR;
                            $dateNow = date('YmdHis');
                            $content = file_get_contents($image_url);

                            UploadFileHelper::checkIfImageCanBeProcessed($content);

                            $slugs = explode ("/", $image_url);
                            $latestSlug = $slugs [(count ($slugs) - 1)];
                            $image_name = strtok($latestSlug, '?');

                            $file_name = Str::slug($description).'-'.$dateNow.'.'.$image_name;
                            $file_name_medium = Str::slug($description).'-'.$dateNow.'_medium.'.$image_name;
                            $file_name_small = Str::slug($description).'-'.$dateNow.'_small.'.$image_name;

                            Storage::put($directory.$file_name, $content);

                            $getImage = Storage::get($directory.$file_name);

                            $image_medium = \Image::make($getImage)
                                            ->resize(512, null, function ($constraint) {
                                                $constraint->aspectRatio();
                                                $constraint->upsize();
                                            })
                                            ->stream();

                            Storage::put($directory.$file_name_medium, $image_medium);

                            $image_small = \Image::make($getImage)
                                            ->resize(256, null, function ($constraint) {
                                                $constraint->aspectRatio();
                                                $constraint->upsize();
                                            })
                                            ->stream();

                            Storage::put($directory.$file_name_small, $image_small);
                        }
                    }
                }

                $warehouse_id = request('warehouse_id');


                if($internal_id) {
                    $item = Item::where('internal_id', $internal_id)
                                    ->first();
                } else {
                    $item = null;
                }
                // $establishment_id = auth()->user()->establishment->id;

                if(!$item) {
                    $category = $category_name ? Category::updateOrCreate(['name' => $category_name]):null;
                    $brand = $brand_name ? Brand::updateOrCreate(['name' => $brand_name]):null;
                    // dd($row, $lot_code ,$date_of_due, $category, $brand);

                    if($lot_code && $date_of_due){

                        $_date_of_due = Date::excelToDateTimeObject($date_of_due)->format('Y-m-d');

                        // dd($lot_code, $date_of_due, $x);

                        $new_item = Item::create([
                            'description' => $description,
                            'model' => $model,
                            'item_type_id' => $item_type_id,
                            'internal_id' => $internal_id,
                            'item_code' => $item_code,
                            'unit_type_id' => $unit_type_id,
                            'currency_type_id' => $currency_type_id,
                            'sale_unit_price' => $sale_unit_price,
                            'sale_affectation_igv_type_id' => $sale_affectation_igv_type_id,
                            'has_igv' => $has_igv,
                            'purchase_unit_price' => $purchase_unit_price,
                            'purchase_affectation_igv_type_id' => $purchase_affectation_igv_type_id,
                            'stock' => $stock,
                            'stock_min' => $stock_min,
                            'category_id' => optional($category)->id,
                            'brand_id' => optional($brand)->id,
                            'name' => $name,
                            'second_name' => $second_name,

                            'lots_enabled' => true,
                            'lot_code' => $lot_code,
                            'date_of_due' => $_date_of_due,
                            'barcode' => $barcode,
                            'warehouse_id' => $warehouse_id,
                            'image' => $file_name,
                            'image_medium' => $file_name_medium,
                            'image_small' => $file_name_small,
                        ]);

                        $new_item->lots_group()->create([
                            'code'  => $lot_code,
                            'quantity'  => $stock,
                            'date_of_due'  => $_date_of_due,
                        ]);

                    }else{

                        Item::create([
                            'description' => $description,
                            'model' => $model,
                            'item_type_id' => $item_type_id,
                            'internal_id' => $internal_id,
                            'item_code' => $item_code,
                            'unit_type_id' => $unit_type_id,
                            'currency_type_id' => $currency_type_id,
                            'sale_unit_price' => $sale_unit_price,
                            'sale_affectation_igv_type_id' => $sale_affectation_igv_type_id,
                            'has_igv' => $has_igv,
                            'purchase_unit_price' => $purchase_unit_price,
                            'purchase_affectation_igv_type_id' => $purchase_affectation_igv_type_id,
                            'stock' => $stock,
                            'stock_min' => $stock_min,
                            'category_id' => optional($category)->id,
                            'brand_id' => optional($brand)->id,
                            'name' => $name,
                            'second_name' => $second_name,
                            'barcode' => $barcode,
                            'warehouse_id' => $warehouse_id,
                            'image' => $file_name,
                            'image_medium' => $file_name_medium,
                            'image_small' => $file_name_small,
                        ]);

                    }


                    $registered += 1;

                }else{

                    $inventory_transaction = InventoryTransaction::findOrFail('102');
                    $stock = $row[11];
                    if (!$stock) {
                        throw new Exception("Debe ingresar el stock", 500);
                    }

                    $item->update([
                        'description' => $description,
                        'model' => $model,
                        'item_type_id' => $item_type_id,
                        'internal_id' => $internal_id,
                        'item_code' => $item_code,
                        'unit_type_id' => $unit_type_id,
                        'currency_type_id' => $currency_type_id,
                        'sale_unit_price' => $sale_unit_price,
                        'sale_affectation_igv_type_id' => $sale_affectation_igv_type_id,
                        'has_igv' => $has_igv,
                        'purchase_unit_price' => $purchase_unit_price,
                        'purchase_affectation_igv_type_id' => $purchase_affectation_igv_type_id,
                        'stock_min' => $stock_min,
                        'name' => $name,
                        'second_name' => $second_name,
                        'barcode' => $barcode,
                    ]);


                    $inventory = new Inventory();
                    $inventory->type = null;
                    $inventory->description = $inventory_transaction->name;
                    $inventory->item_id = $item->id;
                    $inventory->warehouse_id = $warehouse_id;
                    $inventory->quantity = $stock;
                    $inventory->inventory_transaction_id = $inventory_transaction->id;
                    $inventory->lot_code = $lot_code;
                    $inventory->save();


                    $lot_code = $row[17];
                    if($lot_code){

                        $date_of_due = $row[18];
                        if(!$date_of_due) {
                            throw new Exception("Debe ingresar el Fecha de vencimiento para el Lote", 500);
                        }


                        $current_lot = ItemLotsGroup::where([
                            'code' => $lot_code,
                            'item_id'=>$item->id
                        ])->first();

                        if($current_lot) {
                            $current_lot->quantity = $current_lot->quantity + (int)$stock;
                            $current_lot->old_quantity = $current_lot->quantity;
                            $current_lot->save();
                        }
                        else {
                            $_date_of_due = Date::excelToDateTimeObject($date_of_due)->format('Y-m-d');

                            ItemLotsGroup::create([
                                'code' => $lot_code,
                                'quantity' => $stock,
                                'old_quantity' => $stock,
                                'date_of_due' =>  $_date_of_due,
                                'item_id' => $item->id
                            ]);
                        }


                    }


                    $registered += 1;

                }
            }
            $this->data = compact('total', 'registered', 'warehouse_id_de');

    }

    public function getData()
    {
        return $this->data;
    }
}
