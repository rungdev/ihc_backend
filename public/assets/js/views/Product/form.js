var starter = 1;
var dropzones = [];
var branch = [];


Dropzone.autoDiscover = false;

var myDropzone = new Dropzone("#productGallery", 
{
    cursor: 'move',
    tolerance: 'pointer',
    autoProcessQueue: false,
    url: "/product/imgtemp", 
    acceptedFiles: ".png, .jpg, .jpeg",
    thumbnailWidth: 120,
    thumbnailHeight: 120,
    addRemoveLinks: true,
    uploadMultiple: true,
    parallelUploads: 100,
    maxFilesize: 20,
    update: function(e, ui){
        console.log(e, ui);
    },
    removedfile: function(file) {
        // var fileName = file.name;  
        // $.ajax({
        //     type: 'POST',
        //     url: 'uploadFile.php',
        //     data: {name: fileName,request: 'remove'},
        //     sucess: function(data){
        //         console.log('success: ' + data);
        //     }
        // });
        var _ref;
        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
    },
    init: function(e) {
        var t = this;
        $('#productGallery').sortable({
            stop: function () {
                var queue = t.files;
                newQueue = [];
                $('#productGallery .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
                    var name = el.innerHTML;
                    queue.forEach(function (file) {
                        if (file.name === name) {
                            newQueue.push(file);
                        }
                    });
                });
                t.files = newQueue;  
          }
        })
        this.on("sending", function(file, xhr, formData){
            formData.append("token", token_login);
            formData.append("type", 'product');
            formData.append("row", '0');
        });
        // this.on("complete", function(file) {
        //     $(".dz-remove").html("<button class='btn btn-remove-image d-flex align-items-center justify-content-center'><span class='bx bx-trash text-danger' style='font-size: 1.5em'></span></button>");
        // });
    }
});
$(".fileGallery").each(function () {
    var mockFile = {
        name: $(this).data("file"),
        status: Dropzone.ADDED, 
    };
    myDropzone.emit("addedfile", mockFile);
    myDropzone.emit("thumbnail", mockFile, $(this).val());
    myDropzone.emit("complete", mockFile);
    myDropzone.files.push(mockFile);
})

dropzones.push(myDropzone);

$('.option-choose').each(function () {
    var id = $(this).data('id');
    var optionFile = new Dropzone("#productOption_"+id, 
        { 
            autoProcessQueue: false,
            url: "/product/imgtemp", 
            acceptedFiles: ".png, .jpg, .jpeg",
            thumbnailWidth: 120,
            thumbnailHeight: 120,
            addRemoveLinks: true,
            uploadMultiple: true,
            parallelUploads: 100,
            maxFilesize: 20,
            removedfile: function(file) {
                console.log(file);
                // var fileName = file.name;  
                // $.ajax({
                //     type: 'POST',
                //     url: 'uploadFile.php',
                //     data: {name: fileName,request: 'remove'},
                //     sucess: function(data){
                //         console.log('success: ' + data);
                //     }
                // });
                // var _ref;
                // return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
            },
            init: function() {
                var t = this;
                $('#productOption_1').sortable({
                    stop: function () {
                        var queue = t.files;
                        newQueue = [];
                        $('#productOption_'+id+' .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
                            var name = el.innerHTML;
                            queue.forEach(function (file) {
                                if (file.name === name) {
                                    newQueue.push(file);
                                }
                            });
                        });
                        t.files = newQueue;  
                }
                })
                this.on("sending", function(file, xhr, formData){
                    formData.append("token", token_login);
                    formData.append("type", 'option');
                    formData.append("row", 1);
                });
            }
        });
        $(".fileoption"+id).each(function () {
            var mockFile = {
                name    : $(this).data("file"),
                id      : $(this).data("id"),
                status  : Dropzone.ADDED, 
            };
            optionFile.emit("addedfile", mockFile);
            optionFile.emit("thumbnail", mockFile, $(this).val());
            optionFile.emit("complete", mockFile);
            optionFile.files.push(mockFile);
        })

    dropzones.push(optionFile);
})



