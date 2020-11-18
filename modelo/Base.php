<?php
abstract class Base
{
  protected $conBD=NULL;
  
  public function __construct()
  {
  	 try
     {
	     if(!class_exists('BDManager'))
           require_once 'BDManager.php';
	
	     $this->conBD = BDManager::getBDManager()->getConectionBD();
	 }
	 catch(PDOException $er)
     {
        throw $er;
     }
     catch(Exception $er)
     {
          throw $er;
     }
  }

}
?>