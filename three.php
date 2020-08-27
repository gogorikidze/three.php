<?php
session_start();

class Vec3{
  public function __construct($x, $y, $z) {
    $this->x = $x;
    $this->y = $y;
    $this->z = $z;
  }
  public function subv($other){
    return new Vec3($this->x - $other->x, $this->y - $other->y, $this->z - $other->z);
  }
  public function scalarProduct($other){
    return $this->x * $other->x + $this->y * $other->y + $this->z * $other->z;
  }
  public function vectorProduct($other){
    return new Vec3($this->y * $other->z - $this->z * $other->y, $this->z * $other->x - $this->x * $other->z, $this->x * $other->y - $this->y * $other->x);
  }
}
class Camera{
  public function __construct($position, $h, $w, $far, $density, $cacheEnabled){
    $this->density = $density;
    $this->far = $far;
    $this->position = $position;
    $this->h = $h;
    $this->w = $w;
    $this->frustum = [];
    $this->buffer = [];

    if($cacheEnabled){
      if(isset($_SESSION['emptyBuffer'])){
        $this->buffer = $_SESSION['emptyBuffer'];
      }else{
        $this->createBuffer();
      }

      if(isset($_SESSION['frustum'])){
        $this->frustum = $_SESSION['frustum'];
      }else{
        $this->createfrustum();
      }
    }else{
      $this->createBuffer();
      $this->createfrustum();
    }
  }
  public function createfrustum(){ // this grid creates an array of pixels in front of the camera
    $frustum = [];
    //these variables are used to center the array in front of the camera
    $halfwfrustum = $this->w/$this->density/2 - 1/$this->density/2;
    $halfhfrustum = $this->h/$this->density/2 - 1/$this->density/2;

    for($h = 0; $h < $this->h; $h++){
      $line = [];
      for($w = 0; $w < $this->w; $w++){
        $thisray = new Vec3(
          $this->position->x - $w/$this->density + $halfwfrustum,
          $this->position->y - $h/$this->density + $halfhfrustum,
          $this->position->z + $this->far
        );
        array_push($line, $thisray); //every pixel is pushed in line array
      }
      array_push($frustum, $line); //this scanline (arr) is pushed to main array
    }

    $_SESSION['frustum'] = $frustum;
    $this->frustum = $_SESSION['frustum'];
  }
  public function createBuffer(){
    $buffer = [];
    for($h = 0; $h < $this->h; $h++){
      $line = [];
      for($w = 0; $w < $this->w; $w++){
        array_push($line, "empty");
      }
      array_push($buffer, $line);
    }
    $_SESSION['emptyBuffer'] = $buffer;
    $this->buffer = $_SESSION['emptyBuffer'];
  }
  public function renderTriangle($triangle){
    for($h = 0; $h < $this->h; $h++){
      for($w = 0; $w < $this->w; $w++){
        $rayorigin = $this->frustum[$h][$w];
        $raydirection = new Vec3(0,0,1);
        if(check($rayorigin, $raydirection, $triangle)){
          $this->buffer[$h][$w] = $triangle->colorChar;
        }
      }
    }
  }
  public function render($scene, $frame){
    $frame += 1;
    header('refresh:0.001; url=index.php?frame='.$frame);

    foreach($scene->objects as $object){
      foreach($object->triangles as $triangle){
          $this->renderTriangle($triangle);
      }

    }
    $this->renderBuffer();
  }
  public function renderBuffer(){
    for($h = 0; $h < $this->h; $h++){
      for($w = 0; $w < $this->w; $w++){
        if($this->buffer[$h][$w] != "empty"){
          echo $this->buffer[$h][$w];
          echo $this->buffer[$h][$w];
        }else{
          echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
      }
      echo nl2br("\n");
    }
  }
}
class Triangle{
  public function __construct($v0, $v1, $v2, $colorChar){
    $this->v0 = $v0;
    $this->v1 = $v1;
    $this->v2 = $v2;
    $this->colorChar = $colorChar;
  }
}
class Scene{
  public function __construct(){
    $this->objects = [];
  }
  public function addObject($object){
    array_push($this->objects, $object);
  }
}
class Mesh{
  public function __construct(){
    $this->triangles = [];
  }
  public function addTriangle($triangle){
    array_push($this->triangles, $triangle);
  }
}
function check($o, $d, $triangle){ //checks for ray triangle intersection
  $eO = $o; // origin point
  $eD = $d; // direction
  $eV0 = $triangle->v0;
  $eV1 = $triangle->v1;
  $eV2 = $triangle->v2;
  $eE1 = $eV1->subv($eV0);
  $eE2 = $eV2->subv($eV0);
  $eT = $eO->subv($eV0);
  $eP = $eD->vectorProduct($eE2);
  $eQ = $eT->vectorProduct($eE1);
  if($eP->scalarProduct($eE1) != 0){
    $equc = 1 / $eP->scalarProduct($eE1);
  }else{
    return false;
  }
  $u = $equc * $eP->scalarProduct($eT);
  if($u < 0){return false;}
  $v = $equc * $eQ->scalarProduct($eD);
  if($v < 0 || $v + $u > 1){return false;}
  $t = $equc * $eQ->scalarProduct($eE2);
  if($t < 0){return false;}
  return true;
}
?>