// myDropzone.on("sending", function(file, xhr, formData) {
//     fformData.append("token", token_login);
// });

// myDropzone.on("successmultiple", function(file, response) {
//     // get response from successful ajax request
//     console.log(response);
//     // submit the form after images upload
//     // (if u want yo submit rest of the inputs in the form)
//     document.getElementById("dropzone-form").submit();
// });

// tinymce.init({
//     selector: 'textarea',
//     plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
//     toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
//     tinycomments_mode: 'embedded',
//     tinycomments_author: 'Author name',
//     mergetags_list: [
//         { value: 'First.Name', title: 'First Name' },
//         { value: 'Email', title: 'Email' },
//     ],
//     image_title: true,
//     automatic_uploads: true,
//     images_upload_url: '/product/uploadimagemce',
//     file_picker_types: 'image',
//     file_picker_callback: function(cb, value, meta) {
//         var input = document.createElement('input');
//         input.setAttribute('type', 'file');
//         input.setAttribute('accept', 'image/*');
//         input.onchange = function() {
//             var file = this.files[0];

//             var reader = new FileReader();
//             reader.readAsDataURL(file);
//             reader.onload = function () {
//                 var id = 'blobid' + (new Date()).getTime();
//                 var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
//                 var base64 = reader.result.split(',')[1];
//                 var blobInfo = blobCache.create(id, file, base64);
//                 blobCache.add(blobInfo);
//                 cb(blobInfo.blobUri(), { title: file.name });
//             };
//         };
//         input.click();
//     }
// });

tinymce.init({
    selector: 'textarea',

    image_class_list: [
    {title: 'img-responsive', value: 'img-responsive'},
    ],
    height: 500,
    setup: function (editor) {
        editor.on('init change', function () {
            editor.save();
        });
    },
    statusbar: false,
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    image_title: true,
    automatic_uploads: true,
    images_upload_url: '/product/uploadimagemce',
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.onchange = function() {
            var file = this.files[0];

            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                var id = 'blobid' + (new Date()).getTime();
                var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), { title: file.name });
            };
        };
        input.click();
    }
});

function example_image_upload_handler (blobInfo, success, failure, progress) {


    // var formData = new FormData(this);
    // formData.append('token', token_login);
    // formData.append('userid', user_id);
    // $.ajax({
    //     type: "POST",
    //     url: '/product/uploadimagemce',
    //     data: formData,
    //     success: function (response) {
            
    //     }
    // });
        

    var xhr, formData;
  
    xhr = new XMLHttpRequest();
    xhr.withCredentials = false;
    xhr.open('POST', '/product/uploadimagemce');
  
    xhr.onload = function() {
      var json;
  
      if (xhr.status === 403) {
        failure('HTTP Error: ' + xhr.status, { remove: true });
        return;
      }
  
      if (xhr.status < 200 || xhr.status >= 300) {
        failure('HTTP Error: ' + xhr.status);
        return;
      }
  
      json = JSON.parse(xhr.responseText);
  
      if (!json || typeof json.location != 'string') {
        failure('Invalid JSON: ' + xhr.responseText);
        return;
      }
  
      success(json.location);
    };
  
    xhr.onerror = function () {
      failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
    };

    xhr.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
    formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());
  
    xhr.send(formData);

    return xhr;
};

var optionMaster = [];

$(document).ready(function() {
    $(".select-filter-option").select2()
    $(".select-icon-promation").select2()
    $("#promotion-select2").select2({
        maximumSelectionLength: 3
    })
    console.log($("#promotion-select2"));
    $.ajax({
        type: "get",
        url: "/api/backoffice/branch",
        data: {},
        success: function (response) {
            branch = response;
            console.log(branch.branch_id);
        }
    });
    $('.selectpicker').selectpicker();
    var readURL = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.profile-pic').attr('src', e.target.result);
            }
    
            reader.readAsDataURL(input.files[0]);
        }
    }
    

    $(".file-upload").on('change', function(){
        readURL(this);
    });
    
    $(".upload-button").on('click', function() {
       $(".file-upload").click();
    });

    // url: "/api/backoffice/getProductOption",
    $.ajax({
        type: "get",
        url: "/product/option",
        data: {},
        success: function (response) {
            console.log(response);
            optionMaster = response.res_result;
            console.log(optionMaster);
        }
    });

    // checkshow()
    
});

