<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'cpf', 'position', 'birth_date',
        'zip_code', 'address', 'number', 'complement', 'neighborhood', 'city',
        'state', 'role', 'admin_id'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'password' => 'hashed',
    ];

    public function timeClocks()
    {
        return $this->hasMany(TimeClock::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'admin_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function getFormattedCpfAttribute()
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    public function getFormattedZipCodeAttribute()
    {
        $zipCode = preg_replace('/\D/', '', $this->zip_code);
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $zipCode);
    }
}