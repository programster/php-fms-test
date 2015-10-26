<?php

/**
 * File System Management
 */
class MongoFileSystem implements FileSystemInterface
{
  private static $s_instance;
  
  private function __construct(){}
  
  
  /**
   * Get the instance of the filesystem.
   * @return MongoFileSystem
   */
  public static function getInstance()
  {
    if (self::$s_instance === null)
    {
      self::$s_instance = new MongoFileSystem();
    }
    
    return self::$s_instance;
  }
  
  /**
   * Create the specified file.
   * I dont understand why this exists with the provided $file. Surely if we want to 
   * create a file we should be calling the file's constructor or one of its static
   * creation methods?
   * @param FileInterface   $file - the file to be created?
   * @param FolderInterface $parent - the parent folder to stick the file within.
   *
   * @return FileInterface
   */
  public function createFile(FileInterface $file, FolderInterface $parent)
  {
    $filepath = $file->getPath() . '/' . $file->getName();
    
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    $timeNow = time();
    
    $mongoFolder = MongoFolder::loadFromFolderInterface($parent);
    
    $metadata = array(
      'creation_time'     => $file->getCreatedTime(),
      'modification_time' => $file->getModifiedTime(),
      'parent'            => $mongoFolder->getMongoId(),
      'type'              => 'file',
    );
    
    # This is another hack where the getPath() method here is returning the path to the
    # file as it is locally stored in the linux filesystem, rather than the path of where
    # it will be stored withing the mongo based filesystem.
    $mongoId = $gridFs->storeFile($file->getPath(), $metadata);
    $mongoFile = MongoFile::loadFromMongoId($mongoId);
    return $mongoFile;
  }
  
  /**
   * Update the provided file (in what way!?). 
   * Surely we should call un update() method on the file with some sort of parameters?
   * @param FileInterface $file - the file to be modified.
   *
   * @return FileInterface
   */
  public function updateFile(FileInterface $file)
  {
    
  }
  
  /**
   * Rename a file.
   * @param FileInterface $file - the file to be renamed.
   * @param String        $newName - the new name for the file.
   *
   * @return FileInterface
   */
  public function renameFile(FileInterface $file, $newName)
  {
    return $file;
  }
  
  /**
   * Delete the provided folder.
   * This should be a wrapper around the file's deletion method.
   * @param FileInterface $file - the file we wish to delete.
   *
   * @return bool - true if the file was deleted, false if there was an error.
   */
  public function deleteFile(FileInterface $file)
  {
    
  }
  
  /**
   * Create the root folder that has no parent or name, and its path is just /
   * I do not understand why this takes a FolderInterface as a parameter.
   * @param FolderInterface $folder - the folder we wish to have as the root.
   *
   * @return FolderInterface - the created folder.
   */
  public function createRootFolder(FolderInterface $folder)
  {
    $folder->setName("");
    $folder->setPath("/");
    
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    
    $metadata = array(
      'name'              => $folder->getName(),
      'path'              => $folder->getPath(),
      'creation_time'     => $folder->getCreatedTime(),
      'type'              => 'folder',
    );
    
    $mongoId = $gridFs->storeBytes("", $metadata); # folder is an empty file with metadata
    $mongoFolder = MongoFolder::loadFromMongoId($mongoId);
    return $mongoFolder;
  }

  /**
   * Create the specified folder.
   * I dont understand why this exists with the provided $folder. Surely if we want to 
   * create a folder we should be calling the $folders constructor or one of its static
   * creation methods?
   * @param FolderInterface $folder - the folder to be created?
   * @param FolderInterface $parent - the folder the new folder will be within.
   *
   * @return FolderInterface - the newly created folder.
   */
  public function createFolder(FolderInterface $folder, FolderInterface $parent)
  {
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    
    $parentMongoFolder = MongoFolder::loadFromFolderInterface($parent);
    
    if ($folder->getCreatedTime() === null)
    {
      $folder->setCreatedTime(time());
    }
    
    $metadata = array(
      'name'              => $folder->getName(),
      'path'              => $parent->getPath() . '/' . $parent->getName(),
      'creation_time'     => $folder->getCreatedTime(),
      'parent'            => $parentMongoFolder->getMongoId(),
      'type'              => 'folder'
    );
    
    print "creating folder with details: " . print_r($metadata, true);
    
    $gridFs->storeBytes("", $metadata); # folder is an empty file with metadata
    $mongoFolder = MongoFolder::loadFromFolderInterface($folder);
    return $mongoFolder;
  }
  