// function checkshow() {  
//     console.log($('#box-option'));
//     console.log($('#box-not-option'));
//     if($('#checkOptionST').is(':checked')){
//         $('#box-option').show();
//         $('#box-not-option').hide();
//     }else{
//         $('#box-option').hide();
//         $('#box-not-option').show();
//     }
// }

// $('body').on('change', '#checkOptionST', function () {
//     checkshow()
// })

// $('body').on('click', '.sel_option', function () {
//     var t = $(this);
//     var id = $(this).val();
//     let obj = optionMaster.find(o => o.m_option_id == id);
//     var name = $(this).data('name');
//     var option = "";
//     obj.option_parent.forEach(element => {
//         option += '<option value="'+element.m_option_id+'">'+element.option_name+'</option>';
//     });
//     $('.hidden_option_rowno').each(function () { 
//         var ids = $(this).val();
//         console.log(ids);
//         var html = '\
//             <div class="col-4 mb-3 option-'+id+'" data-id="'+id+'">\
//                 <label class="form-label" for="product-title-input">'+name+'</label>\
//                 <input type="hidden" name="sel_master_option_'+ids+'[]" class="sel_master_option" value="'+obj.m_option_id+'" />\
//                 <select class="selectpicker" name="sel_m_option_id_'+ids+'[]" id="">'+option+'\
//                 </select>\
//             </div>\
//         ';
//         if(t.is(":checked")){
//             $('.option-'+ids+' .box-option-add').append(html);
//         }else{
//             $('.option-'+id).remove()
//         }
//     })
    
//     $('.selectpicker').selectpicker();
// })




