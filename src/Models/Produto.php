<?php
namespace Models;

use Config\Database;
use PDO;

class Produto {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getById($id) {
        $sql =$this->pdo->prepare("SELECT * FROM `produtos` WHERE id = ?");
        $sql->execute([$id]);
        return $sql->fetch(\PDO::FETCH_ASSOC);
    }
     
   public function buscarFiltrado($termo = '') {
        try {
            // Limpa espaços em branco acidentais
            $termo = trim($termo);

            if (empty($termo)) {
                $sql =$this->pdo->prepare("SELECT * FROM `produtos` ORDER BY id DESC");
                $sql->execute();
                return $sql->fetchAll(\PDO::FETCH_ASSOC);
            }

            // A forma mais segura de fazer pesquisas LIKE no PHP (Named Parameters)
            $sql =$this->pdo->prepare("SELECT * FROM `produtos` WHERE nome LIKE :termoPesquisa ORDER BY id DESC");
            
            // Vincula o valor dizendo explicitamente que é uma STRING
            $sql->bindValue(':termoPesquisa', '%' . $termo . '\%', \PDO::PARAM_STR);$sql->execute();
            
            return $sql->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            // Se houver erro, exibe uma tarja vermelha com o motivo exato
            echo "<div style='background:#dc3545; color:#fff; padding:10px; text-align:center; font-weight:bold; z-index:9999; position:relative;'>
                    ERRO DO PDO: " . $e->getMessage() . "
                  </div>";
            
            // Retorna array vazio para não quebrar a página
            return [];
        }
    }

  public function buscarPorCategoria($categoria) {
        // Puxa todos os produtos ativos
        $sql =$this->pdo->prepare("SELECT * FROM `produtos` WHERE ativo = 1 ORDER BY id DESC");
        $sql->execute();
        $todos =$sql->fetchAll(\PDO::FETCH_ASSOC);

        $filtrados = [];
        
        // Função interna para limpar acentos, espaços e letras maiúsculas
        $normalizar = function($str) {$str = strtolower(trim($str));$from = ['á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç'];
            $to   = ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c'];
            return str_replace($from, $to,$str);
        };

        $catBuscada = $normalizar($categoria);

        // Compara ignorando acentos e maiúsculas/minúsculas
        foreach ($todos as $p) {$catProduto = $normalizar($p['categoria'] ?? '');

            if ($catProduto ===$catBuscada) {
                $filtrados[] =$p;
            }
        }

        return $filtrados;
    }
     
    
    public function getAll() {
        // Puxa todos os produtos do mais recente para o mais antigo
        $sql =$this->pdo->prepare("SELECT * FROM `produtos` ORDER BY id DESC");
        $sql->execute();
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find(int $id): array|false {
        $stmt =$this->pdo->prepare("SELECT * FROM produtos WHERE id = :id AND ativo = 1");
        $stmt->execute(['id' =>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool {
        // A categoria foi adicionada aqui
        $stmt =$this->pdo->prepare("INSERT INTO produtos (nome, categoria, preco, estoque, imagem, ativo) VALUES (:nome, :categoria, :preco, :estoque, :imagem, 1)");
        return $stmt->execute([
            'nome' => $data['nome'],
            'descricao' => $dados['descricao'] ?? '',
            'categoria' => $data['categoria'] ?? null,
            'preco' => $data['preco'],
            'estoque' => $data['estoque'],
            'imagem' => $data['imagem'] ?? null
        ]);
    }

    public function update($dados) {
        $sql = $this->pdo->prepare("
            UPDATE produtos 
            SET nome = :nome, 
                descricao = :descricao, /* O marcador aqui... */
                preco = :preco, 
                categoria = :categoria, 
                imagem = :imagem, 
                ativo = :ativo 
            WHERE id = :id
        ");
        
        return $sql->execute([
            'nome'      => $dados['nome'],
            'descricao' => $dados['descricao'], /* ...tem de ter o par exato aqui! */
            'preco'     => $dados['preco'],
            'categoria' => $dados['categoria'],
            'imagem'    => $dados['imagem'],
            'ativo'     => $dados['ativo'],
            'id'        => $dados['id']
        ]);
    }

   public function delete(int $id): bool {
        // Agora sim, apaga definitivamente da base de dados
        $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}