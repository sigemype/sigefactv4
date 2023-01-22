<?php

    namespace App\Models\Tenant;

    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Modules\Inventory\Models\Warehouse;


    /**
     * Class ModelTenant
     *
     * @package App\Models\Tenant
     * @mixin Model
     * @mixin \Eloquent
     * @mixin \Illuminate\Database\Query\Builder as Builder
     * @mixin \Illuminate\Database\Eloquent\Collection
     * @method static Builder|ModelTenant newModelQuery()
     * @method static Builder|ModelTenant newQuery()
     * @method static Builder|ModelTenant query()
     */
    class ModelTenant extends Model
    {
        use UsesTenantConnection;

        public const RESERVED_SYMBOLS_FILTER = ['-', '+', '<', '>', '@', '(', ')', '~'];

        public const VOIDED_REJECTED_IDS = ['09', '11'];

        public const STATE_TYPES_ACCEPTED = ['01', '03', '05', '07', '13'];

        public const STATE_TYPE_REGISTERED = '01';

        public const SOAP_TYPE_PRODUCTION = '02';

        public const NATIONAL_CURRENCY_ID = 'PEN';

        public const INVOICE_DOCUMENTS_IDS = ['01', '03'];

        /**
         * Devuelve un esqueleto del array de data extra. Previene error de no enconrarse la funcion en otros modelos
         *
         * @return array
         */
        public function getPrintExtraData()
        {

            return [
                'colors' => null,
                'CatItemUnitsPerPackage' => null,
                'CatItemMoldProperty' => null,
                'CatItemProductFamily' => null,
                'CatItemMoldCavity' => null,
                'CatItemPackageMeasurement' => null,
                'CatItemStatus' => null,
                'CatItemUnitBusiness' => null,
                'CatItemSize' => null,
                'image' => null,
                'image_medium' => null,
                'image_small' => null,
            ];
        }

        /**
         * Evalua si el attr se encuentra en el modelo.
         *
         * @param $attr
         *
         * @return bool
         *@example
         *<code>
         *         if($row->hasAttribute('item')) {
         *         // do something
         * }
         * </code>
         */
        public function hasAttribute($attr)
        {
            return array_key_exists($attr, $this->attributes);
        }

                
        /**
         * @param  float $value
         * @param  float $exchange_rate_sale
         * @return float
         */
        public function generalConvertValueToPen($value, $exchange_rate_sale)
        {
            return $value * $exchange_rate_sale;
        }
        

        /**
         * 
         * Aplicar formato a fechas
         *
         * @param  $date
         * @param  string $format
         * @return string
         */
        public function generalFormatDate($date, $format = 'Y-m-d')
        {
            return $date->format($format);
        }
        
        
        /**
         * 
         * Reemplazar simbolos reservados por espacio, para busqueda avanzada
         *
         * @param  string $value
         * @return string
         */
        public function replaceReservedSymbols($value)
        {
            return str_replace(self::RESERVED_SYMBOLS_FILTER, ' ', $value);
        }

        
        /**
         * 
         * Obtener arreglo con los valores a buscar - busqueda avanzada
         *
         * @param  string $value
         * @return array
         */
        public function getSearchValues($value)
        {
            $search_term = $this->replaceReservedSymbols($value);

            return preg_split('/\s+/', $search_term, -1, PREG_SPLIT_NO_EMPTY);
        }

    
        /**
         * 
         * Aplicar formato
         *
         * @param  $value
         * @param  int $decimals
         * @return string
         */
        public function generalApplyNumberFormat($value, $decimals = 2)
        {
            return number_format($value, $decimals, ".", "");
        }
        
        
        /**
         * 
         * Obtener el id del almacen relacionado al establecimiento asignado al usuario
         *
         * @return int
         */
        public function getCurrentWarehouseId()
        {
            return Warehouse::select('id')->where('establishment_id', auth()->user()->establishment_id)->first()->id;
        }
        

        /**
         * 
         * Obtener relaciones necesarias o aplicar filtros a documentos, para reporte pagos - finanzas 
         * 
         * Se define scope global para no afectar a modelos que no aplican filtros al reporte
         * si se requiere usar filtros, sobreescribir el metodo en el modelo afectado
         * 
         *
         * @param  Builder $query
         * @return Builder
         */
        public function scopeFilterRelationsGlobalPayment($query)
        {
            return $query;
        }
        

        /**
         * 
         * Filtro para no incluir relaciones en consultas de tablas asociadas a pagos
         *
         * @param \Illuminate\Database\Eloquent\Builder $query
         * @return \Illuminate\Database\Eloquent\Builder
         */  
        public function scopeGeneralPaymentsWithOutRelations($query)
        {
            return $query->withOut(['payment_method_type', 'card_brand']);
        }
        
        
        /**
         * 
         * Url imagen
         *
         * @param  string $folder
         * @param  string $filename
         * @return string
         */
        public function getPathPublicUploads($folder, $filename)
        {
            return asset('storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename);
        }

        
        /**
         * 
         * Path imagen
         *
         * @param  string $folder
         * @param  string $filename
         * @return string
         */
        public function getGeneralFilePublicPath($folder, $filename)
        {
            return public_path('storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename);
        }

    }
