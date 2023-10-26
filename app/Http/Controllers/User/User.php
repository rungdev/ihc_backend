<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;

class User extends Controller
{
    function usergroup()
    {
        return view('User.Group');
    }

    function formgroup(Request $request, $id = null)
    {

        $user = $request->session()->get('user');
        $group = DB::table('user_groups')
            ->select('auto_id', 'name', 'active_status', 'create_by', 'create_dtm', 'update_by', 'update_dtm')
            ->where('auto_id', $id)->first();

        $permission = array();
        $permis = DB::table('group_permission')->where('usergroup_id', $id)->get();

        foreach ($permis as $key => $value) {
            $permission[$value->module_id . '_' . $value->permission] = 1;
        }
        // dd($permission);       
        $modules = DB::table('modules')
            ->select('module_id', 'modulesub_id', 'module_name_th', 'module_type', 'module_permission_name_th', 'module_permission')
            ->whereNotIn('module_id', [7, 1, 74, 75])
            ->where('modulesub_id', '0')
            ->whereIn('module_type', ['A', 'E'])
            ->get();
        $data = ["A" => [], "E" => []];
        foreach ($modules as $key => $value) {
            $modulesub = DB::table('modules')
                ->select('module_id', 'modulesub_id', 'module_name_th', 'module_type', 'module_permission_name_th', 'module_permission')
                ->whereNotIn('module_id', [22, 23, 44, 54])
                ->where('modulesub_id', $value->module_id)
                ->get();
            $data_m = [];
            foreach ($modulesub as $k => $v) {
                $v->per_txt = explode(',', $v->module_permission);
                $data_m[] = $v;
            }
            $value->modulesub = $modulesub;
            $data[$value->module_type][] = $value;
        }

        return view('User.Formgroup', compact(["data", "id", "permission", "group", "user"]));
    }

    function getListUser(Request $request)
    {
        $sort           = ($request->sort == '' ? 'auto_id' : $request->sort);
        $orderby        = ($request->order == '' ? 'ASC' : $request->order);
        $offset         = $request->offset;
        $limit          = $request->limit;

        $list = DB::table('user_groups')
            ->where('status', 'Y');
        $rows_number = $list->count();

        $list->orderBy($sort, $orderby);
        $list->skip($offset)->take($limit);
        // echo getStatusName($value->order_status);
        $output = [];
        foreach ($list->get() as $key => $value) {
            $value->no = $key + 1;
            $st = $value->active_status == 'Y' ? 'checked' : '';
            $value->status = '<div class="form-check form-switch text-center">
                                <input class="form-check-input activeStatus" type="checkbox" ' . $st . ' data-id="' . $value->auto_id . '">
                              </div>';

            $value->manager = '<a href="formgroup/' . $value->auto_id . '" class="text-primary d-inline-block edit-item-btn me-3"><i class="ri-pencil-fill fs-16"></i></a>';
            if ($value->auto_id != 1) {
                $value->manager .= '<a data-id="' . $value->auto_id . '" class="text-danger d-inline-block remove-item-btn"><i class="ri-delete-bin-5-fill fs-16"></i></a>';
            }
            $output[] = $value;
        }


        return ["total" => $rows_number, "totalNotFiltered" => $rows_number, "rows" => $output];
    }


    function saveGroup(Request $request)
    {
        $date = date("Y-m-d H:i");
        if ($request->groupid != "") {
            DB::table('user_groups')->where('auto_id', $request->groupid)->update(["name" => $request->gname, "update_by" => $request->userid]);
            DB::table('group_permission')->where('usergroup_id', $request->groupid)->delete();
            DB::table('group_permission')->insert($request->permission);
        } else {
            $lastid = DB::table('user_groups')->insertGetId([
                "name" => $request->gname,
                "active_status" => "Y",
                "status"        => "Y",
                "create_by"     => $request->userid,
                "create_dtm"    => $date,
                "update_by"     => $request->userid,
                "update_dtm"    => $date
            ]);
            $ins = [];

            foreach ($request->permission as $key => $value) {
                $ins[] = [
                    "usergroup_id" => $lastid, "module_id" => $value["module_id"], "permission" => $value["permission"]
                ];
            }
            DB::table('group_permission')->insert($ins);
        }
        return ["res_code" => '00'];
    }

    function groupStatus(Request $request)
    {
        if ($request->type == "S") {
            DB::table('user_groups')->where('auto_id', $request->groupid)->update(["status" => "N", "update_by" => $request->userid]);
        } else {
            DB::table('user_groups')->where('auto_id', $request->groupid)->update(["active_status" => $request->status, "update_by" => $request->userid]);
        }
        return ["res_code" => '00'];
    }

