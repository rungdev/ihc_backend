function ajaxRequest(params) {
    params.data.search = $("#searchInp").val();
    $.post('calldata/supplierList', $.param(params.data)).then(function (res) {
        params.success(res);
    })
}
$('body').on('keypress', '#searchInp', function (e) {
    if(e.which == 13){
        $('#table').bootstrapTable('refresh');
    }
})
$('body').on('change', '.activeStatus', function () {
    var txt = $(this).is(':checked') ? 'ต้องการเปิดแสดงผู้ผลิตนี้ใช่หรือไม่' : 'ต้องการปิดแสดงผู้ผลิตใช่หรือไม่';
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
                url: "calldata/supplierStatus",
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
                url: "calldata/supplierStatus",
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