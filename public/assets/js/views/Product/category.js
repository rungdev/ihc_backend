$('body').on('keypress', '#searchInp', function (e) {
    if(e.which == 13){
        refresh_table()
    } 
})

function refresh_table() {
    $.ajax({
        type: "POST",
        url: "calldata/searchCategory",
        data: { search : $('#searchInp').val() },
        success: function (response) {
            $("#tableData").html(response);
            $('#basic').simpleTreeTable({
                expander: $('#expander'),
                collapser: $('#collapser'),
                opened: [0],
                store: 'session',
                storeKey: 'simple-tree-table-basic'
            });
        }
    });
}

$('body').on('click', '.btn-edit', function () {
    var id = $(this).data("id");
    var main = $(this).data("main");
    $.ajax({
        type: "POST",
        url: "calldata/categoryById",
        data: { id : id},
        success: function (response) {
            var data = response.res_result;
            $('#maincatid').val(main);
            $('#categoryid').val(data.cat_id);
            $('#categoryth').val(data.cat_name_th);
            $('#categoryen').val(data.cat_name_gb);
            $('#flexSwitchCheckDefault').prop('checked', data.active_status == 'Y' ? true : false)
            $('#categoryModal').modal('show')
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
                url: "calldata/saveCategory",
                data: { 
                    categoryid  :  $('#categoryid').val(),
                    categoryth  :  $('#categoryth').val(),
                    categoryen  :  $('#categoryen').val(),
                    catmain     :  $('#maincatid').val(),
                    categorychk :  chk,
                },
                success: function (response) {
                    if(response.res_code == '00'){
                        $('#categoryModal').modal('hide')
                        Swal.fire('บันทึกสำเร็จ','','success')
                        refresh_table()
                    }else{
                        Swal.fire('ผิดพลาด',response.res_text,'fail')
                    }
                }
            });
        }
    })
})


$('body').on('change', '.activeStatus', function () {
    var txt = $(this).is(':checked') ? 'ต้องการเปิดแสดงหมวดหมู่สินค้าใช่หรือไม่' : 'ต้องการปิดแสดงหมวดหมู่สินค้าใช่หรือไม่';
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
                url: "calldata/categoryStatus",
                data: { 
                    type        :  0,
                    status      :  chk,
                    categoryid  :  id,
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
                url: "calldata/categoryStatus",
                data: { 
                    type        :  1,
                    categoryid    :  id,
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

$('body').on('click', '.btn-add-child', function () {
    $('#maincatid').val($(this).data('id'));
    $('#categoryid').val('');
    $('#categoryth').val('');
    $('#categoryen').val('');
    $('#flexSwitchCheckDefault').prop('checked', false);
    $('#categoryModal').modal('show')
})

$('#basic').simpleTreeTable({
    expander: $('#expander'),
    collapser: $('#collapser'),
    opened: [0],
    store: 'session',
    storeKey: 'simple-tree-table-basic'
});