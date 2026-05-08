<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model{
    
    protected $table = 'menus';

    protected $guarded = ['id'];

    protected $fillable = [];


    public function menuItems(){
    	return $this->hasMany('App\Models\MenuItem', 'menu_id')->orderBy('sort_order');
    }


    public function menuParentItems(){
    	return $this->hasMany('App\Models\MenuItem', 'menu_id')->where('parent_id', 0)->orWhereRaw('parent_id IS NULL')->orderBy('sort_order');
    }

}