<?php

namespace Modules\Item\Observers;

use App\Models\Tenant\Item;

class ItemObserver
{
    public function saving(Item $item)
    {
        $text = [];
        if(!is_null($item->name) && $item->name !== '') {
            $text[] = $item->name;
        }
        if(!is_null($item->second_name) && $item->second_name !== '') {
            $text[] = $item->second_name;
        }
        if(!is_null($item->description) && $item->description !== '') {
            $text[] = $item->description;
        }
        if(!is_null($item->model) && $item->model !== '') {
            $text[] = $item->model;
        }
        if(!is_null($item->barcode) && $item->barcode !== '') {
            $text[] = $item->barcode;
        }
        if(!is_null($item->internal_id) && $item->internal_id !== '') {
            $text[] = $item->internal_id;
        }
        if(!is_null($item->category_id)) {
            $text[] = $item->category->name;
        }
        if(!is_null($item->brand_id)) {
            $text[] = $item->brand->name;
        }

        $item->text_filter = join(' ', $text);
    }
}
