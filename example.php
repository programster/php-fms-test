<?php

/*
 * Example usage of the Mongo Grid FS based filesystem.
 */

require_once(__DIR__ . '/settings/Settings.php');
require_once(__DIR__ . '/AutoLoader.php');

$dirs = array(
  __DIR__
);

$autoloader = new Autoloader($dirs);

function main()
{
  $filesystem = MongoFileSystem::getInstance();
  $rootFolder = new MongoFolder("/");
  $filesystem->createRootFolder($rootFolder);
  $myNewFolder = new MongoFolder("user1");
  $filesystem->createFolder($myNewFolder, $rootFolder);

  $filepath = __DIR__ . '/merkel-tree.jpg';
  $myFile = new MongoFile($filepath);

  # At this point the file is saved into the storage system.
  $filesystem->createFile($myFile, $rootFolder);

  $myNewFolder->setPath("/a/really/long/path/of/folders/that/dont/exist");
  $myNewFolder->setName("myNewName");

  # Get the parent, this should be called "dont"
  print "my new folder after setting name: " . print_r($myNewFolder, true) . PHP_EOL;
  $parentFolder = $myNewFolder->getParentFolder();
  $parentFolderName = $parentFolder->getName();
  
  if ($parentFolderName !== "exist") 
  { 
    die("Parent folder should be called exist"); 
  }

  if ($filesystem->getDirectorySize($parentFolder) != 1)
  {
    die("The 'dont' folder should only contain one folder so size should be 1");
  }
  
  $filesystem->renameFolder($parentFolder, "changedName");
  
  if ($myNewFolder->getPath() !== "/a/really/long/path/of/folders/that/dont/changedName")
  {
    $msg =
        "checking folder path after rename failed" . PHP_EOL .
        "expected: /a/really/long/path/of/folders/that/dont/changedName" . PHP_EOL . 
        "recieved: " . $myNewFolder->getPath();
    die($msg);
  }
  
  $filesystem->deleteFolder($myNewFolder);
  
  if ($filesystem->getDirectorySize($parentFolder) != 0)
  {
    die("The 'dont' folder should not contain anything");
  }
  
  print "Completed without failing!" . PHP_EOL;
}


main();