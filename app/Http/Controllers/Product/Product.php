<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Ecom_product_iconpromotion_Model;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use File;

class Product extends Controller
{
    function productlist(Request $request) {
        $catagory = $this->getCatMain();
        $brand = $this->getMasterProductBrand();

        $cathtml = '<ul style="padding: 0;">
                    <li>
                    <input type="checkbox" name="cat-1" id="cat-1" class="form-check-input checkcata" value="">
                    <label for="cat-1">ทั้งหมด</label><ul>';
        foreach ($catagory as $key => $item) {
            $cathtml .= '<li>
                        <input type="checkbox" name="cat-1-'.$item->cat_id.'" id="cat-1-'.$item->cat_id.'" class="form-check-input checkcata" value="'.$item->cat_id.'">
                        <label for="check">'.$item->cat_name_th.'</label>';
            if(!empty($item->sub_list)){
                $cathtml .= $this->forHtmlCat($item, "cat-1-".$item->cat_id);
            }
            $cathtml .= '</li>';
        }
        $cathtml .= "</ul></li></ul>";

        // $suppiler = $this->getMasterProductSupplier();
        return view('Product.list', compact(['catagory', 'brand', 'cathtml']));
    } 

    
    
    
    function productCreate() {

        $icon_m = new Ecom_product_iconpromotion_Model();
        $icon = $icon_m->get();
        
        $masterOption = $this->getMtOption();
        $brand = $this->getMasterProductBrand();
        $supplier = [];
        $supp = DB::table('mt_supplier')
                ->select('supplier_id', 'name_th as name')
                ->where('active_status', 'Y');
        if($supp->count() > 0){
            foreach ($supp->get() as $key => $value) {
                $supplier[] = $value;
            }
        }
        $catagory = $this->getCatMain();
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

        $branch = [];
        $bran = DB::table('branchs')
                ->select('branch_id', 'branch_name_th' , 'active_status')
                ->where('status', 'Y')
                ->orderBy('branch_online', 'DESC');
        foreach ($bran->get() as $key => $value) {
            $branch[] = $value;
        }
        $product = "";
        $filter = $this->get_mt_filter('','','Y');


        // dd($filter);
        return view('Product.form', compact(['masterOption', 'supplier', 'brand', 'cat', 'branch', 'product', 'filter', 'icon']));
    }

    function filtersubById($id) {
        $result = [];
        $query = DB::table('mt_filter_group')
                ->leftJoin('mt_filter', 'mt_filter_group.filter_id_parent', 'mt_filter.filter_id')
                ->select('mt_filter_group.filter_id', 'filter_id_parent', 'name_th')
                ->where('mt_filter_group.filter_id', $id);
        if($query->count() > 0){
            foreach ($query->get() as $key => $value) {
                $sub = DB::table('mt_filter')
                        ->select('filter_id', 'name_th')
                        ->where('active_status', 'Y')
                        ->where('parent_id', $value->filter_id_parent)
                        ->orderBy('name_th', 'ASC')
                        ->get();
                $value->subs = $sub;
                $result[] = $value;
            }
            $output = ['res_code' => '00', 'res_text' => 'สำเร็จ','res_result' => $result];
        }else{
            $output =['res_code' => '01', 'res_text' => 'ไม่พบข้อมูล'];
        }
        
        return $output;
    }

    function getProductOption() {
        $masterOption = $this->getMtOption();
        return ['res_code' => '00', 'res_result' => $masterOption];
    }

    function getDataOptions($product_id) {
        $output = [];
        $data = DB::table('product_option')
                ->select('option_id', 'product_option.product_id', 'm_option_id', 'm_option_size_id', 'm_option_third_id', 'barcode', 'sku', 'option_name_th', 'cost_price', 'market_price', 
                'sell_price', 'ecom_market_price', 'status_preorder','stock_preorder','deposit_preorder','limit_preorder','branch_pricesale', 'picture_name', 'picture_extension',
                'weight','orderby', 'products.name_th AS product_name')
                ->leftJoin('products', 'product_option.product_id', 'products.product_id')
                ->where('product_option.product_id', $product_id)
                ->orderBy('orderby', 'ASC');
        if($data->count() > 0){
            foreach ($data->get() as $key => $value) {
                $value->m_option_name = $this->getMasterOptionName($value->m_option_id);
				$value->m_option_size_name = $this->getMasterOptionName($value->m_option_size_id);
				$value->m_option_third_name = $this->getMasterOptionName($value->m_option_third_id);
				$value->images = $this->getDataoption_images($value->option_id, $value->product_id);
                $output[] = $value;
            }
        }
        
    }

    function getMasterBranch($active_status=false) {
        $output = [];
        $active = !empty($active_status) ? " active_status='$active_status' AND " : "";
        $data = DB::table('branchs')
                ->select('branch_id', 'branch_name_th AS branch_name' , 'active_status')
                ->where('status', 'Y');
        if(!empty($active_status)){
            $data->where('active_status', $active_status);
        }
        if($data->count() > 0){
            foreach ($data->get() as $key => $value) {
                $output[] = $value;
            }
        }
        return $output;
	}

    function getProductstock($product_id) {
        $output = [];
        $data = DB::table('product_stock')
                ->select('option_id', 'branch_id','m_option_id', 'stock', 'stock_alert', 'cost_price', 'market_price', 'sell_price')
                ->where('product_id', $product_id);
        if ($data->count() > 0) {
            foreach ($data->get() as $key => $value) {
                $option_id = $value->option_id;
				$branch_id = $value->branch_id;
				$stock = $value->stock;
				$stock_alert = $value->stock_alert;
				$branch_pricesale = $value->branch_pricesale;
				$cost_price = $value->cost_price;
				$market_price = $value->market_price;
				$sell_price = $value->sell_price;
				$m_option_id = $value->m_option_id;
                $output[$product_id.'_'.$option_id.'_'.$branch_id]['stock'] = $branch_pricesale;
				$output[$product_id.'_'.$option_id.'_'.$branch_id]['stock'] = $stock;
				$output[$product_id.'_'.$option_id.'_'.$branch_id]['stock_option'][$m_option_id] = $stock;
				$output[$product_id.'_'.$option_id.'_'.$branch_id]['stock_alert'] = $stock_alert;
				$output[$product_id.'_'.$option_id.'_'.$branch_id]['cost_price'] = $cost_price;
				$output[$product_id.'_'.$option_id.'_'.$branch_id]['market_price'] = $market_price;
				$output[$product_id.'_'.$option_id.'_'.$branch_id]['sell_price'] = $sell_price;
            }
        }
        return $output;
	}

    public function getMasterProductOption() {
        $output = [];
        $data = DB::table('mt_product_option')
                ->select('m_option_id', 'm_option_name_th AS m_option_name' ,'m_option_parent_id')
                ->where('active_status', 'Y');
        if($data->count() > 0){
            foreach ($data->get() as $key => $value) {
                $output[] = $value;
            }
        }
        return $output;
	}

    function getMasterOptionName($m_option_id){
        $name = "";
        $data = DB::table("mt_product_option")
                ->select('m_option_parent_id', 'm_option_name_th')
                ->where('active_status', 'Y')
                ->where('m_option_id', $m_option_id);
        if($data->count() > 0){
            $name = $data->first();
        }
        return $name;
	}

    protected function getDataoption_images($option_id,$product_id) {
        $output = [];
        $data = DB::table("product_option_picture")
                ->select('id', 'picture_name', 'picture_extension')
                ->where('option_id', $option_id)
                ->where('product_id', $product_id);
        if($data->count() > 0){
            foreach ($data->get() as $key => $value) {
                $output[] = $value;
            }
        }
        return $output;
	}

    function forHtmlCat($catagory, $id){
        $html = "<ul>";
        foreach ($catagory->sub_list as $key => $item) {
            $html .= '<li>
                        <input type="checkbox" name="'.$id.'-'.$item->cat_id.'" id="'.$id.'-'.$item->cat_id.'" class="form-check-input checkcata" value="'.$item->cat_id.'">
                        <label for="check">'.$item->cat_name_th.'</label>';
            if(!empty($item->sub_list)){
                $html .= $this->forHtmlCat($item, $id.'-'.$item->cat_id);
            }   
            $html .= '</li>';       
        }
        $html .= "</ul>";
        return $html;
    }

