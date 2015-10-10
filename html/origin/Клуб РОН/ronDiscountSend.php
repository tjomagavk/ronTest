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


$individualCod = generateCode($userId);
$fullNameTranslate = transliterate($fullName);
$dirClub = "./ron";

//echo $img . '<br/>';

$message;
if (!newCard($individualCod, $profile, $dirClub)) {
    $message = '������ ��������';
}
return $message;

exit;

function newCard($individualCod, $profile, $dirClub)
{
    $fullName = $profile->get('fullname');
    $email = $profile->get('email');
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


    $font = $dirClub . '/club/resources/fonts/HALTER__.ttf'; // ���� �����, �� ����������� �������
//    echo $font . '<br/>';
    $discountImage = current(explode("?", $discountImage)); // ���� �����, �� ����������� �������
    $size = getimagesize($discountImage);
    $width = $size[1]; // ������
    $height = $size[0]; // ������
    // �������� �����������
    $mainImg = imagecreatetruecolor($height, $width);
    $rgb = 0xffffff; //���� ������� ����
    imagefill($mainImg, 0, 0, $rgb); //�������� ��� ����� ������
    // ��������� ��������(����)
    //���������� ��� (����������) ��������
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $format;   //����������� ������� ��� ���������� �����
    //���� ��� ����� �������, �� ���������� ������ �������
    if (!function_exists($icfunc)) return false;
    $image = $icfunc($discountImage);
    // ����������� �� �������� ��� ����������
    imagecopy($mainImg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

    $gray = 0x97121b; //���� ������� ����
    $black = 0xf8f0e3; //���� ������� ����
    $gold = 0x9a4c00; //���� ������� ����

    imagefttext($mainImg, 28, 0, 282, 477, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 282, 473, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 284, 475, $black, $font, $individualCod);
    imagefttext($mainImg, 28, 0, 280, 475, $black, $font, $individualCod);

    imagefttext($mainImg, 28, 0, 280, 475, $gray, $font, $individualCod);
//    imagefttext($mainImg, 20, 0, 52, 402, $black, $font, $fullName);
//    imagefttext($mainImg, 20, 0, 50, 400, $gray, $font, $fullName);
//    header('Content-Type: image/png');

    $pathForReady = './assets/uploads/temp/';

//    echo $pathForReady . '<br/>';

    $randName = md5(time() . mt_rand(0, 9999));
    $imgReady = $randName . '.jpg';
//    echo 'imgReady  ' . $imgReady . '<br/>';
    // ��������� ��������
    $formatImg = 'jpeg';
    $func = 'image' . $formatImg;
    $func($mainImg, $pathForReady . $imgReady, 100);
//    imagepng($mainImg);
    imagedestroy($mainImg);
    return sendMail($pathForReady . $imgReady, $email, $fullName);
}

function sendMail($mainImg, $email, $fullName)
{
    // ��������
    $attach = array($mainImg);
// ����� ������������ �������� � �� �� ���� � ������
// ���� � �������� �������� ����� CID: - Content-ID
// �������� �����
    $text = '
    <p>' . $fullName . ', ���� ������������ ������ � �� �������� � ����� ������.</p>
<p>��������� ����� �� ��������� ��� ������������ � ������ �� ����� ������� ������ ���������� �� ����� � ������
    �������.</p>
<p>�� ������� ��������� �����, ��������������� ������, �������� ���� ��������� ���������� � ������� �����.</p>
<p>����� �� ������ ��������� � ������� �������� ������������ ������.</p>
<p>�������� �������� - ������ ��������� ������ �� ��������� ����� �������� � �� ���������������� �� ���������
    ����������� ��������!</p>
<p>�������� ���������, � ������� �� ������ ���������� ��������� ����� �������� �� �������:</p>
<p>- �������� ���� �� ���������<br /> �����: ��. ���������, �.5, ����.1<br /> ���.: (499) 740 30 01<br /> �������� � 10 �� 22, ��� ��������� � ��������.</p>
<p>- ��������� ���������<br /> �����: ��. ������������������, �.15/29<br /> ���.: (499) 129 91 94<br /> �������� � 10 �� 22, ��� ��������� � ��������.</p>
<p>- ��������� ��������<br /> �����: ����������� �-� 7, ����.1.<br /> ���.: (495) 758 86 02<br /> �������� � 10 �� 22, ��� ��������� � ��������.</p>
<p>- ��������� ���������<br /> �����: ��������� ��������, �. 64/2<br /> ���.: (499) 137 30 07<br /> ������� �������� � 10 �� 22, ��� ��������� � ��������.</p>
<p>- ��������� ���������<br /> �����: ��. �������, �.6<br /> ���.: (499) 909 40 08<br /> �������� � 10 �� 22, ��� ��������� � ��������.</p>
<p>- ���������<br /> �����: ��. ����������� �.5<br /> ���.: (495) 684 57 57<br /> �������� � 10 �� 23, ��� ��������� � ��������.</p>
<p>- �������� ����<br /> �����: �������������� �., �. 7<br /> ���.: (499) 256 80 11<br /> ������� �������� � 10 �� 22, ��� ��������� � ��������.</p>
<p>- ���������<br /> �����: ��. 3-� ��������, �. 26/2<br /> ���.: (499) 165 62 27<br /> ������� �������� � 10 �� 22, ��� ��������� � ��������.</p>
<p>� ���������,<br/><em>�������� ������������ �������.</em></p>
';

    $from = "info@r-o-n.ru";
    $to = $email;
    $subject = "���� ���, �������������� ������";

// ��������� ������ === >>>
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

// ���� ������ === >>>
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
// ����������� ����� ===>>>
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

// ����������� ���� ������, ���������� �����������
    $message .= "--$newboundary--\r\n\r\n";
    $message .= "--$baseboundary--\r\n";

// �������� ������
    return mail($to, $subject, $message, $headers);
//    var_dump($result);
}


function transliterate($input)
{
    $gost = array(
        "�" => "YE", "�" => "I", "�" => "G", "�" => "i", "�" => "-", "�" => "ye", "�" => "g",
        "�" => "A", "�" => "B", "�" => "V", "�" => "G", "�" => "D",
        "�" => "E", "�" => "YO", "�" => "ZH",
        "�" => "Z", "�" => "I", "�" => "J", "�" => "K", "�" => "L",
        "�" => "M", "�" => "N", "�" => "O", "�" => "P", "�" => "R",
        "�" => "S", "�" => "T", "�" => "U", "�" => "F", "�" => "X",
        "�" => "C", "�" => "CH", "�" => "SH", "�" => "SHH", "�" => "'",
        "�" => "Y", "�" => "", "�" => "E", "�" => "YU", "�" => "YA",
        "�" => "a", "�" => "b", "�" => "v", "�" => "g", "�" => "d",
        "�" => "e", "�" => "yo", "�" => "zh",
        "�" => "z", "�" => "i", "�" => "j", "�" => "k", "�" => "l",
        "�" => "m", "�" => "n", "�" => "o", "�" => "p", "�" => "r",
        "�" => "s", "�" => "t", "�" => "u", "�" => "f", "�" => "x",
        "�" => "c", "�" => "ch", "�" => "sh", "�" => "shch", "�" => "",
        "�" => "y", "�" => "", "�" => "e", "�" => "yu", "�" => "ya"
    );

    return strtr($input, $gost);
}

/**
 * ������� �������������� ��� �� �����:
 * ������ 4 ����� - ��� ��������
 * ������ 4 ����� - ������� ���
 * ������ 4 ����� - ���������
 * ��������� 4 ����� - �����, ���� ��������
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
 * ������� �������������� ��� �� �����:
 * ���������� � �������������� ������������ �����
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
 * ������� �������������� ��� �� �����:
 * ���������� � �������������� ������������ �����
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