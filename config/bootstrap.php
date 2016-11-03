<?php
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Stories\Middleware\LoggerMiddleware;

collection((array)Configure::read('Missions.config'))->each(function ($file) {
    Configure::load($file);
});
