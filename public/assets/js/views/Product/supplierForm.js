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
                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
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





$('body').on("submit", "form#formSupplier", function(e) {
    e.preventDefault();
    let form = $('#formSupplier');
    let is_valid = form.get(0).checkValidity();
    if(!is_valid){
        form.addClass("was-validated");
        return false;
    }

    var url = "/calldata/saveSupplier";
    var formData = new FormData(this);
    $('.loader-frame').show();
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
                Swal.fire('เรียบร้อย','บันทึกข้อมูลเรียบร้อย','success').then(() => {
                    window.location.href = '/supplier';
                })
            }else{
                Swal.fire('ผิดพลาด',data.res_text,'error')
            }
        }, 1000);
    })
});