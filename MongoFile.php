<?php

class MongoFile extends MongoObject implements FileInterface
{
  private $mModificationTime;
  private $mImportFilepath;
  private $mPath = null;
  
  /**
   * Make the constructor private. If you want to create MongoFile objects
   * either "load()" an existing one or "create()" a new one.
   */
  public function __construct($localFilepath)
  {
    $this->mPath = $localFilepath;
  }
  
  /**
   * Get the size of this file in bytes.
   * @return int - the size of the file in bytes.
   */
  public function getSize()
  {
    
  }
  
  /**
   * Set the size of this file.
   * 
   * I don't understand why this is a public method. Surely the size is just the size
   * of the data being stored and this object should be handling this internally.
   * Also why am I returning this object?
   * @param int $size - the size of the file in bytes.
   *
   * @return $this
   */
  public function setSize($size)
  {
    # Do nothing. Size is determined by the size of the object in the system
    return $this;
  }
  
  /**
   * Get the time this file was last modified.
   * @return DateTime - the time the file was last modified.
   */
  public function getModifiedTime() 
  { 
    return $this->mModificationTime;
  }
  
  /**
   * Set the time this file was modified.
   * Why is this a public method? Shouldnt the modification time be handled internally and 
   * be updated whenever a user changes the data in the file?
   * Also why am I returning $this?
   * @param DateTime $modified
   *
   * @return $this
   */
  public function setModifiedTime($modified)
  {
    $this->mModificationTime = $modified;
    $this->updateField("modification_time", $modified);
    return $this;
  }
  
  /**
   * Load an existing Mongo object.
   * @param MongoId $id
   * @return MongoFile
   */
  public static function loadFromMongoId(MongoId $id)
  {
    $query = array("_id" => $id);
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    /* @var $mongoDoc MongoGridFSFile */
    $mongoDoc = $gridFs->findOne($query);
    $mongoFile = MongoFolder::loadFromMongoDoc($mongoDoc);
    return $mongoFile;
  }
  
  /**
   * Load this object from the provided document within the gridfs system.
   * @param MongoGridFSFile $doc - the doc that represents a file.
   * @return \MongoFile
   */
  public static function loadFromMongoDoc(MongoGridFSFile $doc)
  {
    $name = $doc->file['name'];
    $modificationTime = $doc->file['modification_time'];
    $creationTime = $doc->file['creation_time'];
    $parentFolder = MongoFolder::loadFromMongoId($doc->file['parent']);
    
    $mongoFile = new MongoFile($name, $parentFolder, $creationTime, $modificationTime, $doc);
    $mongoFile->mMongoId = $id;
    return $mongoFile;
  }
  
  /**
   * Fetch the path to the file within the mongo filesystem. This does not include
   * the name of the file/folder, although I am unsure of whether this is "correct" or not.
   * @return string - the path to the file/folder.
   */
  public function getPath()
  {
    if(isset($this->mPath))
    {
      # path to file in local storage.
      $path = $this->mPath;
    }
    else 
    {
      # path withing the mongo filesystem
      $path = parent::getPath();
    }
    
    return $path;
  }
}
