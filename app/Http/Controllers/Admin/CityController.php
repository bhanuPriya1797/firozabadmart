<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

use Validator;
use Storage;

use App\Helpers\CustomHelper;

use Image;
use DB;

class CityController extends Controller{


    private $limit;
    private $ADMIN_ROUTE_NAME;

    public function __construct(){
        $this->limit = 100;
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;

        $name = (isset($request->name))?$request->name:'';
        $category = (isset($request->category))?$request->category:'';
        $designer = (isset($request->designer))?$request->designer:'';
        $price_scope = (isset($request->price_scope))?$request->price_scope:'=';
        $price = (isset($request->price))?$request->price:'';

        $stock_scope = (isset($request->stock_scope))?$request->stock_scope:'=';
        $stock = (isset($request->stock))?$request->stock:'';

        $status = (isset($request->status))?$request->status:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');

        $d_query = City::orderBy('name', 'asc');

        if(!empty($name)){
            $d_query->where(function($query) use($name)
            {
                $query->where('name', 'like', $name.'%');
                //$query->orWhere('nicename', 'like', '%'.$name.'%');
            });
        }

        if(is_numeric($category) && $category > 0){
            $d_query->where('category_id', $category);
        }

        if(is_numeric($designer) && $designer >= 0){
            $d_query->where('user_id', $designer);
        }

        if(is_numeric($price) && $price > 0){
            $d_query->where('price', $price_scope, $price);
        }

        if(is_numeric($stock) && $stock > 0){
            $d_query->where('stock', $stock_scope, $stock);
        }

        if( strlen($status) > 0 ){
            $d_query->where('status', $status);
        }

        if(!empty($from_date)){
            $d_query->whereRaw('DATE(created_at) >= "'.$from_date.'"');
        }

        if(!empty($to_date)){
            $d_query->whereRaw('DATE(created_at) <= "'.$to_date.'"');
        }
        
        $cities = $d_query->paginate($limit);
        
        $data['cities'] = $cities;
        
        $data['limit'] = $limit;

        $data['country_model'] = new Country;

        return view('admin.cities.index', $data);

    }

    public function save(Request $request, $id= ''){
         $data= [];
         $page_heading= 'Add City';
         $state= array(); 
         $country_id=101;  
         if(!empty($id))
         {
            $page_heading= 'Edit City';

            $city= City::where(['id'=>$id])->first();
            $country_id= $city->cityState->country_id;
            // prd($country_id);
            
            $data['city']=   $city;

         } 

         $method= $request->method(); 
         if($method=='POST')
         { 
               $rules = [];
               $rules['name'] = 'required';
               $rules['country'] = 'required';
               $rules['state'] = 'required';
               $this->validate($request, $rules);
               
               $req_data['name']=$request->name;
               $req_data['country_id']=$request->country;
               //prd($req_data);
               $req_data['state_id']=$request->state;
               $req_data['status']=(!empty($request->status))?$request->status:0;

               if(!empty($id))
               {

                   $req_data['updated_at']= date('Y-m-d H:i:s');
                   $isSaved = City::where('id',$id)->update($req_data);
               }
               else 
               {
                    $req_data['created_at']= date('Y-m-d H:i:s');
                    $req_data['updated_at']= date('Y-m-d H:i:s');
                    $isSaved = City::create($req_data);
                    $country_id = $isSaved->id;
               }
                if ($isSaved) 
                {
                    return redirect(url($this->ADMIN_ROUTE_NAME.'/cities'))->with('alert-success', 'The city has been saved successfully.');
                } 
                else 
                {
                    return back()->with('alert-danger', 'The city cannot be saved, please try again or contact the administrator.');
                }
         
         }

         $data['page_heading']= $page_heading;
         $data['country']= Country::get();
         $data['country_id']= $country_id;
         $state = State::where(['country_id'=>$country_id])->orderBy('name','ASC')->get();
         $data['state']= $state;
         return view('admin.cities.form', $data);
    }

    /* end of controller */
}