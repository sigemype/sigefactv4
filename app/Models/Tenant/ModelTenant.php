<?php

    namespace App\Models\Tenant;

    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;

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
    }
