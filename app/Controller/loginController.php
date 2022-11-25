<?php
    namespace App\Controller;
    use Medoo\Medoo;

    class LoginController {
        public static function login($app, $req, $rsp, $args) {
            $app->get('renderer')->render($rsp, 'login.twig', $args);
        }

        public static function islogin($app,$req, $res, $args){
            $body = $req->getParsedBody();
            $user = $body['username'];
            $pass = $body['password'];

            $data = $app->db->get("tbl_users",[
                'username', 'password'
            ], [
                'username'=>$user
            ]);

            if($data){
                if ($data['password'] = $pass){
                    $_SESSION['username'] = $data['username'];
                    
                    return $res
                        ->withHeader('content-type', 'application/json')
                        ->withStatus(200)
                        ->withRedirect('/');
                } else {
                    $app->flash->addMessage('errors', 'Password Anda Salah');
                    return $res->withRedirect('/auth/login');
                  }
                } else {
                  $app->flash->addMessage('errors', 'Username belum terdaftar');
                  return $res->withRedirect('/auth/login');
                }
                return $res->withRedirect('/home');
            }

            // public function register(Request $req, Response $res, array $args)
            // {
            //     $data = $req->getParsedBody();
            //     $user = $app->db->select('tbl_users', ['username'], [
            //     'username' => $data['username']
            //     ]);
            //     if (!$user) {
            //     $result = $app->db->insert('tbl_users', [
            //         'username' => $data['username'],
            //         'first_name' => $data['first_name'],
            //         'last_name' => $data['last_name'],
            //         'gender' => $data['gender'],
            //         'password' => md5($data['password'])
            //     ]);
            //     if (!$result) {
            //         $app->flash->addMessage('errors', 'gagal daftar ada sesuatu yang errors');
            //         return $res->withRedirect('/auth/register');
            //     } else {
            //         $_SESSION['username'] = $data['username'];
            //     }
            //     // dd($result);
            //     } else {
            //     $app->flash->addMessage('errors', 'username telah terdaftar');
            //     return $res->withRedirect('/auth/register');
            //     }
            //     return $res->withRedirect('/signup');
            // }
    }
?>