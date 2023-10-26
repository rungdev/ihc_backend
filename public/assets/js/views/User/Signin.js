$(function () {  
    $('#formLogin').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "checkSignin",
            data: { username: $("#username").val(), password: $("#password").val()},
            success: function (response) {
                console.log(response);
                if(response.res_code == '00'){
                    window.location.href = 'dashboard';
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
    console.log($('#formLogin'));
})