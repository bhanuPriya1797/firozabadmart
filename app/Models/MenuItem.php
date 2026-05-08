<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model{
    
    protected $table = 'menu_items';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'slug',
        'link_type',
        'page_id',
        'url',
        'target',
        'status',
        'sort_order'
    ];

    public function children(){
    	return $this->hasMany('App\Models\MenuItem', 'parent_id')->orderBy('sort_order');
    }

}