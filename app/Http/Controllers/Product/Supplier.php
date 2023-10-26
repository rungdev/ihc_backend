<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Supplier extends Controller
{
    //
    function supplier() {
        $country = $this->get_country();
        return view('Product.supplier', compact(["country"]));
    }

    function supplierView($id = '') {
        $data = "";
        $country = $this->get_country();
        if($id != ''){
            $data = DB::table('mt_supplier')
                    ->where('supplier_id', $id)
                    ->first();
        }
        return view('Product.supplierForm', compact(["country", "id", "data"]));
    }

    function saveSupplier(Request $request) {
        $user = session()->get('user')->user_id;
        $request->active_status = isset($request->active_status) ? $request->active_status : 'N';
        
        $data = [
            "name_th"           => $request->name_th,
            "name_gb"           => $request->name_gb,
            "taxid"             => $request->taxid,
            "description_th"    => $request->description_th,
            "description_gb"    => $request->description_gb,
            "address1_th"       => $request->address1_th,
            "address1_gb"       => $request->address1_gb,
            "address2_th"       => $request->address2_th,
            "address2_gb"       => $request->address2_gb,
            "city_th"           => $request->city_th,
            "city_gb"           => $request->city_gb,
            "state_th"          => $request->state_th,
            "state_gb"          => $request->state_gb,
            "country_id"        => $request->country_id,
            "postcode"          => $request->postcode,
            "contact_name_th"   => $request->contact_name_th,
            "contact_name_gb"   => $request->contact_name_gb,
            "email"             => $request->email,
            "mobile"            => $request->mobile,
            "phone"             => $request->phone,
            "fax"               => $request->fax,
            "website"           => $request->website,
            "facebook"          => $request->facebook,
            "active_status"     => $request->active_status,
            "update_by"         => $user,
            "update_dtm"        => $this->data_now
        ];
        
        if(isset($request->supplier_id)){
            $ins = DB::table('mt_supplier')->where('supplier_id', $request->supplier_id)->update($data);
        }else{
            $data["create_by"]  = $this->data_now;
            $data["create_dtm"] = $user;
            $ins = DB::table('mt_supplier')->insert($data);
        }
        if($ins){
            $res = ['res_code' => '00', 'res_text' => 'บันทึกข้อมูลเรียบร้อย'];
        }else{
            $res = ['res_code' => '01', 'res_text' => 'เกิดข้อผิดพลาดกรุณาลองอีกครั้ง'];
        }
        return $res;
    }

    function supplierList(Request $request) {
        $sort           = ($request->sort == '' ? 'name_th' : $request->sort);
        $orderby        = ($request->order == '' ? 'ASC' : $request->order);
        $offset         = $request->offset;
        $limit          = $request->limit;
        $search         = $request->search;

        $table = DB::table('mt_supplier')
                ->where('status', 'Y');
        if ($search != '') {
            $table->where(function ($query) use ($search) {
                $query->where('name_th', 'LIKE', "%$search%")
                    ->orWhere('name_gb', 'LIKE', "%$search%")
                    ->orWhere('taxid', 'LIKE', "%$search%");
            });
        }

        $table->groupBy();
        $rows_number = $table->count();
        $table->orderBy($sort, $orderby);
        $table->skip($offset)->take($limit);
        $output = [];
        foreach ($table->get() as $key => $value) {
            $st = $value->active_status == 'Y' ? 'checked' : '';
            $value->active_status = '<div class="form-check form-switch text-center">
                                        <input class="form-check-input activeStatus" type="checkbox" ' . $st . ' data-id="' . $value->supplier_id . '">
                                    </div>';
            $value->manager = '<a class="btn btn-ghost-secondary btn-sm shadow-none btn-edit" href="/supplierEdit/'.$value->supplier_id.'" data-id="'.$value->supplier_id.'"><i class="ri-pencil-fill fs-16"></i></a>';
            $value->manager .= '<button class="btn btn-ghost-danger btn-sm shadow-none btn-remove" data-id="' . $value->supplier_id . '"><i class="ri-delete-bin-5-fill fs-16"></i></button>';
            $output[] = $value;
        }

        return ["total" => $rows_number, "totalNotFiltered" => $rows_number, "rows" => $output];
    }

    function supplierStatus(Request $request) {
        $user = session()->get('user')->user_id;
        try {
            // $request->type 0 = view , 1 = status
            DB::beginTransaction();
            if($request->type == '1'){
                DB::table('mt_supplier')->where('supplier_id', $request->brandid)->update(["status" => "N", "update_by" => $user, "update_dtm" => $this->data_now]);
            }else if($request->type == '0'){
                DB::table('mt_supplier')->where('supplier_id', $request->brandid)->update(["active_status" => $request->status, "update_by" => $user, "update_dtm" => $this->data_now]);
            }
            DB::commit();
            $output = ['res_code' => '00', 'res_text' => 'บันทึกข้อมูลสำเร็จ'];
        } catch (\Exception $th) {
            //throw $th;
            DB::rollback();
            $output = ["res_code" => '01', "res_text" => "เกิดข้อผิดพลาดกรุณาลองใหม่"];
        }
        return $output;
    }
}