    function productTable(Request $request) {
        $sort           = ($request->sort == '' ? 'p.product_id' : $request->sort);
        $orderby        = ($request->order == '' ? 'DESC' : $request->order);
        $offset         = $request->offset;
        $limit          = $request->limit;
        $group_id       = $request->header('User-Group');

        if($sort == 'name_product'){
            $sort = 'p.name_th';
        }

        $search                 = $request->search;
        $product_cat_id         = $request->product_cat_id;
        $product_brand_id       = $request->product_brand_id;
        $product_active_status  = $request->product_active_status;
        $minCost                = $request->minCost;
        $maxCost                = $request->maxCost;

        $update = $this->checkRole("9", "UPDATE", $group_id);
        $delete = $this->checkRole("9", "DELETE", $group_id);
        $view = $this->checkRole("9", "VIEW", $group_id);

        $table = DB::table('products AS p')
                ->select('p.product_id', 'p.product_code', 'p.supplier_id', 'p.name_th', 'p.cat_id', 'p.brand_id', 'p.barcode', 'p.sku', 'p.cost_price', 'p.market_price', 'p.sell_price', 'p.product_vat', 'p.stock_status',
                'p.product_view', 'p.active_status', 'p.sync_status', 'p.sync_dtm', 'p.create_by', 'p.create_dtm', 'p.update_by', 'p.update_dtm', 'ps.stocktt', 'b.brand_name_th');
        $table->leftJoin(DB::raw('(SELECT SUM(stock) AS stocktt,product_id FROM product_stock GROUP BY product_id) AS ps'), 'p.product_id', 'ps.product_id');
        $table->leftJoin('product_option AS po', 'p.product_id', 'po.product_id');
        $table->leftJoin('mt_category AS c', 'p.cat_id', 'c.cat_id');
        $table->leftJoin('product_additionalcategory AS othc', 'p.product_id', 'othc.product_id');
        $table->leftJoin('mt_brand AS b', 'p.brand_id', 'b.brand_id');
        $table->where('p.status', 'Y');
        if ($search != "") {
            $table->where(function ($query) use ($search) {
                $query->where('p.barcode', 'LIKE', "%$search%")
                    ->orWhere('po.barcode', 'LIKE', "%$search%")
                    ->orWhere('p.name_th', 'LIKE', "%$search%")
                    ->orWhere('p.name_gb', 'LIKE', "%$search%")
                    ->orWhere('po.option_name_th', 'LIKE', "%$search%")
                    ->orWhere('po.option_name_gb', 'LIKE', "%$search%")
                    ->orWhere('p.sku', 'LIKE', "%$search%")
                    ->orWhere('po.sku', 'LIKE', "%$search%")
                    ->orWhere('p.product_code', 'LIKE', "%$search%");
            });
        }
        if ($minCost != "" && $maxCost != "") {
            $table->where(function ($query) use ($minCost, $maxCost) {
                $query->where('p.sell_price', '>=', $minCost)
                        ->Where('p.sell_price', '<=', $maxCost);
            });
        }
        if(!empty($product_cat_id) && count($product_cat_id)>0){
            $table->where(function ($query) use ($product_cat_id) {
                $query->whereIn('p.cat_id', $product_cat_id)
                    ->orWhereIn('othc.cat_id', $product_cat_id);
            });
        }
        if(!empty($product_brand_id) && count($product_brand_id)>0){
            $table->whereIn('p.brand_id', $product_brand_id);
        }
        if(!empty($product_active_status) && count($product_active_status)>0){
            $table->whereIn('p.active_status', $product_active_status);
        }
        $table->groupBy('p.product_id');
        $table->orderBy($sort, $orderby);
        $rows_number = $table->get()->count();
        $table->skip($offset)->take($limit);

        $output = [];
        if($rows_number > 0){            
            foreach ($table->get() as $key => $value) {
                $value->othcat = $this->getProductsubcat($value->product_id);
                $value->checkbox = '<input type="checkbox" class="checkrow" value="'.$value->product_id.'">';
                $value->name_product = $value->name_th;
                $value->name_product .= (!empty($value->barcode) ? '<div class="txt-desc"><i class="ri-barcode-line align-text-bottom"></i> '.$value->barcode.'<div>' : '');
                $value->sku .= (!empty($value->sku) ? '<div class="txt-desc">รุ่น :'.$value->sku.'</div>' : '');
                if($value->sync_status == 'Y'){
                    $sync = '<i class="ri-checkbox-circle-fill align-text-bottom" style="color:green"></i>';
                }else{
                    $sync =  '<i class="ri-checkbox-circle-fill align-text-bottom" style="color:red"></i>';
                }
                
                $value->cat_id = $this->getCatagryByID($value->cat_id);
                $value->cat_id .= $value->othcat;

                $sell_price = 0;
                $option = $this->get_optionone($value->product_id);
                if(!empty($option["op"])){
                    foreach ($option["op"] as $k => $v) {
                        $sell_price = $v["sell_price"];
                    }
                }
                
                if($sell_price == 0){
                    $value->sell_price = number_format($value->sell_price,2);
                }else{
                    $value->sell_price = number_format($sell_price,2);
                }

                $url = "window.location='?load=syncAPI&product_id=".$value->product_id."'";
                $value->sync_dtm = $this->showDate($value->sync_dtm);
                $value->name_product .= '<div class="txt-desc"><a href="#" onclick="'.$url.'">'.$sync.' Sync '.$value->sync_dtm.'</a></div>';
                $value->checkbox = '<input type="checkbox" class="checkrow" value="'.$value->product_id.'">';
                $st = $value->active_status == 'Y' ? 'checked' : '';
                $value->status = '<div class="form-check form-switch text-center">
                                    <input class="form-check-input activeStatus" type="checkbox" ' . $st . ' data-id="' . $value->product_id . '">
                                </div>';
                $value->manager = "";
                if(!empty($update)){
                    $value->manager    .= '<a href="edit/' . $value->product_id . '" class="text-primary d-inline-block edit-item-btn"><i class="ri-pencil-fill fs-16"></i></a>';
                }else if(!empty($view)){
                    $value->manager    .= '<a href="show/' . $value->product_id . '" class="text-primary d-inline-block"><i class="ri-eye-fill fs-16"></i></a>';
                }
                if(!empty($delete)){
                    $value->manager  .= ' <a data-id="' . $value->product_id . '" class="text-danger d-inline-block remove-item-btn btn-remove"><i class="ri-delete-bin-5-fill fs-16"></i></a>';
                }
                $output[] = $value;
            }
        }

        return ["total" => $rows_number, "totalNotFiltered" => $rows_number, "rows" => $output];
    }


    function createProduct(Request $request) {
        DB::beginTransaction();
        try {
            if(isset($request->sel_option)){
                $sel_option = implode(',',$request->sel_option);
                if(!$sel_option){
                    $sel_option = NULL;
                }
            }else{
                $sel_option = NULL;
            }
            $icon_promotion = "";
            if(isset($request->icon_promotion)){
                $icon_promotion = implode(',', $request->icon_promotion);
            }
           
            $arr = [
                "product_code"              => $request->product_code, 
                "supplier_id"               => $request->supplier_id, 
                "cat_id"                    => $request->cat_id, 
                "brand_id"                  => $request->brand_id, 
                "barcode"                   => $request->barcode,
                "sku"                       => $request->sku, 
                "cost_price"                => $request->cost_price ? $request->cost_price : 0,
                "market_price"              => $request->cost_price ? $request->cost_price : 0, 
                "sell_price"                => $request->sell_price ? $request->sell_price : 0, 
                "sel_option"                => $sel_option, 
                "branch_pricesale"          => 'N', 
                "option_status"             => $request->option_status ? 'Y' : 'N', 
                "shipping_status"           => 'N', 
                "stock_status"              => $request->stock_status ? 'Y' : 'N', 
                "product_vat"               => 'Y', 
                "visibility_point_of_sale"  => 'Y', 
                "visibility_online_store"   => 'Y', 
                "visibility_facebook"       => 'N', 
                "visibility_marketplace"    => 'N', 
                "online_store_startdate"    => date('Y-m-d'), 
                "online_store_enddate"      => '',
                "preorder_status"           => '',
                "preorder_price"            => '',
                "preorder_deposit"          => '',
                "preorder_start_date"       => '',
                "preorder_end_date"         => '',
                "preorder_date_stock_in"    => '',
                "preorder_date_expired"     => '',
                "preorder_stock"            => '', 
                "ecom_status"               => isset($request->ecom_status) ? $request->ecom_status : 'N', 
                'active_status'             => isset($request->active_status) ? $request->active_status : 'N', 
                'tags'                      => $request->tags, 
                'icon_promotion'            => $icon_promotion, 
                "create_by"                 => $request->userid, 
                'create_dtm'                => $this->data_now, 
                "update_by"                 => $request->userid, 
                "update_dtm"                => $this->data_now,
                "description_th"            => $request->description_th,
                "description_gb"            => $request->description_en ? $request->description_en : $request->description_th,
                "size_guide_th"             => $request->guide_th,
                "size_guide_th"             => $request->guide_en ? $request->guide_en : $request->guide_th,
                "name_th"                   => $request->name_th,
                "name_gb"                   => $request->name_en ? $request->name_en : $request->name_th,
                "meta_title_th"             => $request->meta_title_th,
                "meta_title_gb"             => $request->meta_title_en ? $request->meta_title_en : $request->meta_title_th,
                "meta_description_th"       => $request->meta_description_th,
                "meta_description_gb"       => $request->meta_description_en ? $request->meta_description_en : $request->meta_description_th,            
                "meta_keyword_th"           => $request->meta_keyword_th,
                "meta_keyword_gb"           => $request->meta_keyword_en ? $request->meta_keyword_en : $request->meta_keyword_th,
                "product_type_option"       => $request->optionsType ? $request->optionsType : 'N',
                "product_type_price"        => $request->priceType ? $request->priceType : 'N',
            ];
            $product_id = DB::table('products')->insertGetId($arr);
            // DB::rollback();
            // exit();
            if($product_id){
                if(empty($request->product_code)){
                    $gen_product_code = 'SKU-'.sprintf("%05d",$product_id);
                    DB::table('products')->where('product_id', $product_id)
                    ->update(['product_code' => $gen_product_code]);
                }

                if(!empty($request->other_cat_id) ){
                    foreach ($request->other_cat_id as $key => $value) {
                        $other_cat_arr = [
                            "product_id"    => $product_id, 
                            "cat_id"        => $value, 
                            "create_by"     => $request->userid, 
                            "create_dtm"    => $this->data_now, 
                            "update_by"     => $request->userid, 
                            "update_dtm"    => $this->data_now,
                        ];
                        $ins_other = DB::table('product_additionalcategory')
                                    ->insert($other_cat_arr);
                    }
                }
                
                $arr_o = [];
                
                if($request->optionsType == 'N'){
                    $option_arr = [
                        "product_id"        => $product_id,
                        "barcode"           => $request->barcode,
                        "sku"               => $request->sku,
                        "cost_price"        => $request->cost_price ? $request->cost_price : 0,
                        "market_price"      => $request->market_price ? $request->market_price : 0,
                        "sell_price"        => $request->sell_price ? $request->sell_price : 0,
                        "status_preorder"   => "N",
                        "stock_preorder"    => "",
                        "deposit_preorder"  => "",
                        "limit_preorder"    => $request->barcode,
                        "create_by"         => $request->userid, 
                        "create_dtm"        => $this->data_now, 
                        "update_by"         => $request->userid, 
                        "update_dtm"        => $this->data_now,
                        "option_name_th"    => '',
                        "option_name_gb"    => '',
                        "option_type"       => '0',
                        "option_view"       => '0',
                        "option_status"     => '0',
                    ];
                    $op_id = DB::table('product_option')
                                ->insertGetId($option_arr);
                    $option_arr['option_id']    = $op_id;
                    $option_arr['stock']        = $request->stock;
                    $option_arr['stock_alert']  = $request->stock_alert;
                    $option_arr['cost_price']   = $request->cost_price;
                    $option_arr['market_price'] = $request->market_price;
                    $option_arr['sell_price']   = $request->sell_price;
                    $arr_o[] = $option_arr;
                    
                }else{
                    $option_of_sub = [];
                    foreach ($request->sel_option as $key => $value) {
                        $sort = 1;
                        foreach ($request->input('option_text'.$value) as $k => $v) {
                            $sub = [
                                "sub_product_id"    => $product_id,
                                "sub_m_option_id"   => $value,
                                "sub_text"          => $v,
                                "sub_sort"          => $sort,
                                "sub_create_user"   => $request->userid,
                                "sub_create_at"     => $this->data_now,
                                "sub_update_user"   => $request->userid,
                                "sub_update_at"     => $this->data_now,
                            ];
                            $sub_id = DB::table('product_option_subs')->insertGetId($sub);
                            if($sub_id){
                                $option_of_sub[] = [
                                    "sub_id"    => $sub_id,
                                    "sub_text"  => $v,
                                    "m_option"  => $value,
                                ];

                                if(isset($request->file('img-option-'.$value)[$k])){
                                    $file = $request->file('img-option-'.$value)[$k];
                                    $pt = public_path()."/data/img/product_option/images/";
                                    if(!is_dir($pt)){
                                        File::makeDirectory($pt);
                                    }
                                    $extension = $file->getClientOriginalExtension();
                                    $targetFile =  $pt.'option'.$sub_id.'.'.$extension;
                                    move_uploaded_file($file,$targetFile);
    
                                    $image = imagecreatefromstring(file_get_contents($targetFile));
                                    $exif = @exif_read_data($targetFile);
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
                                    imagejpeg($image, $targetFile, 150);
    
                                    $arraydot = explode('.',$targetFile);
                                    
                                    $picid = DB::table('product_option_subs')
                                            ->where('sub_id', $sub_id)
                                            ->update(['sub_path_img' => 'option'.$sub_id.'_150.'.$extension]);
    
                                    $this->resize(150, null, $targetFile, $pt.'/option'.$sub_id.'_150.'.$extension);
                                    $sumsize = 0;
                                    if(is_file($pt.'/option'.$sub_id.'_150.'.$extension)){
                                        $sumsize += (int)@filesize($pt.'/option'.$sub_id.'_150.'.$extension);
                                    }
                                    if($sumsize > 0){
                                        $this->saveFilesize($sumsize,'A');
                                    }
                                    @unlink($targetFile);
                                }
                                
                            }                           
                            
                            $sort++;
                        }
                    }

                    if(!empty($option_of_sub)){
                        foreach ($request->barcode_option as $key => $value) {
                            $opt_arr = [
                                "product_id"            => $product_id,
                                "barcode"               => $value, 
                                "sku"                   => $request->input('sku_option')[$key], 
                                "cost_price"            => $request->input('cost_option')[$key] ? $request->input('cost_option')[$key] : 0,  
                                "ecom_market_price"     => '0',
                                "sell_price"            => $request->input('sell_option')[$key] ? $request->input('sell_option')[$key] : 0,
                                "status_preorder"       => 'N',
                                "orderby"               => '999',
                                "create_by"             => $request->userid,
                                "create_dtm"            => $this->data_now,
                                "update_by"             => $request->userid,
                                "update_dtm"            => $this->data_now,
                                "option_type"           => '1',
                                "option_view"           => isset($request->input('webshow')[$key]) ? $request->input('webshow')[$key] : 0,
                                "option_status"         => '0',
                            ];
                            $op_id = DB::table('product_option')
                                    ->insertGetId($opt_arr);
                            $option_arr['option_id']    = $op_id;
                            $option_arr['stock']        = $request->input('quatity_option')[$key];
                            $option_arr['stock_alert']  = 0;
                            $option_arr['cost_price']   = $request->input('cost_option')[$key];
                            $option_arr['market_price'] = $request->input('sell_option')[$key];
                            $option_arr['sell_price']   = $request->input('sell_option')[$key];
                            $arr_o[] = $option_arr;
                            
                            $sort = 1;
                            foreach ($request->sel_option as $k => $v) {
                                $var1 = $request->input('option_table'.$v)[$key];
                                $var2 = $v;
                                $filtered_array = array_values(array_filter($option_of_sub, function($val) use($var1, $var2){
                                    return ($val['sub_text'] == $var1 && $val['m_option'] == $var2);
                                }));
                                if($filtered_array){
                                    $arr = [
                                        "ch_option_id"  => $op_id,
                                        "ch_sub_id"     => $filtered_array[0]["sub_id"],
                                        "ch_sort"       => $sort,
                                        "ch_create_at"  => $this->data_now,
                                    ];
                                    DB::table('product_option_choose')->insert($arr);
                                    $sort++;
                                }
                            }
                        }
                    }
                }

                if(count($arr_o) > 0){
                    foreach ($arr_o as $key => $value) {
                        if(isset($value["option_id"]) && $value["option_id"] != ''){

                            $stock_arr = [
                                "product_id"        => $product_id, 
                                "option_id"         => $value["option_id"], 
                                "branch_id"         => $this->branch_id, 
                                'stock'             => $value["stock"] ? $value["stock"] : 0,  
                                "stock_alert"       => $value["stock_alert"] ? $value["stock_alert"] : 0, 
                                "cost_price"        => $value["cost_price"] ? $value["cost_price"] : 0, 
                                "market_price"      => $value["market_price"] ? $value["market_price"] : 0, 
                                "sell_price"        => $value["sell_price"] ? $value["sell_price"] : 0, 
                                "create_by"         => $request->userid, 
                                "create_dtm"        => $this->data_now, 
                                "update_by"         => $request->userid, 
                                "update_dtm"        => $this->data_now,
                            ];
                            
                            $stock_id = DB::table('product_stock')
                                        ->insert($stock_arr);
                            

                            if($request->stock_status == 'N'){
                                $name = trim($request->name_th);
                                $arraydata = [
                                    'ref_id'            => 0,
                                    'product_id'        => $product_id,
                                    'option_id'         => $value["option_id"],
                                    'product_name'      => $name,
                                    'branch_id'         => $this->branch_id,
                                    'movement_type'     => 'CS',
                                    'old_cost_price'    => $value["cost_price"],
                                    'new_cost_price'    => $value["cost_price"],
                                    'userID'            => $request->userid,
                                    'before_stock'      => '0',
                                    'change_stock'      => $value["stock"],
                                    'current_stock'     => $value["stock"],
                                ];
                                $this->saveProductMovement($arraydata);
                            }
                        }
                    }
                }

                if(isset($request->filter)){
                    foreach ($request->filter as $key => $value) {
                        $subs = "";
                        if(!empty($request->input('select-filter-'.$value))){
                            foreach ($request->input('select-filter-'.$value) as $k => $v) {
                                $subs .= $v.",";
                            }
                        }
                        DB::table('product_filter')->insert([
                            "product_id"        => $product_id,
                            "filter_id"         => $value,
                            "filter_sub_id"     => $subs,
                            "create_by"         => $request->userid, 
                            'create_dtm'        => $this->data_now, 
                            "update_by"         => $request->userid, 
                            "update_dtm"        => $this->data_now
                        ]);
                    }
                }

                
                
                
                $product_qrcode_url = env('APP_ECOMMERCEURL_URL')."productdetail/".$product_id."/".$request->name_th;
            
                $path = public_path('uploads/img/qrcode/product_qrcode'.$product_id.'.svg');
                QrCode::size(300)->encoding('UTF-8')->generate($product_qrcode_url, $path);

                $ds = public_path('uploads/tempupload/'.$request->token.'/product_0');
                $datamove = $this->moveFile($ds, [800,150], [null, 113], 'product_picture', ["product_id" => $product_id, "picture_name" => 'product'], 'data/img/product/', 'A');
                foreach ($datamove as $k_d => $v_d) {
                    $upd_img = [
                        "picture_name"          => $v_d["picture_name"],
                        "picture_extension"     => $v_d["extension"],
                        "setdefault"            => $v_d["default"],
                    ];
                    DB::table('product_picture')
                    ->where('id', $v_d["option_id"])
                    ->update($upd_img);
                }
            }
            DB::commit();
            $res = ['res_code' => '00', 'res_text' => 'บันทึกข้อมูลสำเร็จ'];
        } catch (\Illuminate\Database\QueryException $ex) {
            // something went wrong
            DB::rollback();
            $res = ['res_code' => '01', 'res_text' => 'บันทึกข้อมูลไม่สำเร็จ'];
            dd($ex->getMessage()); 
        }
        return $res;
    }


