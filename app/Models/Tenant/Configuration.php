<?php

namespace App\Models\Tenant;


use App\Models\Tenant\Catalogs\CurrencyType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Modules\Inventory\Models\Warehouse;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Configuration
 *
 * @package App\Models\Tenant
 * @mixin ModelTenant
 * @property bool $item_name_pdf_description
 * @property bool $show_extra_info_to_item
 * @property bool $show_items_only_user_stablishment
 * @method static Builder|Configuration newModelQuery()
 * @method static Builder|Configuration newQuery()
 * @method static Builder|Configuration query()
 * @method static Builder|Configuration whereCertificate($value)
 * @method static Builder|Configuration whereCreatedAt($value)
 * @method static Builder|Configuration whereId($value)
 * @method static Builder|Configuration whereLockedAdmin($value)
 * @method static Builder|Configuration whereLogin($value)
 * @method static Builder|Configuration whereSoapPassword($value)
 * @method static Builder|Configuration whereSoapSendId($value)
 * @method static Builder|Configuration whereSoapTypeId($value)
 * @method static Builder|Configuration whereSoapUrl($value)
 * @method static Builder|Configuration whereSoapUsername($value)
 * @method static Builder|Configuration whereTokenApiruc($value)
 * @method static Builder|Configuration whereTokenPrivateCulqui($value)
 * @method static Builder|Configuration whereTokenPublicCulqui($value)
 * @method static Builder|Configuration whereUpdatedAt($value)
 * @method static Builder|Configuration whereUrlApiruc($value)
 * @method static Builder|Configuration whereUseLoginGlobal($value)
 */
class Configuration extends ModelTenant
{
    protected $fillable = [
        'send_auto',
        'formats',
        'cron',
        'stock',
        'locked_emission',
        'locked_users',
        'limit_documents',
        'sunat_alternate_server',
        'plan',
        'visual',
        'enable_whatsapp',
        'phone_whatsapp',
        'apk_url',
        'limit_users',
        'quantity_documents',
        'date_time_start',
        'locked_tenant',
        'compact_sidebar',
        'decimal_quantity',
        'amount_plastic_bag_taxes',
        'colums_grid_item',
        'options_pos',
        'edit_name_product',
        'restrict_receipt_date',
        'affectation_igv_type_id',
        'terms_condition',
        'cotizaction_finance',
        'quotation_allow_seller_generate_sale',
        'allow_edit_unit_price_to_seller',
        'include_igv',
        'product_only_location',
        'header_image',
        'legend_footer',
        'default_document_type_03',
        'destination_sale',
        'terms_condition_sale',
        'login',
        'finances',
        'ticket_58',
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_password',
        'smtp_encryption',
        'seller_can_create_product',
        'seller_can_generate_sale_opportunities',
        'seller_can_view_balance',
        'update_document_on_dispaches',
        'is_pharmacy',
        'auto_send_dispatchs_to_sunat',
        'active_warehouse_prices',
        'active_allowance_charge',
        'percentage_allowance_charge',
        'send_data_to_other_server',
        'search_item_by_series',
        'change_free_affectation_igv',
        'select_available_price_list',
        'currency_type_id',
        'show_extra_info_to_item',
        'group_items_generate_document',
        'enabled_global_igv_to_purchase',
        'show_pdf_name',
        'dispatches_address_text',
        'set_address_by_establishment',
        'permission_to_edit_cpe',
        'show_items_only_user_stablishment',
        'igv_retention_percentage',
        'name_product_pdf_to_xml',
        'default_document_type_80',
        'search_item_by_barcode',
        'item_name_pdf_description',
    ];

