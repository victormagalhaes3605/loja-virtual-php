<?php
namespace Controllers;

use Models\Banner;

class BannerController {

    public function index() {
        $bannerModel = new Banner();
        $banners = $bannerModel->getAll();
        
        // Aponta para o caminho real na sua estrutura (src/Views/admin/banners.php)
        require __DIR__ . '/../Views/admin/banners.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $posicao = trim($_POST['posicao'] ?? 'principal');
            $titulo = trim($_POST['titulo'] ?? '');
            $subtitulo = trim($_POST['subtitulo'] ?? '');
            $link = trim($_POST['link'] ?? '#');

            $nomeImagem = null;
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $nomeImagem = 'banner_' . uniqid() . '.' . strtolower($extensao);
                $destino = __DIR__ . '/../../public/uploads/' . $nomeImagem;

                if (!is_dir(__DIR__ . '/../../public/uploads')) {
                    mkdir(__DIR__ . '/../../public/uploads', 0777, true);
                }

                move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
            }

            if ($nomeImagem) {
                $bannerModel = new Banner();
                $bannerModel->criar([
                    'posicao'   => $posicao,
                    'titulo'    => $titulo,
                    'subtitulo' => $subtitulo,
                    'imagem'    => $nomeImagem,
                    'link'      => $link
                ]);
            }

            header('Location: index.php?route=admin-banners');
            exit;
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $bannerModel = new Banner();
            $bannerModel->deletar($id);
        }
        header('Location: index.php?route=admin-banners');
        exit;
    }
}