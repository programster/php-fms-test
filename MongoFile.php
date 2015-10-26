<?php

class MongoFile extends MongoObject implements FileInterface
{
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
    return $this;
  }
 

  /**
   * Get the time this file was last modified.
   * @return DateTime - the time the file was last modified.
   */
  public function getModifiedTime() 
  { 
    
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
    return $this;
  }
  
  
  /**
   * Fetch the path to the file within the mongo filesystem. This does not include
   * the name of the file/folder, although I am unsure of whether this is "correct" or not.
   * @return string - the path to the file/folder.
   */
  public function getPath()
  {
    
  }
}
