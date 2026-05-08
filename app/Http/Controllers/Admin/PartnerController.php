<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partner;
use App\Helpers\CustomHelper;
use Validator;
use DataTables;
use Storage;

class PartnerController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct() {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request) {
        if ($request->ajax()) {
            $data = Partner::select('*')->orderBy('sort_order', 'asc');
            return Datatables::of($data)
                ->addColumn('logo', function($row){
                    if($row->logo_path) {
                        return '<img src="'.asset('storage/'.$row->logo_path).'" style="height: 50px;" />';
                    }
                    return '';
                })
                ->addColumn('status', function($row){
                    if($row->status == 1){
                        return '<span class="badge bg-success">Active</span>';
                    }else{
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })
                ->addColumn('action', function($row){
                    $encryptedId = CustomHelper::encrypt($row->id);
                    $editUrl = route($this->ADMIN_ROUTE_NAME . '.partners.edit', $encryptedId);
                    $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.partners.delete', $encryptedId);

                    $btn = '<div class="d-flex gap-2">';
                    if(auth()->user()->can('partners.edit')){
                        $btn .= '<a href="'.$editUrl.'" class="btn btn-primary btn-sm"><i class="ti tabler-edit"></i></a>';
                    }
                    if(auth()->user()->can('partners.delete')){
                        $btn .= '<button onclick="deletePartner(\''.$deleteUrl.'\')" class="btn btn-danger btn-sm"><i class="ti tabler-trash"></i></button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['logo', 'status', 'action'])
                ->make(true);
        }
        return view('admin.partners.index');
    }

    public function add() {
        return view('admin.partners.form');
    }

    public function edit($id) {
        $id = CustomHelper::decrypt($id);
        $partner = Partner::findOrFail($id);
        return view('admin.partners.form', compact('partner'));
    }

    public function save(Request $request, $id = null) {
        if($id){
            $id = CustomHelper::decrypt($id);
        }
        
        $rules = [
            'title' => 'required|max:255',
            'sort_order' => 'integer|min:0',
            'status' => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['_token']);

        if ($id) {
            $partner = Partner::findOrFail($id);
            $partner->update($data);
            $msg = 'Partner updated successfully.';
        } else {
            Partner::create($data);
            $msg = 'Partner created successfully.';
        }

        return redirect()->route($this->ADMIN_ROUTE_NAME . '.partners.index')->with('success', $msg);
    }

    public function delete($id) {
        try {
            $id = CustomHelper::decrypt($id);
            $partner = Partner::findOrFail($id);
            $partner->delete();
            return response()->json(['success' => true, 'message' => 'Partner deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting partner.']);
        }
    }
}
