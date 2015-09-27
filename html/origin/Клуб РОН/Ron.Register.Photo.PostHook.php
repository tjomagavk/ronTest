<?php
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
return TRUE;