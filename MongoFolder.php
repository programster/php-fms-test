<?php

class MongoFolder extends MongoObject implements FolderInterface
{  
  
  /**
   * Please use one of the "create" or "load" methods to create a mongoFolder object.
   * @param type $name
   * @param string $path
   */
  public function __construct($name, $creationTime=0)
  {
    $this->mName = $name;
    
    if ($creationTime === 0)
    {
      $creationTime = time();
    }
    
    $this->mCreationTime = $creationTime;
  }
   
  /**
   * Convert a FolderInterface object into a MongoFolder object.
   * @param FolderInterface $folder
   * @return \MongoFolder
   */
  public static function loadFromFolderInterface(FolderInterface $folder)
  {
    $conditions = array();
    $conditions[] = array("path" => $folder->getPath());
    $conditions[] = array("name" => $folder->getName());
    $query = array('$and' => $conditions);
    
    /* @var $gridFsDoc MongoGridFsFile */
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    $gridFsDoc = $gridFs->findOne($query);
    
    if ($gridFsDoc === null)
    {
      print "Failed to find mongo folder for provided folder interface." . PHP_EOL;
      print "path: " . $folder->getPath() . PHP_EOL;
      print "name: " . $folder->getName() . PHP_EOL;
      die();
    }
    
    /* @var $mongoId MongoId */
    $folderMongoId = $gridFsDoc->file["_id"];
    
    $name = $gridFsDoc->file['name'];
    $creationTime = $gridFsDoc->path['creation_time'];
    $mongoFolder = new MongoFolder($name, $creationTime);
    
    if (isset($gridFsDoc->file["parent"]))
    {
      $parent = MongoFolder::loadFromMongoId($gridFsDoc->file["parent"]);
      $mongoFolder->setParentFolder($parent);
    }
    
    $mongoFolder->mMongoId = $folderMongoId;
    $mongoFolder->mGridFsFile = $gridFsDoc;
    return $mongoFolder;
  }
  
  /**
   * Load an existing Mongo object.
   * @param MongoId $id
   * @return MongoFolder
   */
  public static function loadFromMongoId(MongoId $id)
  {
    $query = array('_id' => $id);
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    /* @var $mongoDoc MongoGridFSFile */
    $mongoDoc = $gridFs->findOne($query);
    
    
    if ($mongoDoc === null)
    {
      throw new Exception("failed to find folder from id: " . print_r($id, true));
    }
    
    $mongoFile = MongoFolder::loadFromMongoDoc($mongoDoc);
    return $mongoFile;
  }
  
  
  /**
   * Load the MongoFolder from the provided MongoGridFSFile within the system.
   * @param MongoGridFSFile $file - a file withing Mongo GridFS.
   * @return \MongoFolder
   */
  public static function loadFromMongoDoc(MongoGridFSFile $file)
  {
    $name = $file->file['name'];
    $creationTime = $file->file['creation_time'];
    $mongoFolder = new MongoFolder($name, $creationTime);
    $mongoFolder->setMongoGridFsFile($file);
    return $mongoFolder;
  }
  
  
  /**
   * Setht the MongoGridFSFile of this object. This is the actual storage data.
   * @param MongoGridFSFile $file
   */
  public function setMongoGridFsFile(MongoGridFSFile $file)
  {
    $this->mGridFsFile = $file;
  }
  
  
  /**
   * Fetch an array of folders that are directly within this folder.
   * @return MongoFolder[]
   */
  public function getSubFolders()
  {
    $subfolders = array();
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    $conditions = array();
    $conditions[] = array('parent' => $this->mGridFsFile->file['_id']);
    $conditions[] = array('type' => $this->mGridFsFile->file['folder']);
    $query = array('$and' => $conditions);
    $cursor = $gridFs->find($query);
    
    while (($folder = $cursor->getNext()) != null)
    { 
      /* @var $folder MongoGridFsFile */
      $subfolders[] = MongoFolder::loadFromMongoDoc($folder);
    }
    
    return $subfolders;
  }
}
