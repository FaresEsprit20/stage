<?php

 
// imports will be here
require "ProductionByMonth.php";
require "Months.php";


class ProductionModel {

    //atributes
    private $id;
    private $ligne;
    private $produit;
    private $date;
    private $heure;
    private $qte;
    private $conn;

    //constructor
    public function __construct($db) {
       $this->conn = $db;
    }

    //getters and setters
    public function getId()
    {
        return $this->id;
    }
    public function setName($var)
    {
        $this->id = $var;
    }

    public function getLigne()
    {
        return $this->ligne;
    }
    public function setLigne($var)
    {
        $this->ligne = $var;
    }

    public function getProduit()
    {
        return $this->produit;
    }
    public function setProduit($var)
    {
        $this->produit = $var;
    }

    public function getDate()
    {
        return $this->date;
    }
    public function setDate($var)
    {
        $this->date = $var;
    }

    public function getHeure()
    {
        return $this->heure;
    }
    public function setHeure($var)
    {
        $this->heure = $var;
    }

    public function getQte()
    {
        return $this->heure;
    }
    public function setQte($var)
    {
        $this->heure = $var;
    }

    //to string Method
    public function __toString()
    {
        print(" ID  : ".$this->id." Ligne : ".$this->ligne." Produit : ".$this->produit.
        " Date : ".$this->date." Heure : ".$this->heure." Qte : ".$this->qte);
    }
    

    public function getProductionAll() {

        $stmt = $this->conn->prepare("SELECT * FROM production");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result) ;
     
    }

   public function getTodayProduction() {
    $today =  date("Y-m-d");
    $stmt = $this->conn->prepare("SELECT * FROM production where date = ? ");
    $stmt->execute([$today]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result) ;

   }

   public function getProductsStatsByType(){
    $today =  date("Y-m-d");    
    $stmt = $this->conn->prepare("SELECT SUM(qte) as total,ligne, produit,date,heure FROM production where date = ? group by LOWER(ligne) ");
    $stmt->execute([$today]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result) ;
   }
    

   
   public function getProductsStatsByMonths() {
    $today =  date("Y-m-d"); 
    $date_arr = explode("-", $today);  
    $year = $date_arr[0];
    $month = $date_arr[1];
    $day = $date_arr[2];
 
    $prods = array();
    $months = array(
    1 ,
    2 ,
    3 ,
    4 ,
    5 ,
    6 ,
    7 ,
    8 ,
    9 ,
    10 ,
    11 ,
    12
 );


        $stmt = $this->conn->prepare("SELECT SUM(qte) as total , id ,ligne, produit, date, heure FROM production where YEAR(date) = ?  GROUP BY LOWER(ligne) ");
        $stmt->execute([$year]);
       
    while( $row = $stmt->fetch(pdo::FETCH_ASSOC)){
        $date_arrrow = explode("-", $row["date"]);  
        $rowmonth = $date_arrrow[1];
        $ligne = $row["ligne"];
        $produit = $row["produit"];
        $p = new ProductionByMonth($row["id"],$row["ligne"],$row["produit"],$row["date"],$row["heure"],$row["total"],-1,$year);
        $prodmonths = array();
    foreach ($months as $month){
       
        if(($rowmonth == $month) ){
            array_push($prodmonths,new Months($month,(int) $row["total"]));
            // array_push($prods, new ProductionByMonth($row["id"],$row["ligne"],$row["produit"],$row["date"],$row["heure"],$row["total"],$month,$year));
         }else if ($rowmonth != $month){
            array_push($prodmonths,new Months($month,0));
            // array_push($prods, new ProductionByMonth($row["id"],$row["ligne"],$row["produit"],$row["date"],$row["heure"],0,$month,$year));  
          }
            
        }
    $p->months = $prodmonths;
    array_push($prods,$p);
    
    }
   

    echo json_encode($prods);   



   }




}


