<?php

namespace App;

use Illuminate\Notifications\Notifiable;
//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
	use Notifiable, HasApiTokens;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	protected $hidden = ['password_confirmation'];

	public function client()
	{
		return $this->hasOne(Client::class, 'userId');
	}

	public function checkClientType()
	{
		$type = $this->client()->first();
		return strtolower($type->client_type);
	}
}
