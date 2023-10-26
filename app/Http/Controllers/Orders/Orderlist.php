<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\DataCenter;
use Mail;
use App\Mail\SendMail;
use PDF;

class Orderlist extends Controller
{
    function orderlist()
    {
        $perchk = $this->checkRole('35', 'VIEW');
        if ($perchk == 0) {
            return view('Pageerror.notpermission');
        }
        return view('Orders.index');
    }

    function orderview($id)
    {
        $order_data = "";
        $address = "";
        $taxaddr = "";

        $perchk = $this->checkRole('35', 'VIEW');
        if (empty($perchk)) {
            return view('Pageerror.notpermission');
        }
        $update = $this->checkRole("35", "UPDATE");

        $order = DB::table('ecom_order')
            ->select('*')
            ->where('order_id', $id);
        if ($order->count() > 0) {
            $order_data = $order->first();
            $itemlist = [];
            $adr = DB::table('ecom_order_address')
                ->select('*')
                ->where('order_id', $order_data->order_id);
            if ($adr->count() > 0) {
                foreach ($adr->get() as $key => $value) {
                    if ($value->address_type == "2") {
                        $taxaddr = $value;
                    } else {
                        $address = $value;
                    }
                }
            }

            $item = DB::table('ecom_order_item')
                ->select('*')
                ->leftJoin('products', 'ecom_order_item.product_id', 'products.product_id')
                ->where('order_id', $order_data->order_id);
            if ($adr->count() > 0) {
                $itemlist = [];
                foreach ($item->get() as $key => $value) {
                    $pic = DB::table('product_option')
                        ->select('*')
                        ->where('option_id', $value->option_id)
                        ->whereNotNull('picture_name');
                    if ($pic->count() > 0) {
                        $image = $pic->first();
                        $value->picture = "/uploads/img/product_option/" . $image->picture_name . "150" . $image->picture_extension;
                    } else {
                        $pic2 = DB::table('product_picture')
                            ->select('*')
                            ->where('product_id', $value->product_id);
                        if ($pic2->count() > 0) {
                            $image = $pic2->first();
                            $value->picture = "/uploads/img/product/" . $image->picture_name . "150" . $image->picture_extension;
                        }
                    }
                    $itemlist[] = $value;
                }
            }

            $customer = [];
            if ($order_data->customer_id == '9999') {
                $customer = (object)[
                    "email"         => $address->email,
                    "firstname"     => $address->firstname,
                    "lastname"      => $address->lastname,
                    "phone"         => $address->mobile,
                    "customer_id"   => "",
                ];
            } else {
                $cus = DB::table('customer')
                    ->select('*')
                    ->where('customer_id', $order_data->customer_id);
                if ($cus->count() > 0) {
                    $get_cus = $cus->first();
                    $customer = (object)[
                        "email"         => $get_cus->email,
                        "firstname"     => $get_cus->firstname,
                        "lastname"      => $get_cus->lastname,
                        "phone"         => ($get_cus->mobile != '' ? $get_cus->mobile : $get_cus->phone),
                        "customer_id"   => $get_cus->customer_id,
                        "photo"         => $get_cus->customer_id
                    ];
                }
            }
        }

        return view('Orders.view', compact(["order_data", "address", "taxaddr", "itemlist", "customer", "update"]));
    }


    function saveTracking(Request $request)
    {
        $arr = ['tracking_no' => $request->tracking];
        $sel = DB::table('ecom_order')
            ->where('order_id', $request->order_id)
            ->first();
        if($sel->order_status != 'Completed' && $sel->order_status != 'Cancel'){
            $arr['order_status'] = 'Shipped';
        }
        $upd = DB::table('ecom_order')
            ->where('order_id', $request->order_id)
            ->update($arr);

        $this->sendMailUpdateOrder('Shipped', $sel->customer_id, $request->order_id);
        return ["res_code" => "00"];
    }