$('body').on('click', '.add-option-box', function () {
    var count = $(this).data('count');
    starter = count;
    var length = ($('.option-choose').length)+1;
    var option = "";
    var branch_h = "";

    $('.option-1 .box-option-add .col-4').each(function () {
        var dataID = $(this).data('id');
        var id_master = $(this).find('.sel_master_option').val();
        option += '<div class="col-4 mb-3 option-'+starter+'" data-id="'+starter+'">\
                        <label class="form-label" for="product-title-input">'+$(this).find('.form-label').text()+'</label>\
                        <input type="hidden" name="sel_master_option_'+starter+'[]" class="sel_master_option" value="'+id_master+'" />\
                        <select class="selectpicker" name="sel_m_option_id_'+starter+'[]" id="">'+$(this).find('.selectpicker').html()+'\
                        </select>\
                   </div>';
    })

    branch.forEach(element => {
        branch_h += '\
            <div class="col-4 mb-2">'+element.branch_name_th+'</div>\
            <div class="col-4 mb-2">\
                <input type="hidden" name="branchid_'+starter+'[]" value="'+element.branch_id+'">\
                <input type="text" name="stock_'+starter+element.branch_id+'[]" class="form-control" id="product-title-input" value="" placeholder="จำนวนสินค้าตอนนี้">\
            </div>\
            <div class="col-4 mb-2">\
                <input type="text" name="stock_alert_'+starter+element.branch_id+'[]" class="form-control" id="product-title-input" value="" placeholder="จำนวนสินค้าขั้นต่ำ">\
            </div>\
        ';
    });


    var html = '\
    <div class="option-choose option-'+starter+'" data-id="'+starter+'">\
        <hr>\
        <div class="form-group">\
            <input type="hidden" name="hiddenoption'+starter+'" id="hiddenoption'+starter+'" value="">\
            <input type="hidden" name="hidden_option_rowno[]" class="hidden_option_rowno" value="'+starter+'">\
            <input type="hidden" id="option_name_section_'+starter+'" value="name th">\
        </div>\
        <div class="row">\
            <div class="col-8"><h5>ตัวเลือกที่ '+length+'</h5></div>\
            <div class="col-4 text-end"><button type="button" class="btn btn-danger btn-sm btn-remove-option" data-id="'+starter+'"><i class="mdi mdi-trash-can-outline"></i> ลบตัวเลือก</button></div>\
        </div>\
        <div class="row mb-3">\
            <div class="col-4">\
                <label class="form-label" for="product-title-input">บาร์โค้ด</label>\
                <input type="text" class="form-control" id="product-title-input" name="barcode_option'+starter+'[]" value="" placeholder="บาร์โค้ด" >\
            </div>\
            <div class="col-4">\
                <label class="form-label" for="product-title-input">รุ่น</label>\
                <input type="text" class="form-control" id="product-title-input" name="sku_option'+starter+'[]" value="" placeholder="รุ่น">\
            </div>\
            <div class="col-4">\
                <label class="form-label" for="product-title-input">Order</label>\
                <input type="text" class="form-control" id="product-title-input" name="orderby_option'+starter+'[]" value="" placeholder="Order">\
            </div>\
        </div>\
        <div class="row box-option-add">'+option+'</div>\
            <div class="row mb-3">\
                <div class="col-4">\
                    <label class="form-label" for="product-title-input">ราคาทุน</label>\
                    <input type="text" class="form-control" id="product-title-input" name="cost_price_option'+starter+'[]" value="" placeholder="ราคาทุน">\
                </div>\
                <div class="col-4">\
                    <label class="form-label" for="product-title-input">ราคาตลาด</label>\
                    <input type="text" class="form-control" id="product-title-input" name="market_price_option'+starter+'[]" value="" placeholder="ราคาตลาด">\
                </div>\
                <div class="col-4">\
                    <label class="form-label" for="product-title-input">ราคาขาย <span class="text-red">*</span></label>\
                    <input type="text" class="form-control" id="product-title-input" name="sell_price_option'+starter+'[]" value="" placeholder="ราคาขาย" required="">\
                </div>\
            <div class="col-12 mt-3">\
                <div class="dropzone" id="productOption_'+starter+'"></div>\
            </div>\
        </div>\
        <div class="row">\
            <div class="col-4"><b>สาขา</b></div>\
            <div class="col-4"><b>จำนวนสินค้าตอนนี้</b></div>\
            <div class="col-4"><b>จำนวนสินค้าขั้นต่ำ(ซ่อนสินค้าหน้าเว็บ)</b></div>\
            <div class="col-12"><hr></div>\
            '+branch_h+'\
        </div>\
    </div>';
    $('.option-box').append(html);

    $('.selectpicker').selectpicker();

    
    dropzones.push(new Dropzone("#productOption_"+starter, 
    { 
        autoProcessQueue: false,
        url: "/product/imgtemp", 
        acceptedFiles: ".png, .jpg, .jpeg",
        thumbnailWidth: 120,
        thumbnailHeight: 120,
        addRemoveLinks: true,
        uploadMultiple: true,
        parallelUploads: 100,
        maxFilesize: 20,
        removedfile: function(file) {
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        },
        init: function() {
            var t = this;
            $("#productOption_"+starter).sortable({
                stop: function () {
                    var queue = t.files;
                    newQueue = [];
                    $("#productOption_"+starter+' .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
                        var name = el.innerHTML;
                        queue.forEach(function (file) {
                            if (file.name === name) {
                                newQueue.push(file);
                            }
                        });
                    });
                    t.files = newQueue;  
              }
            })
            this.on("sending", function(file, xhr, formData){
                formData.append("token", token_login);
                formData.append("type", 'option');
                formData.append("row", starter);
            });
        }
    }));

    count++;
    $(this).data('count', count);
})


$('body').on('click', '.btn-remove-option', function () {
    var id = $(this).data('id');
    $('.option-'+id).remove();
    var count = 0;
    $('.option-choose').each(function () {
        count++;
        $(this).find('h5').text('ตัวเลือกที่ '+count);
    })
})



