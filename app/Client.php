<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
	/**
	 * Fill all columns [email, password]
	 * @var array
	 */
    protected $guarded =[];

	/**
	 * Client haves User {Login,Password} for sign in
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
    }

	/**
	 * In SELLER Type: Will do the CURD of Products
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function product(){
		return $this->hasMany(Product::class, 'id');
	}


	/**
	 * In CUSTOMER Type: Will Make Orders
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function order()
	{
		return $this->hasMany(Order::class, 'id');
	}


}