    function getOrderList(Request $request)
    {

        $search         = $request->search;
        $daterang       = $request->daterang;
        $status         = $request->status;
        $payment        = $request->payment;
        $group_id       = $request->group_id;

        $sort           = ($request->sort == '' ? 'order_id' : $request->sort);
        $orderby        = ($request->order == '' ? 'DESC' : $request->order);
        $offset         = $request->offset;
        $limit          = $request->limit;

        $update = $this->checkRole("35", "UPDATE", $group_id);
        $delete = $this->checkRole("35", "DELETE", $group_id);
        $view = $this->checkRole("35", "VIEW", $group_id);

        if(!empty($view)){
            $order = DB::table('ecom_order')
            ->select(
                'ecom_order.order_id',
                'ecom_order.order_number',
                'ecom_order.order_shipping_name',
                'ecom_order.order_status',
                'ecom_order.create_dtm',
                'ecom_order.order_price',
                'ecom_order.order_payment_name',
                'ecom_order_address.firstname',
                'ecom_order_address.lastname'
            )
            ->leftJoin('customer', 'ecom_order.customer_id', 'customer.customer_id')
            ->leftJoin('ecom_order_address', 'ecom_order.order_id', 'ecom_order_address.order_id');
            // $order->where('address_type', '1');

            if ($status != "") {
                $order->where('order_status', $status);
            }
            if ($search != "") {
                $order->where(function ($query) use ($search) {
                    $query->where('order_number', 'LIKE', "%$search%")
                        ->orWhere('customer.customer_code', 'LIKE', "%$search%")
                        ->orWhere('customer.customer_name', 'LIKE', "%$search%")
                        ->orWhere('customer.firstname', 'LIKE', "%$search%")
                        ->orWhere('customer.lastname', 'LIKE', "%$search%")
                        ->orWhere('customer.email', 'LIKE', "%$search%");
                });
            }
            if ($payment != "all") {
                $order->where('order_payment_id', $payment);
            }
            if ($daterang != "") {
                $daterang =  str_replace("/", "-", $daterang);
                $date1 =     date('Y-m-d', strtotime(explode(' - ', $daterang)[0]));
                $date2 =     date('Y-m-d', strtotime(explode(' - ', $daterang)[1]));
                $order->where('ecom_order.create_dtm', '>=', $date1);
                $order->where('ecom_order.create_dtm', '<=', $date2);
            }
            $rows_number = $order->count();
            $order->orderBy($sort, $orderby);
            $order->skip($offset)->take($limit);
            // echo getStatusName($value->order_status);
            $output = [];
            foreach ($order->get() as $key => $value) {

                switch ($value->order_status) {
                    case 'Progress':
                        $class = 'bg-warning-subtle text-warning';
                        break;
                    case 'Pending':
                        $class = 'bg-danger-subtle text-danger';
                        break;
                    case 'Paid':
                        $class = 'bg-secondary-subtle text-secondary';
                        break;
                    case 'Paid_D':
                        $class = 'bg-dark-subtle text-dark';
                        break;
                    case 'Waiting_D':
                        $class = 'bg-warning-subtle text-warning';
                        break;
                    case 'Shipped':
                        $class = 'bg-info-subtle text-info';
                        break;
                    case 'Completed':
                        $class = 'bg-success-subtle text-success';
                        break;
                    case 'Cancel':
                        $class = 'bg-danger-subtle text-danger';
                        break;
                    default:
                        $class = 'bg-warning-subtle text-warning';
                        break;
                }
                $value->create_dtm      = $this->DateThai($value->create_dtm);
                $value->order_status    = $this->getStatusName($value->order_status);
                $value->fullname        = $value->firstname . ' ' . $value->lastname;
                $value->order_price     = number_format($value->order_price, 2);
                $value->order_status    = '<span class="badge ' . $class . ' text-uppercase">' . $value->order_status . '</span>';
                $value->manager         = '';
                if(!empty($update)){
                    $value->manager    .= '<a href="orderview/' . $value->order_id . '" class="text-primary d-inline-block edit-item-btn"><i class="ri-pencil-fill fs-16"></i></a>';
                }else if(!empty($view)){
                    $value->manager    .= '<a href="orderview/' . $value->order_id . '" class="text-primary d-inline-block"><i class="ri-eye-fill fs-16"></i></a>';
                }


                if(!empty($delete)){
                    $value->manager         .= ' <a data-id="' . $value->order_id . '" class="text-danger d-inline-block remove-item-btn"><i class="ri-delete-bin-5-fill fs-16"></i></a>';
                }
                
                $output[] = $value;
            }
        }
        return ["total" => $rows_number, "totalNotFiltered" => $rows_number, "rows" => $output];
    }
    function deposit_advance(Request $request)
    {
        $update = DB::table('ecom_order')
            ->where('order_id', $request->order_id)
            ->update(['receive_advance_deposit' => $request->order_deposit]);
        return ["res_code" => "00"];
    }
    function note_internal(Request $request)
    {
        $update = DB::table('ecom_order')
            ->where('order_id', $request->order_id)
            ->update(['order_note_internal' => $request->note_internal]);
        return ["res_code" => "00"];
    }

