<?php
namespace Models;

use PDO;

class Banner {
    private $pdo;

    public function __construct() {
        if (class_exists('\MySql')) {
            $this->pdo = \MySql::conectar();
        } else {
            $this->pdo = new PDO('mysql:host=localhost;dbname=gestao_vendas;charset=utf8', 'root', '');
        }
    }

    public function getAll() {
        $sql =$this->pdo->prepare("SELECT * FROM `banners` ORDER BY id DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mapeia os banners por posição em um array indexado (ex: $banners['principal'],$banners['lateral_1'])
     */
    public function getAllMapped() {
        $banners = $this->getAll();$mapped = [];

        foreach ($banners as$banner) {
            $posicao =$banner['posicao'] ?? 'principal';
            
            // Se houver mais de um banner na posição "principal", agrupa em lista para o carrossel
            if ($posicao === 'principal') {$mapped[$posicao][] =$banner;
            } else {
                $mapped[$posicao] =$banner;
            }
        }

        return $mapped;
    }

    public function getAtivos() {
        $sql =$this->pdo->prepare("SELECT * FROM `banners` ORDER BY id DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $sql =$this->pdo->prepare("INSERT INTO `banners` (posicao, titulo, subtitulo, link, imagem) VALUES (?, ?, ?, ?, ?)");
        return $sql->execute([
            $dados['posicao'] ?? 'principal',$dados['titulo'] ?? '',
            $dados['subtitulo'] ?? '',$dados['link'] ?? '#',
            $dados['imagem'] ?? null
        ]);
    }

    public function deletar($id) {
        $sql =$this->pdo->prepare("DELETE FROM `banners` WHERE id = ?");
        return $sql->execute([$id]);
    }
}