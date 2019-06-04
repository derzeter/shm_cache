What's shm_cache ?
==================

shm_cache can be very useful so you can cache data directly in shared memory (on linux at least), and load it pretty fast.
This little class permits to do this, and also adds expiration to the cache.

Why ?
=====

As much as we love reading text files (on file system), or other information elsewhere which lags and slow downs our servers,
it's much better to read it fast from memory !

How to use it ?
===============

1. Clone this repository
2. Test out with "php test_shm.php" that it works fine
3. If all works out well, use test_shm.php as an example to implement your cache control

Credits ?
=========

Enjoy ! derzeter@2019