  /**
   * Delete the specified folder. Returns true or false depending on whether
   * succeeded or failed.
   * This could fail if the $folder doesn't exist anymore.
   * @param FolderInterface $folder
   *
   * @return bool - true if succeeded, false if there was an error.
   */
  public function deleteFolder(FolderInterface $folder)
  {
    
  }
  
  /**
   * Rename the specified folder.
   * @param FolderInterface $folder
   * @param String          $newName - the new name for the folder.
   *
   * @return FolderInterface
   */
  public function renameFolder(FolderInterface $folder, $newName)
  {
    $folder->setName($newName);
    return $folder;
  }
  
  /**
   * Fetch the number of folders direclty below the specified folder.
   * Should this not be a method within the Folder interface that this wraps around?
   * e.g. $folder->getFolderCount()
   * @param FolderInterface $folder - the folder we wish to get the folder count of.
   *
   * @return int - the number of folders within the provided folder.
   */
  public function getFolderCount(FolderInterface $folder)
  {
    
  }

  /**
   * Get the number of files (not including folders) directly below the specified folder.
   * Should this not be a method within the Folder interface that this wraps around?
   * e.g. $folder->getFileCount()
   * @param FolderInterface $folder - the folder we wish to get the file count of.
   *
   * @return int
   */
  public function getFileCount(FolderInterface $folder)
  {
    
  }
  
  /**
   * Fetch the number of files/folders within the directory. I am basing this on the fact that
   * in Ubuntu Nautilus, this is what "size" is. Although I could have misunderstood 
   * the expectations and maybe the size of all the bytes under this directory is 
   * expected?
   * @param FolderInterface $folder
   *
   * @return int
   */
  public function getDirectorySize(FolderInterface $folder)
  {
    
  }

  /**
   * Fetch the folders within the provided folder.
   * @param FolderInterface $folder
   *
   * @return FolderInterface[]
   */
  public function getFolders(FolderInterface $folder)
  {
    $folder->getPath();
  }

  /**
   * Fetch the files from inside a folder, not including any folders.
   * @param FolderInterface $folder
   *
   * @return FileInterface[]
   */
  public function getFiles(FolderInterface $folder)
  {
    $files = array();
    $gridFs = ConnectionHandler::getInstance()->getConnection();
    
    # Fetch the folder by it's path.
    $mongoFolder = MongoFolder::loadFromFolderInterface($folder);
    
    $filesQueryConditions = array();
    $filesQueryConditions[] = array('type' => 'file');
    $filesQueryConditions[] = array('parent' => $mongoFolder->getMongoId());
    $searchFilesQuery = array('$and' => $filesQueryConditions);
    $cursor = $gridFs->find($searchFilesQuery);
    
    while (($document = $cursor->getNext()) != null)
    { 
      /* @var $document MongoGridFsFile */
      $files[] = MongoFile::load($document->file["_id"]);
    }
    
    return $files;
  }
  
  
  /**
   * Load the root folder from which everything else is under.
   * @return MongoFolder
   */
  public static function loadRoot()
  {
    $gridfs = ConnectionHandler::getInstance()->getConnection();
    $conditions = array();
    $conditions[] = array("type" => "folder");
    $conditions[] = array("parent" => array('$exists' => false));
    $document = $gridfs->findOne(array('$and' => $conditions));
        
    $rootFolder = MongoFolder::loadFromMongoDoc($document);
    return $rootFolder;
  }
}
