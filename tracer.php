<?php
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
  public function __construct($position, $h, $w, $far, $density){
    $this->density = $density;
    $this->far = $far;
    $this->position = $position;
    $this->h = $h;
    $this->w = $w;
    $this->frustrum = [];
    $this->createGrid();
  }
  public function createGrid(){ // this grid creates an array of pixels in front of the camera
    //these variables are used to center the array in front of the camera
    $halfwfrustrum = $this->w/$this->density/2 - 1/$this->density/2;
    $halfhfrustrum = $this->h/$this->density/2 - 1/$this->density/2;

    for($h = 0; $h < $this->h; $h++){
      $line = [];
      for($w = 0; $w < $this->w; $w++){
        $thispixel = new Vec3(
          $this->position->x - $w/$this->density + $halfwfrustrum,
          $this->position->y - $h/$this->density + $halfhfrustrum,
          $this->position->z + $this->far
        );
        array_push($line, $thispixel); //every pixel is pushed in line array
      }
      array_push($this->frustrum, $line); //this scanline (arr) is pushed to main array
    }
  }
  public function renderTriangle($triangle){
    for($h = 0; $h < $this->h; $h++){
      for($w = 0; $w < $this->w; $w++){
        $rayorigin = $this->frustrum[$h][$w];
        $raydirection = new Vec3(0,0,1);
        if(check($rayorigin, $raydirection, $triangle)){
          echo "##";
        }else{
          echo "__";
        }
      }
      echo nl2br("\n");
    }
  }
}
class Triangle{
  public function __construct($v0, $v1, $v2){
    $this->v0 = $v0;
    $this->v1 = $v1;
    $this->v2 = $v2;
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
  $equc = 1 / $eP->scalarProduct($eE1);
  $u = $equc * $eP->scalarProduct($eT);
  if($u < 0){return false;}
  $v = $equc * $eQ->scalarProduct($eD);
  if($v < 0 || $v + $u > 1){return false;}
  $t = $equc * $eQ->scalarProduct($eE2);
  if($t < 0){return false;}
  return true;
}
?>
