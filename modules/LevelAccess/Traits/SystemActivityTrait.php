<?php

namespace Modules\LevelAccess\Traits;

use Jenssegers\Agent\Agent;
use Modules\LevelAccess\Models\{
    SystemActivityLog
};
use App\Models\Tenant\{
    Company,
    User,
};
use Modules\LevelAccess\Helpers\DataClientHelper;
use Illuminate\Support\Facades\Log;
use Exception;


trait SystemActivityTrait
{ 
    
    /**
     * 
     * Registrar datos en log de actividades para el usuario, inicio y cierre de sesión
     * Para cada Tenant
     *
     * @param  $event
     * @param  string $system_activity_log_type_id
     * @return void
     */
    public function saveSystemActivityUser($event, $system_activity_log_type_id)
    {
        try 
        {
            if($this->isGuardWeb($event))
            {
                $client_data = $this->getClientData();
                $user = $event->user;
                $origin_id = null;
                $origin_type = null;

                if($user)
                {
                    $origin_id = $user->id;
                    $origin_type = get_class($user);
                }

                $base_data = [
                    'user_id' => $user->id ?? null,
                    'system_activity_log_type_id' => $system_activity_log_type_id,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'origin_id' => $origin_id,
                    'origin_type' => $origin_type,
                ];

                $this->onlyCreateSystemActivityLog($this->getParamsSystemActivity($client_data, $base_data));
            }
        } 
        catch (Exception $e) 
        {
            $this->showErrorLog($e, User::class, $system_activity_log_type_id);
        }
    }

        
    /**
     * 
     * Registrar datos en log de actividades para el usuario, bloqueo por exceder limite de intentos
     * Para cada Tenant
     *
     * @return void
     */
    public function saveSystemActivityUserLockout($request)
    {
        $system_activity_log_type_id = 'login_lockout';
        $email = $request['email'] ?? null;

        try 
        {
            $connection_name = (new User)->getDbConnectionName();

            if($this->isTenantConnection($connection_name))
            {
                $client_data = $this->getClientData();
                $base_data = [
                    'user_id' => null,
                    'system_activity_log_type_id' => $system_activity_log_type_id,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'origin_id' => null,
                    'origin_type' => null,
                    'request_email' => $email,
                ];

                $this->onlyCreateSystemActivityLog($this->getParamsSystemActivity($client_data, $base_data));
            }

        } 
        catch (Exception $e) 
        {
            $this->setErrorLog($e, " Se ha excedido el límite de intentos permitidos al iniciar sesión - admin/system - {$system_activity_log_type_id} ");
        }
    }

    
    /**
     * 
     * Registrar datos en log de actividades - transacciones en general
     *
     * @param  string $model
     * @param  string $system_activity_log_type_id
     * @return void
     */
    public function saveGeneralSystemActivity($model, $system_activity_log_type_id, $route = null)
    {
        try 
        {
            $client_data = $this->getClientData();
    
            $base_data = [
                'user_id' => auth()->id(),
                'system_activity_log_type_id' => $system_activity_log_type_id,
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'route' => $route,
            ];
    
            $model->system_activity_logs()->create($this->getParamsSystemActivity($client_data, $base_data));
        } 
        catch (Exception $e) 
        {
            $this->showErrorLog($e, get_class($model), $system_activity_log_type_id);
        }
    }

    
    /**
     *
     * @param  Exception $e
     * @param  string $model
     * @param  string $system_activity_log_type_id
     * @return void
     */
    public function showErrorLog($e, $model, $system_activity_log_type_id)
    {
        $this->setErrorLog($e, 'Ocurrió un error al registrar las actividades del sistema - SystemActivityLog, modelo asociado: '.$model. ' - tipo transacción: '.$system_activity_log_type_id.' - Detalle del error: ');
    }
    

    /**
     * 
     * Verificar cambios en las columnas del modelo y registra en tabla de actividades del sistema si hubo cambio
     * 
     * El modelo asociado debe tener los metodos definidos:
     * getCheckColumnsForSystemActivity - Columnas que serviran para verificar si hubo cambio en ellas
     * getTransactionTypeForSystemActivity - Obtener descripcion del tipo de transacción
     *
     * @param  $model
     * @return void
     */
    public function checkModelChanges($model)
    {
        foreach ($model->getCheckColumnsForSystemActivity() as $column)
        {
            if($model->wasChanged($column))
            {   
                $this->saveGeneralSystemActivity($model, $model->getTransactionTypeForSystemActivity($column));
            }
        }
    }

    
    /**
     * 
     * Registro en bd
     *
     * @param  array $data
     * @return void
     */
    public function onlyCreateSystemActivityLog($data)
    {
        SystemActivityLog::create($data);
    }

        
    /**
     * 
     * Data para registro de actividades
     *
     * @param  array $new_data
     * @param  array $base_data
     * @return array
     */
    public function getParamsSystemActivity($new_data, $base_data)
    {
        return array_merge($new_data, $base_data);
    }

    /**
     * 
     * Obtener información del cliente
     *
     * @return array
     */
    public function getClientData()
    {
        $agent = new Agent();
        $browser = $agent->browser();
        $platform = $agent->platform();
        $data_client_helper = new DataClientHelper();

        return [
            'platform_name' => $platform,
            'platform_version' => $agent->version($platform),
            'browser_name' => $browser,
            'browser_version' => $agent->version($browser),
            'device_name' => $agent->device(),
            'device_type' => $agent->deviceType(),
            'ip' => $data_client_helper->getClientIp(),
            'location' => $data_client_helper->getLocation(),
        ];
    }

    
    /**
     *
     * @return bool
     */
    public function isGuardWeb($event)
    {
        return $event->guard === 'web';
    }
    
    
    /**
     *
     * @param  string $connection
     * @return bool
     */
    public function isTenantConnection($connection)
    {
        return $connection === 'tenant';
    }

    
    /**
     *
     * @param  Exception $exception
     * @return void
     */
    public function setErrorLog($exception, $base_message = null)
    {
        Log::error(($base_message ?? '')."Line: {$exception->getLine()} - Message: {$exception->getMessage()} - File: {$exception->getFile()}");
    }

}
