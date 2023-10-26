<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Image;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public $image_product = 'https://admin.ihaverung.com/data/img/shop/ntss/product';
    public $image_product_opt = 'https://admin.ihaverung.com/data/img/shop/ntss/product_option';
    public $data_now;
    public $branch_id = '1';

    public function __construct()
    {
        $this->data_now = date('Y-m-d H:i:s');
    }

    function getStatusName($status) {
        $lang = 'th';
        switch ($status) {
            case 'Pending'  : $classes = ( $lang=="th" ? 'ทำรายการไม่สำเร็จ'   : 'Unsuccessful'); break;
            case 'Waiting'  : $classes = ( $lang=="th" ? 'รอชำระเงิน'         : 'Wait for payment'); break;
            case 'Waiting_D': $classes = ( $lang=="th" ? 'รอยืนยันการสั่งซื้อ'    : 'Waiting for Confirm'); break;
            case 'Progress' : $classes = ( $lang=="th" ? 'กำลังดำเนินการ'      : 'Progress'); break;
            case 'Paid'     : $classes = ( $lang=="th" ? 'รอดำเนินการ'        : 'Progress'); break;
            case 'Paid_D'   : $classes = ( $lang=="th" ? 'เตรียมการจัดส่ง'      : 'Waiting for Delivery'); break;
            case 'Shipped'  : $classes = ( $lang=="th" ? 'จัดส่งแล้ว'          : 'Shipped'); break;
            case 'Completed': $classes = ( $lang=="th" ? 'เสร็จสิ้น'            : 'Completed'); break;
            case 'Cancel'   : $classes = ( $lang=="th" ? 'ยกเลิก'             : 'Cancel'); break;
            default: $classes = ""; break;
        }
        return $classes;
    }

    function DateThai($strDate)
	{
		$strYear = date("Y",strtotime($strDate))+543;
		$strMonth= date("n",strtotime($strDate));
		$strDay= date("j",strtotime($strDate));
		$strHour= date("H",strtotime($strDate));
		$strMinute= date("i",strtotime($strDate));
		$strSeconds= date("s",strtotime($strDate));
		$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
		$strMonthThai=$strMonthCut[$strMonth];
		return "$strDay-$strMonthThai-$strYear $strHour:$strMinute";
	}


    function resize($width, $height, $path, $path_new) {
        $img = Image::make($path);
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        })->save($path_new);
    }

    function checkPermission($module, $permission) {
        $user = session()->get('user');
        $check = DB::table('group_permission')
                ->select('usergroup_id')
                ->where('usergroup_id' , $user->user_group)
                ->where('module_id' , $module)
                ->where('permission' , $permission)
                ->count();
        return $check;
    }

    function Mt_OptionInfoByID($m_option_id = '') {
        if($m_option_id != ''){
            $data = DB::table('mt_product_option')
                    ->select('m_option_parent_id', 'm_option_picture', 'm_option_extension', 'm_option_id', 'm_option_name_th as option_name', 'update_dtm')
                    ->where('m_option_id', $m_option_id);
            if($data->count() > 0){
                $data = $data->first();
                $data->m_option_extension = $data->m_option_extension."?".strtotime($data->update_dtm);
                $data->parent_option = $this->Mt_OptionInfoByID($data->m_option_parent_id);
                return $data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function generalsetting() {
        $res = DB::table('general_settings')
            ->where('general_settings_id', '1')
            ->get();
        return $res;
    }

    static function checkRole($module, $permission, $group = ''){
        if($group == ''){
            $permission_arr =  session()->get('permission');
        }else{
            $check = DB::table('group_permission')
                    ->where('usergroup_id' , $group)
                    ->get();
            foreach ($check as $key => $value) {
                $permission_arr[] = (array)$value;
            }
        }
        $filtered_array = array_filter($permission_arr, function($val) use($module, $permission){
            return ($val['module_id']==$module and $val['permission']==$permission);
        });
        return $filtered_array;
    }

    function getCatMain($all = '', $where = '') {
        
        $arr_cat = [];
        $cats = DB::table('mt_category')
            ->select('cat_id', 'cat_parent_id', 'cat_name_th', 'active_status')
            ->where('cat_parent_id', 0)
            ->where('status', 'Y');
        if($all == ''){
            $cats->where('active_status', 'Y');
        }
        if($where != ''){
            $cats->where(function ($query) use ($where) {
                $query->where('cat_name_th', 'LIKE', "%$where%")
                    ->orWhere('cat_name_gb', 'LIKE', "%$where%");
            });
        }
        $cat = $cats->orderBy('cat_id')->get();
        foreach ($cat as $key => $value) {
            $value->sub_list = $this->getSubCat($value->cat_id, $all, $where);
            $arr_cat[] = $value;
        }
        return $arr_cat;
    }

    function getSubCat($cat_id, $all = '', $where = ''){
        $arr = [];
        $cats = DB::table('mt_category')
                ->select('cat_id', 'cat_parent_id', 'cat_name_th', 'active_status')
                ->where('cat_parent_id', $cat_id)
                ->where('status', 'Y');
        if($all == ''){
            $cats->where('active_status', 'Y');
        }
        if($where != ''){
            $cats->where(function ($query) use ($where) {
                $query->where('cat_name_th', 'LIKE', "%$where%")
                    ->orWhere('cat_name_gb', 'LIKE', "%$where%");
            });
        }
        $sub = $cats->orderBy('cat_id');
        if($sub->count() > 0){
            foreach ($sub->get() as $key => $value) {
                $value->sub_list = $this->getSubCat($value->cat_id, $all, $where);
                $arr[] = $value;
            }
        }
        return $arr;
    }

    function getMasterProductBrand() {
        $arr = [];
        $brand = DB::table('mt_brand')
                ->select('brand_id', 'brand_name_th')
                ->where('active_status', 'Y');
        if($brand->count() > 0){
            foreach ($brand->get() as $key => $value) {
                $arr[] = $value;
            }
        }
        return $arr;
    }

    function getMasterProductSupplier() {
        $arr = [];
        $supplier = DB::table('mt_supplier')
                    ->select('supplier_id', 'name_th')
                    ->where('active_status', 'Y');
        if($supplier->count() > 0){
            foreach ($supplier->get() as $key => $value) {
                $arr[] = $value;
            }
        }
        return $arr;
    }

    function showDate($strDate) {
        if($strDate != "0000-00-00 00:00:00" && $strDate != "0000-00-00" && !empty($strDate)){
            $strYear = date("Y",strtotime($strDate))+543;
            $strMonth= date("n",strtotime($strDate));
            $strDay= date("j",strtotime($strDate));
            $strHour= date("H",strtotime($strDate));
            $strMinute= date("i",strtotime($strDate));
            $strSeconds= date("s",strtotime($strDate));
            $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
            $strMonthThai=$strMonthCut[$strMonth];
            return "$strDay-$strMonthThai-$strYear $strHour:$strMinute:$strSeconds";
        }else{
            return "-";
        }
    }

    function getCatagryByID($cat_id) {
        $datavalue = '';
        $cat = DB::table('mt_category')
            ->select('cat_id', 'cat_parent_id', 'cat_name_th')
            ->where('cat_id', $cat_id);
        if($cat->count() > 0){
            $cat = $cat->first();
            $datavalue .= $cat->cat_name_th;
            if(!empty($cat->cat_parent_id)){
                $datavalue .= '/';
                $datavalue .= $this->getDataMasterCategoryMain($cat->cat_parent_id);
            }
        }

        $splitvalue = explode('/',$datavalue);
        if(count($splitvalue)>1){
            $datavalue = '';
            for($j=(count($splitvalue)-1);$j>=0;$j--){
                $slat = ($j>0) ? '/' : '';
                $datavalue .= $splitvalue[$j].$slat;
            }
        }else{
            $datavalue = $splitvalue[0];
        }
        return $datavalue;
    }

    function getDataMasterCategoryMain($cat_id) {
        $datavalue = '';
        $cat = DB::table('mt_category')
            ->select('cat_id', 'cat_parent_id', 'cat_name_th')
            ->where('cat_id', $cat_id);
        if($cat->count() > 0){
            $cat = $cat->first();
            $slat = !empty($cat->cat_parent_id) ? '/' : '';
            $datavalue .= $cat->cat_name_th.$slat;
            if(!empty($cat->cat_parent_id)){
                $datavalue .= $this->getDataMasterCategoryMain($cat->cat_parent_id);
            }
        }

        return $datavalue;
    }

    public function getProductsubcat($product_id) {
        $data = "";
		$cat = DB::table('product_additionalcategory')
                ->select('cat_id')
                ->where('product_id', $product_id);
       
        if($cat->count() > 0){
            foreach ($cat->get() as $key => $value) {
                $data .= '<br>'.$this->getCatagryByID($value->cat_id);
            }
        }
        return $data;
	}

    function get_optionone($product_id) {
        $data = array();
        $data['option_id'] = "";
        $data['option_name'] = "";
        $data['barcode'] = "";
        $data['sku'] = "";
        $data['cost_price'] = "";
        $data['market_price'] = "";
        $data['sell_price'] = "";
        $data['picture_name'] = "";
        $data['picture_extension'] = "";
        if(!empty($product_id)){
            $product = DB::table('product_option')
                        ->select('option_id', 'option_name_th', 'barcode', 'sku', 'cost_price', 'market_price', 'sell_price',
                        'picture_name', 'picture_extension', 'create_by', 'create_dtm', 'update_by', 'update_dtm')
                        ->where('product_id', $product_id)
                        ->orderBy('option_id');
            if($product->count() > 0){
                foreach ($product->get() as $key => $value) {
                    $data['op'][$key]['option_id'] = $value->option_id;
                    $data['op'][$key]['option_name'] = $value->option_name_th;
                    $data['op'][$key]['barcode'] = $value->barcode;
                    $data['op'][$key]['sku'] = $value->sku;
                    $data['op'][$key]['cost_price'] = $value->cost_price;
                    $data['op'][$key]['market_price'] = $value->market_price;
                    $data['op'][$key]['sell_price'] = $value->sell_price;
                    $data['op'][$key]['picture_name'] = $value->picture_name;
                    $data['op'][$key]['picture_extension'] = $value->picture_extension;
                }
            }
        }
        return $data;
	}

    function getMtOption(){
        $output = [];
        $data = DB::table('mt_product_option')
                ->select('m_option_id', 'm_option_parent_id', 'm_option_name_th AS option_name')
                ->where('m_option_parent_id', '0')
                ->where('active_status', 'Y');
        if($data->count() > 0){
            foreach ($data->get() as $key => $value) {
                $value->option_parent = $this->getMtOptionParent($value->m_option_id);
                $output[] = $value;
            }
        }
        return $output;		
	}
    
    function getMtOptionParent($parentID = 0){
        $output = [];
        $data = DB::table('mt_product_option')
                ->select('m_option_id','m_option_parent_id','m_option_name_th as option_name')
                ->where('m_option_parent_id', $parentID);
        if($data->count() > 0){
            foreach ($data->get() as $key => $value) {
                $output[] = $value;
            }
        }
		return $output;
	}

    function saveProductMovement($arraydata){

		$datetoday = date("Y-m-d H:i:s");
		$sesuser_id = $arraydata['userID'];

        $product_name = !empty($arraydata['product_name']) ? $arraydata['product_name'] : '';

        $arr = [
            "ref_id" => !empty($arraydata['ref_id']) ? $arraydata['ref_id'] : 0, 
            "product_id" => $arraydata['product_id'], 
            "option_id" => $arraydata['option_id'], 
            "product_name" => $product_name, 
            "branch_id" => !empty($arraydata['branch_id']) ? $arraydata['branch_id'] : 0, 
            "movement_type" => $arraydata['movement_type'], 
            "before_stock" => $arraydata['before_stock'], 
            "change_stock" => $arraydata['change_stock'], 
            "current_stock" => $arraydata['current_stock'], 
            "old_cost_price" => !empty($arraydata['old_cost_price']) ? $arraydata['old_cost_price'] : 0, 
            "new_cost_price" => !empty($arraydata['new_cost_price']) ? $arraydata['new_cost_price'] : 0, 
            "sale_cost_price" => !empty($arraydata['sale_cost_price']) ? $arraydata['sale_cost_price'] : 0, 
            "create_by" => $sesuser_id, 
            "create_dtm" => $datetoday
        ];
        $ins = DB::table('product_movement')
                ->insertGetId($arr);
        
		if($ins){
			$movename = '';
			if($arraydata['movement_type']=='IV'){
		      $movename = 'ขายสินค้า '.$product_name;
		      $showamount = number_format($arraydata['change_stock']);
		      $movename .= ' '.$showamount.' ชิ้น';
		    }else if($arraydata['movement_type']=='SO'){
		      $movename = 'สั่งขายสินค้า '.$product_name;
		      $showamount = number_format($arraydata['change_stock']);
		      $movename .= ' '.$showamount.' ชิ้น';
		    }else if($arraydata['movement_type']=='TO'){
		      $movename = 'ย้ายออกสินค้า '.$product_name;
		      $showamount = number_format($arraydata['change_stock']);
		      $movename .= ' '.$showamount.' ชิ้น';
		    }else if($arraydata['movement_type']=='TI'){
		      $movename = 'ย้ายเข้าสินค้า '.$product_name;
		      $showamount = number_format($arraydata['change_stock']);
		      $movename .= ' '.$showamount.' ชิ้น';
		    }else if($arraydata['movement_type']=='GR'){
		      $movename = 'ซื้อสินค้า '.$product_name;
		      $showamount = number_format($arraydata['change_stock']);
		      $movename .= ' '.$showamount.' ชิ้น';
		    }else if($arraydata['movement_type']=='VC'){
		      $movename = 'คืนขายสินค้า '.$product_name;
		      $showamount = number_format($arraydata['change_stock']);
		      $movename .= ' '.$showamount.' ชิ้น';
		    }
		}
		
		return 1;
	}	

    function readDir($dir)
	{
		$dirs = array($dir);
		$files = array() ;
		for($i=0;;$i++)
		{
			if(isset($dirs[$i]))
				$dir =  $dirs[$i];
			else
				break ;
	   
			if($openDir = @opendir($dir))
			{
				while($readDir = @readdir($openDir))
				{
					if($readDir != "." && $readDir != "..")
					{
					
						if(is_dir($dir."/".$readDir))
						{
							$dirs[] = $dir."/".$readDir ;
						}
						else
						{
							$files[] = $dir."/".$readDir ;
						}
					}
				}
		   
			}
		   
		}	   
	   
		return $files;
    }
    public function myFilesize(){

		$customerID = 1;
		
		//bytes/1024/1024 = MB
		//bytes/1024/1024/1024 = GB
		/*'B' => 1,
        'KB' => 1024,
        'MB' => 1024 * 1024,
        'GB' => 1024 * 1024 * 1024, */	
		$data = array();
		$data['mysize_package'] = 0;
		$data['use_space'] = 0;
		$data['use_space_text'] = 0;
		$data['free_space'] = 0;
		$data['free_space_text'] = 0;

        $data_get = DB::connection('mysql_main')->table('customers')
                ->select('package_config_harddiskspace', 'my_harddiskspace')
                ->where('customer_id' ,$customerID);

		if($data_get->count() > 0){
            $res = $data_get->first(); 
			$package_config_harddiskspace = (int)$res->package_config_harddiskspace;
			$my_harddiskspace = (int)$res->my_harddiskspace;		
			$my_package_bytes = (($package_config_harddiskspace*1024)*1024);
			
			$use_bytes = ($my_harddiskspace);
			$use_bytes_text = "";
			if($use_bytes < 1024){
				$use_bytes_text = $use_bytes."Bytes";
			}else if($use_bytes < ((1024*1024)*1)){
				$use_bytes_text = ($use_bytes/1024)."KB";
			}else if($use_bytes < ((1024*1024)*1024)){
				$use_bytes_text = (($use_bytes/1024)/1024)."MB";
			}else{
				$use_bytes_text = ((($use_bytes/1024)/1024)/1024)."GB";
			}
			
			$free_bytes = (int)($my_package_bytes-$my_harddiskspace);
			$free_bytes_text = "";
			if($free_bytes < 1024){
				$free_bytes_text = $free_bytes."Bytes";
			}else if($free_bytes < ((1024*1024)*1)){
				$free_bytes_text = ($free_bytes/1024)."KB";
			}else if($free_bytes < ((1024*1024)*1024)){
				$free_bytes_text = (($free_bytes/1024)/1024)."MB";
			}else{
				$free_bytes_text = ((($free_bytes/1024)/1024)/1024)."GB";
			}
			
			$data['mysize_package'] = ($package_config_harddiskspace < 1024) ? $package_config_harddiskspace.'MB' : ($package_config_harddiskspace/1024).'GB';
			$data['use_space'] = $use_bytes;
			$data['use_space_text'] = $use_bytes_text;
			$data['free_space'] = $free_bytes;
			$data['free_space_text'] = $free_bytes_text;		
		}
		return $data;
	}

    public function saveFilesize($sumsize,$type){
		
		$save = DB::connection('mysql_main')
                ->table('customers')
                ->select('my_harddiskspace')
                ->where('customer_id', '1');
		if($save->count() > 0){
            $res = $save->first();
			$my_harddiskspace = (int)$res->my_harddiskspace;
			$sumsize = (int)$sumsize;
			if($type=='A'){
				$totalsize = (int)($my_harddiskspace+$sumsize);
                DB::connection('mysql_main')
                ->table('customers')
                ->where('customer_id', '1')
                ->update(['my_harddiskspace' => $totalsize]);
			}else{
				$totalsize = (int)($my_harddiskspace-$sumsize);
                DB::connection('mysql_main')
                ->table('customers')
                ->where('customer_id', '1')
                ->update(['my_harddiskspace' => $totalsize]);
			}
		}
	}

    function loopCat($sub_list, $name) {
        $arr_l = [];
        foreach ($sub_list as $key => $value) {
            $arr = [
                "cat_id" => $value->cat_id,
                "cat_name" => $name.'/'.$value->cat_name_th,
            ];
            $arr_l[] = $arr;
            if(!empty($value->sub_list)){
                $l = $this->loopCat($value->sub_list, $name.'/'.$value->cat_name_th);
                foreach ($l as $key => $value) {
                    $arr_l[] = $value;
                }
            }
        }
        return $arr_l;
    }

    static function unlinkDir($dir)
	{
		$dirs = array($dir);
		$files = array() ;
		for($i=0;;$i++)
		{
			if(isset($dirs[$i]))
				$dir =  $dirs[$i];
			else
				break ;
	   
			if($openDir = @opendir($dir))
			{
				while($readDir = @readdir($openDir))
				{
					if($readDir != "." && $readDir != "..")
					{
					
						if(is_dir($dir."/".$readDir))
						{
							$dirs[] = $dir."/".$readDir ;
						}
						else
						{
						   
							$files[] = $dir."/".$readDir ;
						}
					}
				}
		   
			}
		   
		}	   
	   
		foreach($files as $file)
		{
			@unlink($file) ;
		}
		$dirs = array_reverse($dirs) ;
		foreach($dirs as $dir)
		{
			@rmdir($dir) ;
		}
	}

    function moveFile($ds, $width, $height, $table, $field, $path, $type) {
        $output = [];
        $filenames = $this->readDir($ds);
        if(count($filenames) > 0){
            $space = $this->myFilesize();
            $free_space = $space["free_space"];
            if($free_space > 1){
                $sumsize = 0;
                $setdefault = 'Y';
                for($i=0;$i<count($filenames);$i++){
                    $filepath = $filenames[$i];
                    $image = imagecreatefromstring(file_get_contents($filepath));
                    $exif = @exif_read_data($filepath);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 1: // nothing
                                break;
                            case 2: // horizontal flip
                                imageflip($image, IMG_FLIP_HORIZONTAL);
                                break;
                            case 3: // 180 rotate left
                                $image = imagerotate($image, 180, 0);
                                break;
                            case 4: // vertical flip
                                imageflip($image, IMG_FLIP_VERTICAL);
                                break;
                            case 5: // vertical flip + 90 rotate right
                                imageflip($image, IMG_FLIP_VERTICAL);
                                $image = imagerotate($image, -90, 0);
                                break;
                            case 6: // 90 rotate right
                                $image = imagerotate($image, -90, 0);
                                break;
                            case 7: // horizontal flip + 90 rotate right
                                imageflip($image, IMG_FLIP_HORIZONTAL);
                                $image = imagerotate($image, -90, 0);
                                break;
                            case 8:    // 90 rotate left
                                $image = imagerotate($image, 90, 0);
                                break;
                        }
                    }
                    imagejpeg($image, $filepath, 100);

                    $arraydot = explode('.',$filepath);
                    
                    
                    $picture_extension = '.'.$arraydot[count($arraydot)-1];
                    $picid = DB::table($table)->insertGetId($field);
                    $picture_name = "product".$picid.'_';
                    $output[] = [
                        'option_id'     => $picid,
                        'extension'     => $picture_extension,
                        'picture_name'  => $picture_name,
                        'default'       => $setdefault
                    ];
                    $setdefault = 'N';
                    $product_phat = public_path($path);
                    foreach ($width as $key => $value) {
                        $Imgsize = 'product'.$picid.'_'.$value.$picture_extension;

                        $dims = getimagesize($filepath);
                        $source_width = $dims[0];
                        $img_width = $value;
                        if($source_width < 800){
                            $img_width = $source_width;
                        }
                        $this->resize($source_width, $height[$key], $filepath, $product_phat.$Imgsize);
                        if(is_file($product_phat.$Imgsize)){
                            $sumsize += (int)@filesize($product_phat.$Imgsize);
                        }
                    }
                    if($sumsize > 0){
                        $this->saveFilesize($sumsize,$type);
                    }
                }
            }
        }
        $this->unlinkDir($ds);
        return $output;
    }

    function get_country() {
        $country = DB::connection('mysql_main')->table('country')->get();
        return $country;
    }

    function get_mt_filter($filter = '', $active = '', $m_status = '') {
        $query = DB::table('mt_filter');
        if($active != ''){
            $query->where('active_status', $active);
        }else{
            $query->where('active_status', 'Y');
        }
        if($filter != ''){
            $query->where('parent_id', $filter);
        }else{
            $query->where('parent_id', '0');
        }
        if($m_status != ''){
            $query->where('m_status', $m_status);
        }
        $res = $query->get();

        return $res;
    }
    
}
