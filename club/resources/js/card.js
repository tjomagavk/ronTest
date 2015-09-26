//var num_elem = 0; // счетчик для аппликаций

//$(document).ready(function () {
//    // добавление аппликации в рабочую область
//    $('.applications_div img').click(function () {
//        addApplication($(this));
//    });
//
//    // удаление аппликации
//    $('body').on("click", ".close_applic", function () {
//        $(this).parent().remove();
//    });
//
//    // сохранение картинки
//    $('#create_image').click(function () {
//        ajaxMakeImage();
//    });
//});

function sendCardMail(fullName, dob, email, id) {
    $.ajax({
        type: "POST",
        url: "./ron/club/resources/php/generate.php",
        data: {
            'fullName': fullName, // ФИО члена клуба
            'dob': dob, // дата рождения члена клуба
            'email': email,// email члена клуба
            'id': id // идентификатор члена клуба
        },
        dataType: "json",
        success: function (data) {
            if (data.result == 'success') {
                alert('Карта отправлена');
            } else {
                alert('Ошибка при отправке карты');
            }
        }
    });
}
//// добавление аппликации в рабочую область
//function addApplication(element) {
//    var applicImg = element.clone(); // создаем копию элемента
//    // удаляем аттрибуты размеров картинки
//    applicImg.removeAttr('width');
//    applicImg.removeAttr('height');
//    // добавляем родительский див для аппликации в рабочую область
//    var allElement = '<div class="applic_new_el_div" id="move_applic_' + num_elem + '"><span class="close_applic"></span></div>';
//    $('.work_area').append(allElement);
//    // добавляем класс для перетаскивания
//    applicImg.addClass('applic_new_el');
//    // задаем место появления в рабочей области
//    $('#move_applic_' + num_elem).css({
//        'top': '0px',
//        'left': '0px'
//    });
//    applicImg.attr('id', 'applic_' + num_elem);
//    // добавляем элемент
//    $('#move_applic_' + num_elem).append(applicImg);
//
//    init_drag(num_elem); // задаем перетаскивание
//    init_resize(num_elem); // задаем резайз
//    num_elem++; // увиличение счетчика для аппликаций
//}
//
//// задаем перетаскивание для апликации
//function init_drag(num_el) {
//    $('#move_applic_' + num_el).draggable({
//        cursor: 'move', // вид курсора
//        containment: '.work_area', // ограничение перемещения
//        scroll: false, // автоскроллинг
//        drag: null // событие при перемещении
//    });
//}
//
//// ресайз для аппликаций
//function init_resize(num_el) {
//    $('#move_applic_' + num_el).resizable({
//        aspectRatio: true, // сохранять пропорции
//        handles: 'ne, nw, se, sw', // имена классов для угловых блоков
//        alsoResize: "#applic_" + num_el // расайзим еще и родительский див - рамку
//    });
//}
//
//// создание картинки с наложением аппликации. Запрос на сервер
//function ajaxMakeImage() {
//    var arrayWidth = [];
//    var arrayHeight = [];
//    var arraySrc = [];
//    var arrayTop = [];
//    var arrayLeft = [];
//    var srcImage = $('#main_img_big').attr('src');
//    var workAreaTop = $('.work_area').offset().top;
//    var workAreaLeft = $('.work_area').offset().left;
//
//    var num = 0;
//    $('.applic_new_el_div').each(function (e) {
//        arrayWidth[num] = $(this).width();
//        arrayHeight[num] = $(this).height();
//        arraySrc[num] = $(this).children('.applic_new_el').attr('src');
//        arrayTop[num] = $(this).offset().top;
//        arrayLeft[num] = $(this).offset().left;
//        num++;
//    });
//
//    // отправляем данные на сервер
//
//    return false;
//}