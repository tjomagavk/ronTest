$(document).ready(function () {
    var loginModal = $('#login');
    if (loginModal.find('.alert') && loginModal.find('.alert').size() > 0) {
        loginModal.modal();
    }
    var editProfileModal = $('#editProfile');
    if (editProfileModal.find('.error') && editProfileModal.find('.error').size() > 0) {
        editProfileModal.modal();
    }
    var editProfilePhotoModal = $('#editProfilePhoto');
    if (editProfilePhotoModal.find('.error') && editProfilePhotoModal.find('.error').size() > 0) {
        editProfilePhotoModal.modal();
    }
    var changePasswordModal = $('#changePassword');
    if (changePasswordModal.find('.error p') && changePasswordModal.find('.error p').html()) {
        changePasswordModal.modal();
    }

    if (isMobile) {
        $("header.desktop").remove();
        $("header.mobile").css({'display': 'block'});
        $("section.first").removeClass("first");
        removeMainSlider();
    } else {
        $("header.mobile").remove();
        $("header.desktop").css({'display': 'block'});
        //$(window).scroll(function () {
        //    if (!isMobile && $(this).scrollTop() > 150) {
        //        $('header').addClass("header-collapse");
        //        $('body section.first').addClass("header-collapse");
        //    } else {
        //        $('header').removeClass("header-collapse");
        //        $('body section.first').removeClass("header-collapse");
        //    }
        //});
    }
    //собираем слайдер
    createMainSlider();
});

/**
 * Для стилизации загрузки файлов
 */
$(document).on('change', '.btn-file :file', function () {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});
/**
 * Для стилизации загрузки файлов
 */
$(document).ready(function () {
    $('.btn-file :file').on('fileselect', function (event, numFiles, label) {

        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

        if (input.length) {
            input.val(log);
        } else {
            if (log) alert(log);
        }

    });
});


/**
 * Вешаем слушателя на мужчину/женщину
 * radio ids ['btn-male', 'btn-female']
 * поле, уходящее на сервер id gender
 */
function genderListener() {
    var btns = ['btn-male', 'btn-female'];
    var input = document.getElementById('gender');
    for (var i = 0; i < btns.length; i++) {
        document.getElementById(btns[i]).addEventListener('click', function () {
            input.value = this.value;
            if (input.value == 1) {
                $('#btn-male').prop('checked', true);
                $('#btn-female').prop('checked', false);
            } else {
                $('#btn-male').prop('checked', false);
                $('#btn-female').prop('checked', true);
            }
        });
    }
}

/**
 * проверяем, выбран ли пол
 * radio ids ['btn-male', 'btn-female']
 * поле, уходящее на сервер id gender
 * если 1 = btn-male, если 2 = btn-female
 */
function checkedGenderRadio() {
    var gender = $('#gender').val();
    if (gender) {
        if (gender == 1) {
            $('#btn-male').prop('checked', true);
        } else {
            $('#btn-female').prop('checked', true);
        }
    }
}

/**
 * Конверстация даты рождения в timestamp unix
 *
 * Дата из поля с id = dobUser
 * timestamp unix = dob
 */
function dobToMs() {
    var dobUser = $('#dobUser').val();
    if (dobUser) {
        var dateArr = dobUser.split('.');
        $('#dob').val(new Date(dateArr[2], dateArr[1] - 1, dateArr[0]).getTime() / 1000);
    }
}

/**
 * Для ajax-пагинации
 */
$(document).on('as_complete', document, function (e, d) {
    $(".pagin." + d["key"]).append(d["pagination"]);
    upPage();
    $(document).on('click', '.' + d["key"] + ' .pagination a', function (e) {
        upPage();
        e.preventDefault();
        var url = $(this).attr('href');
        //console.log(url);
        $.post(url, {as_action: d["key"]}, function (response) {
            if (typeof response.output !== "undefined") {
                $('.ajax-snippet#' + d["key"]).html(response.output);
                $(".pagin." + d["key"]).html(response.pagination);
            }
        }, "json");
    });
});

/**
 * Уходим вверх страницы
 */
function upPage() {
    $("body,html").animate({scrollTop: 0}, 500);
}
//
///**
// * Для ajax-пагинации
// */
//$(document).ready(function () {
//    var active = $(document).find('.changeNews .active').attr('href');
//    if (active && active == '#event') {
//        $(document).find('event').removeClass(hidden)
//    }
//    if (changePasswordModal.find('.error p') && changePasswordModal.find('.error p').html()) {
//        changePasswordModal.modal();
//    }
//    $(".pagin." + d["key"]).append(d["pagination"]);
//    $(document).on('click', '.' + d["key"] + ' .pagination a', function (e) {
//        e.preventDefault();
//        var url = $(this).attr('href');
//        //console.log(url);
//        $.post(url, {as_action: d["key"]}, function (response) {
//            if (typeof response.output !== "undefined") {
//                $('.ajax-snippet#' + d["key"]).html(response.output);
//                $(".pagin." + d["key"]).html(response.pagination);
//            }
//        }, "json");
//    });
//});
$(document).ready(function () {
    var loc = window.location.hash;
    if (loc) {
        $(document).find(".changeNews li a[href$='" + loc + "']").click();
    }
});