$('#uploadfiles').click(function(){

});
$('body').on("submit", "form#createproduct-form", function(e) {
    e.preventDefault();
    console.log($('#discript_th').val());
    var chk1 = $('input[name="optionsType"]:checked').val();
    var chk2 = $('input[name="price_type"]:checked').val();
    if($('#name_th').val() == ''){
        Swal.fire('แจ้งเตือน','กรุณากรอกชื่อสินค้า (th)','warning')
        return false;
    }else if($('#cat_id').val() == ''){
        Swal.fire('แจ้งเตือน','กรุณาเลือกหมวดหมู่หลัก','warning')
        return false;
    }else if($('#sell_price').val() == ''){
        if(chk1 == 'N'){
            Swal.fire('แจ้งเตือน','กรุณากรอกราคาขาย','warning')
            return false;
        }else if(chk1 == 'N'){
            Swal.fire('แจ้งเตือน','กรุณากรอกราคาขาย','warning')
            return false;
        }
        
    }

    $('.loader-frame').show();
    var checkcount = 0;
    var checkupload = 0;

    dropzones.forEach(element => {
        // console.log(element.files);
        element.files.forEach(ele => {
            if(ele.status == "queued"){
                checkcount++;
            }
        });
        // checkcount += element.files.length;
        element.on("complete", function() {
            checkupload++;
        });
        element.processQueue()
    });
    
    var setIn = setInterval(() => {
        console.log(checkcount, checkupload);
        if(checkcount == checkupload){
            clearInterval(setIn);
            // var url = "/api/backoffice/createProduct";
            var url = "/product/store";
            if($('#product_id').val() != ''){
                // var url = "/api/backoffice/updateProduct";  
                var url = "/product/update";  
            }
            
            var formData = new FormData(this);
            formData.append('token', token_login);
            formData.append('userid', user_id);
            $.ajax({
                type: 'POST',
                url: url, 
                data: formData,
                contentType: false,
                cache: false,
                processData:false,
                dataType: "json",
            }).then((data) => {
                setTimeout(() => {
                    $('.loader-frame').hide();
                    if (data.res_code == '00') {
                        Swal.fire('เรียบร้อย','บันทึกข้อมูลเรียบร้อย','success')
                    }else{
                        Swal.fire('ผิดพลาด',data.res_text,'error')
                    }
                }, 1000);
            })
        }
    }, 500);

});

$('body').on('click', '#stock_status', function () {
    if($(this).is(':checked')){
        $('#branchBox').hide();
    }else{
        $('#branchBox').show();
    }
})

function isData() {
    var t = document.getElementsByClassName("plus"),
        e = document.getElementsByClassName("minus"),
        n = document.getElementsByClassName("product");
    t && Array.from(t).forEach(function(t) {
        t.addEventListener("click", function(e) {
            parseInt(t.previousElementSibling.value) < e.target.previousElementSibling.getAttribute("max") && (e.target.previousElementSibling.value++, n) && Array.from(n).forEach(function(t) {
                updateQuantity(e.target)
            })
        })
    }), e && Array.from(e).forEach(function(t) {
        t.addEventListener("click", function(e) {
            parseInt(t.nextElementSibling.value) > e.target.nextElementSibling.getAttribute("min") && (e.target.nextElementSibling.value--, n) && Array.from(n).forEach(function(t) {
                updateQuantity(e.target)
            })
        })
    })
}
isData();

var arr_option = [];
$('.text-title-option').each(function () {
    arr_option.push($(this).text());
})

