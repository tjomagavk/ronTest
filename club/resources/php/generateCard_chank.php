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


$fullName = $profile->get('fullname');
$email = $profile->get('email');
$dob = $profile->get('dob');
$individualCod = generateCodeById($userId);

$dirClub = "./ron";
$img = $dirClub . '/club/resources/images/Copper1.png';
//echo $img . '<br/>';

$message;
if (!newCard($img, $individualCod, $fullName, $email, $dirClub, $dob, $profile)) {
    $message = 'Ошибка отправки';
}
return $message;

exit;

function newCard($img, $individualCode, $fullName, $email, $hostUrl, $dob, $profile)
{
    $fullNameTranslate = transliterate($fullName);
    $font = $hostUrl . '/club/resources/fonts/HALTER__.ttf'; // если нужно, то отбрасываем ревизию
//    echo $font . '<br/>';
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
    // накладываем на основной фон фотографию
    imagecopy($mainImg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

    $gray = 0x97121b; //цвет заливки фона
    $black = 0x4f2912; //цвет заливки фона
    $gold = 0xf8ba44; //цвет заливки фона

    imagefttext($mainImg, 28, 0, 62, 262, $black, $font, $individualCode);
    imagefttext($mainImg, 28, 0, 60, 260, $gold, $font, $individualCode);

    imagefttext($mainImg, 20, 0, 62, 312, $black, $font, $fullNameTranslate);
    imagefttext($mainImg, 20, 0, 60, 310, $gold, $font, $fullNameTranslate);

    imagefttext($mainImg, 20, 0, 62, 372, $black, $font, date("m/Y", $dob));
    imagefttext($mainImg, 20, 0, 60, 370, $gold, $font, date("m/Y", $dob));
//    header('Content-Type: image/png');

    $pathForReady = $hostUrl . '/club/cardMember/';

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
    return sendMail($pathForReady . $imgReady, $email, $fullName, $profile);
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
<p>Со всеми вопросами Вы всегда можете обратиться к нам, написав письмо на адрес: <a><span lang="en-US" xml:lang="en-US">info</span>@<span lang="en-US" xml:lang="en-US">r</span>-<span lang="en-US" xml:lang="en-US">o</span>-<span lang="en-US" xml:lang="en-US">n</span>.<span lang="en-US" xml:lang="en-US">ru</span></a></p>
<p>С уважением,<br /><em>«Русские Оригинальные Напитки»</em></p>';

    $from = "test@test.com";
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
 * первые 4 цифры - год рождения
 * вторые 4 цифры - текущий год
 * третьи 4 цифры - случайные
 * четвертые 4 цифры - месяц, день роджения
 * @param $dob
 * @return string
 */
function generateCodeByBirthDate($dob)
{
    $first = strrev(date("Y", strtotime($dob)));
    $second = strrev(date("Y"));
    $third = rand(1000, 9999);
    $fourth = strrev(date("md", strtotime($dob)));
    return $first . " " . $second . " " . $third . " " . $fourth;
}

/**
 * Создаем индивидуальный код на карту:
 * прибавляем к идентификатору определенное число
 * @param $id
 * @return string
 * @internal param $dob
 */
function generateCode($id)
{
    $first = generateCodeById($id);
    $second = rand(100, 999);;
    $third = date("y") . date("m");
    return $first . "-" . $second . $third;
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