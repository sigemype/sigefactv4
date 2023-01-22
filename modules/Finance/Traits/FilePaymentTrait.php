<?php

namespace Modules\Finance\Traits;

use Carbon\Carbon;
use App\Models\Tenant\DocumentPayment;
use App\Models\Tenant\SaleNotePayment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Finance\Helpers\UploadFileHelper;


trait FilePaymentTrait
{

    public function saveFiles($record, $request, $type)
    {

        $temp_path = $request->temp_path;

        if($temp_path) {

            $file_name_old = $request->filename;
            $file_name_old_array = explode('.', $file_name_old);
            $file_content = file_get_contents($temp_path);
            $extension = $file_name_old_array[1];
            $file_name = Str::slug($file_name_old_array[0])."-{$type}-".$record->id.'.'.$extension;

            // validaciones archivos
            $allowed_file_types_images = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg'];
            $is_image = UploadFileHelper::getIsImage($temp_path, $allowed_file_types_images);

            $allowed_file_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg', 'application/pdf'];
            UploadFileHelper::checkIfValidFile($file_name, $temp_path, $is_image, 'jpg,jpeg,png,gif,svg,pdf', $allowed_file_types);
            // validaciones archivos

            $record->payment_file()->create([
                'filename' => $file_name
            ]);

            Storage::disk('tenant')->put('payment_files'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$file_name, $file_content);


        }

    }
    

    /**
     * 
     * Guardar archivos de los pagos relacionados a cada documento
     * 
     * Usado en:
     * Facturalo
     * SaleNoteController
     *
     * @param  array $row
     * @param  DocumentPayment|SaleNotePayment $record
     * @param  string $append_folder
     * @return void
     */
    private function saveFilesFromPayments($row, $record, $append_folder)
    {
        $temp_path = $row['temp_path'] ?? false;

        if($temp_path)
        {
            $params_payment_file = [
                'temp_path' => $temp_path,
                'filename' => $row['filename'],
            ];

            $this->saveFiles($record, (object) $params_payment_file, $append_folder);
        }
    }

}
