<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Brand extends Controller
{
    function brand() {
        return view('Product.brand');
    }

    function brandList(Request $request) {
        $sort           = ($request->sort == '' ? 'brand_name_th' : $request->sort);
        $orderby        = ($request->order == '' ? 'ASC' : $request->order);
        $offset         = $request->offset;
        $limit          = $request->limit;
        $search         = $request->search;

        $table = DB::table('mt_brand')
                ->where('status', 'Y');
        if ($search != '') {
            $table->where(function ($query) use ($search) {
                $query->where('brand_name_th', 'LIKE', "%$search%")
                    ->orWhere('brand_name_gb', 'LIKE', "%$search%");
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
                                        <input class="form-check-input activeStatus" type="checkbox" ' . $st . ' data-id="' . $value->brand_id . '">
                                    </div>';
            $value->manager = '<button class="btn btn-ghost-secondary btn-sm shadow-none btn-edit" data-id="'.$value->brand_id.'"><i class="ri-pencil-fill fs-16"></i></button>';
            $value->manager .= '<button class="btn btn-ghost-danger btn-sm shadow-none btn-remove" data-id="' . $value->brand_id . '"><i class="ri-delete-bin-5-fill fs-16"></i></button>';
            $output[] = $value;
        }

        return ["total" => $rows_number, "totalNotFiltered" => $rows_number, "rows" => $output];
    }

    function brandById(Request $request) {
        $output = ["res_code" => '01', "res_text" => "เกิดข้อผิดพลาดกรุณาลองใหม่"];
        if($request->id){
            $table = DB::table('mt_brand')->where("brand_id", $request->id)->first();
            $output = ["res_code" => '00', "res_result" => $table];
        }
        return $output;
    }
    function saveBrand(Request $request) {
        $user = session()->get('user')->user_id;
        try {
            DB::beginTransaction();
            if ($request->brandid != '') {
                $arr = [
                    "brand_name_th"  => $request->brandth,
                    "brand_name_gb"  => $request->branden,
                    "active_status"     => $request->brandchk,
                    "update_by"         => $user,
                    "update_dtm"        => $this->data_now,
                ];
                DB::table('mt_brand')->where('brand_id', $request->brandid)->update($arr);
            }else{
                $arr = [
                    "brand_name_th"     => $request->brandth,
                    "brand_name_gb"     => $request->branden,
                    "active_status"     => $request->brandchk,
                    "sort"              => 9999,
                    "status"            => "Y",
                    "update_by"         => $user,
                    "update_dtm"        => $this->data_now,
                    "create_by"         => $user,
                    "create_dtm"        => $this->data_now,
                ];
                DB::table('mt_brand')->insert($arr);
            }
            DB::commit();
            $output = ['res_code' => '00', 'res_text' => 'บันทึกข้อมูลสำเร็จ'];
        } catch (\Exception $th) {
            //throw $th;
            DB::rollback();
            dd($th);
            $output = ["res_code" => '01', "res_text" => "เกิดข้อผิดพลาดกรุณาลองใหม่"];
        }
        return $output;
    }

    function brandStatus(Request $request) {
        $user = session()->get('user')->user_id;
        try {
            // $request->type 0 = view , 1 = status
            DB::beginTransaction();
            if($request->type == '1'){
                DB::table('mt_brand')->where('brand_id', $request->brandid)->update(["status" => "N"]);
            }else if($request->type == '0'){
                DB::table('mt_brand')->where('brand_id', $request->brandid)->update(["active_status" => $request->status]);
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
