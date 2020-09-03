# three.php
Three.php is a PHP 3D renderer that can run on ordinary webservers. On most hardware it can run at least 10 fps and the animation can be seen on any ordinary browser (if you zoom out a little that is)

![License](https://badgen.net/github/license/gogorikidze/three.php)
![lastCommit](https://badgen.net/github/last-commit/gogorikidze/three.php)
![Commits](https://badgen.net/github/commits/gogorikidze/three.php)
![Size](https://img.shields.io/github/repo-size/gogorikidze/three.php)
![LibSemicolonCount](http://gogorikidze.com/semicolon/badge.php?user=gogorikidze&repo=three.php&branch=master&path=three.php)

![demo](https://img.ge/images/09095034995461985102.gif)\
left output uses PurePHP renderer and the right - HTMLRenderer

# Rules
This uses no libraies, frameworks or any other language what so ever. Outputs using 'echo' and newLines using 'nl2br' function. nl2br converts '\n' to '<\br>' tags. I couldnt find any way to newline whithout this and also it could be argued that this is a function of php so it's not cheating.\
Also, This runs on any webserver with PHP. I have tested it on shared hosting webservers that use LiteSpeed.\
HTMLRenderer doesnt follow the rules as it uses (echo "<html></html>") output
