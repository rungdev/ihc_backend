function ajaxRequest(params) {
    $.post('api/backoffice/usergroup', $.param(params.data)).then(function (res) {
        params.success(res);
    })
}
function ajaxRequestUsers(params) {
    params.data.username = $("#username").val();
    params.data.selGroup = $("#selGroup").val();
    params.data.selBranc = $("#selBranch").val();
    $.post('api/backoffice/getUserlist', $.param(params.data)).then(function (res) {
        params.success(res);
    })
}

$(function () { 
    setTimeout(() => {
        $('#selGroup').selectpicker();
        $('#selBranch').selectpicker();
    }, 3000);
})

$('body').on('click', '#btn-filter', function () {
    $('#usersTable').bootstrapTable('refresh');
})

$('body').on('click', '#btn-save', function () {
    Swal.fire({
        title: 'ยืนยันการบันทึก',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        icon: 'question',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            var arr_check   = [];
            var gname       = $("#groupname").val();
            var groupid     = $("#groupid").val();
            var userid      = $("#userid").val();
            $(".premission-check").each(function () {
                var id          = $(this).data('id');
                var type        = $(this).data('type');
                if ($(this).prop('checked') == true) {
                    arr_check.push({ usergroup_id: groupid, module_id: id, permission: type })
                }
            });
            $.ajax({
                type: "patch",
                url: "/api/backoffice/saveGroup",
                data: {
                    userid      : userid,
                    gname       : gname,
                    groupid     : groupid,
                    permission  : arr_check
                },
                success: function (response) {
                    if(response.res_code == "00"){
                        Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success').then(() => {

                        })
                    }
                }
            });
        }
    })
})

$('body').on('click', '.remove-item-btn', function () {
    Swal.fire({
        title: 'ยืนยันการลบ',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        icon: 'question',
    }).then((result) => {
        var id = $(this).data('id');
        $.ajax({
            type: "put",
            url: "api/backoffice/groupStatus",
            data: {type: "S", groupid: id, userid: user_id},
            success: function (response) {
                if(response.res_code == "00"){
                    Swal.fire('สำเร็จ', 'ลบข้อมูลเรียบร้อย', 'success').then(() => {
                        $('#groupTable').bootstrapTable('refresh');
                    })
                }
            }
        });
    })
})

$('body').on('change', '.activeStatus', function () {  
    var groupid = $(this).data('id');
    var type = $(this).prop('checked') ? 'Y' : 'N';
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        icon: 'question',
    }).then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: "put",
                url: "api/backoffice/groupStatus",
                data: {type: "A", groupid: groupid, userid: user_id, status: type},
                success: function (response) {
                    if(response.res_code == "00"){
                        Swal.fire('สำเร็จ', 'เปลี่ยนสถานะเรียบร้อย', 'success').then(() => {

                        })
                    }
                }
            });
        }else{
            // $('#groupTable').bootstrapTable('refresh');
            $(this).prop("checked", type == 'Y' ? false : true);
        }
        
    })
})

$('body').on('change', '.activeStatusUser', function () {
    var groupid = $(this).data('id');
    var type = $(this).prop('checked') ? 'Y' : 'N';
    Swal.fire({
        title: 'ยืนยันการเปลี่ยนสถานะ',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        icon: 'question',
    }).then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: "put",
                url: "api/backoffice/activeUser",
                data: {type: "A", uid: groupid, userid: user_id, status: type},
                success: function (response) {
                    if(response.res_code == "00"){
                        Swal.fire('สำเร็จ', 'เปลี่ยนสถานะเรียบร้อย', 'success').then(() => {

                        })
                    }
                }
            });
        }else{
            // $('#groupTable').bootstrapTable('refresh');
            $(this).prop("checked", type == 'Y' ? false : true);
        }
        
    })
})

$('body').on('click', '.remove-user-btn', function () {
    Swal.fire({
        title: 'ยืนยันการลบ',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        icon: 'question',
    }).then((result) => {
        var id = $(this).data('id');
        $.ajax({
            type: "put",
            url: "api/backoffice/activeUser",
            data: {type: "S", uid: id, userid: user_id},
            success: function (response) {
                if(response.res_code == "00"){
                    Swal.fire('สำเร็จ', 'ลบข้อมูลเรียบร้อย', 'success').then(() => {
                        $('#groupTable').bootstrapTable('refresh');
                    })
                }
            }
        });
    })
})


FilePond.registerPlugin(FilePondPluginFileEncode, FilePondPluginFileValidateSize, FilePondPluginImageExifOrientation, FilePondPluginImagePreview,)

var option = { 
    labelIdle: 'Drag & Drop your picture or <span class="filepond--label-action">Browse</span>', 
    imagePreviewHeight: 170, 
    imageCropAspectRatio: "1:1", 
    imageResizeTargetWidth: 200, 
    imageResizeTargetHeight: 200, 
    stylePanelLayout: "compact circle", 
    styleLoadIndicatorPosition: "center bottom", 
    styleProgressIndicatorPosition: "right bottom", 
    styleButtonRemoveItemPosition: "left bottom", 
    styleButtonProcessItemPosition: "right bottom",
    acceptedFileTypes: ['image/*']
}
if($('#urlPreview').val() != ''){
    option.files = [$('#urlPreview').val()]
}

const pond = FilePond.create(
    document.querySelector(".filepond-input-circle"), 
    option
);

$('#birthday').datetimepicker({
    timepicker:false,
    format:'Y-m-d',
    lang:'th'
});

$('#formUser').submit(function (e) {
    e.preventDefault();
    var form = $('#formUser')[0];
    var data = new FormData(form);
    data.append("user_update", user_id);
    data.append("branch", $('#selBranch').val());
    $.ajax({
        type: "POST",
        url: "/api/backoffice/userSave",
        data: data,
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData:false,
        success: function (response) {
            if(response.res_code == '00'){
                Swal.fire(
                    'สำเร็จ',
                    'บันทึกข้อมูลเรียบร้อย',
                    'success'
                ).then(() => {
                    window.location.href = '/users';
                })
            }else{
                Swal.fire(
                    'Login fail',
                    'username or password incorrect',
                    'error'
                )  
            }
        }
    });
});