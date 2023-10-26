function ajaxRequest(params) {
    params.data.search = $("#searchInp").val();
    $.post('calldata/brandList', $.param(params.data)).then(function (res) {
        params.success(res);
    })
}
$('body').on('keypress', '#searchInp', function (e) {
    if(e.which == 13){
        $('#table').bootstrapTable('refresh');
    }
})

$('body').on('click', '.btn-edit', function () {
    var id = $(this).data("id");
    $.ajax({
        type: "POST",
        url: "calldata/brandById",
        data: { id : id},
        success: function (response) {
            var data = response.res_result;
            $('#brandid').val(data.brand_id);
            $('#brandth').val(data.brand_name_th);
            $('#branden').val(data.brand_name_gb);
            $('#flexSwitchCheckDefault').prop('checked', data.active_status == 'Y' ? true : false)
            $('#brandModal').modal('show')
        }
    });
})

$('body').on('click', '.btn-save', function () {
    Swal.fire({
        title: 'ยืนยัน',
        text: "ยืนยันการบันทึกข้อมูล",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log($('#flexSwitchCheckDefault').is(':checked'));
            var chk = $('#flexSwitchCheckDefault').is(':checked') ? 'Y' : 'N';
            $.ajax({
                type: "POST",
                url: "calldata/saveBrand",
                data: { 
                    brandid  :  $('#brandid').val(),
                    brandth  :  $('#brandth').val(),
                    branden  :  $('#branden').val(),
                    brandchk :  chk,
                },
                success: function (response) {
                    if(response.res_code == '00'){
                        $('#brandnModal').modal('hide')
                        Swal.fire('บันทึกสำเร็จ','','success')
                        $('#table').bootstrapTable('refresh');
                    }else{
                        Swal.fire('ผิดพลาด',response.res_text,'fail')
                    }
                }
            });
        }
    })
})


$('body').on('change', '.activeStatus', function () {
    var txt = $(this).is(':checked') ? 'ต้องการเปิดแสดงตัวเลือกใช่หรือไม่' : 'ต้องการปิดแสดงตัวเลือกใช่หรือไม่';
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
                url: "calldata/brandStatus",
                data: { 
                    type        :  0,
                    status      :  chk,
                    brandid     :  id,
                },
                success: function (response) {
                    if(response.res_code == '00'){
                        Swal.fire('บันทึกสำเร็จ','','success')
                    }else{
                        Swal.fire('ผิดพลาด',response.res_text,'fail')
                    }
                    $('#table').bootstrapTable('refresh');
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
                url: "calldata/brandStatus",
                data: { 
                    type        :  1,
                    brandid    :  id,
                },
                success: function (response) {
                    if(response.res_code == '00'){
                        Swal.fire('ลบข้อมูลสำเร็จ','','success')
                    }else{
                        Swal.fire('ผิดพลาด',response.res_text,'fail')
                    }
                    $('#table').bootstrapTable('refresh');
                }
            });
        }else{
            $(this).prop('checked', !$(this).is(':checked'));
        }
    })
})

$('body').on('click', '.btn-add-brand', function () {
    $('#brandid').val('');
    $('#brandth').val('');
    $('#branden').val('');
    $('#flexSwitchCheckDefault').prop('checked', false);
    $('#brandModal').modal('show')
})