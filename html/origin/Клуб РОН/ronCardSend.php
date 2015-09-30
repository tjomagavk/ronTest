<?php
$userId = (int)$modx->getOption('userId', $scriptProperties, false);
$message = $modx->getOption('message', $scriptProperties, false);

if (empty($userId)) return 'userId';

/* get user and profile by user id */
$user = $modx->getObject('modUser', $userId);
if (!$user) return 'user';
$profile = $user->getOne('Profile');
if (!$profile) return 'Profile';

$userArray = array_merge($user->toArray(), $profile->toArray());

$hostUrl = 'http://' . $_SERVER['SERVER_NAME'];
//echo $hostUrl . '<br/>';


$individualCod = generateCodeById($userId);


$dirClub = "./ron";
//echo $avatar . '<br/>';
//
//$filelist = array();
//if ($handle = opendir("./assets/uploads/temp/")) {
//    while ($entry = readdir($handle)) {
//        echo $entry . '<br/>';
//
//    }
//    closedir($handle);
//}
//
//$avatar = "./assets/uploads/temp/le1x3v7zni8.jpg";

$message;
if (!newCard($individualCod, $dirClub, $profile)) {
    $message = 'Ошибка отправки';
}
return $message;

exit;

function newCard($individualCode, $dirClub, $profile)
{
    $cardName = '';
    $rgbAvatar = ''; //цвет заливки фона под аватаром
    $fontColorMain = ''; // цвет текста
    $fontColorBack = ''; // цвет под текстом(тень)
    $statusMember = $profile->get('extended')['statusMember'];
    if (strstr($statusMember, 'Copper')) {
        $cardName = 'Copper.png';
        $rgbAvatar = 0x90451e;
        $fontColorBack = 0x4f2912;
        $fontColorMain = 0xf8ba44;
    } else if (strstr($statusMember, 'Silver')) {
        $cardName = 'Silver.png';
        $rgbAvatar = 0x666b74;
        $fontColorBack = 0x5f5f60;
        $fontColorMain = 0x868686;
    } else if (strstr($statusMember, 'Platinum')) {
        $cardName = 'Platinum.png';
        $rgbAvatar = 0x666b74;
        $fontColorBack = 0x4f2912;
        $fontColorMain = 0xf8ba44;
    }
    if (empty($cardName)) {
        return false;
    }

    $img = $dirClub . '/club/resources/images/' . $cardName;
    $fullName = $profile->get('fullname');
    $email = $profile->get('email');

    $userAvatar = $profile->get('photo');
//    $img = $avatar;
    $font = $dirClub . '/club/resources/fonts/HALTER__.ttf'; // если нужно, то отбрасываем ревизию
//    echo $font . '<br/>';
//    echo $userAvatar . '<br/>';
//    echo $dirClub . '<br/>';
    $img = current(explode("?", $img)); // если нужно, то отбрасываем ревизию
    $size = getimagesize($img);
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
    $image = $icfunc($img);


    // накладываем на основной фон карту
    imagecopy($mainImg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

    addAvatar($mainImg, $userAvatar, $rgbAvatar);
    addText($mainImg, $font, $fontColorMain, $fontColorBack, $profile, $individualCode, $fullName);

    $pathForReady = $dirClub . '/club/cardMember/';

    $randName = md5(time() . mt_rand(0, 9999));
    $imgReady = $randName . '.jpg';
//    echo 'imgReady  ' . $imgReady . '<br/>';
    // сохраняем картинку
    $formatImg = 'jpeg';
    $func = 'image' . $formatImg;
    $func($mainImg, $pathForReady . $imgReady, 100);
    header("Content-type: image/png");
//    imagepng($mainImg);
    imagedestroy($mainImg);
    return sendMail($pathForReady . $imgReady, $email, $fullName, $profile);
}

/**
 * Функция добавляет аватар пользователя на изображение
 * @param $mainImg
 * @param $userAvatar
 * @param $rgbBack
 * @return bool
 */
function addAvatar($mainImg, $userAvatar, $rgbBack)
{
    //создаем подложку под изображение
    $defaultWidthAvatar = 216; // ширина
    $defaultHeightAvatar = 259; // высота
    $mainAvatarImg = imagecreatetruecolor($defaultWidthAvatar, $defaultHeightAvatar);
    $rgbAvatar = $rgbBack; //цвет заливки фона
    imagefill($mainAvatarImg, 0, 0, $rgbAvatar); //заливаем его коричневым

    $userAvatar = current(explode("?", $userAvatar)); // если нужно, то отбрасываем ревизию
    $sizeAvatar = getimagesize($userAvatar);
    $widthAvatar = $sizeAvatar[0]; // ширина
    $heightAvatar = $sizeAvatar[1]; // высота
    //определяем тип (расширение) картинки
    $formatAvatar = strtolower(substr($sizeAvatar['mime'], strpos($sizeAvatar['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $formatAvatar;   //определение функции для расшерения файла
    //если нет такой функции, то прекращаем работу скрипта
    if (!function_exists($icfunc)) return false;
    $avatar = $icfunc($userAvatar);

    $src = $avatar;
//Ширину фотографии делим на 216 т.к. на выходе мы хотим получить фото шириной в 150 пикселей. В результате получаем коэфициент соотношения ширины оригинала с будущей превьюшкой.
    $koe = $widthAvatar / 216;
//Делим высоту изображения на коэфициент, полученный в предыдущей строке, и округляем число до целого в большую сторону — в результате получаем высоту нового изображения.
    $new_h = ceil($heightAvatar / $koe);
//Создаём пустое изображение шириной в 150 пикселей и высотой, которую мы вычислили в предыдущей строке.
//Данная функция копирует прямоугольную часть изображения в другое изображение, плавно интерполируя пикселные значения таким образом, что, в частности, уменьшение размера изображения сохранит его чёткость и яркость.
    ImageCopyResampled($mainAvatarImg, $src, 0, 0, 0, 0, 216, $new_h, $widthAvatar, $heightAvatar);

    // накладываем на основной фон фотографию
    imagecopy($mainImg, $mainAvatarImg, 527, 58, 0, 0, $defaultWidthAvatar, $defaultHeightAvatar);
}

/**
 * Добавляем текст да картинку: имя пользователя, номер карты, дату
 * @param $mainImg
 * @param $font
 * @param $fontColorMain
 * @param $fontColorBack
 * @param $profile
 * @param $individualCode
 * @param $fullName
 */
function addText($mainImg, $font, $fontColorMain, $fontColorBack, $profile, $individualCode, $fullName)
{
    $dob = $profile->get('dob');
    $fullNameTranslate = transliterate($fullName);

    imagefttext($mainImg, 28, 0, 62, 262, $fontColorBack, $font, $individualCode);
    imagefttext($mainImg, 28, 0, 60, 260, $fontColorMain, $font, $individualCode);

    imagefttext($mainImg, 20, 0, 62, 312, $fontColorBack, $font, $fullNameTranslate);
    imagefttext($mainImg, 20, 0, 60, 310, $fontColorMain, $font, $fullNameTranslate);

    imagefttext($mainImg, 20, 0, 62, 372, $fontColorBack, $font, date("m/Y", $dob));
    imagefttext($mainImg, 20, 0, 60, 370, $fontColorMain, $font, date("m/Y", $dob));
}

function sendMail($mainImg, $email, $fullName, $profile)
{
    // картинки
    $attach = array($mainImg);
// чтобы отображалась картинка и ее не было в аттаче
// путь к картинке задается через CID: - Content-ID
// тестовый текст
    $text = '
    <p>' . $fullName . ', поздравляем Вас со вступлением в ряды участников Клуба любителей русских оригинальных напитков или Клуб РОН, как мы коротко называем его.</span></p>
<p>Ваша именная карта члена Клуба – во вложении к этому письму.</p>
<p>Пожалуйста, сохраните ее на любом удобном Вам носителе (например, в Вашем смартфоне) для того, чтобы мы могли в дальнейшем идентифицировать Вас при предоставлении скидок и привилегий, которых в самом скором времени будет очень много.</p>
<p>На сегодня мы рады сообщить, ЧТО ВЫ ПОЛУЧАЕТЕ СКИДКУ 10 % НА ВСЮ ПРОДУКЦИЮ КОМПАНИИ «РУССКИЕ ОРИГИНАЛЬНЫЕ НАПИТКИ» в сети магазинов «Массандра – Легенда Крыма».</p>
<p>Узнать о том, где можно получить скидки, а также об иных привилегиях, правилах клуба и его возможностях, Вы можете в своем личном кабинете на сайте РОН.</p>
<p></p>
<p>Со всеми вопросами Вы всегда можете обратиться к нам, написав письмо на адрес: info@r-o-n.ru</p>
<p>С уважением,<br /><em>«Русские Оригинальные Напитки»</em></p>';

    $from = "info@r-o-n.ru";
    $to = $email;
    $subject = "РОН, Премиальная карта";

// Заголовки письма === >>>
    $headers = "From: $from\r\n";
//$headers .= "To: $to\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "Date: " . date("r") . "\r\n";
    $headers .= "X-Mailer: zm php script\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative;\r\n";
    $baseboundary = "------------" . strtoupper(md5(uniqid(rand(), true)));
    $headers .= "  boundary=\"$baseboundary\"\r\n";
// <<< ====================

// Тело письма === >>>
    $message = "--$baseboundary\r\n";
    $message .= "Content-Type: text/plain;\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= "--$baseboundary\r\n";
    $newboundary = "------------" . strtoupper(md5(uniqid(rand(), true)));
    $message .= "Content-Type: multipart/related;\r\n";
    $message .= "  boundary=\"$newboundary\"\r\n\r\n\r\n";
    $message .= "--$newboundary\r\n";
    $message .= "Content-Type: text/html; charset=utf-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $text . "\r\n\r\n";
// <<< ==============
// прикрепляем файлы ===>>>
    foreach ($attach as $filename) {
        $mimeType = 'image/png';
        $fileContent = file_get_contents($filename, true);
        $filename = basename($filename);
        $message .= "--$newboundary\r\n";
        $message .= "Content-Type: $mimeType;\r\n";
        $message .= " name=\"$filename\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-ID: <$filename>\r\n";
        $message .= "Content-Disposition: inline;\r\n";
        $message .= " filename=\"$filename\"\r\n\r\n";
        $message .= chunk_split(base64_encode($fileContent));
    }
// <<< ====================

// заканчиваем тело письма, дописываем разделители
    $message .= "--$newboundary--\r\n\r\n";
    $message .= "--$baseboundary--\r\n";

// отправка письма
    return mail($to, $subject, $message, $headers);
//    var_dump($result);
}


function transliterate($input)
{
    $gost = array(
        "Є" => "YE", "І" => "I", "Ѓ" => "G", "і" => "i", "№" => "-", "є" => "ye", "ѓ" => "g",
        "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
        "Е" => "E", "Ё" => "YO", "Ж" => "ZH",
        "З" => "Z", "И" => "I", "Й" => "J", "К" => "K", "Л" => "L",
        "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
        "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "X",
        "Ц" => "C", "Ч" => "CH", "Ш" => "SH", "Щ" => "SHH", "Ъ" => "'",
        "Ы" => "Y", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA",
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
 * прибавляем к идентификатору определенное число
 * @param $id
 * @return string
 * @internal param $dob
 */
function generateCodeById($id)
{
    $startNumber = "53500";
    return intval($startNumber) + intval($id);
}


return true;