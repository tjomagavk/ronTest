<?php
$userId = (int)$modx->getOption('userId', $scriptProperties, false);
$message = $modx->getOption('message', $scriptProperties, false);
$status = 1;

if (empty($userId)) return 'userId';

/* get user and profile by user id */
$user = $modx->getObject('modUser', $userId);
if (!$user) return 'user';
$profile = $user->getOne('Profile');
if (!$profile) return 'Profile';

$hostUrl = 'http://' . $_SERVER['SERVER_NAME'];

$individualCod = generateCode($profile, $modx);
$fullNameTranslate = transliterate($fullName);
$dirClub = "./ron";

$fullName = $profile->get('fullname');
$from = "info@r-o-n.ru";
$to = $profile->get('email');
$subject = "Клуб РОН, Индивидуальная скидка";
$attachImage = newCard($individualCod, $profile, $dirClub);
$shops = '
<table class="table table-hover" style="width: 100%">
            <thead>
            <tr class="text-center" style="text-align: center">
                <th>Название</th>
                <th>Адрес</th>
                <th>Телефон</th>
                <th>Часы работы</th>
            </tr>
            </thead>
           <tbody>';

$params['parents'] = $parents;
$params['depth'] = 0;
$params['tpl'] = $tpl;
$params['includeTVs'] = $includeTVs;
$shops .= $modx->runSnippet('pdoResources', $params);
$shops .= '</tbody></table>';

$mailBody = '
    <p>' . $fullName . ', Ваша персональная скидка – во вложении к этому письму.</p>
<p>Сохраните купон на смартфоне или распечатайте и вместе со своей клубной картой предъявите на кассе в момент
    покупки.</p>
<p>На сегодня партнером Клуба, предоставляющим скидки, является сеть магазинов «Массандра – Легенда Крыма».</p>
<p>Купон на скидку действует в течение текущего календарного месяца.</p>
<p>Обратите внимание - скидка действует только на продукцию нашей компании и не распространяется на остальной
    ассортимент магазина!</p>
<p>Перечень магазинов, в которых Вы можете приобрести продукцию нашей компании со скидкой:</p>
' . $shops . '
<p>С уважением,<br/><em>«Русские Оригинальные Напитки».</em></p>
';

$modx->getService('mail', 'mail.modPHPMailer');
$modx->mail->set(modMail::MAIL_BODY, $mailBody);
$modx->mail->set(modMail::MAIL_FROM, 'info@r-o-n.ru');
$modx->mail->set(modMail::MAIL_FROM_NAME, '«Русские Оригинальные Напитки»');
$modx->mail->set(modMail::MAIL_SUBJECT, $subject);
$modx->mail->address('to', $to);
$modx->mail->setHTML(true);
$modx->mail->attach($attachImage);
if (!$modx->mail->send()) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: ' . $modx->mail->mailer->ErrorInfo);
    $message = 'Ошибка отправки';
    $status = 0;
}
$modx->mail->reset();

$result = array('status' => $status, 'message' => $message);
return json_encode($result, JSON_UNESCAPED_UNICODE);

exit;

