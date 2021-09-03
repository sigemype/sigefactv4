<?php

namespace App\Models\Tenant;

use App\Notifications\Tenant\PasswordResetNotification;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\LevelAccess\Models\ModuleLevel;
use Modules\Sale\Models\UserCommission;


/**
 * Class User
 *
 * @package App\Models\Tenant
 * @mixin Model
 * @mixin Authenticatable
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $api_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $phone
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\Document[] $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\Tenant\Establishment $establishment
 * @property-read \Illuminate\Database\Eloquent\Collection|ModuleLevel[] $levels
 * @property-read int|null $levels_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\Module[] $modules
 * @property-read int|null $modules_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\SaleNote[] $sale_notes
 * @property-read int|null $sale_notes_count
 * @property-read UserCommission|null $user_commission
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTypeUser()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
class User extends Authenticatable
{
    use Notifiable, UsesTenantConnection;

    protected $with = ['establishment'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'establishment_id', 'type', 'locked', 'identity_document_type_id', 'number',
        'address', 'telephone',
         'document_id',
         'series_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'series_id'=> 'int'
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class);
    }

    public function levels()
    {
        return $this->belongsToMany(ModuleLevel::class);
    }

    public function authorizeModules($modules)
    {
        if ($this->hasAnyModule($modules)) {
            return true;
        }
        abort(401, 'Esta acción no está autorizada.');
    }

    public function hasAnyModule($modules)
    {
        if (is_array($modules)) {
            foreach ($modules as $module)
            {
                if ($this->hasModule($module)) {
                    return true;
                }
            }
        } else {
            if ($this->hasModule($modules)) {
                return true;
            }
        }
        return false;
    }

    public function hasModule($module)
    {
        if ($this->modules()->where('name', $module)->first()) {
            return true;
        }
        return false;
    }



    public function getModule()
    {
        $module = $this->modules()->orderBy('id')->first();
        if ($module) {
            return $module->value;
        }
        return null;
    }

    public function getModules()
    {
        $modules = $this->modules()->get();
        if ($modules) {
            return $modules;
        }
        return null;
    }


    public function searchModule($module)
    {
        if ($this->modules()->where('value', $module)->first()) {
            return true;
        }
        return false;
    }


    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }


    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function sale_notes()
    {
        return $this->hasMany(SaleNote::class);
    }

    public function scopeWhereTypeUser($query)
    {
        $user = auth()->user();
        return ($user->type == 'seller') ? $query->where('id', $user->id) : null;
    }



    public function getLevel()
    {
        $level = $this->levels()->orderBy('id')->first();
        if ($level) {
            return $level->value;
        }
        return null;
    }

    public function getLevels()
    {
        $levels = $this->levels()->get();
        if ($levels) {
            return $levels;
        }
        return null;
    }


    public function searchLevel($Level)
    {
        if ($this->levels()->where('value', $Level)->first()) {
            return true;
        }
        return false;
    }

    public function user_commission()
    {
        return $this->hasOne(UserCommission::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * @return mixed
     */
    public function getDocumentId() {
        return $this->document_id;
    }

    /**
     * @param mixed $document_id
     *
     * @return User
     */
    public function setDocumentId($document_id) {
        $this->document_id = $document_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeriesId() {
        return $this->series_id;
    }

    /**
     * @param mixed $series_id
     *
     * @return User
     */
    public function setSeriesId($series_id) {
        $this->series_id = $series_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return User
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Establece los niveles y modulos del usuario
     * @param array $modules
     * @param array $modules_levels
     *
     * @return $this
     */
    public function setModuleAndLevelModule($modules= [],$modules_levels = []){
        $user_array = [
            'user_id' => $this->id,
        ];
        /*** Estableciendo los modulos */
        /** @var array $module_array */
        $module_array = $modules;

        $work = DB::connection('tenant')
                  ->table('module_user')
                  ->where($user_array);

        $deletes = $work
            ->whereNotIn('module_id', $module_array)
            ->delete();
        $total_modules = count($module_array);
        for ($i = 0; $i < $total_modules; $i++) {
            $item = (int)$module_array[$i];
            $module_ = $work
                ->where([
                            'module_id' => $item,
                        ])->first();
            if (empty($module_)) {
                $user_array['module_id'] = $item;
                $work->insert($user_array);
            }
        }
        unset($user_array['module_id']);

        $levels_array =$modules_levels;

        $work =DB::connection('tenant')
                 ->table('module_level_user')
                 ->where($user_array)
        ;
        $deletes = $work->whereNotIn('module_level_id', $levels_array)
                        ->delete();

        $total_modules_levels = count($levels_array);

        for ($i = 0; $i < $total_modules_levels; $i++) {
            $item = (int)$levels_array[$i];


            $module_ = $work
                ->where([
                            'module_level_id' => $item,
                        ])->first();
            if (empty($module_)) {
                $user_array['module_level_id'] = $item;
                $work->insert($user_array);
            }
        }
        return $this;
    }

    /**
     * Obtiene los niveles de modulo definidos por tenant
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrentModuleLevelByTenant(){
        return  DB::connection('tenant')
                      ->table('module_level_user')
                      ->select('module_level_id')
                      ->where('user_id', $this->id)
                      ->get();

    }
    /**
     * Obtiene los modulo definidos por tenant
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrentModuleByTenant(){
        return  DB::connection('tenant')
                  ->table('module_user')
                  ->select('module_id')
                  ->where('user_id', $this->id)
                  ->get();

    }

    /**
     * Devuelve una lista de usuarios vendedores junto con el usuario actual.
     * Si $withEstablishment es verdadero, devuelve usuarios con establecimientos asignados carlomagno83/facturadorpro4#627
     * Si $withEstablishment es falso, devuelve usuarios sin establecimientos asignados carlomagno83/facturadorpro4#233
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param bool                               $withEstablishment
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetSellers(  $query,$withEstablishment = true){
        if($withEstablishment == false) {
            $query->without(['establishment']);
        }else{
            $query->with(['establishment']);

        }
        $query->whereIn('type', ['seller']);
        $query->orWhere('id', auth()->user()->id);
        return  $query;
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeGetWorkers($query){
        $query->whereIn('type', ['seller','admin']);
        return  $query;
    }

    /**
     * Devuelve verdadero si el usuario es Admin.
     * @return bool
     */
    public function isAdmin()
    {
        return $this->type === 'admin';
    }

    /**
     * Genera un token al azar de $length caracteres
     * @param int|null $length
     * @return $this
     */
    public function updateToken($length = 60)
    {
        $this->api_token = Str::random($length);
        return $this;
    }

    /**
     * @return array
     */
    public function getCollectionData(){
        $type = '';
        switch ($this->type) {
            case 'admin':
                $type =  'Administrador' ;
                break;
            case 'seller':
                $type =  'Vendedor' ;
                break;
            case 'client':
                $type =  'Cliente' ;
                break;
            default:
                # code...
                break;
        }

        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'api_token' => $this->api_token,
            'document_id' => $this->document_id,
            'serie_id' => ($this->series_id == 0)?null:$this->series_id,
            'establishment_description' => optional($this->establishment)->description,
            'type' => $type,
            'locked' => (bool) $this->locked,
        ];
    }
}