$('body').on('change', '.sel_option', function () {
    var id = $(this).val()
    var name = $(this).data('name');
    
    if ($(this).is(':checked')) {
        var html = '' + 
        '<div class="row" id="choose'+id+'" data-id="'+id+'">' + 
        '    <div class="col-12 text-title-option"><h5>'+name+'</h5></div>' + 
        '    <div class="col-10">' + 
        '        <input type="text" class="form-control inp-choose" id="inp-choose-'+id+'" placeholder="กรอกคำที่ต้องการแล้ว Enter" data-id="'+id+'">' + 
        '    </div>' + 
        '    <div class="col-2">' + 
        '        <button class="btn btn-soft-secondary btn-option-choose form-control shadow-none btn-add-choose" type="button" data-id="'+id+'">เพิ่ม</button>' + 
        '    </div>' + 
        '    <div class="col-12 mt-3" id="choose-box-'+id+'"></div>' + 
        '</div>' + 
        '';
        $('.box-option-choose').append(html);
        arr_option.push(name);
    }else{
        var index = arr_option.indexOf(name);
        if (index !== -1) {
            arr_option.splice(index, 1);
        }
        $('#choose'+id).remove();
        filter_option_check()
    }
    if (arr_option.length <= 0) {
        $('.box-option-choose').hide()
    }else{
        $('.box-option-choose').show()
    }
})

$('body').on('keypress', '.inp-choose',function (e) {
    if(e.which == 13){
        // console.log('enter');
        add_choose($(this).data('id'))
        setPrice()
        e.preventDefault();
        return false;
    }
})
$('body').on('keypress', 'input',function (e) {
    if(e.which == 13){
        e.preventDefault();
        return false;
    }
})

function add_choose(id,text){
    var text = $('#inp-choose-'+id).val();
    console.log(text);
    if(text != ''){
        var no = 1;
        if($('.option-'+id+':last').data('no')){
            no = $('.option-'+id+':last').data('no') + 1;
        }
        text = text.trim();
        if(text != ''){
            var html = '' + 
            '<div class="option-tags option-'+id+' me-2 mb-2" id="option-'+id+'-'+no+'" data-no="'+no+'"  data-id="'+id+'">' + 
            '    <img class="img-preview-option" src="" alt="" id="preview-option-'+id+'-'+no+'" onerror="this.onerror=null;this.src=\'/assets/images/notImageAva2.jpg\'" data-id="'+id+'" data-no="'+no+'">' + 
            '    <input type="file" name="img-option-'+id+'[]" id="img-option-'+id+'-'+no+'" class="d-none img-inp" data-id="'+id+'" data-no="'+no+'">' + 
            '    <input type="hidden" name="option_text'+id+'[]" value="'+text+'" id="option-text-'+id+'-'+no+'">'+
            '    <input type="hidden" name="sub_id'+id+'[]" value="">'+
            '    <span id="text-option-'+id+'-'+no+'">'+text+'</span>' + 
            '    <i class="mdi mdi-pencil btn-edit-option" data-id="'+id+'" data-no="'+no+'" data-text="'+text+'"></i>' + 
            '</div>' + 
            '';
            $('#choose-box-'+id).append(html);
            $('#inp-choose-'+id).val('');
        }
        filter_option_check()
        if($('.option-tags').length <= 0){
            $('.filter-option-box').hide()
            $('.scrollme').hide()
        }else{
            $('.filter-option-box').show()
            $('.scrollme').show()
        }
    }
    
}

