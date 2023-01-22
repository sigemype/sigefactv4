<?php

namespace Modules\Template\Helpers;

use App\Helpers\StorageHelper;
use Exception;
use mikehaertl\wkhtmlto\Pdf;
use Modules\Company\Models\Company;
use Modules\Template\Models\Template;

class TemplatePdf
{
    public function create($model, $document, $size = 'a4')
    {
        //if($size === 'a4') {
            $size_width = '21cm';
            $size_height = '29.7cm';
            $margin_top = '1cm';
            $margin_bottom = '2cm';
            $margin_right = '1cm';
            $margin_left = '1cm';
            $path_css = public_path('css/template.css');
        //}
        if ($size === 'a5') {
            $size_width = '21cm';
            $size_height = '14.5cm';
            $margin_top = '.5cm';
            $margin_bottom = '1cm';
            $margin_right = '1cm';
            $margin_left = '1cm';
            $path_css = public_path('css/template.css');
        }
        if ($size === 'ticket') {
            $size_width = '8cm';
            $size_height = '20cm';
            $margin_top = '0cm';
            $margin_bottom = '.5cm';
            $margin_right = '.25cm';
            $margin_left = '.25cm';
            $path_css = public_path('css/template_ticket.css');
        }

        if($model === 'quotation') {
            //$document->document_type_name .= ' ELECTRÓNICA';
            $footer_text_1 = null;
            $footer_text_2 = null;
            $footer_text_3 = null;
        } elseif ($model === 'purchase_order') {
            //$document->document_type_name .= ' ELECTRÓNICA';
            $footer_text_1 = null;
            $footer_text_2 = null;
            $footer_text_3 = null;
        } elseif ($model === 'ticket') {
            $document->document_type_name .= ' ELECTRÓNICA';
            $footer_text_1 = null;
            $footer_text_2 = null;
            $footer_text_3 = null;
        } else {
            if($model === 'dispatch') {
                $document->document_type_name = 'GUÍA DE REMISIÓN ELECTRÓNICA - REMITENTE';
            } else {
                $document->document_type_name .= ' ELECTRÓNICA';
            }
            $footer_text_1 = $document->print_footer_text;
            $footer_text_2 = 'Representación impresa de la ' . $document->document_type_name;
            $footer_text_3 = 'Para consultar el comprobante ingresar a ' . url('comprobantes');
        }

        $template = Template::query()->where('is_default', true)->first();

        if($model === 'sale') {
            if(in_array($document->document_type_id, ['01', '03'])) {
                $template_view = $model.'.'.$size.'.invoice.template';
            } else {
                $template_view = $model.'.'.$size.'.note.template';
            }
        } else {
            $template_view = $model.'.'.$size.'.template';
        }

        $template_data = view('template::'.$template_view, [
            'company_configuration' => Company::query()->first()->configuration,
            'document' => $document,
            'items' => [],
            'template_id' => $template->template_id,
            'model' => $model,
            'size' => $size,
            'footer_text_1' => $footer_text_1,
            'footer_text_2' => $footer_text_2,
            'footer_text_3' => $footer_text_3
        ])->render();

        $html = view('template::main_template', ['template_data' => $template_data,
            'filename' => $document->filename,
            'template' => $template
        ])->render();

        $footer_html = '';
        if($size !== 'ticket') {
            $footer_html = view('template::partials.footers.'.$size.'.template', [
                'footer_text_1' => $footer_text_1,
                'footer_text_2' => $footer_text_2,
                'footer_text_3' => $footer_text_3
            ])->render();
        }

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
            'page-width' => $size_width,
            'page-height' => $size_height,
            'user-style-sheet' => $path_css,
            'footer-html' => $footer_html,
        ]);

        $pdf->addPage($html);

        if (is_windows()) {
            $pdf->binary = public_path('vendor/wkhtmltopdf.exe');
        }
        $result = $pdf->toString();

        if (!$result) {
            throw new Exception($pdf->getError());
//            return [
//                'success' => false,
//                'message' => $pdf->getError(),
//            ];
        }

        $storage = new StorageHelper();
        $storage->setFolder($document->soap_type_id, $model);
        $storage->uploadPdf($document->filename, $result);
//        $storage->uploadHtml($document->filename, $html);

        return [
            'success' => true
        ];
    }
}
