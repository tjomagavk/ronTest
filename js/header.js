/**
 * Определение мобильного устройства
 */
function mobile() {
    var isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i);
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i);
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i);
        },

        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        any: function () {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };
    return isMobile.any() ? true : false;
}