function filter_option_check() {
    var filter = 'แบบสินค้า : <span class="filter-option active">ทั้งหมด</span>';
    var arr = [];

    $('.option-tags').each(function (ele) {
        var id = $(this).data('id');
        var no = $(this).data('no');
        var text = $('#text-option-'+id+'-'+no).text();
        var chk = arr.findIndex((opt) => opt.id==id);
        if(chk != -1){
            arr[chk].op.push(text);
        }else{
            arr.push({
                id : id,
                op : [text]
            })
        }
        filter += ' <span class="filter-option">'+text+'</span>';
    });

    var probability = [];
    arr.forEach(element => {
        probability.push(element.op);
    });
    // console.log(arr);
    var proba = [];
    if(probability.length > 0){
        // console.log(probability);
        proba = generateAllPossiblePairs(probability);
    }
    
    $('.filter-option-box').html(filter);
    var t_html = '<table class="table table-option table-responsive">'+
    '<thead>'+
        '<tr>';
        arr_option.forEach(element => {
            t_html +='<th width="120">'+element+'</th>'
        });
    t_html +='<th width="120">บาร์โค้ด</th>'+
        '    <th width="100">รุ่น</th>'+
        '    <th width="100">ราคาทุน</th>'+
        '    <th width="100">ราคาขาย</th>'+
        '    <th width="100">จำนวน</th>'+
        '    <th width="100" class="text-center">แสดง/ซ่อน</th>'+
        '</tr>'+
    '</thead>'+
    '<tbody>'

    proba.forEach(element => {
        t_html += '<tr>';
        element.forEach((el, index) => {
            // console.log(el, index);
            t_html += '<td class="option_table'+arr[index].id+'">'+
            '<span id="">'+el+'</span>'+
            '<input type="hidden" value="'+el+'" name="option_table'+arr[index].id+'[]">'+
            '<input type="hidden" value="" name="ch_id'+arr[index].id+'[]"></input>'
            '</td>';
        });
        t_html += '<td><input type="hidden" class="form-control inp-data" name="option_id[]" value=""><input type="text" class="form-control inp-data" name="barcode_option[]" placeholder="บาร์โค้ด"></td>'+
            '<td><input type="text" class="form-control inp-data" name="sku_option[]" placeholder="รุ่น"></td>'+
            '<td><input type="text" class="form-control inp-data" name="cost_option[]" placeholder="ราคาทุน"></td>'+
            '<td><input type="text" class="form-control inp-data" name="sell_option[]" placeholder="ราคาขาย"></td>'+
            '<td><input type="text" class="form-control inp-data" name="quatity_option[]" placeholder="จำนวน"></td>'+
            '<td>'+
                '<div class="form-check form-switch form-switch text-center" dir="ltr">'+
                    '<input type="checkbox" class="form-check-input" id="customSwitchsizelg" name="webshow[]">'+
                '</div>'+
            '</td>'+
        '</tr>'
    });
    t_html += '</tbody>'
            '</table>';
    $('.scrollme').html(t_html);
}

$('body').on('click', '.img-preview-option', function () {
    var id = $(this).data('id')
    var no = $(this).data('no')
    console.log(id, no);
    console.log($('#img-option-'+id+'-'+no));
    $('#img-option-'+id+'-'+no).click()

})

$('body').on('change', '.img-inp', function(){
    var id = $(this).data('id')
    var no = $(this).data('no')
    const file = this.files[0];
    console.log(file);
    if (file){
      let reader = new FileReader();
      reader.onload = function(event){
        console.log(event.target.result);
        $('#preview-option-'+id+'-'+no).attr('src', event.target.result);
        
      }
      reader.readAsDataURL(file);
    }
});


function generateAllPossiblePairs(arrays) {
    
    if (arrays.length === 0) {
        return [[]];
    }

    const firstArray = arrays[0];
    const remainingArrays = arrays.slice(1);
    const remainingPairs = generateAllPossiblePairs(remainingArrays);
    

    const pairs = [];

    for (const item of firstArray) {
        for (const pair of remainingPairs) {
            pairs.push([item, ...pair]);
        }
    }
    return pairs;
}

