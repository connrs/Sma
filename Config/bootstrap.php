<?php
Cache::config('Sma', array(
    'engine' => 'File',
    'duration' => '+30 days',
    'path' => CACHE . 'sma' . DS,
    'prefix' => ''
));
