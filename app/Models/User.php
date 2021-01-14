<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cartalyst\Stripe\Exception\NotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_customer',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            // When user is being CREATED and sripe_customer is empty create the customer in stripe and add to user
            if (!$model->stripe_customer) {
                $customer = Stripe::customers()->create([
                    'email' => $model->email,
                    'name' => $model->name,
                ]);
                $stripe_customer = $customer['id'];
                $model->stripe_customer = $stripe_customer;
            }
        });

        self::updating(function ($model) {
            // When user is being UPDATED and sripe_customer is empty create the customer in stripe and add to user
            if (!$model->stripe_customer) {
                $customer = Stripe::customers()->create([
                    'email' => $model->email,
                    'name' => $model->name,
                ]);
                $stripe_customer = $customer['id'];
                $model->stripe_customer = $stripe_customer;
            } else {
                // Lets update the customer in stripe with the user info found in the database
                try {
                    Stripe::customers()->update($model->stripe_customer, [
                        'email' => $model->email,
                        'name' => $model->name,
                    ]);
                } catch (NotFoundException $e) {
                    // If the user was not found for some reason, create it
                    $customer = Stripe::customers()->create([
                        'email' => $model->email,
                        'name' => $model->name,
                    ]);
                    $stripe_customer = $customer['id'];
                    $model->stripe_customer = $stripe_customer;
                }
            }
        });
    }
}