    function update_status_order(Request $request)
    {
        $date_new = date('Y-m-d H:i:s');
        if ($request->status == 'Paid' || $request->status == 'Shipped' || $request->status == 'Completed') {
            if ($request->pointstatus == 'N') {
                // earn point
                DB::table('customer')
                    ->where('customer_id', $request->customer)
                    ->update(["customer_point" => DB::raw('customer_point+' . $request->point)]);

                // burn point
                if ($request->burnpoint > 0) {
                    DB::table('customer')
                        ->where('customer_id', $request->customer)
                        ->update(["customer_point" => DB::raw('customer_point-' . $request->burnpoint)]);
                }

                DB::table('ecom_order')
                    ->where('order_id', $request->order_id)
                    ->update(["status_earn" => "Y"]);
            }
        }

        DB::table('ecom_order')
            ->where('order_id', $request->order_id)
            ->update([
                "order_status" => $request->status,
                "order_cancel_message" => $request->message,
                "update_dtm" => $date_new,
                "update_by" => $request->userid,
            ]);

        if ($request->status == "Cancle") {
            if ($request->pointstatus == 'N') {
                DB::table('customer')
                    ->where('customer_id', $request->customer)
                    ->update(["customer_point" => DB::raw('customer_point+' . $request->burnpoint)]);
            } else {
                DB::table('customer')
                    ->where('customer_id', $request->customer)
                    ->update(["customer_point" => DB::raw('customer_point-' . $request->point - $request->burnpoint)]);
            }

            $branch = DB::table('ecom_order')
                ->where("order_id", $request->order_id)
                ->first();

            $orderlist = DB::table('ecom_order_item')
                ->where("order_id", $request->order_id);
            if ($orderlist->count() > 0) {
                foreach ($orderlist->get() as $key => $value) {
                    DB::table('product_stock')
                        ->where('product_id', $value->product_id)
                        ->where('option_id', $value->option_id)
                        ->where('branch_id', $branch->branch_id)
                        ->update(['stock' => DB::raw('stock+' . $value->item_qty)]);
                }
            }
        }
        if ($request->status != 'Pending' && $request->status != 'Progress' && $request->status != 'Paid_D' && $request->status != 'Waiting_D') {
            $this->sendMailUpdateOrder($request->status, $request->customer, $request->order_id);
        }
    }

