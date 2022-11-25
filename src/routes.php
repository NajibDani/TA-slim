<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Middleware\Auth;
use App\Controller\IndexController;
use App\Controller\loginController;
use App\Controller\registerController;
use App\Controller\homeController;
use App\Controller\DashboardController;

return function (App $app) {
    $container = $app->getContainer();

    $container['view'] = function ($container) {
        $view = new \Slim\Views\Twig('../templates', [
            'cache' => false
        ]);

        //instantiate and add slim specific extension
        $router = $container->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

        return $view;
    };

    $app->get('/', function (Request $request, Response $response, array $args) use ($container) {
        return $this->view->render($response, 'home.twig', $args);
    });
    
    $app->get('/login', function (Request $request, Response $response, array $args) use ($container) {
        return $this->view->render($response, 'login.twig', $args);
    });
    $app->post('/login', function(Request $request, Response $response, array $args) use ($container) {
        $data = $request->getParsedBody();
        return loginController::islogin($this, $request, $response,[
            'data'=>$data
        ]);
    });
    
    $app->get('/signup', function (Request $request, Response $response, array $args) use ($container) {
        if (Auth::isLogined()) {
            return $response->withRedirect('/');
        }
    });

    $app->post('/signup', function(Request $request, Response $response, array $args) use ($container) {
        $data = $request->getParsedBody();
        return registerController::register($this, $request, $response,[
            'data'=>$data
        ]);
    });

    $app->get('/logout', function ($request, $response, $args) {
        session_destroy();
        return $response->withRedirect('/login');
      })->add(new Auth());
    
    $app->get('/home', function (Request $request, Response $response, array $args) use ($container) {
        return $this->view->render($response, 'home.twig', $args);
    })->add(new Auth());;
    
    
    $app->get('/getSiswa', function (Request $request, Response $response, array $args) use ($container) {
        $data = $request->getParsedBody();
        return homeController::getSiswa($this, $request, $response,[
            'data'=>$data
        ]);
    })->add(new Auth());;
    
    $app->post('/addSiswa', function (Request $request, Response $response, array $args) use ($container) {
        $data = $request->getParsedBody();
        return homeController::tambahSiswa($this, $request, $response,[
            'data'=>$data
        ]);
    })->add(new Auth());;
    
    $app->delete('/removeSiswa/{id}', function (Request $request, Response $response, array $args) use ($container) {
        $data = $request->getParsedBody();
        return homeController::hapusData($this, $request, $response,$args);
    })->add(new Auth());
};
