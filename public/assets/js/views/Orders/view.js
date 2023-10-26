$('body').on('click', '.save-tracking', function () {
    var tracking = $('#trackingnumber').val();
    var order_id = $('#order_id').val();
    $.ajax({
        type: "put",
        url: "/api/backoffice/saveTracking",
        data: { tracking: tracking, order_id: order_id},
        success: function (response) {
            console.log(response);
            Swal.fire(
              'บันทึกสำเร็จ',
              '',
              'success'
            )
        }
    });
})

tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
    image_title: true,
    automatic_uploads: true,
    file_picker_types: 'file image media',
    file_picker_callback: function (cb, value, meta) {
      var input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      input.onchange = function () {
        var file = this.files[0];
  
        var reader = new FileReader();
        reader.onload = function () {
          var id = 'blobid' + (new Date()).getTime();
          var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
          var base64 = reader.result.split(',')[1];
          var blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);
          cb(blobInfo.blobUri(), { title: file.name });
        };
        reader.readAsDataURL(file);
      };
      input.click();
    },
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
});

$('body').on('click', '#btn_deposit_advance', function () {
    Swal.fire({
        title: 'ยืนยันการบันทึก',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        icon: 'question',
    }).then((result) => {
        if(result.isConfirmed){
            var id = $(this).data('id');
            $.ajax({
                type: "patch",
                url: "/api/backoffice/deposit_advance",
                data: {order_id: $('#order_id').val(), uid: id, order_deposit: $('#deposit_advance').val()},
                success: function (response) {
                    if(response.res_code == "00"){
                        Swal.fire('สำเร็จ', 'บันทึกข้อมูลเรียบร้อย', 'success').then(() => {
                            var order_price = $(".order_price").text() == '' ? 0 : $(".order_price").text();
                            var order_advan = $("#deposit_advance").val() == '' ? 0 : $("#deposit_advance").val();
                            console.log(order_price, order_advan);
                            var calculate = (parseFloat(order_price) - parseFloat(order_advan)).toFixed(2);
                            calculate = parseFloat(calculate);
                            var calu = calculate.toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                            $('.advance_deposit').text(calu);
                        })
                    }
                }
            });
        }
    })
})

$('body').on('click', '.btn_save_note', function () {
    Swal.fire({
        title: 'ยืนยันการบันทึก',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        icon: 'question',
    }).then((result) => {
        if(result.isConfirmed){
            var id = $(this).data('id');
            $.ajax({
                type: "patch",
                url: "/api/backoffice/note_internal",
                data: {order_id: $('#order_id').val(), uid: id, note_internal: tinyMCE.activeEditor.getContent()},
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

$('body').on('click', '.btn-update-status', function () {
    var order_id = $('#order_id').val();
    var datamessage = "";
    var datapoint = $(this).attr('data-point');
    var datapointstatus = $(this).attr('data-status-earn');
    var datacustomer = $(this).attr('data-customer');
    var databurnpoint = $(this).attr('data-burn-point');
    var status = $("#status_select").val();

    if(status == 'Cancel'){
        Swal.fire({
            title: 'Submit your Github username',
            input: 'text',
            inputAttributes: {
              autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Look up',
            showLoaderOnConfirm: true,
            preConfirm: (res_inp) => {
                return res_inp
            },
            allowOutsideClick: () => !Swal.isLoading()
          }).then((result) => {
            console.log(result.value);
            if (result.isConfirmed) {
                datamessage = result.value;
                update_status(order_id, datamessage, datapoint, datapointstatus, datacustomer, databurnpoint, status)
            }
          })
    }else{
        update_status(order_id, datamessage, datapoint, datapointstatus, datacustomer, databurnpoint, status)
    }
})


function update_status(order_id, message, point, pointstatus, customer, burnpoint, status) {
    $.ajax({
        type: "PATCH",
        url: "/api/backoffice/update_status_order",
        data: {
            order_id    : order_id,
            message     : message,
            point       : point,
            burnpoint   : burnpoint,
            pointstatus : pointstatus,
            customer    : customer,
            status      : status,
            userid      : user_id
        },
        success: function (response) {
            
        }
    });
}