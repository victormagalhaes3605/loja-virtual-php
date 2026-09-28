<?php
namespace Models;

use PDO;
use PDOException;

class Venda {
    private $pdo;

    public function __construct() {
        try {
            if (class_exists('\MySql')) {
                $this->pdo = \MySql::conectar();
            } else {
                $this->pdo = new PDO('mysql:host=localhost;dbname=gestao_vendas;charset=utf8', 'root', '');
            }
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro na Conexão com o Banco de Dados: " . $e->getMessage());
        }
    }

    public function getResumo() {
        try {
            // Conta o total de pedidos e soma o valor de todas as vendas
            $sql =$this->pdo->prepare("SELECT COUNT(*) as total_vendas, SUM(valor_total) as faturamento_total FROM `vendas`");
            $sql->execute();
            $resultado =$sql->fetch(PDO::FETCH_ASSOC);

            // Retorna os dados garantindo que não há valores nulos caso a tabela esteja vazia
            return [
                'total_vendas' => $resultado['total_vendas'] ?? 0,
                'faturamento_total' => $resultado['faturamento_total'] ?? 0.00             ];         } catch (PDOException$e) {
            return [
                'total_vendas' => 0,
                'faturamento_total' => 0.00
            ];
        }
    }

    public function criar($dados) {
        try {
            $this->pdo->beginTransaction();

            // 1. Insere o cabeçalho na tabela vendas
            $sqlVenda =$this->pdo->prepare("INSERT INTO `vendas` (cliente_nome, cliente_email, cliente_telefone, endereco, forma_pagamento, valor_total) VALUES (?, ?, ?, ?, ?, ?)");
            $sqlVenda->execute([
                $dados['cliente_nome'],$dados['cliente_email'],
                $dados['cliente_telefone'],$dados['endereco'],
                $dados['forma_pagamento'],$dados['valor_total']
            ]);

            $vendaId =$this->pdo->lastInsertId();

            // 2. Insere os itens na tabela correta: itens_venda (no singular)
            if (!empty($dados['itens']) && is_array($dados['itens'])) {
                $sqlItem =$this->pdo->prepare("INSERT INTO `itens_venda` (venda_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)");
                
                foreach ($dados['itens'] as $item) {$sqlItem->execute([
                        $vendaId,$item['produto_id'],
                        $item['quantidade'],$item['preco']
                    ]);
                }
            }

            $this->pdo->commit();
            return $vendaId;

        } catch (PDOException $e) {$this->pdo->rollBack();
            echo "<div style='background: #ffe6e6; color: #cc0000; padding: 20px; font-family: monospace; margin: 20px; border: 1px solid #cc0000;'>";
            echo "<h3>Erro ao gravar no Banco de Dados:</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "</div>";
            exit;
        }
    }

    public function getAll() {
        $sql =$this->pdo->prepare("SELECT * FROM `vendas` ORDER BY id DESC");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql =$this->pdo->prepare("SELECT * FROM `vendas` WHERE id = ?");
        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function getItens($vendaId) {
        // Faz um JOIN com a tabela de produtos para trazer o nome e a imagem
        $sql =$this->pdo->prepare("
            SELECT iv.*, p.nome, p.titulo, p.imagem, p.foto 
            FROM `itens_venda` iv
            LEFT JOIN `produtos` p ON iv.produto_id = p.id
            WHERE iv.venda_id = ?
        ");
        $sql->execute([$vendaId]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}