    function sendMailUpdateOrder($status, $customer, $order_id)
    {
        $temp_id = '3';
        if ($status == 'Completed') {
            $temp_id = '4';
        } else if ($status == 'Cancel') {
            $temp_id = '5';
        } else if ($status == 'Paid') {
            $temp_id = '8';
        } else if ($status == 'Paid_D') {
            $temp_id = '9';
        }
        // $customer = DB::table('customer')->where('customer_id', $customer)->first();
        $template = DB::table('ecom_email_template')->where('temp_id', $temp_id)->first();
        $setting_arr = $this->getGeneralSetting();
        $orderInfo = $this->orderInfo($order_id);
        
        $member_info = DB::table('customer')->where('customer_id', $customer)->first();

        $txt_product      = "สินค้า";
        $txt_product_code = "รหัสสินค้า";
        $txt_product_qty  = "จำนวน";
        $txt_product_unit = "ราคาต่อหน่วย";
        $txt_product_total  = "ราคารวม";
        $txt_product_barcode  = "บาร์โค้ด";
        $txt_product_model  = "รุ่น";
        $txt_product_bf  = "ราคาก่อนภาษี";
        $txt_product_tax  = "ภาษี ";
        $txt_product_total_2  = "ยอดรวม";
        $txt_product_ship  = "ค่าจัดส่ง";
        $txt_product_dis = "ส่วนลด";
        $txt_product_dis_coupon = "ส่วนลดคูปอง";
        $txt_product_grand = "ยอดรวมสุทธิ";
        $txt_payment_info = "หมายเหตุชำระเงิน";
        $txt_ship_info = "หมายเหตุจัดส่ง";
        $txt_add_note = "หมายเหตุเพิ่มเติม";
        $txt_payment_instr = "ข้อมูลการชำระเงิน";

        $dataHTML = '<table class="table" style="width:700px;border-collapse: collapse;line-height:25px;">
                        <thead>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td><h4>'.$txt_product.'</h4></td>
                                <td></td>
                                <td><h4>'.$txt_product_code.'</h4></td>
                                <td align="center"><h4>'.$txt_product_qty.'</h4></td>
                                <td align="right"><h4>'.$txt_product_unit.'</h4></td>
                                <td align="right"><h4>'.$txt_product_total.'</h4></td>
                            </tr>
                        </thead>
                    <tbody>';
        
        $i=1; $tt_l = 0;
        foreach($orderInfo->product as $product){

            $masterOptionArr1 = $this->Mt_OptionInfoByID($product->m_option_id);
            $masterOptionArr2 = $this->Mt_OptionInfoByID($product->m_option_size_id);
            $masterOptionArr3 = $this->Mt_OptionInfoByID($product->m_option_third_id);

            $masterName1 = $product->option_name_1 ? $masterOptionArr1['parent_option']['option_name'].' : '.$product->option_name_1.'<br />' : '';
            $masterName2 = $product->option_name_2 ? $masterOptionArr2['parent_option']['option_name'].' : '.$product->option_name_2.'<br />' : '';
            $masterName3 = $product->option_name_3 ? $masterOptionArr3['parent_option']['option_name'].' : '.$product->option_name_3.'<br />' : '';

            
            //change img when item hane option
            if($product->option_name_1 || $product->option_name_2 || $product->option_name_3){
                $src_img = $this->image_product_opt.'/'.$product->img_option['picture_name'].'150'.$product->img_option['picture_extension'];
            }else{
                $src_img = $this->image_product.'/'.$product->img->picture_name.'150'.$product->img->picture_extension;
            }

            $dataHTML .= '<tr style="border-bottom: 1px solid #ddd;">
                            <td><img src="'.$src_img.'" class="img-responsive" style="max-width:60px;" /></td>
                            <td style="padding: 10px 0;">'.$product->item_name_th.'<br />
                                '.$txt_product_barcode.' : '.$product->product_barcode.'<br />
                                '.$txt_product_model.' : '.$product->item_sku.'<br />
                                '.$masterName1.'
                                '.$masterName2.'
                                '.$masterName3.'
                            </td>
                            <td>
                            '.$product->product_code.'
                            </td>
                            <td align="center">
                            '.$product->item_qty.'
                            </td>
                            <td align="right">'.number_format($product->item_price,2).'</td>
                            <td align="right">'.number_format($product->item_price*$product->item_qty,2).'</td>
                        </tr>';
            $tt_l = $tt_l+($product->item_price*$product->item_qty);
            $i++;
        }
        $tax_echo = '';
        if($orderInfo->tax_price>0){
            if($orderInfo->tax_type=='I'){
                $ttlc =  $orderInfo->order_price-$orderInfo->tax_price;
                $tax_echo = number_format($ttlc,2);
            }
            else if($orderInfo->tax_type=='E'){
                $ttlc =  $orderInfo->order_price-$orderInfo->tax_price;
                $tax_echo = number_format($ttlc,2);
            }else{
                $tax_echo = number_format($orderInfo->order_price,2);
            }
            $text_td_vat = $txt_product_bf.'<br />'.$txt_product_tax.$orderInfo->tax.'%';
            $text_total_vat = $tax_echo.'<br />'.number_format($orderInfo->tax_price,2);
        }else{
            $text_td_vat = '';
            $text_total_vat = '';
        }
        if($orderInfo->royalty_burn_point>0){
            $text_td_point = '<div style="color:red">'.'ส่วนลดคะแนนสะสม</div>';
            $text_total_point = '<div style="color:red">'.number_format($orderInfo->royalty_burn_point,2).'</div>';
        }else{
            $text_td_point = '';
            $text_total_point = '';
        }

        if($orderInfo->coupon_price>0){
            $txt_coupon = '<div style="color:red">'.$txt_product_dis_coupon.'</div>';
            $txt_coupon_value = '<div style="color:red">'.number_format($orderInfo->coupon_price,2).'</div>';
        }else{
            $txt_coupon = '';
            $txt_coupon_value = '';
        }

        $data_title_campaign = '';

        $data_price_campaign = '';

        $order_shipping_rate = number_format($orderInfo->order_shipping_rate,2);
        if(!empty($orderInfo->campaign)){
            foreach($orderInfo->campaign as $campaign){

              $data_title_campaign .= '<div style="color:red">'.$campaign['campaign_name'].'</div>';

              if($campaign['campaign_type']=='D'||$campaign['campaign_type']=='B'){
                //$data_price_campaign .= '<div style="color:red">-'.number_format($campaign['order_campaign_value'],2).'</div>';
                $data_price_campaign .= '<div style="color:red">-</div>';
              }
              elseif($campaign['campaign_type']=='G'){
                $data_price_campaign .= '0.00<br />';
              }
              elseif($campaign['campaign_type']=='F'){
                $data_price_campaign .= '<div style="color:red">-</div>';
                $order_shipping_rate = 'ส่งฟรี';
              }
            }
        }

        $dataHTML .= ' <tr style="border-bottom: 1px solid #ddd;">
                <td align="left"></td>
                <td colspan="4" align="right">
                    '.$txt_product_total_2.' <br />
                    '.$txt_product_ship.'<br />
                    <div style="color:red">ส่วนลด</div>
                    '.$txt_coupon.'
                    '.$text_td_point.'
                    '.$data_title_campaign.'
                    '.$text_td_vat.'
                </td>
                <td align="right">
                    '.number_format($tt_l,2).'<br />
                    '.$order_shipping_rate.'<br />
                    <div style="color:red">'.number_format($orderInfo->discount_price,2).'</div>
                    '.$txt_coupon_value.'
                    '.$text_total_point.'
                    '.$data_price_campaign.'
                    '.$text_total_vat.'
                </td>
            </tr>
            <tr>
                <td align="left"></td>
                <td colspan="4" align="right">
                    <b>'.$txt_product_grand.'</b>
                </td>
                <td align="right">
                    <b>'.number_format($orderInfo->order_price,2).'</b>
                </td>
            </tr>
            </tbody>
            </table>';
        $dataHTML .= '<br /><br /><div>';
        if($orderInfo->order_payment->method_desc!=''){
            $dataHTML .= '<h4 style="margin-top:0px;margin-bottom:5px;font-size:15px;">'.$txt_payment_info.'</h4>
            <p style="font-family:Tahoma;font-size:12px;">'.$orderInfo->order_payment->method_desc.'</p>';
        }
        if($orderInfo->order_shipping['shipping_desc']!=''){
            $dataHTML .= '<h4 style="margin-top:0px;margin-bottom:5px;font-size:15px;">'.$txt_ship_info.'</h4>
            <p style="font-family:Tahoma;font-size:12px;">'.$orderInfo->order_shipping->shipping_desc.'</p>';
        }
        if($orderInfo->order_note!=''){
            $dataHTML .= '<h4 style="margin-top:0px;margin-bottom:5px;font-size:15px;">'.$txt_add_note.'</h4>
            <p style="font-family:Tahoma;font-size:12px;">'.$orderInfo->order_note.'</p>';
        }
        if($orderInfo->order_payment->method_info!=''){
            $dataHTML .= '<h4 style="margin-top:0px;margin-bottom:5px;font-size:15px;">'.$txt_payment_instr.'</h4>
            <p style="font-family:Tahoma;font-size:12px;">'.$orderInfo->order_payment->method_info.'</p>';
        }

        $dataHTML .= '</div>';
        $subject = $template->temp_subject_th;
        $msgHtml = $template->temp_content_th;
        $msgHtml = nl2br($msgHtml);
        $msgHtml = str_replace("{First Name}",$orderInfo->address->firstname,$msgHtml);
        $msgHtml = str_replace("{Last Name}",$orderInfo->address->lastname,$msgHtml);
        $msgHtml = str_replace("{Order Details}",$dataHTML,$msgHtml);
        $msgHtml = str_replace("{Order ID}",$orderInfo->order_number,$msgHtml);
        $msgHtml = str_replace("{Store Name}",$setting_arr['1']['setting_value'],$msgHtml);
        if($orderInfo->tracking_no != ''){
        $msgHtml = str_replace("{TRACKING}",$orderInfo->tracking_no,$msgHtml);
        }else{
        $msgHtml = str_replace("{TRACKING}",'-',$msgHtml);
        }
        if(empty($memberinfo)){
            $member_info['firstname'] = $orderInfo->address->firstname;
            $member_info['lastname'] = $orderInfo->address->lastname;
            $member_info['email'] = $orderInfo->address->email;
        }
        // $mailData = [
        //     'title' => 'Mail from iHAVECPU_DEV',
        //     'body' => $msgHtml
        // ];

        // Mail::to('rung.amnart.021@gmail.com')->send(new SendMail($mailData));
        
        // dd("Email is sent successfully.");

        return ['res_code' => '00'];

    }