    function createProduct2(Request $request) {

        
        try { 
        $data_now = date('Y-m-d H:i:s');
        $fields = [
            "product_code"              => $request->product_code, 
            "supplier_id"               => $request->supplier_id, 
            "cat_id"                    => $request->cat_id, 
            "brand_id"                  => $request->brand_id, 
            "barcode"                   => $request->barcode,
            "sku"                       => $request->sku, 
            "cost_price"                => $request->cost_price ? $request->cost_price : 0,
            "market_price"              => $request->cost_price ? $request->cost_price : 0, 
            "sell_price"                => $request->sell_price ? $request->sell_price : 0, 
            "branch_pricesale"          => 'N', 
            "option_status"             => $request->option_status ? 'Y' : 'N', 
            "shipping_status"           => 'N', 
            "stock_status"              => $request->stock_status ? 'Y' : 'N', 
            "product_vat"               => 'Y', 
            "visibility_point_of_sale"  => 'Y', 
            "visibility_online_store"   => $request->visibility_online_store ? $request->visibility_online_store : "N",
            "visibility_facebook"       => 'N', 
            "visibility_marketplace"    => 'N', 
            "online_store_startdate"    => date('Y-m-d'), 
            "online_store_enddate"      => '',
            "preorder_status"           => '',
            "preorder_price"            => '',
            "preorder_deposit"          => '',
            "preorder_start_date"       => '',
            "preorder_end_date"         => '',
            "preorder_date_stock_in"    => '',
            "preorder_date_expired"     => '',
            "preorder_stock"            => '', 
            "ecom_status"               => isset($request->ecom_status) ? $request->ecom_status : 'N', 
            'active_status'             => isset($request->active_status) ? $request->active_status : 'N', 
            "create_by"                 => $request->userid, 
            'create_dtm'                => $data_now, 
            "update_by"                 => $request->userid, 
            "update_dtm"                => $data_now,
            "description_th"            => $request->description_th,
            "description_gb"            => $request->description_en ? $request->description_en : $request->description_th,
            "name_th"                   => $request->name_th,
            "name_gb"                   => $request->name_en ? $request->name_en : $request->name_th,
            "meta_title_th"             => $request->meta_title_th,
            "meta_title_gb"             => $request->meta_title_en ? $request->meta_title_en : $request->meta_title_th,
            "meta_description_th"       => $request->meta_description_th,
            "meta_description_gb"       => $request->meta_description_en ? $request->meta_description_en : $request->meta_description_th,            
            "meta_keyword_th"           => $request->meta_keyword_th,
            "meta_keyword_gb"           => $request->meta_keyword_en ? $request->meta_keyword_en : $request->meta_keyword_th,
            "product_type_option"       => $request->optionsType ? $request->optionsType : 'N',
            "product_type_price"        => $request->priceType ? $request->priceType : 'N',
        ];
        // dd($fields);
        // exit();
        $ins = DB::table('products')
                ->insertGetId($fields);

        if($ins){
            if(empty($request->product_code)){
                $gen_product_code = 'SKU-'.sprintf("%05d",$ins);
                DB::table('products')->where('product_id', $ins)->update(['product_code' => $gen_product_code]);
            }
            if(!empty($request->other_cat_id) ){
                foreach ($request->other_cat_id as $key => $value) {
                    $other_cat_arr = [
                        "product_id"    => $ins, 
                        "cat_id"        => $value, 
                        "create_by"     => $request->userid, 
                        "create_dtm"    => $data_now, 
                        "update_by"     => $request->userid, 
                        "update_dtm"    => $data_now,
                    ];
                    $ins_other = DB::table('product_additionalcategory')
                                ->insert($other_cat_arr);
                }
            }

            $other_arr = [
                "product_id"        => $ins,
                "m_option_id"       => '0', 
                "barcode"           => $request->barcode,
                "sku"               => $request->sku,
                "cost_price"        => $request->cost_price ? $request->cost_price : 0,
                "market_price"      => $request->market_price ? $request->market_price : 0,
                "sell_price"        => $request->sell_price ? $request->sell_price : 0,
                "status_preorder"   => "N",
                "stock_preorder"    => "",
                "deposit_preorder"  => "",
                "limit_preorder"    => $request->barcode,
                "create_by"         => $request->userid, 
                "create_dtm"        => $data_now, 
                "update_by"         => $request->userid, 
                "update_dtm"        => $data_now,
                "option_name_th"    => $request->name_th,
                "option_name_gb"    => $request->name_en,
            ];
            $other = DB::table('product_option')
                    ->insertGetId($other_arr);

            if($other){
                if(!empty($request->branchid)){
                    foreach ($request->branchid as $key => $value) {
                        $branch_id = $value;
                        $stock = !empty($request->input('stock'.$branch_id)) ? (int)$request->input('stock'.$branch_id) : 0;
                        $stock_alert = !empty($request->input('stock_alert'.$branch_id)) ? (int)$request->input('stock_alert'.$branch_id) : 0;
                        if($request->branch_pricesale=="Y"){
                            $branch_sell_price = !empty($request->input('branchsell_price'.$branch_id)) ? (float)$request->input('branchsell_price'.$branch_id) : 0;
                            $branch_cost_price = !empty($request->input('branchcost_price'.$branch_id)) ? (float)$request->input('branchcost_price'.$branch_id) : 0;
                            $branch_market_price = ($request->input('branchmarket_price'.$branch_id)!='') ? (float)$request->input('branchmarket_price'.$branch_id) : $branch_sell_price;
                        }else{
                            $branch_cost_price = 0;
                            $branch_market_price = 0;
                            $branch_sell_price = 0;
                        }
                        $branch_arr = [
                            "product_id"        => $ins, 
                            "option_id"         => $other, 
                            "branch_id"         => $branch_id, 
                            'stock'             => $stock, 
                            "stock_alert"       => $stock_alert, 
                            "cost_price"        => $branch_cost_price, 
                            "market_price"      => $branch_market_price, 
                            "sell_price"        => $branch_sell_price, 
                            "create_by"         => $request->userid, 
                            "create_dtm"        => $data_now, 
                            "update_by"         => $request->userid, 
                            "update_dtm"        => $data_now,
                        ];
                        $branch_ins = DB::table('product_stock')
                                    ->insert($branch_arr);

                        $name = trim($request->name_th);
                        $arraydata = array();
                        $arraydata['ref_id'] = 0;
                        $arraydata['product_id'] = $ins;
                        $arraydata['option_id'] = $other;
                        $arraydata['product_name'] = $name;
                        $arraydata['branch_id'] = $branch_id;
                        $arraydata['movement_type'] = 'CS';
                        $arraydata['old_cost_price'] = $branch_cost_price;
                        $arraydata['new_cost_price'] = $branch_cost_price;
                        $arraydata['userID'] = $request->userid;

                        $sel = DB::table('products')->select('stock_status')->where('product_id', $ins)->first();
                        if($sel->stock_status == 'N'){
                            $stock = DB::table('product_stock')
                                    ->select('stock')
                                    ->where('product_id', $ins)
                                    ->where('option_id', $other)
                                    ->where('branch_id', $branch_id)
                                    ->first();
                            $curentstock = $stock->stock;
                            $change_stock = 0;
                            $change_stock_del = 0;
                            $change_stock_plus = 0;
                            $stock = $stock->stock;
                            if($curentstock > $stock){
                                $change_stock_del = $curentstock - $stock;
                                $change_stock = $change_stock_del;
                            }elseif($curentstock < $stock){
                                $change_stock_plus = $stock - $curentstock;
                                $change_stock = $change_stock_plus;
                            }
                            $arraydata['before_stock'] = $curentstock;
                            $arraydata['change_stock'] = $change_stock;
                            $arraydata['current_stock'] = $stock;
                            if($change_stock != 0){
                                $this->saveProductMovement($arraydata); 
                            }
                        }
                    }
                }
            }
            $product_qrcode_url = env('APP_ECOMMERCEURL_URL')."productdetail/".$ins."/".$request->name_th;
            
            $path = public_path('uploads/img/qrcode/product_qrcode'.$ins.'.svg');
            QrCode::size(300)->encoding('UTF-8')->generate($product_qrcode_url, $path);

            $return_option = 0;
            if(!empty($request->option_status) && $request->option_status == 'Y'){
                $sel_option = implode(',',$request->sel_option);
                if(!$sel_option){
                    $sel_option = NULL;
                }
                DB::table('products')->where('product_id', $ins)->update(["sel_option" => $sel_option]);
                foreach ($request->hidden_option_rowno as $key => $value) {
                    $cost_price_option = $request->input('cost_price_option'.$value)[0];
                    $market_price_option = $request->input('market_price_option'.$value)[0];
                    $sell_price_option = $request->input('sell_price_option'.$value)[0];
                    $ins_arr = [
                        "product_id"            => $ins,
                        "m_option_id"           => '', 
                        "m_option_size_id"      => '',
                        "m_option_third_id"     => '', 
                        "option_name_th"        => $request->name_th, 
                        "option_name_gb"        => $request->name_th, 
                        "barcode"               => $request->input('barcode_option'.$value)[0], 
                        "sku"                   => $request->input('sku_option'.$value)[0], 
                        "cost_price"            => $cost_price_option ? $cost_price_option : 0,  
                        "ecom_market_price"     => $market_price_option ? $market_price_option : 0,
                        "sell_price"            => $sell_price_option ? $sell_price_option : 0,
                        "weight"                => '',
                        "status_preorder"       => 'N',
                        "stock_preorder"        => '',
                        "deposit_preorder"      => '',
                        "limit_preorder"        => '',
                        "orderby"               => $request->input('orderby_option'.$value.'[]') ? $request->input('orderby_option'.$value.'[]') : '999',
                        "create_by"             => $request->userid,
                        "create_dtm"            => $data_now,
                        "update_by"             => $request->userid,
                        "update_dtm"            => $data_now
                    ];

                    if(empty($option_id)){
                        $option_id = DB::table('product_option')->insertGetId($ins_arr);
                        $return_option++;
                    }else{

                    }
                    
                    foreach($request->input('sel_m_option_id_'.$value) as $round => $seloptionID){
                        $sel_usb = DB::table('product_option_subs')
                        ->where('sub_option_id', $option_id,)
                        ->where('sub_master_option', $seloptionID)
                        ->where('sub_status', 'Y');
                        if($sel_usb->count() > 0){
                            $option_get = $sel_usb->first();
                            $arr_sub = [
                                "sub_option_choose"     => $seloptionID,
                                "sub_update_by"         => $request->userid,
                                "sub_update_by"         => $data_now
                            ];
                            $upd_sub = DB::table('product_option_subs')->where('sub_id', $option_get->sub_id)->update($arr_sub);
                        }else{
                            $arr_sub = [
                                "sub_option_id"         => $option_id,
                                "sub_master_option"     => $request->input('sel_master_option_'.$value)[$round],
                                "sub_option_choose"     => $seloptionID,
                                "sub_create_by"         => $request->userid,
                                "sub_update_by"         => $data_now
                            ];
                            
                            $ins_sub = DB::table('product_option_subs')->insert($arr_sub);
                           
                        }
                    }

                    

                    if ($key == 0) {
                        DB::table('products')
                        ->where('product_id', $ins)
                        ->update([
                            "barcode"           => $request->input('barcode_option'.$value)[0],
                            "sku"               => $request->input('sku_option'.$value)[0],
                            "cost_price"        => $request->input('cost_price_option'.$value)[0],
                            "market_price"      => $request->input('market_price_option'.$value)[0],
                            "sell_price"        => $request->input('sell_price_option'.$value)[0],
                            "update_by"         => $data_now,
                            "update_dtm"        => $request->userid
                        ]);
                    }

                    DB::table('product_option_picture')->where('option_id', $option_id)->update(["m_option_id" => $request->input('sel_m_option_id_'.$value)]);

                    $branchs = !empty($request->input('branchid_'.$value)) ? $request->input('branchid_'.$value) : array();

                    foreach ($branchs as $k => $v) {
                        $branch_id = $v;
                        $stock = !empty($request->input('stock_'.$value.$branch_id)[0]) ? $request->input('stock_'.$value.$branch_id)[0] : 0;
                        $stock_alert = !empty($request->input('stock_alert_'.$value.$branch_id)[0]) ? $request->input('stock_alert_'.$value.$branch_id)[0] : 0;
                        $scount = DB::table('product_stock')
                                    ->select('product_id')
                                    ->where('product_id', $ins)
                                    ->where('option_id', $option_id)
                                    ->where('branch_id', $branch_id)
                                    ->count();
                        if($scount == 0){
                            $op_arr = [
                                "product_id"    => $ins, 
                                "option_id"     => $option_id, 
                                "branch_id"     => $branch_id,
                                "stock"         => $stock, 
                                "stock_alert"   => $stock_alert, 
                                "create_by"     => $request->userid, 
                                "create_dtm"    => $data_now, 
                                "update_by"     => $request->userid, 
                                "update_dtm"    => $data_now
                            ];
                            $ins_op = DB::table('product_stock')->insertGetId($op_arr);

                            $arraydata = array();
                            $arraydata['ref_id'] = 0;
                            $arraydata['product_id'] = $ins;
                            $arraydata['option_id'] = $option_id;
                            $arraydata['product_name'] = $request->name_th;
                            $arraydata['branch_id'] = $branch_id;
                            $arraydata['movement_type'] = 'CS';
                            $arraydata['old_cost_price'] = $request->input('cost_price_option'.$value)[0];
                            $arraydata['new_cost_price'] = $request->input('cost_price_option'.$value)[0];
                            if ($request->stock_status == 'N') {
                                $curentstock = 0;
                                $change_stock = 0;
                                $change_stock_del = 0;
                                $change_stock_plus = 0;

                                if($curentstock > $stock){
                                    $change_stock_del = $curentstock - $stock;
                                    $change_stock = $change_stock_del;
                                }elseif($curentstock < $stock){
                                    $change_stock_plus = $stock - $curentstock;
                                    $change_stock = $change_stock_plus;
                                }

                                $arraydata['before_stock'] = $curentstock;
                                $arraydata['change_stock'] = $change_stock;
                                $arraydata['current_stock'] = $stock;
                                if($change_stock != 0){
                                    $this->saveProductMovement($arraydata);
                                }
                            }
                        }else{
                            $arraydata = array();
                            $arraydata['ref_id'] = 0;
                            $arraydata['product_id'] = $ins;
                            $arraydata['option_id'] = $option_id;
                            $arraydata['product_name'] = $request->name_th;
                            $arraydata['branch_id'] = $branch_id;
                            $arraydata['movement_type'] = 'CS';
                            $arraydata['old_cost_price'] = $request->input('cost_price_option'.$value)[0];
                            $arraydata['new_cost_price'] = $request->input('cost_price_option'.$value)[0];
                            if ($request->stock_status == 'N') {
                                $curentstock = 0;
                                $change_stock = 0;
                                $change_stock_del = 0;
                                $change_stock_plus = 0;
                                if($curentstock > $stock){
                                    $change_stock_del = $curentstock - $stock;
                                    $change_stock = $change_stock_del;
                                }elseif($curentstock < $stock){
                                    $change_stock_plus = $stock - $curentstock;
                                    $change_stock = $change_stock_plus;
                                }
                                $arraydata['before_stock'] = $curentstock;
                                $arraydata['change_stock'] = $change_stock;
                                $arraydata['current_stock'] = $stock;
                                if($change_stock != 0){
                                    $this->saveProductMovement($arraydata);
                                }
                            }
                            $upd_stock = DB::table('product_stock')
                                        ->where('product_id', $ins)
                                        ->where('option_id', $option_id)
                                        ->where('branch_id', $branch_id)
                                        ->update([
                                            "stock" => $stock, 
                                            "stock_alert" => $stock_alert, 
                                            "update_by" => $request->userid, 
                                            "update_dtm" => $data_now
                                        ]);
                        }
                    }

                    if(!empty($request->input('option_status_picture_'.$value)[0])){
						$option_status_picture = $request->input('option_status_picture_'.$value)[0];
						$option_picture_id = $request->input('option_picture_id_'.$value)[0];
						$option_picture_name_default = $request->input('option_picture_name_default_'.$value)[0];
						$option_picture_extension_default = $request->input('option_picture_extension_default_'.$value)[0];
					}else{
						$option_status_picture = 'N';
						$option_picture_id = '0';
						$option_picture_name_default = '';
						$option_picture_extension_default = '';
					}

                    if ($option_status_picture=='Y') {
                        DB::table('product_option')->where('option_id', $option_id)
                        ->update(["picture_name" => $option_picture_name_default, "picture_extension" => $option_picture_extension_default]);
                    }
                    
                    $ds = public_path('uploads/tempupload/'.$request->token.'/option_'.$value);
                    $arr_move = [
                        "m_option_id"   => $request->input('sel_m_option_id_'.$value)[0],
                        "product_id"    => $ins, 
                        "picture_name"  => 'product', 
                        "option_id"     => $option_id
                    ];

                    $datamove = $this->moveFile($ds, [1200,620,150], [null, null, 113], 'product_option_picture', $arr_move, 'data/img/product_option/images/', 'A');

                    foreach ($datamove as $k_d => $v_d) {
                        $upd_img = [
                            "picture_name"          => $v_d["picture_name"],
                            "picture_extension"     => $v_d["extension"],
                        ];
                        DB::table('product_option_picture')
                        ->where('id', $value->option_id)
                        ->update($upd_img);
                        if($k_d == 0){
                            DB::table('product_option')->where('option_id', $option_id)->update($upd_img);
                        }
                    }

                    if(is_dir($ds)){
					
					}else{
						if($option_status_picture=='Y'){
                            $selPic = DB::table('product_option_picture')->where('option_id', $option_picture_id)->select("picture_name", "picture_extension")->get();
                            foreach ($selPic as $kk => $vv) {
                                DB::table('product_option_picture')->insert([
                                    "product_id"            => $ins,
                                    "option_id"             => $option_id, 
                                    "m_option_id"           => $request->input('sel_m_option_id_'.$value)[0], 
                                    "picture_name"          => $vv->picture_name, 
                                    "picture_extension"     => $vv->picture_extension, 
                                    "orderby"               => '9999'
                                ]);
                            }
						}
					}
                }
            }
        }
        $ds = public_path('uploads/tempupload/'.$request->token.'/product_0');
        $datamove = $this->moveFile($ds, [800,150], [null, 113], 'product_picture', ["product_id" => $ins, "picture_name" => 'product'], 'data/img/product/', 'A');
        foreach ($datamove as $k_d => $v_d) {
            $upd_img = [
                "picture_name"          => $v_d["picture_name"],
                "picture_extension"     => $v_d["extension"],
                "setdefault"            => $v_d["default"],
            ];
            DB::table('product_picture')
            ->where('id', $v_d["option_id"])
            ->update($upd_img);
        }
        } catch(\Illuminate\Database\QueryException $ex){ 
        dd($ex->getMessage()); 
      }
      return ['res_code' => '00'];
    }

