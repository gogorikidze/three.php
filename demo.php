<?php
$demoFiles = scandir("demo/");
for($i = 2; $i < count($demoFiles); $i++) {
    if($demoFiles[$i] != "obj"){
        echo $demoFiles[$i].
        " <a href='demo/".$demoFiles[$i]."?frame=0&rendererType='>(TextRenderer)</a>
        <a href='demo/".$demoFiles[$i]."?frame=0&rendererType=HTMLRenderer'>(HTMLRenderer)</a>
        <br>";
    } 
}
?>
