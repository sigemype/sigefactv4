<?php

namespace Modules\Template\Formats;

use Exception;
use Modules\Template\Helpers\TemplateHelper;

class InternalFormat
{
    public function create($model, $document, $format)
    {
        $page_width = '21cm';
        $page_height = '29.7cm';
        $margin_top = '1cm';
        $margin_bottom = '2cm';
        $margin_right = '1cm';
        $margin_left = '1cm';
//        $path_css = public_path('css/template.css');

        $template_id = 'template_internal';
        $template_view = $model . '.' . $format . '.template';

        $template_data = view('template::' . $template_view, [
            'document' => $document,
            'template_id' => $template_id,
            'model' => $model,
            'size' => $format,
        ])->render();

        $body_html = view('template::template_internal', [
            'template_data' => $template_data,
            'filename' => $document->filename,
        ])->render();

        $pdf = new Pdf([
            'no-outline',
            'disable-smart-shrinking',
            'dpi' => '96',
            'encoding' => 'UTF-8',
            'margin-top' => $margin_top,
            'margin-bottom' => $margin_bottom,
            'margin-right' => $margin_right,
            'margin-left' => $margin_left,
            'print-media-type',
            'zoom' => '1',
            'viewport-size' => '1280x1024',
            'page-width' => $page_width,
            'page-height' => $page_height,
//            'user-style-sheet' => $path_css,
            'enable-local-file-access',
        ]);

        $pdf->addPage($body_html);

        if (func_is_windows()) {
            $pdf->binary = public_path('vendor/wkhtmltopdf.exe');
        }

        $pdf_content = $pdf->toString();

        if (!$pdf_content) {
            throw new Exception($pdf->getError());
        }

        TemplateHelper::uploadPdf($model, $document, $pdf_content, $format);

        return [
            'success' => true,
            'pdf_content' => $pdf_content,
        ];
    }
}
