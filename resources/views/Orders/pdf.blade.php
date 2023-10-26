<style>
    @font-face {
        font-family: 'THSarabun';
        font-style: normal;
        font-weight: normal;
        src: local('THSarabun'), local('THSarabun'),url("{{ storage_path('fonts/THSarabun.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'THSarabunBold';
        font-style: normal;
        font-weight: normal;
        src: local('THSarabunBold'), local('TH-Sarabun-Bold'),url("{{ storage_path('fonts/THSarabunBold.ttf') }}") format('truetype');
    }
    @page { margin: 20px; }
    body {
        font-family: 'THSarabun';
        font-size: 25px;
        color: #000;
    }
    .txt-nomal{
        color: #000;
        font-family: 'THSarabun';
        font-size: 16pt;
        line-height: 14.5pt;
    }
    .txt-bold{
        color: #878a99;
        font-family: 'THSarabunBold';
        font-size: 18px;
        line-height: 16px;
    }
    .pr-1{
        padding-right: 5px;
    }
    th{
        color: #000;
        font-family: 'THSarabunBold';
        font-size: 14pt;
        line-height: 14.5pt;
    }
    td{
        color: #000;
        font-family: 'THSarabun';
        font-size: 14pt;
        line-height: 14.5pt;
    }
    .txt-title{
        color: #878a99;
        font-family: 'THSarabun';
        font-size: 18px;
        line-height: 16px;
    }
    .txt-detail{
        color: #495057;
        font-family: 'THSarabun';
        font-size: 18px;
        line-height: 16px;
    }
    .txt-detail-web{
        color: #4b38b3;
        font-family: 'THSarabun';
        font-size: 18px;
        line-height: 16px;
    }
    .box-payment{
        font-family: 'THSarabunBold';
        background-color: #fff5da;
        color: #f1963b;
        font-size: 18px;
        line-height: 16px;
        display: inline-block;
        padding: 0px 10px;
        border-radius: 5px;
    }
    .txt-name-address{
        color: #000000;
        font-family: 'THSarabun';
        font-size: 20px;
        line-height: 18px;
    }
    .txt-foot{
        color: #000000;
        font-family: 'THSarabun';
        font-size: 18px;
        line-height: 18px;
    }
    .txt-foot-bold{
        color: #000000;
        font-family: 'THSarabunBold';
        font-size: 20px;
        line-height: 20px;
    }
    .branch{
        color: #f06548;
    }
    .status{
        color: #45CB85
    }
    
</style>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>iHAVECPU</title>
    
</head>
<body style="font-size:12px; color:grey;width: 100%;">
    <div>
        <img alt="TESTING" style="vertical-align: bottom;" width="300" src="https://shop.posvision.co/paeshop/utility/barcode/?codetype=Code39&size=25&text={{$orderinfo->order_number}}&print=true"/>
        <img style="vertical-align: top;" src="{{public_path('/uploads/invoice_logo/shop1/'.$generalsetting[16]['setting_value'])}}" style="width:40mm;" />
        <div style="float: right">
            <div>
                <span class="txt-title">หมายเลขผู้เสียภาษี:</span> <span class="txt-detail">0265561000100</span>
            </div>
            <div>
                <span class="txt-title">อีเมล:</span> <span class="txt-detail">info@ihavecpu.com</span>
            </div>
            <div>
                <span class="txt-title">เว็บไซต์:</span> <span class="txt-detail-web">www.ihavecpu.com</span>
            </div>
            <div>
                <span class="txt-title">โทรศัพท์:</span> <span class="txt-detail">086 838 5200</span>
            </div>
        </div>
    </div>
    <div style="margin-top: 25px;">
        <div class="txt-bold">ที่อยู่</div>
        <div class="txt-bold">บริษัท ไอ แฮฟ ซีพียู จำกัด</div>
        <div class="txt-detail">เลขที่ 228 ตำบล หนองแสง อำเภอปากพลี นครนายก</div>
        <div class="txt-detail">รหัสไปรษณีย์: 26130</div>        
    </div>
    <div style="border-top: 1px dashed #e9ebec;border-bottom: 1px dashed #e9ebec;margin-top: 20px;padding-top: 20px;padding-bottom: 10px;">
        <div style="width: 24.5%;display:inline-block;">
            <span class="txt-bold">หมายเลขคำสั่งซื้อ</span><br>
            <span class="txt-detail">{{$orderinfo->order_number}}</span>
        </div>
        <div style="width: 24.5%;display:inline-block;">
            <span class="txt-bold">วันที่</span><br>
            <span class="txt-detail">{{date('d-m-Y H:i:s',strtotime($orderinfo->create_dtm))}}</span>
        </div>
        <div style="width: 24.5%;display:inline-block;">
            
            <span class="txt-bold">การชำระเงิน</span><br>
            <div class="box-payment">
                @php
                    if($orderinfo->order_payment_name == "Bank Transfer"){
                        echo "โอนเงินธนาคาร";
                    }else if($orderinfo->order_payment_name == "Cash on Delivery"){
                        echo "เก็บเงินสดปลายทาง";
                    }else{
                        echo $orderinfo->order_payment_name;
                    }
                @endphp
            </div>
        </div>

        <div style="width: 24.5%;display:inline-block;">
            <span class="txt-bold">ยอดเก็บเงินมัดจำล่วงหน้า</span><br>
            <span class="txt-detail">{{number_format($orderinfo->receive_advance_deposit, 2)}}</span>
            
        </div>
    </div>
    <div style="margin-top: 15px;">
        <div style="display: inline-block;width: 49.5%;vertical-align: top;">
            <div class="txt-bold">ที่อยู่ในการออกใบกำกับภาษี</div>
            <div class="txt-name-address">
                @php
                    if($orderinfo->billing->companyname != ''){ echo  $orderinfo->billing->companyname . '&nbsp;'; }
                    else{ echo $orderinfo->billing->firstname . " " . $orderinfo->billing->lastname . '&nbsp;'; }
                @endphp
            </div>
            <div class="txt-detail">
                @php
                    echo $orderinfo->billing->address1 . '&nbsp;';
                    if($orderinfo->billing->address2 != ''){ echo $orderinfo->billing->address2 . '&nbsp;'; }
                    if($orderinfo->billing->city != ''){ echo $orderinfo->billing->city . '&nbsp;'; }
                    if($orderinfo->billing->state != ''){ echo $orderinfo->billing->state . '&nbsp;'; }
                    if($orderinfo->billing->postcode != ''){ echo $orderinfo->billing->postcode . '&nbsp;'; }
                @endphp
            </div>
            <div class="txt-detail">โทรศัพท์: {{$orderinfo->billing->mobile}}</div>
            <div class="txt-detail">หมายเลขผู้เสียภาษี: {{$orderinfo->billing->tax_id}}</div>
        </div>
        <div style="display: inline-block;width: 49.5%;vertical-align: top;">
            <div class="txt-bold">ที่อยู่จัดส่ง</div>
            <div class="txt-name-address">{{$orderinfo->address->firstname . " " . $orderinfo->address->lastname . ' ' . $orderinfo->address->companyname . ' '}}</div>
            <div class="txt-detail">
                @php
                    echo $orderinfo->address->address1 . '&nbsp;';
                    if($orderinfo->address->address2 != ''){ echo $orderinfo->address->address2 . '&nbsp;'; }
                    if($orderinfo->address->city != ''){ echo $orderinfo->address->city . '&nbsp;'; }
                    if($orderinfo->address->state != ''){ echo $orderinfo->address->state . '&nbsp;'; }
                    if($orderinfo->address->postcode != ''){ echo $orderinfo->address->postcode . '&nbsp;'; }
                @endphp
            </div>
            <div class="txt-detail">โทรศัพท์: {{$orderinfo->address->mobile}}</div>
            <div class="txt-detail">หมายเลขผู้เสียภาษี: {{$orderinfo->address->tax_id}}</div>
        </div>
    </div>
    <table style="width: 100%;border-collapse: collapse;margin-top: 15px;">
        <thead>
            <tr style="background: rgb(33 37 41 / 2%);">
                <th style="width: 5%;padding-bottom: 10px;padding-top: 5px;">#</th>
                <th style="width: 50%;padding-bottom: 10px;padding-top: 5px;">รายละเอียด</th>
                <th style="width: 15%;padding-bottom: 10px;padding-top: 5px;">ราคาต่อหน่วย</th>
                <th style="width: 15%;padding-bottom: 10px;padding-top: 5px;">จำนวน</th>
                <th style="width: 15%;padding-bottom: 10px;padding-top: 5px;text-align: right;padding-right: 10px;">ราคารวม</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_line = 0;
            @endphp
            @foreach ($orderinfo->product as $key => $product)
                <tr>
                    <td style="text-align: center">{{$key+1}}</td>
                    <td>
                        <div class="txt-name-address">{{$product->item_name_th}}</div>
                        <div class="txt-detail">
                            @php
                                if($product->masterName1 == '' && $product->masterName2 == '' && $product->masterName3 == ''){
                                    echo "-";
                                }else{
                                    if ($product->masterName1) { echo $product->masterName1 . ": " . $product->option_name_1; }
                                    if ($product->masterName2) { echo $product->masterName2 . ": " . $product->option_name_2; }
                                    if ($product->masterName3) { echo $product->masterName3 . ": " . $product->option_name_3; }
                                }
                            @endphp
                        </div>
                    </td>
                    <td style="text-align: center">
                        <div class="txt-name-address">{{number_format($product->item_price, 2)}}</div>
                    </td>
                    <td style="text-align: center">
                        <div class="txt-name-address">{{$product->item_qty}}</div>
                    </td>
                    <td style="text-align: right;padding-right: 10px;">
                        <div class="txt-name-address">{{number_format(($product->item_qty * $product->item_price), 2)}}</div>
                    </td>
                </tr>
                @php
                    $total_line += number_format(($product->item_qty * $product->item_price), 2);
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top: 1px dashed #e9ebec;">
                <td colspan="3"></td>
                <td class="txt-foot">ยอดรวม</td>
                <td class="txt-name-address" style="text-align: right;padding-right: 10px;">{{$total_line}}</td>
            </tr>
            <tr style="">
                <td colspan="3"></td>
                <td class="txt-foot">ภาษี (7%)</td>
                <td class="txt-name-address" style="text-align: right;padding-right: 10px;">
                    {{number_format($orderinfo->tax_price, 2)}}
                </td>
            </tr>
            <tr style="">
                <td colspan="3"></td>
                <td class="txt-foot">ส่วนลด (VELZON15)</td>
                <td class="txt-name-address" style="text-align: right;padding-right: 10px;">
                    {{number_format($orderinfo->discount_price + $orderinfo->coupon_price, 2)}}
                </td>
            </tr>
            @if ($orderinfo->royalty_burn_point > 0)
                <tr style="">
                    <td colspan="3"></td>
                    <td class="txt-foot">ส่วนลดคะแนนสะสม</td>
                    <td class="txt-name-address" style="text-align: right;padding-right: 10px;">- $53.99</td>
                </tr>
            @endif
            @if (!empty($orderinfo->campaign))
                @foreach ($orderinfo->campaign as $item)
                    <tr style="">
                        <td colspan="3"></td>
                        <td class="txt-foot" style="color: red;">{{$item->campaign_name}}</td>
                        <td class="txt-name-address" style="text-align: right;padding-right: 10px;">-</td>
                    </tr>
                @endforeach
            @endif
            
            <tr style="">
                <td colspan="3"></td>
                <td class="txt-foot">ค่าจัดส่ง</td>
                <td class="txt-name-address" style="text-align: right;padding-right: 10px;">
                    {{$orderinfo->order_shipping_rate > 0 ? number_format($orderinfo->order_shipping_rate , 2) : 'ส่งฟรี'}}
                </td>
            </tr>
            <tr style="">
                <td colspan="3"></td>
                <td class="txt-foot" style="">ดอกเบี้ยผ่อนชำระ</td>
                <td class="txt-name-address" style="text-align: right;padding-right: 10px;">0.00</td>
            </tr>
            @if ($orderinfo->royalty_earn_point > 0)
                <tr style="">
                    <td colspan="3"></td>
                    <td class="txt-foot" colspan="2" style="padding-bottom: 15px;">คะแนนที่ได้รับ {{$orderinfo->royalty_earn_point}} คะแนน</td>
                </tr>
            @endif
            <tr style="">
                <td colspan="3"></td>
                <td class="txt-foot-bold" style="border-top: 1px dashed #e9ebec;padding-top: 5px">จำนวนเงินทั้งหมด</td>
                <td class="txt-foot-bold" style="text-align: right;padding-right: 10px;border-top: 1px dashed #e9ebec;padding-top: 5px">{{$total_line}}</td>
            </tr>
            
        </tfoot>
    </table>

    <div style="margin-top: 20px;">
        <div class="txt-foot-bold">รายละเดียดการชำระเงิน:</div>
        <div class="txt-detail">การชำระเงิน: 
            @php
                if($orderinfo->order_payment_name == "Bank Transfer"){
                    echo "โอนเงินธนาคาร";
                }else if($orderinfo->order_payment_name == "Cash on Delivery"){
                    echo "เก็บเงินสดปลายทาง";
                }else{
                    echo $orderinfo->order_payment_name;
                }
            @endphp
        </div>
        <div class="txt-detail">การจัดส่ง: <span class="branch">{{$orderinfo->branch_name_th}}</span></div>
        <div class="txt-detail">สถานะ: <span class="status">{{$orderinfo->order_status}}</span></div>
        <div class="txt-detail">อัพเดทล่าสุด: {{date('d-m-Y H:i:s',strtotime($orderinfo->update_dtm))}} โดย: {{$orderinfo->admin}}</div>
    </div>

    @if (!empty($orderinfo->order_note))
        <div style="background: #dff0fa;margin-top: 10px;border-radius: 7px;padding: 10px;padding-top: 5px;">
            <div class="txt-foot-bold" style="color: #2385ba;">หมายเหตุ:</div>
            {!! $orderinfo->order_note !!}
        </div>
    @endif    
    @if (!empty($orderinfo->order_message))
        <div style="background: #dff0fa;margin-top: 10px;border-radius: 7px;padding: 10px;padding-top: 5px;">
            <div class="txt-foot-bold" style="color: #2385ba;">หมายเหตุเพิ่มเติม:</div>
            {!! $orderinfo->order_message !!}
        </div>
    @endif    
    @if (!empty($orderinfo->order_note_internal))
        <div style="background: #dff0fa;margin-top: 10px;border-radius: 7px;padding: 10px;padding-top: 5px;">
            <div class="txt-foot-bold" style="color: #2385ba;">หมายเหตุ:</div>
            {!! $orderinfo->order_note_internal !!}
        </div>
    @endif
    
</body>
</html>