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

$userArray = array_merge($user->toArray(), $profile->toArray());

$hostUrl = 'http://' . $_SERVER['SERVER_NAME'];
//echo $hostUrl . '<br/>';


$params['id'] = $profile->get('id');

$cardNumber = $modx->runSnippet('Ron.User.CardNumber', $params) . '';


$dirClub = "./ron";

$email = $profile->get('email');
$statusMember = $profile->get('extended')['statusMember'];
$discount = '10%';
if (strstr($statusMember, 'Silver')) {
    $discount = '15%';
} else if (strstr($statusMember, 'Platinum')) {
    $discount = '20%';
}
$fullName = $profile->get('fullname');
$from = "info@r-o-n.ru";
$to = $email;
$subject = "���� ���, ����������� �����";
$attachImage = newCard($cardNumber, $dirClub, $profile);

$mailBody = '
    <p>' . $fullName . ', ����������� ��� �� ����������� � ���� ���������� ����� ��������� ������� ������������ �������� ��� ���� ���, ��� �� ������� �������� ���.</span></p>
<p>���� ������� ����� ����� ����� � �� �������� � ����� ������.</p>
<p>����������, ��������� �� �� ����� ������� ��� �������� (��������, � ����� ���������) ��� ����, ����� �� ����� � ���������� ���������������� ��� ��� �������������� ������ � ����������, ������� � ����� ������ ������� ����� ����� �����.</p>
<p>�� ������� �� ���� ��������, ��� �� ��������� ������ ' . $discount . ' �� ��� ��������� �������� �������� ������������ ������Ȼ � ���� ��������� ���������� � ������� �����.</p>
<p>������ � ���, ��� ����� �������� ������, � ����� �� ���� �����������, �������� ����� � ��� ������������, �� ������ � ����� ������ �������� �� ����� ���.</p>
<p></p>
<p>�� ����� ��������� �� ������ ������ ���������� � ���, ������� ������ �� �����: info@r-o-n.ru</p>
<p>� ���������,<br /><em>�������� ������������ �������</em></p>';


$modx->getService('mail', 'mail.modPHPMailer');
$modx->mail->set(modMail::MAIL_BODY, $mailBody);
$modx->mail->set(modMail::MAIL_FROM, 'info@r-o-n.ru');
$modx->mail->set(modMail::MAIL_FROM_NAME, '�������� ������������ �������');
$modx->mail->set(modMail::MAIL_SUBJECT, $subject);
$modx->mail->address('to', $to);
$modx->mail->setHTML(true);
$modx->mail->attach($attachImage);
if (!$modx->mail->send()) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: ' . $modx->mail->mailer->ErrorInfo);
    $message = '������ ��������';
    $status = 0;
}
$modx->mail->reset();

$result = array('status' => $status, 'message' => $message);

return json_encode($result, JSON_UNESCAPED_UNICODE);
return $message;

function newCard($individualCode, $dirClub, $profile)
{
    $cardName = '';
    $rgbAvatar = ''; //���� ������� ���� ��� ��������
    $fontColorMain = ''; // ���� ������
    $fontColorBack = ''; // ���� ��� �������(����)
    $statusMember = $profile->get('extended')['statusMember'];
    if (strstr($statusMember, 'Copper')) {
        $cardName = $statusMember . '.png';
        $rgbAvatar = 0x90451e;
        $fontColorBack = 0x4f2912;
        $fontColorMain = 0xf8ba44;
    } else if (strstr($statusMember, 'Silver')) {
        $cardName = $statusMember . '.png';
        $rgbAvatar = 0x666b74;
        $fontColorBack = 0x5f5f60;
        $fontColorMain = 0x868686;
    } else if (strstr($statusMember, 'Platinum')) {
        $cardName = $statusMember . '.png';
        $rgbAvatar = 0x666b74;
        $fontColorBack = 0x4f2912;
        $fontColorMain = 0xf8ba44;
    }
    if (empty($cardName)) {
        return false;
    }

    $img = $dirClub . '/club/resources/images/' . $cardName;
    $fullName = $profile->get('fullname');

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
    return $pathForReady . $imgReady;
//    return sendMail($pathForReady . $imgReady, $email, $fullName, $profile);
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

function transliterate($input)
{
    $gost = array(
        "�" => "Ye", "�" => "I", "�" => "G", "�" => "i", "�" => "-", "�" => "ye", "�" => "g",
        "�" => "A", "�" => "B", "�" => "V", "�" => "G", "�" => "D",
        "�" => "E", "�" => "Yo", "�" => "Zh",
        "�" => "Z", "�" => "I", "�" => "J", "�" => "K", "�" => "L",
        "�" => "M", "�" => "N", "�" => "O", "�" => "P", "�" => "R",
        "�" => "S", "�" => "T", "�" => "U", "�" => "F", "�" => "X",
        "�" => "C", "�" => "Ch", "�" => "Sh", "�" => "Shch", "�" => "'",
        "�" => "Y", "�" => "", "�" => "E", "�" => "Yu", "�" => "YA",
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