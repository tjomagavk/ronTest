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
    $message = '������ ��������';
}
return $message;

exit;

function newCard($individualCode, $dirClub, $profile)
{
    $cardName = '';
    $rgbAvatar = ''; //���� ������� ���� ��� ��������
    $fontColorMain = ''; // ���� ������
    $fontColorBack = ''; // ���� ��� �������(����)
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
    $font = $dirClub . '/club/resources/fonts/HALTER__.ttf'; // ���� �����, �� ����������� �������
//    echo $font . '<br/>';
//    echo $userAvatar . '<br/>';
//    echo $dirClub . '<br/>';
    $img = current(explode("?", $img)); // ���� �����, �� ����������� �������
    $size = getimagesize($img);
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
    $image = $icfunc($img);


    // ����������� �� �������� ��� �����
    imagecopy($mainImg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

    addAvatar($mainImg, $userAvatar, $rgbAvatar);
    addText($mainImg, $font, $fontColorMain, $fontColorBack, $profile, $individualCode, $fullName);

    $pathForReady = $dirClub . '/club/cardMember/';

    $randName = md5(time() . mt_rand(0, 9999));
    $imgReady = $randName . '.jpg';
//    echo 'imgReady  ' . $imgReady . '<br/>';
    // ��������� ��������
    $formatImg = 'jpeg';
    $func = 'image' . $formatImg;
    $func($mainImg, $pathForReady . $imgReady, 100);
    header("Content-type: image/png");
//    imagepng($mainImg);
    imagedestroy($mainImg);
    return sendMail($pathForReady . $imgReady, $email, $fullName, $profile);
}

/**
 * ������� ��������� ������ ������������ �� �����������
 * @param $mainImg
 * @param $userAvatar
 * @param $rgbBack
 * @return bool
 */
function addAvatar($mainImg, $userAvatar, $rgbBack)
{
    //������� �������� ��� �����������
    $defaultWidthAvatar = 216; // ������
    $defaultHeightAvatar = 259; // ������
    $mainAvatarImg = imagecreatetruecolor($defaultWidthAvatar, $defaultHeightAvatar);
    $rgbAvatar = $rgbBack; //���� ������� ����
    imagefill($mainAvatarImg, 0, 0, $rgbAvatar); //�������� ��� ����������

    $userAvatar = current(explode("?", $userAvatar)); // ���� �����, �� ����������� �������
    $sizeAvatar = getimagesize($userAvatar);
    $widthAvatar = $sizeAvatar[0]; // ������
    $heightAvatar = $sizeAvatar[1]; // ������
    //���������� ��� (����������) ��������
    $formatAvatar = strtolower(substr($sizeAvatar['mime'], strpos($sizeAvatar['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $formatAvatar;   //����������� ������� ��� ���������� �����
    //���� ��� ����� �������, �� ���������� ������ �������
    if (!function_exists($icfunc)) return false;
    $avatar = $icfunc($userAvatar);

    $src = $avatar;
//������ ���������� ����� �� 216 �.�. �� ������ �� ����� �������� ���� ������� � 150 ��������. � ���������� �������� ���������� ����������� ������ ��������� � ������� ����������.
    $koe = $widthAvatar / 216;
//����� ������ ����������� �� ����������, ���������� � ���������� ������, � ��������� ����� �� ������ � ������� ������� � � ���������� �������� ������ ������ �����������.
    $new_h = ceil($heightAvatar / $koe);
//������ ������ ����������� ������� � 150 �������� � �������, ������� �� ��������� � ���������� ������.
//������ ������� �������� ������������� ����� ����������� � ������ �����������, ������ ������������ ��������� �������� ����� �������, ���, � ���������, ���������� ������� ����������� �������� ��� �������� � �������.
    ImageCopyResampled($mainAvatarImg, $src, 0, 0, 0, 0, 216, $new_h, $widthAvatar, $heightAvatar);

    // ����������� �� �������� ��� ����������
    imagecopy($mainImg, $mainAvatarImg, 527, 58, 0, 0, $defaultWidthAvatar, $defaultHeightAvatar);
}

/**
 * ��������� ����� �� ��������: ��� ������������, ����� �����, ����
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
    // ��������
    $attach = array($mainImg);
// ����� ������������ �������� � �� �� ���� � ������
// ���� � �������� �������� ����� CID: - Content-ID
// �������� �����
    $text = '
    <p>' . $fullName . ', ����������� ��� �� ����������� � ���� ���������� ����� ��������� ������� ������������ �������� ��� ���� ���, ��� �� ������� �������� ���.</span></p>
<p>���� ������� ����� ����� ����� � �� �������� � ����� ������.</p>
<p>����������, ��������� �� �� ����� ������� ��� �������� (��������, � ����� ���������) ��� ����, ����� �� ����� � ���������� ���������������� ��� ��� �������������� ������ � ����������, ������� � ����� ������ ������� ����� ����� �����.</p>
<p>�� ������� �� ���� ��������, ��� �� ��������� ������ 10 % �� ��� ��������� �������� �������� ������������ ������Ȼ � ���� ��������� ���������� � ������� �����.</p>
<p>������ � ���, ��� ����� �������� ������, � ����� �� ���� �����������, �������� ����� � ��� ������������, �� ������ � ����� ������ �������� �� ����� ���.</p>
<p></p>
<p>�� ����� ��������� �� ������ ������ ���������� � ���, ������� ������ �� �����: info@r-o-n.ru</p>
<p>� ���������,<br /><em>�������� ������������ �������</em></p>';

    $from = "info@r-o-n.ru";
    $to = $email;
    $subject = "���, ����������� �����";

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