    function users()
    {
        $group  = DB::table('user_groups')
            ->where('status', 'Y')
            ->where('active_status', 'Y')
            ->get();
        $branch = DB::table('branchs')
            ->where('status', 'Y')
            ->where('active_status', 'Y')
            ->get();

        return view('User.Users', compact(['group', 'branch']));
    }

    function getUserlist(Request $request)
    {

        $name           = $request->name;
        $group          = $request->selGroup;
        $branch         = $request->selBranc;

        $sort           = ($request->sort == '' ? 'users.user_id' : $request->sort);
        $orderby        = ($request->order == '' ? 'ASC' : $request->order);
        $offset         = $request->offset;
        $limit          = $request->limit;


        $user = DB::table('users')
            ->select('users.*', 'user_groups.name AS gname')
            ->leftJoin('user_branch', 'users.user_id', 'user_branch.user_id')
            ->leftJoin('user_groups', 'users.user_group', 'user_groups.auto_id')
            ->where('users.status', 'Y');

        if ($name != '') {
            $user->where(function ($query) use ($name) {
                $query->where('firstname', 'LIKE', "%$name%")
                    ->orWhere('lastname', 'LIKE', "%$name%");
            });
        }

        if ($group != '') {
            $user->whereIn('user_group', $group);
        }

        if ($branch != '') {
            $user->whereIn('branch_id', $branch);
        }

        $user->groupBy();
        $rows_number = $user->count();
        $user->orderBy($sort, $orderby);
        $user->skip($offset)->take($limit);

        $output = [];
        foreach ($user->get() as $key => $value) {
            // $log = DB::table('users_loglogin')
            //     ->where('user_id', $value->user_id)
            //     ->orderBy('date_dtm', 'DESC')->first();
            $branch = DB::table('user_branch')
                ->leftJoin('branchs', 'user_branch.branch_id', 'branchs.branch_id')
                ->where('user_id', $value->user_id)
                ->get();

            $value->branch = "";
            foreach ($branch as $k => $v) {
                $value->branch .= $v->branch_name_th . " ";
            }

            $value->user_name = '
            <div class="text-start">
                <img src="uploads/img/user/' . $value->picture_name . '100' . $value->picture_extension . '" 
                onerror="this.onerror=null;this.src=' . "'" . asset('/uploads/img/avatar-bg100.png') . "'" . ';"
                alt="" style="width: 50px;height: 50px;object-fit: cover;object-position: center;">
                <span>' . $value->user_name . '</span>
            </div>
            ';

            if($value->lastlogin_dtm != ''){
                $value->log = $this->DateThai($value->lastlogin_dtm);
            }else{
                $value->log = "";
            }
            $st = $value->active_status == 'Y' ? 'checked' : '';
            $value->active_status = '<div class="form-check form-switch text-center">
                                        <input class="form-check-input activeStatusUser" type="checkbox" ' . $st . ' data-id="' . $value->user_id . '">
                                    </div>';
            $value->manager = '<a href="formuser/' . $value->user_id . '" class="text-primary d-inline-block edit-item-btn me-3"><i class="ri-pencil-fill fs-16"></i></a>';
            if ($value->user_id != 1) {
                $value->manager .= '<a data-id="' . $value->user_id . '" class="text-danger d-inline-block remove-user-btn"><i class="ri-delete-bin-5-fill fs-16"></i></a>';
            }
            $output[] = $value;
        }


        return ["total" => $rows_number, "totalNotFiltered" => $rows_number, "rows" => $output];
    }

    function activeUser(Request $request)
    {
        if ($request->type == "S") {
            DB::table('users')->where('user_id', $request->uid)->update(["status" => "N", "update_by" => $request->userid]);
        } else {
            DB::table('users')->where('user_id', $request->uid)->update(["active_status" => $request->status, "update_by" => $request->userid]);
        }
        return ["res_code" => '00'];
    }

    function formuser(Request $request, $id = null)
    {
        $group  = DB::table('user_groups')
            ->where('status', 'Y')
            ->where('active_status', 'Y')
            ->get();

        $branch = DB::table('branchs')
            ->where('status', 'Y')
            ->where('active_status', 'Y')
            ->get();

        $b_arr = [];
        $user = (object)[];
        if ($id != null) {
            $user = DB::table('users')
                ->where('user_id', $id)
                ->first();

            $bran = DB::table('user_branch')
                ->select('branchs.branch_id')
                ->leftJoin('branchs', 'user_branch.branch_id', 'branchs.branch_id')
                ->where('user_id', $id)
                ->get();

            foreach ($bran as $key => $value) {
                $b_arr[] = $value->branch_id;
            }
            // dd($b_arr);
        }





        return view('User.FormUser', compact(["group", "branch", "user", "b_arr"]));
    }