    function imageProductTemp(Request $request) {
        $sesid = $request->token;
        $type = $request->type;
        $ds = public_path()."/uploads/tempupload/".$sesid;
        $ds1 = public_path()."/uploads/tempupload/".$sesid.'/'.$type.'_'.$request->row;
        if(!is_dir($ds)){
            File::makeDirectory($ds);
        }
        if(!is_dir($ds1)){
            File::makeDirectory($ds1);
        }
        $file = $request->file('file');
        foreach ($file as $key => $value) {
            $extension = $value->getClientOriginalExtension();
            // $targetFile =  $ds1.'/'.$value->getClientOriginalName();  //5
            $targetFile =  $ds1.'/'.($key+1).'.'.$extension;  //5
            move_uploaded_file($value,$targetFile);
        }
        return ['res_code' => '00'];
    }

    function productEdit2($id){

        $masterOption = $this->getMtOption();
        $brand = $this->getMasterProductBrand();
        $supplier = [];
        $supp = DB::table('mt_supplier')
                ->select('supplier_id', 'name_th as name')
                ->where('active_status', 'Y');
        if($supp->count() > 0){
            foreach ($supp->get() as $key => $value) {
                $supplier[] = $value;
            }
        }
        $catagory = $this->getCatMain();
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

        $branch = [];
        $bran = DB::table('branchs')
                ->select('branch_id', 'branch_name_th' , 'active_status')
                ->where('status', 'Y')
                ->orderBy('branch_online', 'DESC');
        foreach ($bran->get() as $key => $value) {
            $branch[] = $value;
        }

        $product = DB::table('products')
                        ->where('product_id', $id)
                        ->first();

        $product->option = [];
        $option = DB::table('product_option')
                ->where('product_id', $product->product_id)
                ->get();
        foreach ($option as $key => $value) {
            $value->subs = [];
            $sub = DB::table('product_option_subs')
                    ->where('sub_option_id', $value->option_id)
                    ->get();
            foreach ($sub as $k => $v) {
                $value->subs[] = $v->sub_option_choose;
            }
            $value->picture = [];
            $pic = DB::table('product_option_picture')
                        ->where('option_id', $value->option_id)
                        ->orderBy('id', 'ASC')
                        ->get();
            foreach ($pic as $pic_key => $pic_value) {
                $value->picture[] = $pic_value;
            }

            $value->stock = [];
            $stock = DB::table('branchs')
                    ->leftJoin('product_stock', 'branchs.branch_id', 'product_stock.branch_id')
                    ->where('product_id', $product->product_id)
                    ->where('option_id', $value->option_id)
                    ->get();
            foreach ($stock as $ks => $vs) {
                $value->stock[] = $vs;
            }
            $product->option[] = $value;
        }

        $product->picture = DB::table('product_picture')
                    ->where('product_id', $id)
                    ->orderBy('setdefault', 'asc')
                    ->get();

        

        // dd($product->option);
        return view('Product.form', compact(['masterOption', 'supplier', 'brand', 'cat', 'branch', 'product']));
    }

