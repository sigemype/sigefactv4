<?php

namespace Modules\MobileApp\Models;

use App\Models\Tenant\ModelTenant;

class AppConfiguration extends ModelTenant
{

    public const APP_STYLES_PATH  = 'liveapp'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR;

    protected $fillable = [
        'show_image_item',
        'print_format_pdf',
        'theme_color',
        'card_color',
        'header_waves',
        'app_mode',
        'direct_print',
        'direct_send_documents_whatsapp',
    ];

    protected $casts = [
        'show_image_item' => 'bool',
        'direct_print' => 'bool',
        'direct_send_documents_whatsapp' => 'bool',
    ];


    /**
     * @return array
     */
    public function getRowResource()
    {
        return [
            'id' => $this->id,
            'show_image_item' => $this->show_image_item,
            'print_format_pdf' => $this->print_format_pdf,
            'theme_color' => $this->theme_color,
            'card_color' => $this->card_color,
            'header_waves' => $this->header_waves,
            'app_mode' => $this->app_mode,
            'direct_print' => $this->direct_print,
            'has_igv_31556' => auth()->user() ? auth()->user()->establishment->has_igv_31556 : false,
            'igv_31556_percentage' => config('tenant.igv_31556_percentage'),
            'direct_send_documents_whatsapp' => $this->direct_send_documents_whatsapp,
        ];
    }


    /**
     *
     * Obtener parametros iniciales de configuracion
     *
     * @return array
     */
    public function getRowInitialSettings()
    {
        return [
            'app_mode' => $this->app_mode,
            'theme_color' => $this->theme_color,
            'card_color' => $this->card_color,
            'header_waves' => $this->header_waves,
            'style_theme_content' => $this->getStyleThemeContent(),
            'style_card_content' => $this->getStyleCardContent(),
        ];
    }


    /**
     *
     * Determinar tema del card y obtener estilo
     *
     * @return string
     */
    public function getStyleCardContent()
    {
        $content = null;

        switch ($this->card_color) {
            case 'unicolor':
                $content = $this->getFileStyleContents('cards.css');
                break;
        }

        return $content;
    }


    /**
     *
     * Determinar tema y obtener estilo
     *
     * @return string
     */
    public function getStyleThemeContent()
    {
        $content = null;

        switch ($this->theme_color) {
            case 'red':
                $content = $this->getFileStyleContents('skin-red.css');
                break;
            case 'dark':
                $content = $this->getFileStyleContents('skin-dark.css');
                break;
        }

        return $content;
    }


    /**
     *
     * Obtener contenido del estilo
     *
     * @param  string $filename
     * @return string
     */
    public function getFileStyleContents($filename)
    {
        return file_get_contents(public_path(self::APP_STYLES_PATH.$filename));
    }

}