    protected $casts = [
        'quotation_allow_seller_generate_sale' => 'boolean',
        'allow_edit_unit_price_to_seller' => 'boolean',
        'seller_can_create_product' => 'boolean',
        'seller_can_generate_sale_opportunities' => 'boolean',
        'seller_can_view_balance' => 'boolean',
        'update_document_on_dispaches' => 'boolean',
        'is_pharmacy' => 'boolean',
        'auto_send_dispatchs_to_sunat' => 'boolean',
        'send_data_to_other_server' => 'boolean',
        'select_available_price_list' => 'boolean',
        'show_extra_info_to_item' => 'boolean',
        'group_items_generate_document' => 'boolean',
        'enabled_global_igv_to_purchase' => 'boolean',
        'show_pdf_name' => 'boolean',
        'dispatches_address_text' => 'boolean',
        'set_address_by_establishment' => 'boolean',
        'show_items_only_user_stablishment' => 'boolean',
        'permission_to_edit_cpe' => 'boolean',
        'name_product_pdf_to_xml' => 'boolean',
        'item_name_pdf_description' => 'boolean',
        'default_document_type_80' => 'boolean',
        'search_item_by_barcode' => 'boolean',
    ];


    public static function boot()
    {
        parent::boot();
        static::creating(function (self $item) {

            //i f(empty($item->apk_url)) $item->apk_url = 'https://facturaloperu.com/apk/app-debug.apk';
        });
        static::retrieved(function (self $item) {

           // if (empty($item->apk_url)) $item->apk_url = 'https://facturaloperu.com/apk/app-debug.apk';
        });

    }

    /**
     * @return bool
     */
    public function isAutoSendDispatchsToSunat()
    : bool {
        return $this->auto_send_dispatchs_to_sunat;
    }