    function productEdit($id){

        $icon_m = new Ecom_product_iconpromotion_Model();
        $icon = $icon_m->get();

        $masterOption = $this->getMtOption();
        $brand = $this->getMasterProductBrand();
        $supplier = [];
        $supp = DB::table('mt_supplier')
                ->select('supplier_id', 'name_th as name')
                ->where('active_status', 'Y');
        if($supp->count() > 0){
            foreach ($supp->get() as $key => $value) {
                $supplier[] = $value;
            }
        }
        $catagory = $this->getCatMain();
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

        

        $branch = [];
        $bran = DB::table('branchs')
                ->select('branch_id', 'branch_name_th' , 'active_status')
                ->where('status', 'Y')
                ->orderBy('branch_online', 'DESC');
        foreach ($bran->get() as $key => $value) {
            $branch[] = $value;
        }

        $product = DB::table('products')
                        ->where('product_id', $id)
                        ->first();
        $product->other_cata = [];
        $cat_other = DB::table('product_additionalcategory')
                    ->where('product_id', $product->product_id)
                    ->get();
        foreach ($cat_other as $key => $value) {
            $product->other_cata[] = $value->cat_id;
        }

        $product->picture = DB::table('product_picture')
                    ->where('product_id', $id)
                    ->orderBy('setdefault', 'asc')
                    ->get();
                    
        $product->option = [];
        $option = [];
        $optionx = DB::table('product_option')
                ->select('product_option.*', 'product_stock.stock')
                ->leftJoin('product_stock', 'product_option.option_id', 'product_stock.option_id')
                ->where('product_option.product_id', $product->product_id)
                ->where('product_option.option_status', '0')
                ->get();
        // dd($optionx);
        foreach ($optionx as $x) {
            $choose = DB::table('product_option_choose')
                    ->leftJoin('product_option_subs', 'product_option_subs.sub_id', 'product_option_choose.ch_sub_id')
                    ->where('ch_option_id', $x->option_id)
                    ->get();
            $x->choose = $choose;
            $option[] = $x;
        }
        $main_option = [];

        if($product->sel_option != ''){
            $value->choose = [];
            $value->subs = [];

            $options = explode(',', $product->sel_option);
            $opt_m = DB::table('mt_product_option')->whereIn('m_option_id', $options)->get();
            foreach ($opt_m as $key => $value) {
                $value->subs = [];
                $sub = DB::table('product_option_subs')
                        ->where('sub_product_id', $product->product_id)
                        ->where('sub_m_option_id', $value->m_option_id)
                        ->get();
                foreach ($sub as $k => $v) {
                    $value->subs[] = $v;
                }
                $main_option[] = $value;
            }
            $product->option = $main_option;
        }       
        $filter = $this->get_mt_filter('','','Y');
        $flist = [];
        $f_list = DB::table('product_filter')
                ->select('mt_filter.name_th', 'product_filter.*')
                ->leftJoin('mt_filter', 'product_filter.filter_id', 'mt_filter.filter_id')
                ->where('product_id', $product->product_id)
                ->where('filter_status', 'Y');
        if($f_list->count() > 0){
            foreach ($f_list->get() as $key => $value) {
                $sub = DB::table('mt_filter')
                        ->select('filter_id', 'name_th')
                        ->where('active_status', 'Y')
                        ->where('parent_id', $value->filter_id)
                        ->orderBy('name_th', 'ASC')
                        ->get();
                $value->subs = $sub;
                $flist[] = $value;
            }
        }

        // dd($option);
        return view('Product.form', compact(['masterOption', 'supplier', 'brand', 'cat', 'branch', 'product', 'option', 'filter', 'flist', 'icon']));
    }

