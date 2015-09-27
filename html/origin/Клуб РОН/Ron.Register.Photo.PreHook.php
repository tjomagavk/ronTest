<?php
$output = true;
// помещаем фото во временное хранилище
$fields = $hook->getValues();
/* User's photo */
if (!empty($fields['photo'])) {
    // valid extensions
    $extArray = array('jpg', 'jpeg', 'gif', 'png');
    // create temporary path for this form submission
    $uploadPath = 'assets/uploads/temp/';
    $targetPath = $hook->modx->config['base_path'] . $uploadPath;
    // get uploaded file names:
    $submittedFiles = array_keys($_FILES);
    // loop through files
    foreach ($submittedFiles as $sf) {
        // Get Filename and make sure its good.
        $filename = basename($_FILES[$sf]['name']);
        // Get file's extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // case insensitive
        $ext = mb_strtolower($ext);

        // is the file name empty (no file uploaded)
        if ($filename != '') {
            // is this the right type of file?
            if (in_array($ext, $extArray)) {
                //create file called the user name
                $filename = mb_strtolower($filename);
                // full path to new file
                $uploadFilePath = $targetPath . $filename;
                // create directory to move file into if it doesn't exist
                @mkdir($targetPath, 0755, true);
                if (file_exists($uploadFilePath)) {
                    // Change the file permissions if allowed
                    chmod($uploadFilePath, 0755);
                    // remove the file
                    unlink($uploadFilePath);
                }
                // is the file moved to the proper folder successfully?
                if (move_uploaded_file($_FILES[$sf]['tmp_name'], $uploadFilePath)) {
                    $hook->setValue($sf, $uploadPath . $filename);
                    if (!chmod($uploadFilePath, 0644)) {
                        /* some debug function */
                    }
                } else {

                    // File not uploaded
                    $errorMsg = '<span class="error">Возникли проблемы с загрузкой файла, обратитесь к администратору</span>';
                    $hook->addError($sf, $errorMsg);

                    // generate submission error
                    $output = false;
                }
            } else {

                // File type not allowed
                $errorMsg = '<span class="error">Формат файла должен быть .jpg, .jpeg, .gif, .png</span>';
                $hook->addError($sf, $errorMsg);

                // generate submission error
                $output = false;
            }
        } else {

            // if no file, don't give error, but just return blank
            $hook->setValue($sf, '');
        }
    }
}
// переносим в папку пользователя
/** @var modUser $user */
$user = $modx->getUser();
$userId = $user->get('id');
/** @var modUserProfile $profile */
$profile = &$fields['register.profile'];
if (!empty($fields['photo'])) {
    $photo = array();
    $photo['temp'] = $fields['photo'];
    $photo['basename'] = basename($photo['temp']);
    /***********************************************************************
     * XXX: IMPORTANT XXX
     *
     * Create unique path here for this profile updating.
     * You can change this as you wish.
     * The $userId variable comes from above initiation.
     *
     ***********************************************************************/
    $photo['newdir'] = 'assets/uploads/profiles/' . $userId . '/';
    $photo['newfilepath'] = $photo['newdir'] . $photo['basename'];
    $photo['target'] = $hook->modx->config['base_path'] . $photo['temp'];
    $photo['moved'] = $hook->modx->config['base_path'] . $photo['newfilepath'];
    // make the user's private directory
    mkdir($photo['newdir'], 0755, true);
    $photoUpdated = false;
    // move the photo from the temporary path to the new one
    if (!rename($photo['target'], $photo['moved'])) {
        // if "rename" function fails, try "copy" instead.
        if (!copy($photo['target'], $photo['moved'])) {
            // just dump the log report to the MODX's error log,
            // because both "rename" and "copy" functions fail
            $hook->modx->log(modX::LOG_LEVEL_ERROR, __FILE__ . ' ');
            $hook->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': $userId ' . $userId);
            $hook->modx->log(modX::LOG_LEVEL_ERROR, __LINE__ . ': $photo ' . print_r($photo, 1));
        } else {
            // if copy succeeded, delete the old temporary picture
            unlink($photo['target']);
            $photoUpdated = true;
        }
    } else {
        $photoUpdated = true;
    }
    if ($photoUpdated) {
        /**
         * Now we update the profile
         * The $profile variable comes from above initiation.
         */
        $profile->set('photo', $photo['newfilepath']);
        $profile->save();
        /**
         * Yeah! xPDO rocks! Simply like that!
         */
    }
}
return $output;