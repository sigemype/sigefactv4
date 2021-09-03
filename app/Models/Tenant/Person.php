<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\Country;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\IdentityDocumentType;
use App\Models\Tenant\Catalogs\Province;
use Illuminate\Database\Eloquent\Builder;


/**
 * Class Person
 *
 * @package App\Models\Tenant
 * @mixin ModelTenant
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\PersonAddress[] $addresses
 * @property-read int|null $addresses_count
 * @property-read Country $country
 * @property-read Department $department
 * @property-read District $district
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\Document[] $documents
 * @property-read int|null $documents_count
 * @property-read mixed $address_full
 * @property mixed $contact
 * @property-read IdentityDocumentType $identity_document_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\PersonAddress[] $more_address
 * @property-read int|null $more_address_count
 * @property-read \App\Models\Tenant\PersonType $person_type
 * @property-read Province $province
 * @method static Builder|Person newModelQuery()
 * @method static Builder|Person newQuery()
 * @method static Builder|Person query()
 * @method static Builder|Person whereIsEnabled()
 * @method static Builder|Person whereType($type)
 */
class Person extends ModelTenant
{
    protected $table = 'persons';
    protected $with = ['identity_document_type', 'country', 'department', 'province', 'district'];
    protected $fillable = [
        'type',
        'identity_document_type_id',
        'number',
        'name',
        'trade_name',
        'country_id',
        'department_id',
        'province_id',
        'district_id',
        'address',
        'email',
        'telephone',
        'perception_agent',
        'state',
        'condition',
        'percentage_perception',
        'person_type_id',
        'comment',
        'enabled',
        'contact',
        'internal_code',
        'observation',
        'zone',
        'website',
        'credit_days',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope('active', function (Builder $builder) {
    //         $builder->where('status', 1);
    //     });
    // }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(PersonAddress::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function identity_document_type()
    {
        return $this->belongsTo(IdentityDocumentType::class, 'identity_document_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * @param $query
     * @param $type
     *
     * @return mixed
     */
    public function scopeWhereType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getAddressFullAttribute()
    {
        $address = trim($this->address);
        $address = ($address === '-' || $address === '')?'':$address.' ,';
        if ($address === '') {
            return '';
        }
        return "{$address} {$this->department->description} - {$this->province->description} - {$this->district->description}";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function more_address()
    {
        return $this->hasMany(PersonAddress::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person_type()
    {
        return $this->belongsTo(PersonType::class);
    }

    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeWhereIsEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function getContactAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setContactAttribute($value)
    {
        $this->attributes['contact'] = (is_null($value))?null:json_encode($value);
    }

    /**
     * Retorna un standar de nomenclatura para el modelo
     *
     * @return array
     */
    public function getCollectionData($withFullAddress = false){

        $addresses = $this->addresses;
        if($withFullAddress == true){
            $addresses = collect($addresses)->transform(function ($row) {
                return $row->getCollectionData();
            });
        }
        $person_type_descripton = '';
        if($this->person_type !== null){
            $person_type_descripton = $this->person_type->description;
        }
        $data = [
            'id' => $this->id,
            'description' => $this->number.' - '.$this->name,
            'name' => $this->name,
            'number' => $this->number,
            'identity_document_type_id' => $this->identity_document_type_id,
            'identity_document_type_code' => $this->identity_document_type->code,
            'address' =>  $this->address,
            'internal_code' => $this->internal_code,
            'observation' => $this->observation,
            'zone' => $this->zone,
            'website' => $this->website,
            'document_type' => $this->identity_document_type->description,
            'enabled' => (bool) $this->enabled,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'type' => $this->type,
            'trade_name' => $this->trade_name,
            'country_id' => $this->country_id,
            'department_id' => $this->department_id,
            'province_id' => $this->province_id,
            'district_id' => $this->district_id,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'perception_agent' => (bool) $this->perception_agent,
            'percentage_perception' => $this->percentage_perception,
            'state' => $this->state,
            'condition' => $this->condition,
            'person_type_id' => $this->person_type_id,
            'person_type' => $person_type_descripton,
            'contact' => $this->contact,
            'comment' => $this->comment,
            'addresses' => $addresses,
            'credit_days' => (int)$this->credit_days,
        ];
        return $data;
    }

    /**
     * @return string
     */
    public function getObservation()
    : string {
        return $this->observation;
    }

    /**
     * @param string $observation
     *
     * @return Person
     */
    public function setObservation(string $observation)
    : Person {
        $this->observation = $observation;
        return $this;
    }

    /**
     * @return string
     */
    public function getZone()
    : string {
        return $this->zone;
    }

    /**
     * @param string $zone
     *
     * @return Person
     */
    public function setZone(string $zone)
    : Person {
        $this->zone = $zone;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    : string {
        return $this->website;
    }

    /**
     * @param string $website
     *
     * @return Person
     */
    public function setWebsite(string $website)
    : Person {
        $this->website = $website;
        return $this;
    }


}
