<?php

namespace Modules\Template\Helpers;

use Modules\Store\Helpers\StorageHelper;

class TemplateHelper
{
    static function getTemplateView($model, $format)
    {
        return $model.'.'.$format.'.template';
    }

    static function getBodyHtml($template_data, $template, $document, $watermark, $template_main)
    {
        return view('template::'.$template_main, ['template_data' => $template_data,
            'filename' => $document->filename,
            'establishment' => $document->establishment,
            'template' => $template,
            'watermark' => $watermark
        ])->render();
    }

    static function getFooterHtml($model, $template, $document, $format)
    {
        if($format === 'ticket') {
            return '';
        }
        if($template->id === 6) {
            $template_view = $model.'.'.$format.'.footer_6';
            return view('template::'.$template_view, [
                'document' =>$document,
                'size' => $format
            ])->render();
        }
        return view('template::footers.'.$format.'.template', [
            'document' =>$document
        ])->render();
    }

    static function uploadPdf($model, $document, $content, $format)
    {
        $storage = new StorageHelper();
        $storage->setFolder($document->soap_type_id, $model);
        $storage->uploadPdf($document->filename, $content, $format);
    }
}