    function updateProduct2(Request $request) {

        
        try { 
        $data_now = date('Y-m-d H:i:s');
        $fields = [
            "product_code"              => $request->product_code, 
            "supplier_id"               => $request->supplier_id, 
            "cat_id"                    => $request->cat_id, 
            "brand_id"                  => $request->brand_id, 
            "barcode"                   => $request->barcode,
            "sku"                       => $request->sku, 
            "cost_price"                => $request->cost_price,
            "market_price"              => $request->cost_price, 
            "sell_price"                => $request->sell_price,
            "option_status"             => $request->option_status ? 'Y' : 'N', 
            "stock_status"              => $request->stock_status ? 'Y' : 'N',
            "online_store_startdate"    => date('Y-m-d'), 
            "update_by"                 => $request->userid, 
            "update_dtm"                => $data_now,
            "description_th"            => $request->description_th,
            "description_gb"            => $request->description_en ? $request->description_en : $request->description_th,
            "name_th"                   => $request->name_th,
            "name_gb"                   => $request->name_en ? $request->name_en : $request->name_th,
            "meta_title_th"             => $request->meta_title_th,
            "meta_title_gb"             => $request->meta_title_en ? $request->meta_title_en : $request->meta_title_th,
            "meta_description_th"       => $request->meta_description_th,
            "meta_description_gb"       => $request->meta_description_en ? $request->meta_description_en : $request->meta_description_th,            
            "meta_keyword_th"           => $request->meta_keyword_th,
            "meta_keyword_gb"           => $request->meta_keyword_en ? $request->meta_keyword_en : $request->meta_keyword_th,
        ];
        // dd($fields);
        // exit();
        $ins = DB::table('products')
                ->where('product_id', $request->product_id)
                ->update($fields);

        $request->option_status = $request->option_status ? $request->option_status : "N";
        if($ins){
            if(!empty($request->other_cat_id) ){
                DB::table("product_additionalcategory")->where("product_id", $request->product_id)->delete();
                foreach ($request->other_cat_id as $key => $value) {
                    $other_cat_arr = [
                        "product_id"    => $ins, 
                        "cat_id"        => $value, 
                        "create_by"     => $request->userid, 
                        "create_dtm"    => $data_now, 
                        "update_by"     => $request->userid, 
                        "update_dtm"    => $data_now,
                    ];
                    $ins_other = DB::table('product_additionalcategory')
                                ->insert($other_cat_arr);
                }
            }
            if($request->option_status == 'N'){
                $option_id = DB::table('product_option')->where('product_id', $request->product_id)->first();
                $option_id = $option_id->option_id;
                $other_arr = [
                    "cost_price"        => $request->cost_price ? $request->cost_price : 0,
                    "market_price"      => $request->market_price ? $request->market_price : 0,
                    "sell_price"        => $request->sell_price ? $request->sell_price : 0,
                    "update_by"         => $request->userid,
                    "update_dtm"        => $data_now,
                    "option_name_th"    => $request->name_th,
                    "option_name_gb"    => $request->name_en,
                ];
                $other = DB::table('product_option')
                        ->where('option_id', $option_id)
                        ->update($other_arr);
                
                if(!empty($request->branchid)){
                    foreach ($request->branchid as $key => $value) {
                        $branch_id = $value;
                        $stock = !empty($request->input('stock'.$branch_id)) ? (int)$request->input('stock'.$branch_id) : 0;
                        $stock_alert = !empty($request->input('stock_alert'.$branch_id)) ? (int)$request->input('stock_alert'.$branch_id) : 0;
                        if($request->branch_pricesale=="Y"){
                            $branch_sell_price = !empty($request->input('branchsell_price'.$branch_id)) ? (float)$request->input('branchsell_price'.$branch_id) : 0;
                            $branch_cost_price = !empty($request->input('branchcost_price'.$branch_id)) ? (float)$request->input('branchcost_price'.$branch_id) : 0;
                            $branch_market_price = ($request->input('branchmarket_price'.$branch_id)!='') ? (float)$request->input('branchmarket_price'.$branch_id) : $branch_sell_price;
                        }else{
                            $branch_cost_price = 0;
                            $branch_market_price = 0;
                            $branch_sell_price = 0;
                        }
                        

                        if ($request->stock_status == 'N'){

                            $name = trim($request->name_th);
                            $arraydata = array();
                            $arraydata['ref_id'] = 0;
                            $arraydata['product_id'] = $ins;
                            $arraydata['option_id'] = $option_id;
                            $arraydata['product_name'] = $name;
                            $arraydata['branch_id'] = $branch_id;
                            $arraydata['movement_type'] = 'CS';
                            $arraydata['old_cost_price'] = $branch_cost_price;
                            $arraydata['new_cost_price'] = $branch_cost_price;
                            $arraydata['userID'] = $request->userid;

                            $stock = DB::table('product_stock')
                                    ->select('stock')
                                    ->where('product_id', $ins)
                                    ->where('option_id', $option_id)
                                    ->where('branch_id', $branch_id)
                                    ->first();
                            $curentstock = $stock->stock;
                            $change_stock = 0;
                            $change_stock_del = 0;
                            $change_stock_plus = 0;
                            $stock = $stock->stock;
                            if($curentstock > $stock){
                                $change_stock_del = $curentstock - $stock;
                                $change_stock = $change_stock_del;
                            }elseif($curentstock < $stock){
                                $change_stock_plus = $stock - $curentstock;
                                $change_stock = $change_stock_plus;
                            }
                            $arraydata['before_stock'] = $curentstock;
                            $arraydata['change_stock'] = $change_stock;
                            $arraydata['current_stock'] = $stock;
                            if($change_stock != 0){
                                $this->saveProductMovement($arraydata); 
                            }
                        }

                        $cnt = DB::table('product_stock')
                                ->where('product_id', $request->product_id)
                                ->where('option_id', $option_id)
                                ->where('branch_id', $branch_id)
                                ->count();

                        $branch_arr = [
                            'stock'             => $stock, 
                            "stock_alert"       => $stock_alert, 
                            "cost_price"        => $branch_cost_price, 
                            "market_price"      => $branch_market_price, 
                            "sell_price"        => $branch_sell_price,
                            "update_by"         => $request->userid, 
                            "update_dtm"        => $data_now,
                        ];
                        if($cnt == 0){
                            $branch_arr["product_id"]   = $request->product_id;
                            $branch_arr["option_id"]    = $option_id;
                            $branch_arr["branch_id"]    = $branch_id;
                            $branch_arr["create_by"]    = $request->userid;
                            $branch_arr["create_dtm"]   = $data_now;
                            DB::table('product_stock')->insert($branch_arr);
                        }else{
                            DB::table('product_stock')
                            ->where('product_id', $request->product_id)
                            ->where('option_id', $option_id)
                            ->where('branch_id', $branch_id)
                            ->update($branch_arr);
                        }
                    }
                }
            }

            $product_qrcode_url = env('APP_ECOMMERCEURL_URL')."productdetail/".$ins."/".$request->name_th;
            
            $path = public_path('uploads/img/qrcode/product_qrcode'.$ins.'.svg');
            QrCode::size(300)->encoding('UTF-8')->generate($product_qrcode_url, $path);

            $return_option = 0;
            if(!empty($request->option_status) && $request->option_status == 'Y'){
                
                if(!$request->sel_option){
                    $sel_option = NULL;
                }else{
                    $sel_option = implode(',',$request->sel_option);
                }
                DB::table('products')->where('product_id', $ins)->update(["sel_option" => $sel_option]);
                foreach ($request->hidden_option_rowno as $key => $value) {
                    $cost_price_option = $request->input('cost_price_option'.$value)[0];
                    $market_price_option = $request->input('market_price_option'.$value)[0];
                    $sell_price_option = $request->input('sell_price_option'.$value)[0];
                    $option_id = $request->input('option_id'.$value) ? $request->input('option_id'.$value) : '';

                    $ins_arr = [
                        "option_name_th"        => $request->name_th, 
                        "option_name_gb"        => $request->name_th, 
                        "barcode"               => $request->input('barcode_option'.$value)[0], 
                        "sku"                   => $request->input('sku_option'.$value)[0], 
                        "cost_price"            => $cost_price_option ? $cost_price_option : 0,  
                        "ecom_market_price"     => $market_price_option ? $market_price_option : 0,
                        "sell_price"            => $sell_price_option ? $sell_price_option : 0,
                        "orderby"               => $request->input('orderby_option'.$value.'[]') ? $request->input('orderby_option'.$value.'[]') : '999',
                        "update_by"             => $request->userid,
                        "update_dtm"            => $data_now
                    ];

                    if($option_id == ''){
                        $ins_arr["product_id"]      = $request->product_id;
                        $ins_arr["status_preorder"] = "N";
                        $ins_arr["create_by"]       = $request->userid;
                        $ins_arr["create_dtm"]      = $data_now;
                        $option_id = DB::table('product_option')->insertGetId($ins_arr);
                    }else{
                        $opt_upd = DB::table('product_option')->where('option_id', $option_id)->update($ins_arr);
                    }

                    
                    if(!empty($request->input('sel_m_option_id_'.$value))){
                        foreach($request->input('sel_m_option_id_'.$value) as $round => $seloptionID){
                            $sel_usb = DB::table('product_option_subs')
                            ->where('sub_option_id', $option_id)
                            ->where('sub_master_option', $seloptionID)
                            ->where('sub_status', 'Y');
                            if($sel_usb->count() > 0){
                                $option_get = $sel_usb->first();
                                $arr_sub = [
                                    "sub_option_choose"     => $seloptionID,
                                    "sub_update_by"         => $request->userid,
                                    "sub_update_by"         => $data_now
                                ];
                                $upd_sub = DB::table('product_option_subs')->where('sub_id', $option_get->sub_id)->update($arr_sub);
                            }else{
                                $arr_sub = [
                                    "sub_option_id"         => $option_id,
                                    "sub_master_option"     => $request->input('sel_master_option_'.$value)[$round],
                                    "sub_option_choose"     => $seloptionID,
                                    "sub_create_by"         => $request->userid,
                                    "sub_update_by"         => $data_now
                                ];
                                
                                $ins_sub = DB::table('product_option_subs')->insert($arr_sub);
                               
                            }
                        }
                    }
                    

                    

                    if ($key == 0) {
                        DB::table('products')
                        ->where('product_id', $request->product_id)
                        ->update([
                            "barcode"           => $request->input('barcode_option'.$value)[0],
                            "sku"               => $request->input('sku_option'.$value)[0],
                            "cost_price"        => $request->input('cost_price_option'.$value)[0],
                            "market_price"      => $request->input('market_price_option'.$value)[0],
                            "sell_price"        => $request->input('sell_price_option'.$value)[0],
                            "update_by"         => $data_now,
                            "update_dtm"        => $request->userid
                        ]);
                    }

                    DB::table('product_option_picture')->where('option_id', $option_id)->update(["m_option_id" => $request->input('sel_m_option_id_'.$value)]);

                    $branchs = !empty($request->input('branchid_'.$value)) ? $request->input('branchid_'.$value) : array();

                    foreach ($branchs as $k => $v) {
                        $branch_id = $v;
                        $stock = !empty($request->input('stock_'.$value.$branch_id)[0]) ? $request->input('stock_'.$value.$branch_id)[0] : 0;
                        $stock_alert = !empty($request->input('stock_alert_'.$value.$branch_id)[0]) ? $request->input('stock_alert_'.$value.$branch_id)[0] : 0;
                        $scount = DB::table('product_stock')
                                    ->select('product_id')
                                    ->where('product_id', $request->product_id)
                                    ->where('option_id', $option_id)
                                    ->where('branch_id', $branch_id)
                                    ->count();
                        if($scount == 0){
                            $op_arr = [
                                "product_id"    => $request->product_id, 
                                "option_id"     => $option_id, 
                                "branch_id"     => $branch_id,
                                "stock"         => $stock, 
                                "stock_alert"   => $stock_alert, 
                                "create_by"     => $request->userid, 
                                "create_dtm"    => $data_now, 
                                "update_by"     => $request->userid, 
                                "update_dtm"    => $data_now
                            ];
                            $ins_op = DB::table('product_stock')->insertGetId($op_arr);

                            $arraydata = array();
                            $arraydata['ref_id'] = 0;
                            $arraydata['product_id'] = $request->product_id;
                            $arraydata['option_id'] = $option_id;
                            $arraydata['product_name'] = $request->name_th;
                            $arraydata['branch_id'] = $branch_id;
                            $arraydata['movement_type'] = 'CS';
                            $arraydata['old_cost_price'] = $request->input('cost_price_option'.$value)[0];
                            $arraydata['new_cost_price'] = $request->input('cost_price_option'.$value)[0];
                            if ($request->stock_status == 'N') {
                                $curentstock = 0;
                                $change_stock = 0;
                                $change_stock_del = 0;
                                $change_stock_plus = 0;

                                if($curentstock > $stock){
                                    $change_stock_del = $curentstock - $stock;
                                    $change_stock = $change_stock_del;
                                }elseif($curentstock < $stock){
                                    $change_stock_plus = $stock - $curentstock;
                                    $change_stock = $change_stock_plus;
                                }

                                $arraydata['before_stock'] = $curentstock;
                                $arraydata['change_stock'] = $change_stock;
                                $arraydata['current_stock'] = $stock;
                                if($change_stock != 0){
                                    $this->saveProductMovement($arraydata);
                                }
                            }
                        }else{
                            $arraydata = array();
                            $arraydata['ref_id'] = 0;
                            $arraydata['product_id'] = $request->product_id;
                            $arraydata['option_id'] = $option_id;
                            $arraydata['product_name'] = $request->name_th;
                            $arraydata['branch_id'] = $branch_id;
                            $arraydata['movement_type'] = 'CS';
                            $arraydata['old_cost_price'] = $request->input('cost_price_option'.$value)[0];
                            $arraydata['new_cost_price'] = $request->input('cost_price_option'.$value)[0];
                            if ($request->stock_status == 'N') {
                                $curentstock = 0;
                                $change_stock = 0;
                                $change_stock_del = 0;
                                $change_stock_plus = 0;
                                if($curentstock > $stock){
                                    $change_stock_del = $curentstock - $stock;
                                    $change_stock = $change_stock_del;
                                }elseif($curentstock < $stock){
                                    $change_stock_plus = $stock - $curentstock;
                                    $change_stock = $change_stock_plus;
                                }
                                $arraydata['before_stock'] = $curentstock;
                                $arraydata['change_stock'] = $change_stock;
                                $arraydata['current_stock'] = $stock;
                                if($change_stock != 0){
                                    $this->saveProductMovement($arraydata);
                                }
                            }
                            $upd_stock = DB::table('product_stock')
                                        ->where('product_id', $request->product_id)
                                        ->where('option_id', $option_id)
                                        ->where('branch_id', $branch_id)
                                        ->update([
                                            "stock" => $stock, 
                                            "stock_alert" => $stock_alert, 
                                            "update_by" => $request->userid, 
                                            "update_dtm" => $data_now
                                        ]);
                        }
                    }

                    if(!empty($request->input('option_status_picture_'.$value)[0])){
						$option_status_picture = $request->input('option_status_picture_'.$value)[0];
						$option_picture_id = $request->input('option_picture_id_'.$value)[0];
						$option_picture_name_default = $request->input('option_picture_name_default_'.$value)[0];
						$option_picture_extension_default = $request->input('option_picture_extension_default_'.$value)[0];
					}else{
						$option_status_picture = 'N';
						$option_picture_id = '0';
						$option_picture_name_default = '';
						$option_picture_extension_default = '';
					}

                    if ($option_status_picture=='Y') {
                        DB::table('product_option')->where('option_id', $option_id)
                        ->update(["picture_name" => $option_picture_name_default, "picture_extension" => $option_picture_extension_default]);
                    }
                    
                    $ds = public_path('uploads/tempupload/'.$request->token.'/option_'.$value);

                    if(is_dir($ds)){
                        $arr_move = [
                            "m_option_id"   => $request->input('sel_m_option_id_'.$value)[0],
                            "product_id"    => $request->product_id, 
                            "picture_name"  => 'product', 
                            "option_id"     => $option_id
                        ];
                        $datamove = $this->moveFile($ds, [1200,620,150], [null, null, 113], 'product_option_picture', $arr_move, 'data/img/product_option/images/', 'A');

                        foreach ($datamove as $k_d => $v_d) {
                            $upd_img = [
                                "picture_name"          => $v_d["picture_name"],
                                "picture_extension"     => $v_d["extension"],
                            ];
                            DB::table('product_option_picture')
                            ->where('id', $value->option_id)
                            ->update($upd_img);
                            if($k_d == 0){
                                DB::table('product_option')->where('option_id', $option_id)->update($upd_img);
                            }
                        }
					}else{
						if($option_status_picture=='Y'){
                            $selPic = DB::table('product_option_picture')->where('option_id', $option_picture_id)->select("picture_name", "picture_extension")->get();
                            foreach ($selPic as $kk => $vv) {
                                DB::table('product_option_picture')->insert([
                                    "product_id"            => $request->product_id,
                                    "option_id"             => $option_id, 
                                    "m_option_id"           => $request->input('sel_m_option_id_'.$value)[0], 
                                    "picture_name"          => $vv->picture_name, 
                                    "picture_extension"     => $vv->picture_extension, 
                                    "orderby"               => '9999'
                                ]);
                            }
						}
					}
                }
            }
        }
        $ds = public_path('uploads/tempupload/'.$request->token.'/product_0');
        $datamove = $this->moveFile($ds, [800,150], [null, 113], 'product_picture', ["product_id" => $request->product_id, "picture_name" => 'product'], 'data/img/product/', 'A');
        foreach ($datamove as $k_d => $v_d) {
            $upd_img = [
                "picture_name"          => $v_d["picture_name"],
                "picture_extension"     => $v_d["extension"],
                "setdefault"            => $v_d["default"],
            ];
            DB::table('product_picture')
            ->where('id', $v_d["option_id"])
            ->update($upd_img);
        }
        } catch(\Illuminate\Database\QueryException $ex){ 
        dd($ex->getMessage()); 
      }
      return ['res_code' => '00'];
    }