    function orderInfo($order_id) {
        $order = DB::table('ecom_order')
                ->leftJoin('branchs', 'branchs.branch_id', 'ecom_order.branch_id')
                ->where('order_id', $order_id)->first();
        $campaign_arr = [];
        if ($order->order_campaign_status == 'Y') {
            $campaign = DB::table('ecom_order_campaign')
                ->select('order_id', 'campaign_id', 'campaign_name', 'campaign_type', 'campaign_discount_value', 'campaign_discount_type', 'campaign_shipping', 'order_campaign_value')
                ->where('order_id', $order_id);
            if ($campaign->count() > 0) {
                foreach ($campaign->get() as $key => $value) {
                    $campaign_arr[] = $value;
                }
                $order->campaign = $campaign_arr;
            }
        } else {
            $order->campaign = '';
        }

        

        $order->product = [];
        $product = DB::table('ecom_order_item')->where('order_id', $order_id);
        if ($product->count() > 0) {
            foreach ($product->get() as $key => $value) {
                $value->img = $this->getProductIMG($value->product_id);
                $value->img_option = $this->getProductIMG($value->option_id);
                $order->product[] = $value;
            }
        }
        
        
        $order->category = [];
        $category = DB::table('ecom_order_item')
                    ->leftJoin('ecom_product_category', 'ecom_order_item.category_id', 'ecom_product_category.category_id')
                    ->select(DB::raw("SUM(item_price) as total_price"), DB::raw("SUM(item_qty) as total_qty"), 'ecom_product_category.display_cat_name_th')
                    ->where('order_id', $order_id);
        if($category->count() > 0){
            foreach ($category->get() as $key => $value) {
                $order->category[] = [
                    "category_name" => $value->display_cat_name_th,
                    "total_price" => $value->total_price,
                    "total_qty" => $value->total_qty,
                ];
            }
        }
        
        $order->address = [];
        $order->address = DB::table('ecom_order_address')
                        ->leftJoin('mt_country', 'ecom_order_address.country', 'mt_country.id')
                        ->where('order_id', $order_id)
                        ->where('address_type', '1')
                        ->first();

        $order->billing = [];
        $order->billing = DB::table('ecom_order_address')
                        ->leftJoin('mt_country', 'ecom_order_address.country', 'mt_country.id')
                        ->where('order_id', $order_id)
                        ->where('address_type', '2')
                        ->first();    

        $order->order_shipping = [];
        $shipping = DB::table('ecom_shipping_method')
                    ->where('shipping_id', $order->order_shipping_id)
                    ->first();
        $order->order_shipping = [
            "shipping_name" => $shipping->shipping_name_th,
            "shipping_desc" => $shipping->shipping_desc_th,
        ];
        $order->order_payment = [];
        $order->order_payment = DB::table('ecom_payment_method')
                                ->select('method_id', 'method_name', 'method_desc_th AS method_desc', 'method_info_th AS method_info')
                                ->where('method_id', $order->order_payment_id)
                                ->first();

        return $order;
    }
    function getProductIMG($product_id)
    {
        $img = DB::table('product_picture')
            ->where('product_id', $product_id)
            ->where('setdefault', 'Y')
            ->orderBy('id')
            ->first();
        return $img;
    }
    function getProductOptionIMG($option_id)
    {
        $img = DB::table('product_option')
            ->where('option_id', $option_id)
            ->first();
        return $img;
    }
    function getGeneralSetting() {
        $setting_arr = [];
        $setting = DB::table('ecom_setting')->get();
        foreach ($setting as $key => $value) {
            $setting_arr[$value->setting_id] = [
                "setting_value_optional" =>  $value->setting_value_optional,
                "setting_value" =>  $value->setting_value
            ];
        }
        return $setting_arr;
    }
    function printOrder(Request $request, $id) {
        $orderinfo = $this->orderInfo($id);
        $generalsetting = $this->getGeneralSetting();
        $possetting = $this->generalsetting();
        // dd($orderinfo);
        
        if($orderinfo->order_status == 'Pending'){ $orderinfo->order_status = 'ทำรายการไม่สำเร็จ'; }
        else if($orderinfo->order_status == 'Waiting'){ $orderinfo->order_status = 'รอชำระเงิน'; }
        else if($orderinfo->order_status == 'Waiting_D'){ $orderinfo->order_status = 'รอยืนยันการสั่งซื้อ'; }
        else if($orderinfo->order_status == 'Progress'){ $orderinfo->order_status = 'กำลังดำเนินการ'; }
        else if($orderinfo->order_status == 'Paid'){ $orderinfo->order_status = 'รอดำเนินการ'; }
        else if($orderinfo->order_status == 'Paid_D'){ $orderinfo->order_status = 'เตรียมการจัดส่ง'; }
        else if($orderinfo->order_status == 'Shipped'){ $orderinfo->order_status = 'จัดส่งแล้ว'; }
        else if($orderinfo->order_status == 'Completed'){ $orderinfo->order_status = 'เสร็จสิ้น'; }
        else if($orderinfo->order_status == 'Cancel'){ $orderinfo->order_status = 'ยกเลิก'; }

        $dataShipping = '';
        if($orderinfo->order_shipping_id == '0'){
            $orderinfo->order_shipping->shipping_desc = "รับที่สาขา"." : ".$orderinfo->order_shipping_desc;
            $dataShipping = "รับที่สาขา"." : ".$orderinfo->order_shipping_desc;
        }
        
        foreach ($orderinfo->product as $key => $value) {
            $masterOptionArr1 = $this->Mt_OptionInfoByID($value->m_option_id);
            $masterOptionArr2 = $this->Mt_OptionInfoByID($value->m_option_size_id);
            $masterOptionArr3 = $this->Mt_OptionInfoByID($value->m_option_third_id);
            $orderinfo->product[$key]->masterName1 = $value->option_name_1 ? $masterOptionArr1['parent_option']['option_name'].' : '.$value->option_name_1.'<br />' : '';
            $orderinfo->product[$key]->masterName2 = $value->option_name_2 ? $masterOptionArr2['parent_option']['option_name'].' : '.$value->option_name_2.'<br />' : '';
            $orderinfo->product[$key]->masterName3 = $value->option_name_3 ? $masterOptionArr3['parent_option']['option_name'].' : '.$value->option_name_3.'<br />' : '';
            // $masterName1 = $value->option_name_1 ? $masterOptionArr1['parent_option']['option_name'].' : '.$value->option_name_1.'<br />' : '';
            // $masterName2 = $value->option_name_2 ? $masterOptionArr2['parent_option']['option_name'].' : '.$value->option_name_2.'<br />' : '';
            // $masterName3 = $value->option_name_3 ? $masterOptionArr3['parent_option']['option_name'].' : '.$value->option_name_3.'<br />' : '';
        }
        $orderinfo->admin = "";
        if($orderinfo->update_by != 9999){
            $users = DB::table('users')
            ->where('user_id', $orderinfo->update_by);
            if($users->count() > 0){
                $users = $users->first();
                $orderinfo->admin = $users->firstname . " " . $users->lastname;
            }
        }

        // 'orderinfo' => $orderinfo, 'generalsetting' => $generalsetting, 'possetting' => $possetting,
        $pdf = PDF::loadView('Orders/pdf', compact(['orderinfo', 'generalsetting', 'possetting', 'dataShipping']))->setPaper('a4');
        return @$pdf->stream();
        return view('Orders/pdf', compact(['orderinfo', 'generalsetting', 'possetting', 'dataShipping']));
    }

    
}
