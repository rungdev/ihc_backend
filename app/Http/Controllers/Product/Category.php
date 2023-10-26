<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Category extends Controller
{
    function category() {
        $table = $this->table_load();
        return view('Product.category', compact(["table"]));
    }

    function searchCategory(Request $request) {
        $search = $request->search;
        $catagory = $this->table_load($search);
        return $catagory;
    }

    function table_load($search = '') {
        $catagory = $this->getCatMain('all', $search);
        $cat = [];
        foreach ($catagory as $key => $value) {
            $arr = [
                "cat_id" => $value->cat_id,
                "cat_name" => $value->cat_name_th,
            ];
            $cat[] = $arr;
            if(!empty($value->sub_list)){
                $l = $this->loopCat($value->sub_list, $value->cat_name_th);
                foreach ($l as $key => $value) {
                    $cat[] = $value;
                }
            }
        }

        $table = '';
        foreach ($catagory as $key => $value) {
            $k = $key+1;
            $value->active_status = $value->active_status == 'Y' ? 'checked' : '';
            $table .= '
            <tr data-node-id="'.$k.'">
                <td><b>'.$value->cat_name_th.'</b></td>
                <td class="text-center">
                    <div class="form-check form-switch text-center">
                        <input class="form-check-input activeStatus" type="checkbox" ' . $value->active_status . ' data-id="' . $value->cat_id . '">
                    </div>
                </td>
                <td class="text-center">
                    <button data-id="'.$value->cat_id.'" data-main="0" class="btn btn-ghost-primary btn-sm shadow-none btn-add-child"><i class="ri-add-circle-fill fs-16"></i></button>
                    <button data-id="'.$value->cat_id.'" data-main="0" class="btn btn-ghost-secondary btn-sm shadow-none btn-edit"><i class="ri-pencil-fill fs-16"></i></button>
                    <button data-id="'.$value->cat_id.'" class="btn btn-ghost-danger btn-sm shadow-none btn-remove"><i class="ri-delete-bin-5-fill fs-16"></i></button>
                </td>
            </tr>';
            $table .= $this->loop_table($value->sub_list, $k, $value->cat_id);
            
        }
        return $table;
    }

    function loop_table($sub, $k, $main) {
        $count = 1;
        $table = "";
        foreach ($sub as $key => $value) {
            $kk = $k.'.'.$count;
            $value->active_status = $value->active_status == 'Y' ? 'checked' : '';
            $table .= '<tr data-node-id="'.$kk.'" data-node-pid="'.$k.'">
                            <td>'.$value->cat_name_th.'</td>
                            <td class="text-center">
                                <div class="form-check form-switch text-center">
                                    <input class="form-check-input activeStatus" type="checkbox" ' . $value->active_status . ' data-id="' . $value->cat_id . '">
                                </div>
                            </td>
                            <td class="text-center">
                                <button data-id="'.$value->cat_id.'" data-main="'.$main.'" class="btn btn-ghost-primary btn-sm shadow-none btn-add-child"><i class="ri-add-circle-fill fs-16"></i></button>
                                <button data-id="'.$value->cat_id.'" data-main="'.$main.'" class="btn btn-ghost-secondary btn-sm shadow-none btn-edit"><i class="ri-pencil-fill fs-16"></i></button>
                                <button data-id="'.$value->cat_id.'" class="btn btn-ghost-danger btn-sm shadow-none btn-remove"><i class="ri-delete-bin-5-fill fs-16"></i></button>
                            </td>
                        </tr>';

            $table .= $this->loop_table($value->sub_list, $kk, $value->cat_id);
            $count++;
        }
        return $table;
    }

    function categoryById(Request $request) {
        $output = ["res_code" => '01', "res_text" => "เกิดข้อผิดพลาดกรุณาลองใหม่"];
        if($request->id){
            $table = DB::table('mt_category')->where("cat_id", $request->id)->first();
            $output = ["res_code" => '00', "res_result" => $table];
        }
        return $output;
    }

    function saveCategory(Request $request) {
        $user = session()->get('user')->user_id;
        try {
            DB::beginTransaction();
            if ($request->categoryid != '') {
                $arr = [
                    "cat_name_th"       => $request->categoryth,
                    "cat_name_gb"       => $request->categoryen,
                    "active_status"     => $request->categorychk,
                    "update_by"         => $user,
                    "update_dtm"        => $this->data_now,
                ];
                DB::table('mt_category')->where('cat_id', $request->categoryid)->update($arr);
            }else{
                $arr = [
                    "cat_name_th"       => $request->categoryth,
                    "cat_name_gb"       => $request->categoryen,
                    "cat_parent_id"     => $request->catmain,
                    "active_status"     => $request->categorychk,
                    "orderby"           => 99999,
                    "status"            => "Y",
                    "update_by"         => $user,
                    "update_dtm"        => $this->data_now,
                    "create_by"         => $user,
                    "create_dtm"        => $this->data_now,
                ];
                DB::table('mt_category')->insert($arr);
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

    function categoryStatus(Request $request) {
        $user = session()->get('user')->user_id;
        try {
            // $request->type 0 = view , 1 = status
            DB::beginTransaction();
            if($request->type == '1'){
                DB::table('mt_category')->where('cat_id', $request->categoryid)->update(["status" => "N"]);
            }else if($request->type == '0'){
                DB::table('mt_category')->where('cat_id', $request->categoryid)->update(["active_status" => $request->status]);
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