    function updateProduct(Request $request) {
        
        try { 
        DB::beginTransaction();
        // $sel_option = implode(',',$request->sel_option);
        // if(!$sel_option){
        //     $sel_option = NULL;
        // }
        if(isset($request->sel_option)){
            $sel_option = implode(',',$request->sel_option);
            if(!$sel_option){
                $sel_option = NULL;
            }
        }else{
            $sel_option = NULL;
        }

        $fields = [
            "product_code"              => $request->product_code, 
            "supplier_id"               => $request->supplier_id, 
            "cat_id"                    => $request->cat_id, 
            "brand_id"                  => $request->brand_id, 
            "barcode"                   => $request->barcode,
            "sku"                       => $request->sku, 
            "cost_price"                => $request->cost_price ? $request->cost_price : 0,
            "market_price"              => $request->cost_price ? $request->cost_price : 0, 
            "sell_price"                => $request->sell_price ? $request->sell_price : 0, 
            "sel_option"                => $sel_option, 
            "option_status"             => 'N',
            "stock_status"              => $request->stock_status ? 'Y' : 'N',
            "ecom_status"               => isset($request->ecom_status) ? $request->ecom_status : 'N', 
            'active_status'             => isset($request->active_status) ? $request->active_status : 'N', 
            'visibility_online_store'   => $request->visibility_online_store ? $request->visibility_online_store : "N",
            "update_by"                 => $request->userid, 
            "update_dtm"                => $this->data_now,
            "description_th"            => $request->description_th,
            "description_gb"            => $request->description_en ? $request->description_en : $request->description_th,
            "name_th"                   => $request->name_th,
            "name_gb"                   => $request->name_en ? $request->name_en : $request->name_th,
            "meta_title_th"             => $request->meta_title_th,
            "meta_title_gb"             => $request->meta_title_en ? $request->meta_title_en : $request->meta_title_th,
            "meta_description_th"       => $request->meta_description_th,
            "meta_description_gb"       => $request->meta_description_en ? $request->meta_description_en : $request->meta_description_th,            
            "meta_keyword_th"           => $request->meta_keyword_th,
            "meta_keyword_gb"           => $request->meta_keyword_en ? $request->meta_keyword_en : $request->meta_keyword_th,
            "product_type_option"       => $request->optionsType ? $request->optionsType : 'N',
            "product_type_price"        => $request->priceType ? $request->priceType : 'N',
            "product_related_type"      => $request->recomment ? $request->recomment : '',
        ];
        // dd($fields);
        // exit();
        DB::table('products')
        ->where('product_id', $request->product_id)
        ->update($fields);

        $product_id = $request->product_id;
        if($product_id){

                if(!empty($request->other_cat_id) ){
                    DB::table('product_additionalcategory')->where('product_id', $product_id)->delete();
                    foreach ($request->other_cat_id as $key => $value) {
                        $other_cat_arr = [
                            "product_id"    => $product_id, 
                            "cat_id"        => $value, 
                            "create_by"     => $request->userid, 
                            "create_dtm"    => $this->data_now, 
                            "update_by"     => $request->userid, 
                            "update_dtm"    => $this->data_now,
                        ];
                        $ins_other = DB::table('product_additionalcategory')
                                    ->insert($other_cat_arr);
                    }
                }
                
                $arr_o = [];

                DB::table('product_option')->where('product_id', $product_id)->update(['option_status' => '1']);

                if($request->optionsType == 'N'){
                    $option_arr = [
                        "product_id"        => $product_id,
                        "barcode"           => $request->barcode,
                        "sku"               => $request->sku,
                        "cost_price"        => $request->cost_price ? $request->cost_price : 0,
                        "market_price"      => $request->market_price ? $request->market_price : 0,
                        "sell_price"        => $request->sell_price ? $request->sell_price : 0,
                        "limit_preorder"    => $request->barcode,
                        "update_by"         => $request->userid, 
                        "update_dtm"        => $this->data_now,
                        "option_status"     => '1',
                    ];
                    // $op_id = DB::table('product_option')
                    //             ->insertGetId($option_arr);
                    // $option_arr['option_id']    = $op_id;
                    // $option_arr['stock']        = $request->stock;
                    // $option_arr['stock_alert']  = $request->stock_alert;
                    // $option_arr['cost_price']   = $request->cost_price;
                    // $option_arr['market_price'] = $request->market_price;
                    // $option_arr['sell_price']   = $request->sell_price;
                    // $arr_o[] = $option_arr;
                }else{
                    $option_of_sub = [];
                    foreach ($request->sel_option as $key => $value) {
                        $sort = 1;
                        DB::table('product_option_subs')->where('sub_product_id', $product_id)->update(['sub_status' => 1]);
                        foreach ($request->input('option_text'.$value) as $k => $v) {
                            $sub = [
                                "sub_m_option_id"   => $value,
                                "sub_text"          => $v,
                                "sub_sort"          => $sort,
                                "sub_create_user"   => $request->userid,
                                "sub_create_at"     => $this->data_now,
                                "sub_update_user"   => $request->userid,
                                "sub_update_at"     => $this->data_now,
                                "sub_status"        => '0'
                            ];
                            if($request->input('sub_id'.$value)[$k] != ''){
                                $sub_id = $request->input('sub_id'.$value)[$k];
                                DB::table('product_option_subs')->where('sub_id', $sub_id)->update($sub);
                            }else{
                                $sub_id = DB::table('product_option_subs')->insertGetId($sub);
                            }
                            
                            if($sub_id){
                                $option_of_sub[] = [
                                    "sub_id"    => $sub_id,
                                    "sub_text"  => $v,
                                    "m_option"  => $value,
                                ];
                                if(isset($request->file('img-option-'.$value)[$k])){
                                    echo 'img-option-'.$value;
                                    $file = $request->file('img-option-'.$value)[$k];
                                    $pt = public_path()."/data/img/product_option/images/";
                                    if(!is_dir($pt)){
                                        File::makeDirectory($pt);
                                    }
                                    $extension = $file->getClientOriginalExtension();
                                    $targetFile =  $pt.'option'.$sub_id.'.'.$extension;
                                    move_uploaded_file($file,$targetFile);
    
                                    $image = imagecreatefromstring(file_get_contents($targetFile));
                                    $exif = @exif_read_data($targetFile);
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
                                    imagejpeg($image, $targetFile, 150);
    
                                    $arraydot = explode('.',$targetFile);
                                    
                                    $picid = DB::table('product_option_subs')
                                            ->where('sub_id', $sub_id)
                                            ->update(['sub_path_img' => 'option'.$sub_id.'_150.'.$extension]);
    
                                    $this->resize(150, null, $targetFile, $pt.'/option'.$sub_id.'_150.'.$extension);
                                    $sumsize = 0;
                                    if(is_file($pt.'/option'.$sub_id.'_150.'.$extension)){
                                        $sumsize += (int)@filesize($pt.'/option'.$sub_id.'_150.'.$extension);
                                    }
                                    if($sumsize > 0){
                                        $this->saveFilesize($sumsize,'A');
                                    }
                                    @unlink($targetFile);
                                } 
                            }
                            $sort++;
                        }
                    }

                    if(!empty($option_of_sub)){

                        foreach ($request->barcode_option as $key => $value) {
                            $opt_arr = [
                                "product_id"            => $product_id,
                                "barcode"               => $value, 
                                "sku"                   => $request->input('sku_option')[$key], 
                                "cost_price"            => $request->input('cost_option')[$key] ? $request->input('cost_option')[$key] : 0,
                                "sell_price"            => $request->input('sell_option')[$key] ? $request->input('sell_option')[$key] : 0,
                                "create_by"             => $request->userid,
                                "create_dtm"            => $this->data_now,
                                "update_by"             => $request->userid,
                                "update_dtm"            => $this->data_now,
                                "option_view"           => isset($request->input('webshow')[$key]) ? $request->input('webshow')[$key] : 0,
                                "option_status"         => '0',
                            ];

                            if($request->input('option_id')[$key] != ''){
                                $op_id = $request->input('option_id')[$key];
                                DB::table('product_option')->where('option_id', $op_id)->update($opt_arr);
                            }else{
                                $op_id = DB::table('product_option')
                                    ->insertGetId($opt_arr);
                            }
                            
                            $option_arr['option_id']    = $op_id;
                            $option_arr['stock']        = $request->input('quatity_option')[$key];
                            $option_arr['cost_price']   = $request->input('cost_option')[$key];
                            $option_arr['market_price'] = $request->input('sell_option')[$key];
                            $option_arr['sell_price']   = $request->input('sell_option')[$key];
                            $arr_o[] = $option_arr;
                            
                            $sort = 1;
                            DB::table('product_option_choose')->where('ch_option_id', $op_id)->update(['ch_status' => '1']);
                            // dd($request->sel_option);
                            foreach ($request->sel_option as $k => $v) {
                                $var1 = $request->input('option_table'.$v)[$key];
                                $var2 = $v;
                                $filtered_array = array_values(array_filter($option_of_sub, function($val) use($var1, $var2){
                                    return ($val['sub_text'] == $var1 && $val['m_option'] == $var2);
                                }));
                                
                                if($filtered_array){
                                    // print_r($op_id);
                                    $arr = [
                                        "ch_option_id"  => $op_id,
                                        "ch_sub_id"     => $filtered_array[0]["sub_id"],
                                        "ch_sort"       => $sort,
                                        "ch_status"     => 0,
                                        "ch_create_at"  => $this->data_now,
                                    ];
                                    if($request->input('ch_id'.$v)[$key] != ''){
                                        DB::table('product_option_choose')->where('ch_id', $request->input('ch_id'.$v)[$key])->update($arr);
                                    }else{
                                        DB::table('product_option_choose')->insert($arr);
                                    }
                                    $sort++;
                                }
                            }
                        }
                    }
                }

                if(count($arr_o) > 0){
                    foreach ($arr_o as $key => $value) {
                        if(isset($value["option_id"]) && $value["option_id"] != ''){
                            $before_stock = 0;
                            $stock_id = "";
                            $befor = DB::table('product_stock')
                                    ->where('option_id', $value["option_id"]);
                            if($befor->count() > 0){
                                $befor_d = $befor->first();
                                $before_stock = $befor_d->stock;
                                $stock_id = $befor_d->stock_id;
                            }
                            $stock_arr = [
                                "product_id"        => $product_id, 
                                "option_id"         => $value["option_id"], 
                                "branch_id"         => $this->branch_id, 
                                'stock'             => $value["stock"],  
                                // "stock_alert"       => $value["stock_alert"], 
                                "cost_price"        => $value["cost_price"], 
                                "market_price"      => $value["market_price"], 
                                "sell_price"        => $value["sell_price"], 
                                "create_by"         => $request->userid, 
                                "create_dtm"        => $this->data_now, 
                                "update_by"         => $request->userid, 
                                "update_dtm"        => $this->data_now,
                            ];
                            if($stock_id != ''){
                                DB::table('product_stock')
                                ->where('stock_id', $stock_id)
                                ->update($stock_arr);
                            }else{
                                $stock_id = DB::table('product_stock')
                                            ->insert($stock_arr);
                            }
                            

                            if($request->stock_status == 'N'){
                                $name = trim($request->name_th);
                                $arraydata = [
                                    'ref_id'            => 0,
                                    'product_id'        => $product_id,
                                    'option_id'         => $value["option_id"],
                                    'product_name'      => $name,
                                    'branch_id'         => $this->branch_id,
                                    'movement_type'     => 'CS',
                                    'old_cost_price'    => $value["cost_price"],
                                    'new_cost_price'    => $value["cost_price"],
                                    'userID'            => $request->userid,
                                    'before_stock'      => $before_stock,
                                    'change_stock'      => $value["stock"],
                                    'current_stock'     => $value["stock"],
                                ];
                                $this->saveProductMovement($arraydata);
                            }
                        }
                    }
                }

                if(isset($request->filter)){
                    DB::table('product_filter')->where('product_id', $product_id)->update(["filter_status" => "N"]);
                    foreach ($request->filter as $key => $value) {
                        $subs = "";
                        if(!empty($request->input('select-filter-'.$value))){
                            foreach ($request->input('select-filter-'.$value) as $k => $v) {
                                $subs .= $v.",";
                            }
                        }
                        if($request->product_filter[$key] != ''){
                            DB::table('product_filter')
                            ->where('id', $request->product_filter[$key])
                            ->update([
                                "filter_status"     => "Y", 
                                "filter_sub_id"     => $subs,
                                "update_by"         => $request->userid, 
                                "update_dtm"        => $this->data_now
                            ]);
                        }else{
                            DB::table('product_filter')->insert([
                                "product_id"        => $product_id,
                                "filter_id"         => $value,
                                "filter_sub_id"     => $subs,
                                "create_by"         => $request->userid, 
                                'create_dtm'        => $this->data_now, 
                                "update_by"         => $request->userid, 
                                "update_dtm"        => $this->data_now
                            ]);
                        }
                        
                    }
                }
                
                $product_qrcode_url = env('APP_ECOMMERCEURL_URL')."productdetail/".$product_id."/".$request->name_th;
            
                $path = public_path('uploads/img/qrcode/product_qrcode'.$product_id.'.svg');
                QrCode::size(300)->encoding('UTF-8')->generate($product_qrcode_url, $path);

                $ds = public_path('uploads/tempupload/'.$request->token.'/product_0');
                $datamove = $this->moveFile($ds, [800,150], [null, 113], 'product_picture', ["product_id" => $product_id, "picture_name" => 'product'], 'data/img/product/', 'A');
                foreach ($datamove as $k_d => $v_d) {
                    $upd_img = [
                        "picture_name"          => $v_d["picture_name"],
                        "picture_extension"     => $v_d["extension"],
                        "setdefault"            => $v_d["default"],
                    ];
                    DB::table('product_picture')
                    ->where('id', $v_d["option_id"])
                    ->update($upd_img);
                }
            }
            if(isset($request->recomment) && $request->recomment == 3){
                DB::table('product_speccategory')->where('product_id', $product_id)->delete();
                foreach ($request->recoment_cat as $key => $value) {
                    DB::table('product_speccategory')
                    ->insert([
                        "product_id"    => $product_id,
                        "cat_id"        => $value,
                        "create_by"     => $request->userid,
                        "create_dtm"    => $this->data_now,
                        "update_by"     => $request->userid,
                        "update_dtm"    => $this->data_now
                    ]);
                }
            }
            DB::commit();
        } catch(\Illuminate\Database\QueryException $ex){ 
            DB::rollback();
            dd($ex->getMessage()); 
      }
      return ['res_code' => '00'];
    }

    function productStatus(Request $request) {
        $user = session()->get('user')->user_id;
        try {
            // $request->type 0 = view , 1 = status
            DB::beginTransaction();
            if($request->type == '1'){
                DB::table('products')->where('product_id', $request->productid)->update(["status" => "N"]);
            }else if($request->type == '0'){
                DB::table('products')->where('product_id', $request->productid)->update(["active_status" => $request->status]);
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

    function uploadimagemce(Request $request) {
        $accepted_origins = array("http://localhost", "http://192.168.1.1", "http://example.com");
        $imageFolder = public_path()."/data/img/product";
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $targetFile =  $imageFolder.'/19102023.'.$extension;
        if(move_uploaded_file($file,$targetFile)){
            echo json_encode(array('location' => '/data/img/product/19102023.'.$extension));
        }
    }

    
}