function newCard($individualCod, $profile, $dirClub)
{
    $discountImage = '';
    $statusMember = $profile->get('extended')['statusMember'];
    if (strstr($statusMember, 'Copper')) {
        $discountImage = $dirClub . '/club/resources/images/CopperDiscount.png';
    } else if (strstr($statusMember, 'Silver')) {
        $discountImage = $dirClub . '/club/resources/images/SilverDiscount.png';
    } else if (strstr($statusMember, 'Platinum')) {
        $discountImage = $dirClub . '/club/resources/images/PlatinumDiscount.png';
    }
    if (empty($discountImage)) {
        return false;
    }


    $font = $dirClub . '/club/resources/fonts/HALTER__.ttf'; // если нужно, то отбрасываем ревизию
//    echo $font . '<br/>';
    $discountImage = current(explode("?", $discountImage)); // если нужно, то отбрасываем ревизию
    $size = getimagesize($discountImage);
    $width = $size[1]; // высота
    $height = $size[0]; // ширина
    // Создание изображения
    $mainImg = imagecreatetruecolor($height, $width);
    $rgb = 0xffffff; //цвет заливки фона
    imagefill($mainImg, 0, 0, $rgb); //заливаем его белым цветом
    // загружаем картинку(фото)
    //определяем тип (расширение) картинки
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $format;   //определение функции для расшерения файла
    //если нет такой функции, то прекращаем работу скрипта
    if (!function_exists($icfunc)) return false;
    $image = $icfunc($discountImage);
    // накладываем на основной фон фотографию
    imagecopy($mainImg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

    $gray = 0x97121b; //цвет заливки фона
    $black = 0xf8f0e3; //цвет заливки фона
    $gold = 0x9a4c00; //цвет заливки фона

    imagefttext($mainImg, 28, 0, 132, 477, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 132, 473, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 134, 475, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 130, 475, $black, $font, $individualCod);

    imagefttext($mainImg, 28, 0, 130, 475, $gray, $font, $individualCod);
//    imagefttext($mainImg, 20, 0, 52, 402, $black, $font, $fullName);
//    imagefttext($mainImg, 20, 0, 50, 400, $gray, $font, $fullName);
//    header('Content-Type: image/png');

    imagefttext($mainImg, 24, 0, 7, 77, $black, $font, date('d.m.Y', strtotime('+3 day')));
    imagefttext($mainImg, 24, 0, 7, 73, $black, $font, date('d.m.Y', strtotime('+3 day')));
    imagefttext($mainImg, 24, 0, 9, 75, $black, $font, date('d.m.Y', strtotime('+3 day')));
    imagefttext($mainImg, 24, 0, 5, 75, $black, $font, date('d.m.Y', strtotime('+3 day')));

    imagefttext($mainImg, 24, 0, 5, 75, $gray, $font, date('d.m.Y', strtotime('+3 day')));

    $pathForReady = './assets/uploads/temp/';

//    echo $pathForReady . '<br/>';

    $randName = md5(time() . mt_rand(0, 9999));
    $imgReady = $randName . '.jpg';
//    echo 'imgReady  ' . $imgReady . '<br/>';
    // сохраняем картинку
    $formatImg = 'jpeg';
    $func = 'image' . $formatImg;
    $func($mainImg, $pathForReady . $imgReady, 100);
//    imagepng($mainImg);
    imagedestroy($mainImg);
    return $pathForReady . $imgReady;
}

function transliterate($input)
{
    $gost = array(
        "Є" => "Ye", "І" => "I", "Ѓ" => "G", "і" => "i", "№" => "-", "є" => "ye", "ѓ" => "g",
        "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
        "Е" => "E", "Ё" => "Yo", "Ж" => "Zh",
        "З" => "Z", "И" => "I", "Й" => "J", "К" => "K", "Л" => "L",
        "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
        "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "X",
        "Ц" => "C", "Ч" => "Ch", "Ш" => "Sh", "Щ" => "Shch", "Ъ" => "'",
        "Ы" => "Y", "Ь" => "", "Э" => "E", "Ю" => "Yu", "Я" => "YA",
        "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d",
        "е" => "e", "ё" => "yo", "ж" => "zh",
        "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
        "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
        "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "x",
        "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shch", "ъ" => "",
        "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya"
    );

    return strtr($input, $gost);
}

/**
 * Создаем индивидуальный код на карту:
 * @param $profile
 * @return string
 */
function generateCode($profile, $modx)
{
    $dob = $profile->get('dob');
    $discountSizeStr = '00';
    $statusMember = $profile->get('extended')['statusMember'];
    if (strstr($statusMember, 'Copper')) {
        $discountSizeStr = '10';
    } else if (strstr($statusMember, 'Silver')) {
        $discountSizeStr = '15';
    } else if (strstr($statusMember, 'Platinum')) {
        $discountSizeStr = '20';
    }
    $dobStr = date("Ym", $dob);
    $todayStr = date('Ym');
    $params['id'] = $profile->get('id');

    $cardNumber = $modx->runSnippet('Ron.User.CardNumber', $params) . '';
    $result = $todayStr[0] . $cardNumber[0] . $dobStr[0] . $todayStr[1] . $dobStr[1] . '-'
        . $todayStr[2] . $cardNumber[1] . $dobStr[2] . $todayStr[3] . $discountSizeStr[0] . '-'
        . $dobStr[3] . $todayStr[4] . $cardNumber[2] . $dobStr[4] . $todayStr[5] . '-'
        . $discountSizeStr[1] . $cardNumber[3] . $dobStr[5] . $cardNumber[4];
    return $result;
}