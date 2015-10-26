<?php

/**
 * Both "files" and "folders" are just mongo objects in the underlying system and 
 * share a lot of the same methods which can be implemented here as a shared parent.
 */

class MongoObject
{  
  /**
   * Return the name of this file/folder.
   * @return string
   */
  public function getName() { return $this->mName; }
  
  /**
   * Rename this item.
   * @param string $name - the new name for the file/folder.
   *
   * @return $this
   */
  public function setName($name)
  {
    return $this;
  }

  /**
   * Fetch the time this file/folder was created.
   * @return DateTime - the DateTime when this object was created.
   */
  public function getCreatedTime()
  {
    $dateTime = new DateTime();
    
    # implement...
    
    return $dateTime;
  }
  
  /**
   * Set the time this object was created.
   * I am confused as to why this is a public method. Surely this object and the storage system
   * should be managing this and external classes should not be changing it. But this ties
   * in with my confusion about the FileSystemInterface having the createFile and createFolder
   * methods which themeselves take a File/Folder object as parameters.
   * 
   * @param DateTime $created
   *
   * @return $this
   */
  public function setCreatedTime($created)
  {
    return $this;
  }

  /**
   * Fetch the path to this item. This will get you into the folder that contains
   * this item. Was unsure whether this should include the name, but one can just call
   * getName() in addition.
   * 
   * @return string
   */
  public function getPath()
  {
    $path = "";
    return $path;
  }
  
  /**
   * Set the path to this file/folder. I am assuming that this does not include the 
   * name of the file/folder but am unsure. This means that if you want to rename a file
   * or folder you need to use the setName() method.
   * Setting the path should result in parent folders being created where necessary. (mkdir -p)
   * Changing the path of a folder act like updating a pointer rather than 
   * resulting in having to send queries to update all of the sub-items.
   * 
   * @param string $path - and absolute path such as /path/to/folder/or/file.php
   *
   * @return $this
   */
  public function setPath($path)
  {
    
  }
  
  
  /**
   * Fetch the folder that contains this file/folder.
   * @return FolderInterface - the parent folder containing this file/folder
   */
  public function getParentFolder()
  {
    
  }

  /**
   * Move this object to within the provided parent folder.
   * @param FolderInterface $parent - the folder we wish to move this file/folder to within.
   *
   * @return $this - this modified item.
   */
  public function setParentFolder(FolderInterface $parent)
  {
    return $this;
  }  
}
