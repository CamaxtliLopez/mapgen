# mapgen - Generate Point Crawls for the Worlds Without Number RPG

WWN includes an algorithm for generating point crawls randomly.  mapgen is a PHP script which automates this algorithm.

![example map](map.example.png)

You need to install php and gd, on ubuntu the commands are:
```
sudo apt install php7.4-cli
sudo apt-get install php7.4-gd
```
Then you can run the script in one of two ways, at the command line or in a server.

To run the script at the command line, do:
```
php mapgen.php x
```
Where x is the number of rooms in the dungeon (between 1 and 100). It should generate the file map.png.

To run the script in a server, do:
```
php -S localhost:8000
```
and point your browser at http://localhost:8000/.

