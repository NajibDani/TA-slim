<?php
    namespace App\Controller;
    use Medoo\Medoo;

    class HomeController {
        public static function index($app, $req, $rsp, $args) {
            $app->get('renderer')->render($rsp, 'home.twig', $args);
        }
        
        public function showMenu($app, $req, $rsp, $args){
            $app->get('view')->render($rsp, 'home.twig', $args);
        }

        public function getSiswa($app, $req, $res, $args)
        {
            $page = $req->getParam('page') ?? 1;
            $perpage = $req->getParam('per_page') ?? 5;
            $data = ($page - 1) * $perpage;

            $siswa = $app->db->select("tbl_siswa (siswa)", [
                "[><]tbl_nilai (nilai)" => ["siswa.id_siswa" => "id_siswa"]
            ], [
                "siswa.id_siswa",
                "nama_siswa",
                "gender_siswa",
                "nilai_siswa"
            ], ['LIMIT' => [
                $data, $perpage
              ], 'ORDER' => [
                'id_siswa' => 'ASC'
              ]]);

            $count = $app->db->count('tbl_siswa');

            if (count($siswa) < 1) {
            $data["msg"] = "data tidak tersedia";
            return $res->withJson($data, 404);
            }
            return $res->withJson(array(
            "data" => $siswa,
            "page" => $page,
            "per_page" => $perpage,
            "total_page" => ceil($count / $perpage),

            "jumlah" => $count
            ));
        }
        public function show(Request $req, Response $res, array $args)
        {
            $product = $app->db->get('tbl_siswa', '*', ['id_siswa' => $args['id']]);
            if (count($siswa) < 1) {
            $data["msg"] = "data tidak tersedia";
            return $res->withJson($data, 404);
            }
            return $res->withJson($siswa);
        }

        public function tambahSiswa($app,$req, $res, array $args)
        {

            $data = $req->getParsedBody();

            // $result = $app->db->query("
            // INSERT INTO tbl_siswa( id_siswa, nama_siswa, gender_siswa)
            // SELECT siswa.id_siswa, siswa.nama_siswa, siswa.gender_siswa, nilai.nilai
            // FROM tbl_siswa siswa
            // INNER JOIN tbl_nilai nilai ON nilai.id_siswa = siswa.id_siswa");
            
            $result = $app->db->insert('tbl_siswa', [
                'nama_siswa' => $data['nama'],
                'gender_siswa' => $data['gender']
              ]);
            // die(var_dump($app->db->id('id_siswa')));
            // if($result){
                $app->db->insert('tbl_nilai', [
                    'id_siswa' => $app->db->id('id_siswa'),
                    'nilai_siswa' => $data['nilai']
                ]);
            // }
              
        }

        public function hapusData($app,$req, $res, array $args)
        {
            $id = $args['id'];
            $result = $app->db->delete('tbl_siswa', ['id_siswa' => $id]);
            $app->db->delete('tbl_nilai', ['id_siswa' => $id]);
            if (!$result) {
            return $res->withJson([
                'msg' => 'gagal menghapus data'
            ]);
            }
            return $res->withJson([
            'msg' => 'berhasil menghapus products dengan id ' .  $id
            ]);
        }
    }
?>