'use strict';

listen('click', '#adminLoginBtn', function () {
    $('#formInputEmail').val('admin@infynews.com');
    $('#formInputPassword').val(123456);
});

listen('click', '#staffLoginBtn', function () {
    $('#formInputEmail').val('staff@infynews.com');
    $('#formInputPassword').val(12345678);
});

listen('click', '#customerLoginBtn', function () {
    $('#formInputEmail').val('customer@infynews.com');
    $('#formInputPassword').val(12345678);
});
