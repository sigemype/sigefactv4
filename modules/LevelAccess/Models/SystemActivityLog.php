<?php

namespace Modules\LevelAccess\Models;

use App\Models\Tenant\{
    ModelTenant,
    User,
    Company,
};


class SystemActivityLog extends ModelTenant 
{

    protected $fillable = [
        'user_id',
        'system_activity_log_type_id',
        'date',
        'time',
        'origin_id',
        'origin_type',
        
        'ip',
        'device_name',
        'device_type',
        'platform_name',
        'platform_version',
        'browser_name',
        'browser_version',
        'location',
        'route',
        'request_email',
        
    ];

    
    public function getLocationAttribute($value)
    {
        return (is_null($value)) ? null : (object)json_decode($value);
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = (is_null($value)) ? null : json_encode($value);
    }

    public function user() 
    {
        return $this->belongsTo(User::class);
    }
    
    public function system_activity_log_type() 
    {
        return $this->belongsTo(SystemActivityLogType::class);
    }

    public function origin()
    {
        return $this->morphTo();
    }
    
    
    /**
     * 
     * Filtro para el listado
     *
     * @param Builder $query
     * @return Builder
     */  
    public function scopeFilterRecords($query, $request)
    {
        $query->with([
                    'user' => function($q){
                        $q->filterOnlyUsername();
                    },
                    'system_activity_log_type'
                ]);

        if($request->value)
        {
            $query->where($request->column, 'like', "%{$request->value}%");
        }

        if($request->has('system_activity_log_type_id'))
        {
            $query->where('system_activity_log_type_id', $request->system_activity_log_type_id);
        }

        return $query;
    }


    /**
     * 
     * Transacciones asociadas al registro de actividad del sistema
     *
     * @return array
     */
    // public static function getTransactionTypes()
    // {
    //     $login_transactions = [
    //         'failed' => 'Error de inicio de sesión',
    //         'logout' => 'Cerrar sesión',
    //         'login' => 'Iniciar sesión',
    //     ];

    //     $company_transactions = (new Company)->getTransactionTypesForSystemActivity();

    //     return array_merge($login_transactions, $company_transactions);
    // }
    
        
    /**
     * 
     * Dispositivos
     *
     * @return array
     */
    public function getDeviceTypesAttribute()
    {
        return [
            'desktop' => 'Computadora de escritorio',
            'phone' => 'Teléfono',
            'tablet' => 'Tablet',
            'robot' => 'Robot',
        ];
    }

    
    /**
     *
     * @return string
     */
    public function getDeviceTypeDescriptionAttribute()
    {
        return $this->device_types[$this->device_type];
    }
    
    
    /**
     * 
     * Retorna datos para mostrar en vista
     *
     * @return array
     */
    public function getDataLocation()
    {
        if(!$this->location) return null;

        return [
            'country_code' => $this->location->country_code,
            'country_name' => $this->location->country_name,
            'region_code' => $this->location->region_code,
            'region_name' => $this->location->region_name,
            'city_name' => $this->location->city_name,
            'timezone' => $this->location->timezone,
            'full_description' => $this->getLocationDescription(),
        ];
    }
    

    /**
     *
     * @return string
     */
    public function getLocationDescription()
    {
        return "País: {$this->location->country_code} - {$this->location->country_name}, Región: {$this->location->region_code} - {$this->location->region_name}, Ciudad: {$this->location->city_name}, Zona horaria: {$this->location->timezone}";
    }

    
    /**
     *
     * @return string
     */
    public function getSystemActivityLogTypeDescription()
    {
        $append_text = $this->route ? " - Ruta de acceso: {$this->route}" : '';
        return $this->system_activity_log_type->description.$append_text;
    }
    

    /**
     * 
     * Datos para mostrar en vista - listado
     *
     * @return array
     */
    public function getRowResourceAccess()
    {
        return [
            'user_id' => $this->user_id,
            'system_activity_log_type_id' => $this->system_activity_log_type_id,
            'system_activity_log_type_description' => $this->getSystemActivityLogTypeDescription(),
            'date_time' => "{$this->date} - {$this->time}",
            'user_name' => optional($this->user)->name,
            'date' => $this->date,
            'time' => $this->time,
            'ip' => $this->ip,
            'device_name' => $this->device_name,
            'device_type' => $this->device_type,
            'device_type_description' => $this->device_type_description,
            'platform_name' => $this->platform_name,
            'platform_version' => $this->platform_version,
            'platform_description' => "{$this->platform_name} - {$this->platform_version}",
            'browser_name' => $this->browser_name,
            'browser_version' => $this->browser_version,
            'browser_description' => "{$this->browser_name} - {$this->browser_version}",
            'location' => $this->getDataLocation(),
            'request_email' => $this->request_email,
        ];
    }

}
