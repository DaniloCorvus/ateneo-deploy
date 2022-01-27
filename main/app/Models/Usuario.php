<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{

    // 'person_id' => $person->id,
    // 'usuario' => $person->identifier,
    // 'password' => Hash::make($person->identifier),
    // 'change_password' => 1,

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'usuario';
    protected $fillable = [
        'nombres', 'apellidos', 'identificacion', 'usuario', 'password', 'functionary_id', 'person_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    protected $casts = [
        'menu' => 'array'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
