<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 19/11/2016
 * Time: 17:55
 */

namespace GedBundle\Event;


final class NotificationEvents
{
    const onAddVersion = 'gedBundle.add_version';
    const onRemoveVersion = 'gedBundle.remove_version';
    const onRemoveFile = 'gedBundle.remove_file';
    const onLockFile = 'gedBundle.lock_file';
    const onUnlockFile = 'gedBundle.unlock_file';
    const onRemoveFolder = 'gedBundle.remove_folder';
    const onAddFile = 'gedBundle.add_file';
    const onAddFolder = 'gedBundle.add_folder';
}