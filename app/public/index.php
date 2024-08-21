<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit(0);
}
require_once('../vendor/autoload.php');
$router = new \Bramus\Router\Router();
$router->setNamespace('\Http');
$router->before('GET|POST', '/.*', function () {
    session_start();
});
// add your routes and run!

$router->mount('/api', function () use ($router) {
    $router->get('/home', 'ProductController@home');

    $router->get('/ice', 'ProductController@getIceCreams');
    $router->get('/cake', 'ProductController@getIceCakes');

    $router->get('/products/(\d+)', 'ProductController@productInfo');
    $router->get('/products', 'ProductController@getAllProducts');
    $router->put('/products', 'ProductController@increaseStock');
    $router->put('/products/(\d+)', 'ProductController@editProduct');
    $router->delete('/products/(\d+)', 'ProductController@deleteProduct');
    $router->post('/products', 'ProductController@addProduct');

    $router->get('/about', 'ContactController@about');
    $router->get('/contact', 'ContactController@contact'); //is nothing
    $router->post('/contact', 'ContactController@contactSend');
    $router->get('/order', 'ContactController@order'); //is nothing
    $router->post('/order', 'ContactController@orderPost');
    $router->get('/truck/order', 'ContactController@orderTruck');
    $router->post('/truck/order', 'ContactController@orderTruckPost'); //is nothing

    $router->get('/dashboard', 'StoreController@dashboard'); //is nothing
    $router->post('/products/ice', 'ProductController@addIce');
    $router->get('/calendar/event', 'StoreController@event'); // is nothing

    $router->get('/login', 'StoreController@showLogin');
    $router->post('/login', 'StoreController@login');
    $router->post('/logout', 'StoreController@logout');

    $router->post('/events', 'EventController@addEvent');
    $router->get('/events', 'EventController@getEvents');

    $router->get('/popups', 'PopupController@getPopups');
    $router->post('/popups', 'PopupController@createPopup');
});

$router->run();