$('body').on('click' ,'.filter-option', function () {

    $('.filter-option').removeClass('active');
    $(this).addClass('active');

    var value = $(this).text().toLowerCase();
    if(value == 'ทั้งหมด'){
        value = '';
    }
    console.log($(".table-option tbody tr"));
    $(".table-option tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
})

$('body').on('change', 'input[name="optionsType"]', function () {
    hide_html()
})
$('body').on('change', 'input[name="price_type"]', function () {
    setPrice();
    hide_html()
})

function hide_html() {
    var chk1 = $('input[name="optionsType"]:checked').val();
    var chk2 = $('input[name="price_type"]:checked').val();
    if(chk1 == 'Y'){
        $('#quatation').hide();
        $('#price-switch').show();
        $('#box-option').show();
        if(chk2 == 'Y'){
            $('.price-all').hide();
        }else{
            $('.price-all').show();
        }
    }else{
        $('#quatation').show();
        $('#price-switch').hide();
        $('#box-option').hide();
        $('.price-all').show();
    }
}

$('body').on('click', '.btn-edit-option', function () {
    var id = $(this).data('id')
    var no = $(this).data('no')
    var text = $(this).data('text')
    $('#img-option-modal').attr('src', $('#preview-option-'+id+'-'+no).attr('src'))
    $('#text-option-modal').val(text)
    $('#change-img-modal').data('id', id);
    $('#change-img-modal').data('no', no);    
    $('#remove-img-modal').data('id', id);
    $('#remove-img-modal').data('no', no);
    $('#btn-save-modal').data('id', id);
    $('#btn-save-modal').data('no', no);

    $('#modelOption').modal('show')

})

$('body').on('click', '#btn-save-modal', function () {
    var id = $(this).data('id')
    var no = $(this).data('no')
    var te = $('#text-option-modal').val()

    $("#text-option-"+id+"-"+no).text(te);
    $("#option-text-"+id+"-"+no).val(te);

    // filter_option_check();

    $(".option-table-"+id+'-'+no).each(function () {
        $(this).text(te);
    })
    $(".option-input-"+id+'-'+no).each(function () {
        $(this).val(te);
    })

    $('#modelOption').modal('hide');
})

$('body').on('click', '#change-img-modal', function () {
    $('#image-modal').click();
})

$('body').on('change', '#image-modal', function () {
    const file = this.files[0];
    if (file){
      let reader = new FileReader();
      reader.onload = function(event){
        $('#img-option-modal').attr('src', event.target.result);
      }
      reader.readAsDataURL(file);
    }
})

$('body').on('click', '#btn-add-filter', function () {
    var id = $('#filter_id').val();
    console.log(id);
    $.ajax({
        type: "GET",
        url: "/product/filtersubById/"+id,
        data: {},
        success: function (response) {
            var html = '';
            if(response.res_code == '00'){
                var key = 0;
                if($('.btn-remove-filter:last').data('id')){
                    key = $('.btn-remove-filter:last').data('id');
                }
                console.log(key);
                response.res_result.forEach(element => {
                    key++;
                    html += '' + 
                    '<div id="list-of-filter-'+key+'">' + 
                    '    <div class="d-flex align-self-center">' + 
                    '        <label class="mt-3">'+element.name_th+'</label>' + 
                    '        <i class="ri-delete-bin-fill m-auto mt-3 me-0 btn-remove-filter" data-id="'+key+'"></i>' + 
                    '    </div>' + 
                    '    <input name="product_filter[]" value="" type="hidden" >' + 
                    '    <input name="filter[]" value="'+element.filter_id_parent+'" type="hidden" >' + 
                    '    <select class="form-control select-filter-option select-filter-'+element.filter_id_parent+'" multiple="multiple" name="select-filter-'+element.filter_id_parent+'[]">';
                    element.subs.forEach(ele => {
                        html += '<option value="'+ele.filter_id+'">'+ele.name_th+'</option>';
                    });
                    html += '    </select>' + 
                    '</div>';
                });
                $("#box-filter").append(html);
                $(".select-filter-option").select2()
            }
        }
    });
})

$('body').on('click', '.btn-remove-filter', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'ยืนยันต้องการลบรายการนี้',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        icon: 'warning',
    }).then((result) => {
        if(result.isConfirmed){
            $('#list-of-filter-'+id).remove();
        }
    })
    
})


function setPrice() {
    var chk = $('input[name="price_type"]:checked').val();
    var cost = $('#cost_price').val();
    var sell = $('#sell_price').val();
    if(chk == 'Y'){
        $('input[name="cost_option[]"]').prop('readonly', false);
        $('input[name="sell_option[]"]').prop('readonly', false);
    }else{
        console.log($('input[name="cost_option[]"]'));
        $('input[name="cost_option[]"]').prop('readonly', true).val(cost);
        $('input[name="sell_option[]"]').prop('readonly', true).val(sell);
    }
}


$('body').on('change', '.radio-recoment', function () {
    var id = $(this).find('input').val();
    if(id == 3){
        $('.box-select').show();
    }else{
        $('.box-select').hide();
    }
})