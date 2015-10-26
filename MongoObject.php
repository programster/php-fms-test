<?php

class MongoObject
{
  /*@var $mGridFsFile MongoGridFSFile */
  protected $mGridFsFile;
  protected $mName; 
  protected $mCreationTime;

  
  
  private function __construct() {}
  
  
  /**
   * Fetch the mongoId of this object.
   * @return MongoId
   */
  public function getMongoId() 
  {
    return $this->mGridFsFile->file["_id"];
  }
  
  
  /**
   * Return the name of this item.
   * @return string
   */
  public function getName() { return $this->mName; }
  
  /**
   * Rename this folder.
   * @param string $name
   *
   * @return $this
   */
  public function setName($name)
  {
    /* @TODO - mongo would allow two folders with the same name but should we allow this? */
    $this->mName = $name;
    $this->updateField("name", $name);
    return $this;
  }

  /**
   * Fetch the time this folder was created.
   * @return DateTime
   */
  public function getCreatedTime()
  {
    $creationTime = $this->getMetadataField("creation_time");
    $dateTime = new DateTime();
    $dateTime->setTimestamp($creationTime);
    return $dateTime;
  }
  
  /**
   * @param DateTime $created
   *
   * @return $this
   */
  public function setCreatedTime($created)
  {
    /*@var $created DateTime */
    $unixTimestamp = $created->getTimestamp();
    $this->mCreationTime = $unixTimestamp;
    $this->updateField("creation_time", $unixTimestamp);
    return $this;
  }

  /**
   * @return string
   */
  public function getPath()
  {
    $path = "";
    $folderPointer = $this;
    
    $thrownException = false;
    
    # $folderPointer->getParentFolder() throws exception if there is no parent
    while (!$thrownException)
    {
      try
      {
        $parentFolder = $folderPointer->getParentFolder();
        $path = $parentFolder->getName() . "/" . $path;
        $folderPointer = $parentFolder;
      } 
      catch (Exception $ex) 
      {
        $thrownException = true;
      }
    }
    
    # Strip off the last /
    if ($path !== "")
    {
      $path = substr($path, 0, -1);
    }
    
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
    if ($path !== "" && $path !== "/")
    {
      $folderNames = explode("/", $path);
      array_shift($folderNames);
      $parentFolder = MongoFileSystem::loadRoot();
      
      foreach ($folderNames as $folderName)
      {
        $subFolders = $parentFolder->getSubFolders();
        $indexedSubFolders = array();
        
        foreach ($subFolders as $index => $subFolder)
        {
          $indexedSubFolders[$subFolder->getName()] = $subFolder;
        }
        
        if (isset($indexedSubFolders[$subFolder]))
        {
          $parentFolder = $indexedSubFolders[$subFolder];
        }
        else
        {
          # Need to create the subfolders from here.
          $newFolder = new MongoFolder($folderName);
          $parentFolder = MongoFileSystem::getInstance()->createFolder($newFolder, $parentFolder);
        }
      }
      
      # The last "parent folder" from the loop should be the last in the path which we want to make
      # our parent
      if ($parentFolder === null)
      {
        throw new Exception("error set path resulted in a null parent folder");
      }
      
      print PHP_EOL;
      print "SETPATH $path: setting parent folder" . PHP_EOL;
      $this->setParentFolder($parentFolder);
      $this->updateField('path', $path);
    }
    else
    {
      
    }
    
    return $this;
  }
  
  /**
   * Fetch the parent folder that contains this object.
   * @return FolderInterface
   * @throws Exception - if there is no parent
   */
  public function getParentFolder()
  {
    $parentMongoId = $this->getMetadataField("parent");
    
    if ($parentMongoId !== null)
    {
      $mongoFolder = MongoFolder::loadFromMongoId($parentMongoId);
    }
    else
    {
      throw new Exception("Folder has no parent");
    }
    
    return $mongoFolder;
  }
  
  /**
   * Move this object to within the provided parent folder.
   * @param FolderInterface $parent - the folder we wish to move this file/folder to within.
   *
   * @return $this - this modified item.
   */
  public function setParentFolder(FolderInterface $parent)
  {
    $parentMongoFolder = MongoFolder::loadFromFolderInterface($parent);
    $this->updateField("parent", $parentMongoFolder->getMongoId());
    return $this;
  }
  
  /**
   * Update a metadata field. Taken from https://secure.php.net/manual/en/class.mongogridfs.php
   * @param string $name - the name of the metadata field to update/insert
   * @param mixed $value - the value to put in.
   */
  protected function updateField($name, $value)
  {
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    $this->mGridFsFile->file[$name] = $value;
    $gridFs->save($this->mGridFsFile->file);
  }
  
  /**
   * Fetch a metadata field from this object.
   * @param type $name
   * @return type
   */
  protected function getMetadataField($name)
  {
    return $this->mGridFsFile->file[$name];
  }
}