    function userSave(Request $request)
    {

        $data_file = json_decode($request->filepond);
        $date_new = date('YmdHis');
        $lastid = $request->user_id;
        

        if (isset($request->user_id)) {
            $data_arr = [
                "user_group"    => $request->user_group,
                "user_name"     => $request->user_name,
                "firstname"     => $request->firstname,
                "lastname"      => $request->lastname,
                "nickname"      => ($request->nickname == "" ? '' : $request->nickname),
                "birthday"      => ($request->birthday == "" ? '' : $request->birthday),
                "user_email"    => ($request->user_email == "" ? '' : $request->user_email),
                "user_phone"    => ($request->user_phone == "" ? '' : $request->user_phone),
                "mobile"        => ($request->mobile == "" ? '' : $request->mobile),
                "user_line"     => ($request->user_line == "" ? '' : $request->user_line),
                "user_name"     => $request->user_name,
                "update_by"     => $request->user_update,
                "update_dtm"    => date('Y-m-d H:i:s'),
            ];
            if ($request->user_password != "") {
                $data_arr['user_pwd'] = md5($request->user_password);
            }
            $upd = DB::table('users')
                ->where('user_id', $request->user_id)
                ->update($data_arr);

            $chk = DB::table('users')
                ->where('user_id', $request->user_id)
                ->first();
            if ($chk) {
                try {
                    unlink("uploads/img/user/" . $chk->picture_name . "100" . $chk->picture_extension);
                    unlink("uploads/img/user/" . $chk->picture_name . "300" . $chk->picture_extension);
                } catch (\Throwable $th) {
                }
            }

        }else{
            $data_arr = [
                "user_group"    => $request->user_group,
                "user_name"     => $request->user_name,
                "firstname"     => $request->firstname,
                "lastname"      => $request->lastname,
                "nickname"      => ($request->nickname == "" ? '' : $request->nickname),
                "birthday"      => ($request->birthday == "" ? '' : $request->birthday),
                "user_email"    => ($request->user_email == "" ? '' : $request->user_email),
                "user_phone"    => ($request->user_phone == "" ? '' : $request->user_phone),
                "mobile"        => ($request->mobile == "" ? '' : $request->mobile),
                "user_line"     => ($request->user_line == "" ? '' : $request->user_line),
                "user_name"     => $request->user_name,
                "update_by"     => $request->user_update,
                "update_dtm"    => date('Y-m-d H:i:s'),
                "user_pwd"      => md5($request->user_password),
                "create_by"     => $request->user_update,
                "create_dtm"    => date('Y-m-d H:i:s'),
            ];
            $ins = DB::table('users')
                ->insertGetId($data_arr);
            if ($ins) {
                $lastid = $ins;
            }
        }

        if(isset($data_file->name)){
            $explodeImage = explode("image/", $data_file->type);
            $imageType = $explodeImage[1];
            $file_name = "uploads/img/user/user" . $date_new . '_' . $lastid . '_';
            $data_file->data = str_replace(' ', '+', $data_file->data);
            $path_upload = file_put_contents($file_name . '.' . $imageType, base64_decode($data_file->data));
            if ($path_upload) {
                $oldPath = $file_name . '.' . $imageType;
                $newPath = $file_name . '100.' . $imageType;
                $newPath300 = $file_name . '300.' . $imageType;
                $this->resize(100, 100, $oldPath, $newPath);
                $this->resize(300, 300, $oldPath, $newPath300);
                unlink($file_name . '.' . $imageType);
                $upd = DB::table('users')
                    ->where('user_id', $lastid)
                    ->update([
                        "picture_name" => "user" . $date_new . '_' . $lastid . '_',
                        "picture_extension" => "." . $imageType
                    ]);
            }
        }

        if(isset($request->branch)){
            $branch = explode(',', $request->branch);
            $ins = [];
            foreach ($branch as $key => $value) {
                $ins[] = [
                    "user_id"   => $request->user_id,
                    "branch_id" => $value
                ];
            }
            DB::table('user_branch')
            ->where('user_id', $request->user_id)
            ->delete();
            DB::table('user_branch')
            ->insert($ins);
        }
        
        return ['res_code' => '00', 'res_text' => 'บันทึกข้อมูลเรียบร้อย'];
    }
}
