<?php

namespace Modules\Finance\Helpers; 

use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Support\Str;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Log;


class UploadFileHelper
{ 
    
    /**
     * 
     * Validar archivos
     *
     * @param  Request $request
     * @param  string $column
     * @param  string $mimes
     * @param  bool $is_image
     * @return array
     */
    public static function validateUploadFile($request, $column = 'file', $mimes = 'jpg,jpeg,png,gif,svg,pdf,xlsx', $is_image = true)
    {
        
        $validator = Validator::make($request->all(), [
            $column => 'mimes:'.$mimes
        ]);

        if ($validator->fails()) { 
            return [
                'success' => false,
                'message' =>  'Tipo de archivo no permitido',
            ];
        }

        if($is_image)
        {
            $processed = self::imageCanBeProcessed($request->file($column)->getPathName());
            if(!$processed['success']) return $processed;
        }
        

        return [
            'success' => true,
            'message' =>  '',
        ];

    } 

     
    /**
     * 
     * Obtener archivo temporal en base64
     *
     * @param  $request
     * @return array
     */
    public static function getTempFile($request)
    {
        $file = $request['file'];
        $type = $request['type'];

        $temp = tempnam(sys_get_temp_dir(), $type);
        file_put_contents($temp, file_get_contents($file));

        $mime = mime_content_type($temp);
        $data = file_get_contents($temp);

        return [
            'success' => true,
            'data' => [
                'filename' => $file->getClientOriginalName(),
                'temp_path' => $temp,
                'temp_image' => 'data:' . $mime . ';base64,' . base64_encode($data)
            ]
        ];
    }

    
    /**
     * 
     * Cargar archivo
     *
     * Usado para imágenes en:
     * PaymentConfigurationController
     * PaymentLinkController
     * 
     * @param  string $folder
     * @param  string $old_filename
     * @param  string $temp_path
     * @param  int $id
     * @param  string $prefix
     * @return string
     */
    public static function uploadFileFromTempFile($folder, $old_filename, $temp_path, $id, $prefix = null, $mimes = 'jpg,jpeg,png,svg', $allowed_file_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/svg'])
    {

        $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR;
        $old_filename_array = explode('.', $old_filename);
        $now = date('YmdHis');

        $filename =  ($prefix ? "{$prefix}_" : "")."{$id}_{$now}".'.'.end($old_filename_array);

        self::checkIfValidFile($filename, $temp_path, true, $mimes, $allowed_file_types);

        Storage::put($directory.$filename, file_get_contents($temp_path));

        return $filename;
    }

    
    /**
     * 
     * Cargar imágen
     *
     * @param  string $folder
     * @param  string $old_filename
     * @param  string $temp_path
     * @param  string $name
     * @param  bool $file_get_contents
     * @param  string $suffix
     * @param  string $mimes
     * @param  array $allowed_file_types
     * @return string
     */
    public static function uploadImageFromTempFile($folder, $old_filename, $temp_path, $name, $file_get_contents, $suffix = null, $mimes = 'jpg,jpeg,png,svg', $allowed_file_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/svg'])
    {
        
        $directory = 'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR;
        $old_filename_array = explode('.', $old_filename);
        $now = date('YmdHis');

        $suffix = ($suffix ? "-{$suffix}" : "");
        $filename =  Str::slug($name)."-{$now}{$suffix}".'.'.end($old_filename_array);

        $file = $file_get_contents ? file_get_contents($temp_path) :  $temp_path;

        if($file_get_contents)  
        {
            self::checkIfValidFile($filename, $temp_path, true, $mimes, $allowed_file_types);
        }
        else
        {
            self::checkIfImageCanBeProcessed($temp_path);
        }

        Storage::put($directory.$filename, $file);

        return $filename;
    }


    /**
     * 
     * lanza excepcion si el archivo no es permitido
     *
     * @param  string $message
     * @return void
     */
    public static function notAllowedFile($message)
    {
        throw new Exception($message);
    }

    
    /**
     * 
     * Validar si es un archivo válido
     *
     * @param  string $filename
     * @param  string $temp_path
     * @param  bool $is_image
     * @param  string $mimes
     * @param  array $allowed_file_types
     * @return void
     */
    public static function checkIfValidFile($filename, $temp_path, $is_image = true, $mimes = null, $allowed_file_types = null)
    {
        $error_message = 'Tipo de archivo no permitido';
        $mimes = $mimes ?? self::getGeneralMimes();
        $allowed_file_types = $allowed_file_types ?? self::getGeneralAllowedFileTypes();

        self::checkIfAllowedExtension($filename, $mimes);

        if (!in_array(mime_content_type($temp_path), $allowed_file_types, true)) self::notAllowedFile($error_message);

        $new_file = new File($temp_path);

        $data = [
            'file' => $new_file
        ];

        $validator = Validator::make($data, [
            'file' => 'mimes:'.$mimes
        ]);

        if($validator->fails()) self::notAllowedFile($error_message);

        if($is_image) self::checkIfImageCanBeProcessed($temp_path);
    }

    
    /**
     * 
     * Validar si es un archivo css válido
     *
     * @param  string $filename
     * @param  string $temp_path
     * @param  string $mimes
     * @param  array $allowed_file_types
     * @return void
     */
    public static function checkIfValidCssFile($filename, $temp_path, $mimes, $allowed_file_types)
    {
        $error_message = 'Tipo de archivo no permitido';

        self::checkIfAllowedExtension($filename, $mimes);

        if (!in_array(mime_content_type($temp_path), $allowed_file_types, true)) self::notAllowedFile($error_message);
    }
    

    /**
     * 
     * Determina si es imagen
     *
     * @param  string $temp_path
     * @param  array $allowed_file_types
     * @return bool
     */
    public static function getIsImage($temp_path, $allowed_file_types)
    {
        return in_array(mime_content_type($temp_path), $allowed_file_types, true);
    }

        
    /**
     * Validar si la imagen pudo ser procesada
     *
     * @param  string $temp_path
     * @return void
     */
    public static function checkIfImageCanBeProcessed($temp_path)
    {
        $processed = self::imageCanBeProcessed($temp_path);

        if(!$processed['success']) self::notAllowedFile($processed['message']);
    }

    
    /**
     * 
     * Procesar imagen
     *
     * @param  string $temp_path
     * @return array
     */
    public static function imageCanBeProcessed($temp_path)
    {
        try 
        {
            Image::make($temp_path);
            
            return [
                'success' => true,
            ];
        }
        catch (Exception $e) 
        {
            $message = 'La imágen no puede ser procesada, verifique si es un archivo válido.';
            self::writeErrorLog($e, $message);

            return [
                'success' => false,
                'message' => $message,
            ];
        }
    }

    
    /**
     *
     * @param  string $filename
     * @param  array $mimes
     * @return void
     */
    public static function checkIfAllowedExtension($filename, $mimes)
    {
        $extension = self::getFileExtension($filename);
        $allowed_extensions = explode(',', $mimes);

        if (!in_array($extension, $allowed_extensions, true)) self::notAllowedFile('Extensión del archivo no permitida.');
    }
    
    
    /**
     *
     * @param  string $filename
     * @return string
     */
    public static function getFileExtension($filename)
    {
        $data = explode('.', $filename);
        return end($data);
    }


    /**
     *
     * @return string
     */
    public static function getGeneralMimes()
    {
        return 'jpg,jpeg,png,gif,svg';
    }


    /**
     *
     * @return array
     */
    public static function getGeneralAllowedFileTypes()
    {
        return ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg'];
    }

    
    /**
     *
     * @param  Exception $exception
     * @return void
     */
    public static function writeErrorLog($exception, $message = null)
    {
        Log::error(($message ?? '')."Line: {$exception->getLine()} - Message: {$exception->getMessage()} - File: {$exception->getFile()}");
    }
    
}
