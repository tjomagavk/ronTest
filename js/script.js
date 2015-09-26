$(document).ready(function () {
    var loginModal = $('#login');
    if (loginModal.find('.alert') && loginModal.find('.alert').size() > 0) {
        loginModal.modal();
    }
    if (isMobile) {
        $("header.desktop").remove();
        $("header.mobile").css({'display': 'block'});
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

$('document').ready(function () {

});

(function (i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
    a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

ga('create', 'UA-68120933-2', 'auto');
ga('send', 'pageview');

(function (d, w, c) {
    (w[c] = w[c] || []).push(function () {
        try {
            w.yaCounter32699025 = new Ya.Metrika({
                id: 32699025,
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true
            });
        } catch (e) {
        }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () {
            n.parentNode.insertBefore(s, n);
        };
    s.type = "text/javascript";
    s.async = true;
    s.src = "https://mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else {
        f();
    }
})(document, window, "yandex_metrika_callbacks");
