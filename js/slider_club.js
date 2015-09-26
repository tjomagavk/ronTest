/**
 * Собираем слайдер
 */
function createMainSlider() {
    $(".slider").each(function () { // обрабатываем каждый слайдер
        var obj = $(this);
        $(obj).append("<div class='row'><div class='nav'><div class='hidden-xs hidden-sm col-md-5 col-lg-5'></div> <div class='hidden-xs hidden-sm col-md-6 col-lg-6 text-center navigation'></div>");
        $(obj).find("li ").each(function () {
            $(obj).find(".nav .navigation").append("<span id='slider-navigation-" + $(this).index() + "' rel='" + $(this).index() + "'></span>"); // добавляем блок навигации
            $(obj).find("#slider-navigation-" + $(this).index()).append($(this).find(".navigation img"));
            $(this).addClass("slider" + $(this).index());

        });
        $(obj).find(".nav").find("span").first().addClass("on"); // делаем активным первый элемент меню
    });
    if (autoListingMainSlider) {
        autoListingSlider();
    }
}

/**
 * Удаляем слайдер
 */
function removeMainSlider() {
    $(".section-slider").remove();
}

/**
 * Автоматическое листание слайдера
 */
function autoListingSlider() {
    setInterval(function () {
        nextSlide();
    }, duration * 2)
}

/**
 * Переходим на следующий слайд
 */
function nextSlide() {
    var slider = $(".slider");
    var nav = slider.find(".nav");
    var sl = nav.find(".on");
    $(sl).removeClass("on");
    var obj = $(sl).attr("rel"); // узнаем его номер

    if ($(sl).next() && $(sl).next().size() != 0) {
        $(sl).next().addClass("on");
        obj++;
    } else {
        $(slider).find(".nav").find("span").first().addClass("on");
        obj = 0;
    }

    sliderJS(obj, slider); // слайдим
}

/**
 * Переходим на предыдущий слайд
 */
function prevSlide() {
    var slider = $(".slider");
    var nav = slider.find(".nav");
    var sl = nav.find(".on");
    $(sl).removeClass("on");
    var obj = $(sl).attr("rel"); // узнаем его номер

    if ($(sl).prev() && $(sl).prev().size() != 0) {
        $(sl).prev().addClass("on");
        obj--;
    } else {
        var last = $(slider).find(".nav").find("span").last().attr("rel");
        $(slider).find(".nav").find("span").last().addClass("on");
        obj = last;
    }

    sliderJS(obj, slider); // слайдим

}


/**
 * Перемещение слайдера
 * @param obj слайд в который необходимо переместиться
 * @param sl слайд, из которого необходимо уйти
 */
function sliderJS(obj, sl) { // slider function
    var ul = $(sl).find("ul"); // находим блок
    var bl = $(sl).find("li.slider" + obj); // находим любой из элементов блока
    var step = $(bl).outerWidth(true); // ширина объекта
    $(ul).animate({marginLeft: "-" + step * obj}, 500); // 500 это скорость перемотки
}


/**
 * Отлавливаем клики по навигации
 */
$(document).on("click", ".slider .nav span", function () { // slider click navigate
    var sl = $(this).closest(".slider"); // находим, в каком блоке был клик
    $(sl).find("span").removeClass("on"); // убираем активный элемент
    $(this).addClass("on"); // делаем активным текущий
    var obj = $(this).attr("rel"); // узнаем его номер
    sliderJS(obj, sl); // слайдим
    return false;
});