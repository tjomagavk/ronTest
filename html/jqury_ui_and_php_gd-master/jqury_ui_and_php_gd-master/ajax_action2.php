<?php

$individualCod = generateCode(getdate());//'1234 5678 9012 3457';
$fullName = transliterate('Онищенко Артём Игоревич');
$email = "tjomagavk@gmail.com";
$img = 'http://ron.tjomagavk.ru/ron/html/jqury_ui_and_php_gd-master/jqury_ui_and_php_gd-master/resources/images/sss.png';
newCard($img, $individualCod, $fullName, $email);


function newCard($img, $individualCod, $fullName, $email)
{
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

    $gray = 0x767676; //цвет заливки фона

    imagefttext($mainImg, 30, 0, 50, 325, $gray, './resources/images/HALTER__.ttf', $individualCod);
    imagefttext($mainImg, 20, 0, 50, 400, $gray, './resources/images/HALTER__.ttf', $fullName);
    header('Content-Type: image/png');

    $pathForReady = './resources/ready_foto/';
    $randName = md5(time() . mt_rand(0, 9999));
    $imgReady = $randName . '.jpg';
    // сохраняем картинку
    $formatImg = 'jpeg';
    $func = 'image' . $formatImg;
    $func($mainImg, $pathForReady . $imgReady, 100);
    imagepng($mainImg);
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
    <div style="width: 700px; margin: 0 auto;">
        <p> Поздравляем! </p>
        <p> Для Вас сформирована индивидульная карта</p>
        <p> С уважением, Русские Оригинальные Напитки</p>
    </div>
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
    var_dump($result);
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
function generateCode($dob)
{
    $first = strrev(date("Y", strtotime($dob)));
    $second = strrev(date("Y"));
    $third = rand(1000, 9999);
    $fourth = strrev(date("md", strtotime($dob)));
    return $first . " " . $second . " " . $third . " " . $fourth;
}

?>