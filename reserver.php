<?php


class Reservation {
  private $pdo; 
  private $stmt; 
  public $error; 

  function __construct() {
    try {
      $this->pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
      );
    } catch (Exception $ex) { die($ex->getMessage()); }
  }

  function __destruct() {
    $this->pdo = null;
    $this->stmt = null;
  }

  function save ($date, $slot, $name, $email, $tel, $notes="") {
	   $minhour = date('H');
	  


    try {
      $this->stmt = $this->pdo->prepare(
        "INSERT INTO `reservations` (`res_date`, `res_slot`, `res_name`, `res_email`, `res_tel`, `res_notes`) VALUES (?,?,?,?,?,?)"
      );
      $this->stmt->execute([$date, $slot, $name, $email, $tel, $notes]);
    } catch (Exception $ex) {
      $this->error = $ex->getMessage();
      return false;
    }

   
    $subject = "Confirmation de réservation";
    $message = "Votre rendez-vous dans notre salon a bien été pris en compte.";
    @mail($email, $subject, $message);
    return true;
	
  
  }

  function getDay ($day="") {
    if ($day=="") { $day = date("Y-m-d"); }

    $this->stmt = $this->pdo->prepare(
      "SELECT * FROM `reservations` WHERE `res_date`=?"
    );
    $this->stmt->execute([$day]);
    return $this->stmt->fetchAll(PDO::FETCH_NAMED);
  }
}


define('DB_HOST', 'localhost');
define('DB_NAME', 'test');
define('DB_CHARSET', 'utf8');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

$_RSV = new Reservation();