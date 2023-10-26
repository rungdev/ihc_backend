var slider = document.getElementById('product-price-range');
var cate    = [];
var brand   = [];
var statusc = [];

noUiSlider.create(slider, {
    start: [0, 100000],
    connect: true,
    step: 5000,
    range: {
        'min': 0,
        'max': 100000
    },
    tooltips: true
})

$('#product-price-range')[0].noUiSlider.on('change',function(v,handle){
    $('#minCost').val(v[0]);
    $('#maxCost').val(v[1]);
    $('#productTable').bootstrapTable('refresh');
});

$('#idStatus').selectpicker();
function ajaxRequest(params) {
    params.data.search = $('#inpSearch').val();
    params.data.product_cat_id = cate;
    params.data.product_brand_id = brand;
    params.data.product_active_status = statusc;
    params.data.minCost = $('#minCost').val();
    params.data.maxCost = $('#maxCost').val();
    // '/api/backoffice/productTable'
    $.post('/product/get', params.data).then(function(res) {
        // console.log(JSON.parse(res));
        params.success(res);
    })
}

$('#inpSearch').keyup(function (e) { 
    var code = e.key;
    if(code==="Enter"){
        $('#productTable').bootstrapTable('refresh');
    }
})

var nodeArray = function (selector, parent) {
    if (parent === void 0) { parent = document; }
    return [].slice.call(parent.querySelectorAll(selector));
};
var allThings = nodeArray('input');
addEventListener('change', function (e) {
    cate = [];
    brand = [];
    statusc = [];
    var check = e.target;
    if (allThings.indexOf(check) === -1)
        return;
    var children = nodeArray('input', check.parentNode);
    children.forEach(function (child) { return child.checked = check.checked; });
    while (check) {
        var parent = (check.closest(['ul']).parentNode).querySelector('input');
        var siblings = nodeArray('input', parent.closest('li').querySelector(['ul']));
        var checkStatus = siblings.map(function (check) { return check.checked; });
        var every = checkStatus.every(Boolean);
        var some = checkStatus.some(Boolean);
        parent.checked = every;
        parent.indeterminate = !every && every !== some;
        check = check != parent ? parent : 0;
    }
    var checkboxes = document.querySelectorAll('.checkcata:checked');
    var checkboxesChecked = [];
    for (var i=0; i<checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            cate.push(checkboxes[i].value);
        }
    }

    var checkboxes = document.querySelectorAll('.checkbrand:checked');
    var checkboxesChecked = [];
    for (var i=0; i<checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            brand.push(checkboxes[i].value);
        }
    }

    var checkboxes = document.querySelectorAll('.checkstatus:checked');
    var checkboxesChecked = [];
    for (var i=0; i<checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            statusc.push(checkboxes[i].value);
        }
    }
    $('#productTable').bootstrapTable('refresh');
});


$('body').on('click', '#btn-sync-all', function () {
    // var settings = {
    //     "url": "http://43.254.133.110:31579/API/IHaveCpuAPI/GetProducts",
    //     "method": "POST",
    //     crossDomain: true,
    //     cors: true ,
    //     secure: true,
    //     contentType:'application/json',
    //     dataType: 'json',
    //     headers: {
    //         'Authorization': '*',
    //         'Access-Control-Allow-Origin': 'OTJhZTVmYTctM2RjNi00YTQ4LWFjZWUtMjYxZTAxOTU4NWY4',
    //         'Content-Type':'application/json'
    //     },
    //     "data": JSON.stringify({
    //       "BranchID": 1
    //     }),
    // };
      
    // $.ajax(settings).done(function (response) {
    //     console.log(response);
    // });
})

$('body').on('change', '.activeStatus', function () {
    var txt = $(this).is(':checked') ? 'ต้องการเปิดแสดงสินค้าใช่หรือไม่' : 'ต้องการปิดแสดงสินค้าใช่หรือไม่';
    Swal.fire({
        title: 'ยืนยัน',
        text: txt,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            var chk = $(this).is(':checked') ? 'Y' : 'N';
            var id  = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "product/updatestatus",
                data: { 
                    type        :  0,
                    status      :  chk,
                    productid   :  id,
                },
                success: function (response) {
                    if(response.res_code == '00'){
                        Swal.fire('บันทึกสำเร็จ','','success')
                    }else{
                        Swal.fire('ผิดพลาด',response.res_text,'fail')
                    }
                    $('#productTable').bootstrapTable('refresh');
                }
            });
        }else{
            $(this).prop('checked', !$(this).is(':checked'));
        }
    })
})
$('body').on('click', '.btn-remove', function () {
    Swal.fire({
        title: 'ยืนยัน',
        text: "ต้องการลบรายการนี้หรือไม่",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            var id  = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "product/updatestatus",
                data: { 
                    type        :  1,
                    productid   :  id,
                },
                success: function (response) {
                    if(response.res_code == '00'){
                        Swal.fire('ลบข้อมูลสำเร็จ','','success')
                    }else{
                        Swal.fire('ผิดพลาด',response.res_text,'fail')
                    }
                    $('#productTable').bootstrapTable('refresh');
                }
            });
        }else{
            $(this).prop('checked', !$(this).is(':checked'));
        }
    })
})