    /**
     * @param bool $auto_send_dispatchs_to_sunat
     *
     * @return Configuration
     */
    public function setAutoSendDispatchsToSunat(bool $auto_send_dispatchs_to_sunat)
    : Configuration {
        $this->auto_send_dispatchs_to_sunat = $auto_send_dispatchs_to_sunat;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPharmacy()
    : bool {
        if(empty($this->is_pharmacy)) $this->is_pharmacy = false;
        return (bool) $this->is_pharmacy;
    }

    /**
     * @param bool $is_pharmacy
     *
     * @return Configuration
     */
    public function setIsPharmacy(bool $is_pharmacy)
    : Configuration {
        $this->is_pharmacy = (bool) $is_pharmacy;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getUpdateDocumentOnDispaches() {
        return $this->update_document_on_dispaches;
    }

    /**
     * @param boolean $update_document_on_dispaches
     *
     * @return Configuration
     */
    public function setUpdateDocumentOnDispaches($update_document_on_dispaches) {
        $this->update_document_on_dispaches = (boolean) $update_document_on_dispaches;
        return $this;
    }


    /**
     * @return bool
     */
    public function getSellerCanGenerateSaleOpportunities()
    {
        return (bool) $this->seller_can_generate_sale_opportunities;
    }

    /**
     * @param bool $seller_can_generate_sale_opportunities
     *
     * @return Configuration
     */
    public function setSellerCanGenerateSaleOpportunities($seller_can_generate_sale_opportunities)
    {
        $this->seller_can_generate_sale_opportunities = $seller_can_generate_sale_opportunities;
        return $this;
    }

    /**
     * Establece las configuraciones para envio de correo.
     *
     * @return Configuration
     *@example
     * <?php
     *  Configuration::setConfigSmtpMail();
     *?>
     *
     */
    public static function setConfigSmtpMail(){
        $config = self::first();
        if (empty($config)) $config = new self();
        if (
            !empty($config->smtp_host) &&
            !empty($config->smtp_port) &&
            !empty($config->smtp_user) &&
            !empty($config->smtp_password) &&
            !empty($config->smtp_encryption)
        ) {
            Config::set('mail.host', $config->smtp_host);
            Config::set('mail.port', $config->smtp_port);
            Config::set('mail.username', $config->smtp_user);
            Config::set('mail.password', $config->smtp_password);
            Config::set('mail.encryption', $config->smtp_encryption);
        }
        return $config;
    }
    /**
     * @return mixed
     */
    public function getSmtpHost()
    {
        return empty($this->smtp_host)?config('mail.host'):$this->smtp_host;
    }

    /**
     * @param mixed $smtp_host
     *
     * @return Configuration
     */
    public function setSmtpHost($smtp_host)
    {
        $this->smtp_host = $smtp_host;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSmtpPort()
    {
        return empty($this->smtp_port)?config('mail.port'):$this->smtp_port;
    }

    /**
     * @param mixed $smtp_port
     *
     * @return Configuration
     */
    public function setSmtpPort($smtp_port)
    {
        $this->smtp_port = $smtp_port;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSmtpUser()
    {
        return empty($this->smtp_user)?config('mail.username'):$this->smtp_user;
    }

    /**
     * @param mixed $smtp_user
     *
     * @return Configuration
     */
    public function setSmtpUser($smtp_user)
    {
        $this->smtp_user = $smtp_user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSmtpPassword()
    {
        return empty($this->smtp_password)?config('mail.password'):$this->smtp_password;
    }

    /**
     * @param mixed $smtp_password
     *
     * @return Configuration
     */
    public function setSmtpPassword($smtp_password)
    {
        $this->smtp_password = $smtp_password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSmtpEncryption()
    {
        return empty($this->smtp_encryption)?config('mail.encryption'):$this->smtp_encryption;
    }

    /**
     * @param mixed $smtp_encryption
     *
     * @return Configuration
     */
    public function setSmtpEncryption($smtp_encryption)
    {
        $this->smtp_encryption = $smtp_encryption;
        return $this;
    }

    public function setPlanAttribute($value)
    {
        $this->attributes['plan'] = (is_null($value)) ? null : json_encode($value);
    }

    public function getPlanAttribute($value)
    {
        return (is_null($value)) ? null : (object)json_decode($value);
    }

    public function setVisualAttribute($value)
    {
        $this->attributes['visual'] = (is_null($value)) ? null : json_encode($value);
    }

    public function getVisualAttribute($value)
    {
        return (is_null($value)) ? null : (object)json_decode($value);
    }

    public function setLoginAttribute($value)
    {
        $this->attributes['login'] = is_null($value) ? null : json_encode($value);
    }

    public function getLoginAttribute($value)
    {
        return is_null($value) ? null : (object)json_decode($value);
    }

    public function setFinancesAttribute($value)
    {
        $this->attributes['finances'] = (is_null($value)) ? null : json_encode($value);
    }

    public function getFinancesAttribute($value)
    {
        return is_null($value) ? ['apply_arrears' => false, 'arrears_amount' => 0] : (object)json_decode($value);
    }

    /**
     * Devuelve un json con las propiedades excluidas
     *
     * @return string
     */
    public static function getPublicConfig(){
        $conf = self::first();
        $data = $conf->getCollectionData();

        return json_encode($data);

    }


    /**
     * @return array
     */
    public function getCollectionData() {
        $company = Company::first();
        /** @var User $user */
        $user = new User();

        if(\Auth::user()) {
            $user = auth()->user();
        }

        $establishment =   $user->establishment;
        $establishment_id = $user->establishment_id;
        $serie = $user->series_id;
        $document_id = $user->document_id;
        $typeUser = $user->type;
        $unit_type_id = 'KGM'; //Unidad de medida por defecto
        $warehouse = Warehouse::where('establishment_id', $establishment_id)->first();
        if($warehouse == null){
             $warehouse = new Warehouse();
        }
        $currency = CurrencyType::all();
        return [
            'id' => $this->id,
            'company' => $company,
            'establishment' => $establishment,
            'warehouse_id' => $warehouse->id,
            'send_auto' => (bool)$this->send_auto,
            'formats' => $this->formats,
            'stock' => (bool)$this->stock,
            'cron' => (bool)$this->cron,
            'sunat_alternate_server' => (bool)$this->sunat_alternate_server,
            'compact_sidebar' => (bool)$this->compact_sidebar,
            'subtotal_account' => $this->subtotal_account,
            'decimal_quantity' => $this->decimal_quantity,
            'amount_plastic_bag_taxes' => $this->amount_plastic_bag_taxes,
            'colums_grid_item' => $this->colums_grid_item,
            'options_pos' => (bool)$this->options_pos,
            'edit_name_product' => (bool)$this->edit_name_product,
            'restrict_receipt_date' => (bool)$this->restrict_receipt_date,
            'affectation_igv_type_id' => $this->affectation_igv_type_id,
            'visual' => $this->visual,
            'enable_whatsapp' => (bool)$this->enable_whatsapp,
            'phone_whatsapp' => $this->enable_whatsapp,
            'apk_url' => $this->apk_url,
            'terms_condition' => $this->terms_condition,
            'terms_condition_sale' => $this->terms_condition_sale,
            'cotizaction_finance' => (bool)$this->cotizaction_finance,
            'include_igv' => (bool)$this->include_igv,
            'product_only_location' => (bool)$this->product_only_location,
            'legend_footer' => (bool)$this->legend_footer,
            'default_document_type_03' => (bool)$this->default_document_type_03,
            'header_image' => $this->header_image,
            'destination_sale' => (bool)$this->destination_sale,
            'quotation_allow_seller_generate_sale' => $this->quotation_allow_seller_generate_sale,
            'allow_edit_unit_price_to_seller' => $this->allow_edit_unit_price_to_seller,
            'finances' => $this->finances,
            'ticket_58' => (bool)$this->ticket_58,
            'seller_can_create_product' => (bool)$this->seller_can_create_product,
            'seller_can_view_balance' => (bool)$this->seller_can_view_balance,
            'seller_can_generate_sale_opportunities' => (bool)$this->seller_can_generate_sale_opportunities,
            'update_document_on_dispaches' => (bool)$this->update_document_on_dispaches,
            'is_pharmacy' => (bool)$this->is_pharmacy,
            'auto_send_dispatchs_to_sunat' => (bool)$this->auto_send_dispatchs_to_sunat,
            'send_data_to_other_server' => (bool)$this->send_data_to_other_server,
            'item_per_page' => config('tenant.items_per_page'),
            'active_warehouse_prices' => (bool)$this->active_warehouse_prices,
            'active_allowance_charge' => (bool)$this->active_allowance_charge,
            'dispatches_address_text' => $this->isDispatchesAddressText(),
            'show_items_only_user_stablishment' => $this->isShowItemsOnlyUserStablishment(),
            'search_item_by_series' => (bool)$this->search_item_by_series,
            'change_free_affectation_igv' => (bool)$this->change_free_affectation_igv,
            'select_available_price_list' => (bool)$this->select_available_price_list,
            'show_extra_info_to_item' => (bool)$this->show_extra_info_to_item,
            'percentage_allowance_charge' => $this->percentage_allowance_charge,
            'group_items_generate_document' => $this->group_items_generate_document,
            'set_address_by_establishment' => $this->set_address_by_establishment,
            'permission_to_edit_cpe' => $this->permission_to_edit_cpe,
            'name_product_pdf_to_xml' => $this->name_product_pdf_to_xml,
            'default_document_type_80' => $this->default_document_type_80,
            'search_item_by_barcode' => $this->search_item_by_barcode,
            'igv_retention_percentage' => $this->igv_retention_percentage,
            'currency_type_id' => $this->getCurrencyTypeId(),
            'currency_types' => $currency,
            'affectation_igv_types_exonerated_unaffected' => Item::AffectationIgvTypesExoneratedUnaffected(),
            'typeUser'=>$typeUser,
            'unit_type_id'=>$unit_type_id,
            'enabled_global_igv_to_purchase'=>$this->isEnabledGlobalIgvToPurchase(),
            'show_pdf_name'=>$this->isShowPdfName(),
            'item_name_pdf_description'=>$this->isItemNamePdfDescription(),
            'api_service_token'=>self::getApiServiceToken(),
            'user'=>[
                'serie'=>$serie,
                'document_id'=>$document_id,
                'type'=>$typeUser,
            ],

        ];
    }

    /**
     * Devuelve verdadero o falso si esta habilitado el envio de datos a otro servidor
     *
     * @return bool
     */
    public function isSendDataToOtherServer(): bool
    {
        return (bool)$this->send_data_to_other_server;
    }

    /**
     * Establece el valor para el envio de datos a otro servidor
     *
     * @param bool $send_data_to_other_server
     * @return Configuration
     */
    public function setSendDataToOtherServer(bool $send_data_to_other_server = false): Configuration
    {
        $this->send_data_to_other_server = (bool)$send_data_to_other_server;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrencyTypeId(): ?string
    {
        return empty($this->currency_type_id)?'PEN':$this->currency_type_id;
    }

    /**
     * @param string|null $currency_type_id
     */
    public function setCurrencyTypeId(?string $currency_type_id ='PEN'): Configuration
    {
        $this->currency_type_id = empty($currency_type_id)?'PEN':$currency_type_id;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowExtraInfoToItem(): bool
    {
        return (bool)$this->show_extra_info_to_item;
    }

    /**
     * @param bool $show_extra_info_to_item
     *
     * @return Configuration
     */
    public function setShowExtraInfoToItem(bool $show_extra_info_to_item = false): Configuration
    {
        $this->show_extra_info_to_item = (bool) $show_extra_info_to_item;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSearchItemBySeries(): bool
    {
        return (bool)$this->search_item_by_series;
    }

    /**
     * @param bool $search_item_by_series
     *
     * @return Configuration
     */
    public function setSearchItemBySeries(bool $search_item_by_series): Configuration
    {
        $this->search_item_by_series = (bool) $search_item_by_series;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabledGlobalIgvToPurchase(): bool
    {
        return (bool) $this->enabled_global_igv_to_purchase;
    }

    /**
     * @param bool $enabled_global_igv_to_purchase
     *
     * @return Configuration
     */
    public function setEnabledGlobalIgvToPurchase(bool $enabled_global_igv_to_purchase = false): Configuration
    {
        $this->enabled_global_igv_to_purchase = (bool) $enabled_global_igv_to_purchase;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowPdfName(): bool
    {
        return (bool)$this->show_pdf_name;
    }

    /**
     * @param bool $show_pdf_name
     *
     * @return Configuration
     */
    public function setShowPdfName(bool $show_pdf_name): Configuration
    {
        $this->show_pdf_name = (bool)$show_pdf_name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getApkUrl(): ?string
    {
        return $this->apk_url;
    }

    /**
     * @param string|null $apk_url
     *
     * @return Configuration
     */
    public function setApkUrl(?string $apk_url): Configuration
    {
        $this->apk_url = $apk_url;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDispatchesAddressText(): bool
    {
        return (bool) $this->dispatches_address_text;
    }

    /**
     * @param bool $dispatches_address_text
     *
     * @return Configuration
     */
    public function setDispatchesAddressText(bool $dispatches_address_text): Configuration
    {
        $this->dispatches_address_text = (bool) $dispatches_address_text;
        return $this;
    }


    /**
     * @return bool
     */
    public function isShowItemsOnlyUserStablishment(): bool
    {
        return false;
        return (bool)$this->show_items_only_user_stablishment;
    }

    /**
     * @param bool $show_items_only_user_stablishment
     *
     * @return Company
     */
    public function setShowItemsOnlyUserStablishment(bool $show_items_only_user_stablishment): Configuration
    {
        $this->show_items_only_user_stablishment = (bool) $show_items_only_user_stablishment;
        $this->show_items_only_user_stablishment = false;
        return $this;
    }


    /**
     * Devuelve el template actual para impresion, si no se encuentra el usuario o establecimiento devolvera el formato que esta en configuracion
     * @return string
     */
    public function getFormatsToTemplates(){
        $user =  \Auth::user();
        if(!empty($user)) {
            $establishment =$user->establishment;
            if (!empty($establishment)) {
                return $establishment->template_pdf;
            }
        }
        return  $this->formats;
    }

    /**
     * Devuele el token de apiperu desde configuracion del sistema
     * @return \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed
     */
    public static function getApiServiceToken(){
        $api_service_token = \App\Models\System\Configuration::getApiServiceToken();
        // $api_service_token = $configuration->token_apiruc =! '' ? $configuration->token_apiruc : config('configuration.api_service_token');
        // $api_service_token = $configuration->token_apiruc === 'false' ? config('configuration.api_service_token') : $configuration->token_apiruc;

        return $api_service_token;
    }

    /**
     * @return bool
     */
    public function isItemNamePdfDescription(): bool
    {
        return (bool) $this->item_name_pdf_description;
    }

    /**
     * @param bool|null $item_name_pdf_description
     *
     * @return Configuration
     */
    public function setItemNamePdfDescription(bool $item_name_pdf_description):Configuration
    {
        $this->item_name_pdf_description = (bool)$item_name_pdf_description;
        return $this;
    }

}
