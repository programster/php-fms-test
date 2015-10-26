<?php

/*
 * Singleton that manages the connection to the MongoDB storage system.
 * This ensures that we only ever have one connection at a time and don't have to
 * keep passing it around between classes/methods.
 * This class should also handle any issues such as timeouts etc.
 */


class ConnectionHandler 
{
  private static $sInstance;
  private $mGrid;
  
  
  /*
   * Constructor for this singleton.
   * Must be private so that the object can only be created by this class if one doesn't
   * already exist.
   */
  private function __construct()
  {
    $con = new Mongo("mongodb://" . MONGO_HOST . ":27017");
    $db = $con->selectDB(MONGO_DATABASE); // Connect to Database
    $this->mGrid = $db->getGridFS();
  }
  
  
  /**
   * Fetch the single instance of this object.
   * @return ConnectionHandler
   */
  public static function getInstance()
  {
    if (self::$sInstance === null)
    {
      self::$sInstance = new ConnectionHandler();
    }
    
    return self::$sInstance;
  }
  
  
  /**
   * Fetch the GridFS connection.
   * @return MongoGridFS
   */
  public function getConnection()
  {
    return $this->mGrid;
  }
}
