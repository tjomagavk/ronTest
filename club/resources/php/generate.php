<?php
$hostUrl = 'http://' . $_SERVER['SERVER_NAME'];
$individualCod = generateCode($_POST['id']);//'1234 5678 9012 3457';
//echo json_encode(strtotime($_POST['dob']));
//echo json_encode($individualCod);

$fullNameTranslate = transliterate($_POST['fullName']);

$email = $_POST['email'];
$img = $hostUrl . '/ron/club/resources/images/card.png';

newCard($img, $individualCod, $fullNameTranslate, $email);

$result = array(
    'result' => 'success'
);
echo json_encode($result);
exit;

function newCard($img, $individualCod, $fullName, $email)
{
    $font = '../fonts/HALTER__.ttf'; // если нужно, то отбрасываем ревизию
//    echo json_encode($img);
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
    $black = 0xf8f0e3; //цвет заливки фона
    $gold = 0x9a4c00; //цвет заливки фона

    imagefttext($mainImg, 28, 0, 282, 477, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 282, 473, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 284, 475, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 280, 475, $black, $font, $individualCod);

    imagefttext($mainImg, 28, 0, 280, 475, $gray, $font, $individualCod);
//    imagefttext($mainImg, 20, 0, 52, 402, $black, $font, $fullName);
//    imagefttext($mainImg, 20, 0, 50, 400, $gray, $font, $fullName);
//    header('Content-Type: image/png');

    $pathForReady = '../cardMember/';
    $randName = md5(time() . mt_rand(0, 9999));
    $imgReady = $randName . '.jpg';
    // сохраняем картинку
    $formatImg = 'jpeg';
    $func = 'image' . $formatImg;
    $func($mainImg, $pathForReady . $imgReady, 100);
//    imagepng($mainImg);
    imagedestroy($mainImg);
    sendMail($pathForReady . $imgReady, $email);
}

function sendMail($mainImg, $email)
{
    // картинки
    $attach = array($mainImg);
// чтобы отображалась картинка и ее не было в аттаче
// путь к картинке задается через CID: - Content-ID
// тестовый текст
    $text = '
    <p>'.$_POST['fullName'].', Ваша персональная скидка – во вложении к этому письму.</p>
<p>Сохраните купон на смартфоне или распечатайте и вместе со своей клубной картой предъявите на кассе в момент
    покупки.</p>
<p>На сегодня партнером Клуба, предоставляющим скидки, является сеть магазинов «Массандра – Легенда Крыма».</p>
<p>Купон на скидку действует в течение текущего календарного месяца.</p>
<p>Обратите внимание - скидка действует только на продукцию нашей компании и не распространяется на остальной
    ассортимент магазина!</p>
<p>Перечень магазинов, в которых Вы можете приобрести продукцию нашей компании со скидкой:</p>
<table width="594" cellspacing="0">
    <colgroup>
        <col width="252"/>
        <col width="332"/>
    </colgroup>
    <tbody>
    <tr valign="TOP">
        <td width="252" height="11">
            <p><strong>Массандра - Легенда Крыма</strong></p>
        </td>
        <td width="332">
            <p>ул. Планерная, 5, корп.1</p>
        </td>
    </tr>
    <tr valign="TOP">
        <td width="252" height="12">
            <p><strong>Массандра - Легенда Крыма</strong></p>
        </td>
        <td width="332">
            <p>ул. Новочеремушкинская, д. 15/29</p>
        </td>
    </tr>
    <tr valign="TOP">
        <td width="252" height="12">
            <p><strong>Массандра - Легенда Крыма</strong></p>
        </td>
        <td width="332">
            <p>Строгинский б-р, 7, корп. 1</p>
        </td>
    </tr>
    <tr valign="TOP">
        <td width="252" height="12"><strong>Массандра - Легенда Крыма</strong></td>
        <td width="332">
            <p>Ленинский пр-т, д. 64/2</p>
        </td>
    </tr>
    <tr valign="TOP">
        <td width="252" height="12">
            <p><strong>Массандра - Легенда Крыма</strong></p>
        </td>
        <td width="332">
            <p>ул. Лескова, д. 6</p>
        </td>
    </tr>
    <tr valign="TOP">
        <td width="252" height="12">
            <p><strong>Массандра - Легенда Крыма</strong></p>
        </td>
        <td width="332">
            <p>ул. Октябрьская, д. 5</p>
        </td>
    </tr>
    <tr valign="TOP">
        <td width="252" height="12">
            <p><strong>Массандра - Легенда Крыма</strong></p>
        </td>
        <td width="332">
            <p>ул. 3-я Парковая, д. 26/2</p>
        </td>
    </tr>
    <tr valign="TOP">
        <td width="252" height="12">
            <p><strong>Массандра - Легенда Крыма</strong></p>
        </td>
        <td width="332">
            <p>Звенигородское ш. д.7</p>
        </td>
    </tr>
    <tr valign="TOP">
        <td width="252" height="11">
            <p><strong>Массандра - Легенда Крыма</strong></p>
        </td>
        <td width="332">
            <p>Комсомольский пр., д. 15 стр.2</p>
        </td>
    </tr>
    </tbody>
</table>
<p>С уважением,<br/><em>«Русские Оригинальные Напитки».</em></p>
<p></p>
<p></p>
';

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
    $result = mail($to, $subject, $message, $headers);
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

?>