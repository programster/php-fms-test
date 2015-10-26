<?php

class MongoFolder extends MongoObject implements FolderInterface
{
  /**
   * Fetch an array of folders that are directly within this folder.
   * @return MongoFolder[]
   */
  public function getSubFolders()
  {
    $folders = array();
    return $folders;
  }
}
