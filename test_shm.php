<?php 

//
// Testing script for shm_cache Class
// to run : php test_shm.php
// 

require_once("shm_cache.php");

$cache = new shm_cache;
$cache->setpath("/dev/shm/");
$cache->createdir("cache");

// testing caching with a timer (cached for 3 seconds)

echo "testing cache with expiration 1 minute\n";

for($i=0;$i<70;$i++) {

$res = $cache->cache_write("cache","test_cache.txt",md5(rand(0,10000)),1);
$data = $cache->cache_read("cache","test_cache.txt");
echo "Data in cache :".$data."\n";
sleep(1);

}

// testing caching without a timer (forever cached)

echo "testing cache without expiration \n";


for($i=0;$i<60;$i++) {

$res = $cache->cache_write("cache","test_cache.txt",md5(rand(0,10000)),0);
sleep(1);
$data = $cache->cache_read("cache","test_cache.txt");
echo "Data in cache :".$data."\n";
sleep(1);

}


echo "trying to create a forbidden cache file\n";
try {
$res = $cache->cache_write("cache","test_cache.php",md5(rand(0,10000)),1);
} catch (ForbiddenException $e) {
  echo "Something wrong happened.\n";
  die();
} 

echo "Succeded to create a forbidden cache file\n";




?>
