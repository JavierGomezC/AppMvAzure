<?php
class BDManager
{
   /*private $host='localhost';
   private $database='ini_mbox';
   private $usuario='root';
   private $password='umbrella';
   */
   private static $instancia;
   public $conBD=NULL;

   private function __construct()
   {
      try
      {
      	 include $_SERVER['DOCUMENT_ROOT']."/archivos/datos_app/var_private_db.php";
         $this->conBD = new PDO("mysql:host=".$host."; dbname=".$database,$usuario,$password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 

         //$this->conBD = new PDO("mysql:host=".$this->host."; dbname=".$this->database,$this->usuario,$this->password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); 
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

   public static function getBDManager()
   {
      try
      {
         if(!self::$instancia instanceof self)
           self::$instancia = new self;
      
         return self::$instancia;
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
   
   public function getConectionBD()
   {
      return $this->conBD;
   }
}
?>