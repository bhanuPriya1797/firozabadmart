<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model{

    protected $table = 'newsletter_subscribers';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
    	'email',
    	'status',
    ];

}