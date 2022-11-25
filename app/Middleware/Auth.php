<?php

    namespace App\Middleware;
    // session_start();

    class Auth {
        public function __invoke($req, $res, $next){
            if (!isset($_SESSION['username'])) {
                return $res->withRedirect('/login');
            }
          
            return $next($req, $res);
        }

        public static function isLogined()
        {
            $isLogin = false;
            if (isset($_SESSION['username'])) {
            $isLogin = true;
            }
            return $isLogin;
        }

    }

?>