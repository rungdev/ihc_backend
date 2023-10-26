<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Option extends Controller
{
    function option() {
        return view('Product.option', compact([]));
    }

    function optionList(Request $request) {
        $sort           = ($request->sort == '' ? 'm_option_id' : $request->sort);
        $orderby        = ($request->order == '' ? 'DESC' : $request->order);
        $offset         = $request->offset;
        $limit          = $request->limit;
        $search         = $request->search;

        $table = DB::table('mt_product_option')
                ->where('status', 'Y')
                ->where('m_option_parent_id', '0');
        if ($search != '') {
            $table->where(function ($query) use ($search) {
                $query->where('m_option_name_th', 'LIKE', "%$search%")
                    ->orWhere('m_option_name_gb', 'LIKE', "%$search%");
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
                                        <input class="form-check-input activeStatus" type="checkbox" ' . $st . ' data-id="' . $value->m_option_id . '">
                                    </div>';
            $value->manager = '<button class="btn btn-ghost-secondary btn-sm shadow-none btn-edit" data-id="'.$value->m_option_id.'"><i class="ri-pencil-fill fs-16"></i></button>';
            $value->manager .= '<button class="btn btn-ghost-danger btn-sm shadow-none btn-remove" data-id="' . $value->m_option_id . '"><i class="ri-delete-bin-5-fill fs-16"></i></button>';
            $output[] = $value;
        }

        return ["total" => $rows_number, "totalNotFiltered" => $rows_number, "rows" => $output];
    }

    function optionById(Request $request) {
        $output = ["res_code" => '01', "res_text" => "เกิดข้อผิดพลาดกรุณาลองใหม่"];
        if($request->id){
            $table = DB::table('mt_product_option')->where("m_option_id", $request->id)->first();
            $output = ["res_code" => '00', "res_result" => $table];
        }
        return $output;
    }

    function saveOption(Request $request) {
        $user = session()->get('user')->user_id;
        try {
            DB::beginTransaction();
            if ($request->optionid != '') {
                $arr = [
                    "m_option_name_th"  => $request->optionth,
                    "m_option_name_gb"  => $request->optionen,
                    "active_status"     => $request->optionchk,
                    "update_by"         => $user,
                    "update_dtm"        => $this->data_now,
                ];
                DB::table('mt_product_option')->where('m_option_id', $request->optionid)->update($arr);
            }else{
                $arr = [
                    "m_option_name_th"  => $request->optionth,
                    "m_option_name_gb"  => $request->optionen,
                    "active_status"     => $request->optionchk,
                    "orderby"           => 99999,
                    "status"            => "Y",
                    "update_by"         => $user,
                    "update_dtm"        => $this->data_now,
                    "create_by"         => $user,
                    "create_dtm"        => $this->data_now,
                ];
                DB::table('mt_product_option')->insert($arr);
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

    function optionStatus(Request $request) {
        $user = session()->get('user')->user_id;
        try {
            // $request->type 0 = view , 1 = status
            DB::beginTransaction();
            if($request->type == '1'){
                DB::table('mt_product_option')->where('m_option_id', $request->optionid)->update(["status" => "N"]);
            }else if($request->type == '0'){
                DB::table('mt_product_option')->where('m_option_id', $request->optionid)->update(["active_status" => $request->status]);
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
