<?php
/**
 * api.php
 * Responsabilidade: ser a api da aplicação
 * 
 * Esse arquivo não mostra HTML.
 * Ele apenas:
 * -recebe aquisições
 * -conversa  com o banco
 * -devolve resposta em JSON
 * 
 * As ações são definidas via GET:
 * ?acao=listar
 * ?acao=criar
 * ?acao=atualizar
 * ?acao=excluir
 * ?acao=toggle 
 */

// aqui nasce o $conn (PDO)
require "../config/conexao.php";

// Define que Toda resposta desse aquivo será um JSON
// Isso é essencial para o JS entender o retorno
header("Content-type: application/json; charset=utf-8");

//Lê a ação enviada pela URL(?acao=..)
// se não vier nada, assumi "listar"
$acao = $_GET["acao"] ?? "listar";


try {

    //Listar

    if($acao === "listar"){
        
        // Busca todas as tarefas do banco, da mais recente para a mais antiga
        $stmt = $conn->query("SELECT *  FROM tarefas ORDER BY id DESC");

        // Converte o resultado do banco em um array PHP
        $tarefas = $stmt->fetchAll();

        // retornar os dados em um Json para o front
        echo json_encode([
            "ok" => true,
            "tarefas" => $tarefas
        ]);

        //Encerra a execução (API respondeu, acabou)
        exit;
    }

    // Criar

    if ($acao === "criar"){

        // Pega o Titulo enviado ia POST
        // trim remove espaços existentes
        $titulo = trim($_POST["titulo"] ?? ""); 


        // Validação  basica 
        if($titulo === ""){
            echo json_encode([
                "ok" => false,
                "msg" => "Titulo é obrigatório"
            ]);
            exit;
        }

        // Query com placeholder para evitar SQL Injection
        $sql = "INSERT INTO  tarefas (titulo) VALUES (?)";

        // Preraçao da query
        $stmt = $conn->prepare($sql);

        //executando e passando o valor real pra query 
        $stmt->execute([$titulo]);

        // Retorno sucesso 
        echo json_encode([
            "ok" => true,
            "msg" => "Tarefa Criada"
        ]);
        exit;

    }


    // ATUALIZAR 

    if ($acao === "atualizar") {

        // ID e novo titulo vindos do POST
        $id = $_POST["id"] ?? null;
        $titulo = trim($_POST['novoTitulo'] ?? "");

        if (!$id || $titulo === "") {
            echo json_encode([
                "ok" => false,
                "msg" => "Dados inválidos"
            ]);
            exit;
        }

        // Atualiar apenas a tarefa com o ID informado
        $sql = "UPDATE tarefas SET  titulo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$titulo, $id]);

        echo json_encode([
            "ok" => true,
            "msg" => "Tarefa Atualizada"
        ]);
        exit;
    }


    // EXCLUIR
    
    if($acao === "excluir") {

        // ID enviado pelo front
        $id = $_POST["id"] ?? null;

        if (!$id){
            echo json_encode([
                "ok" => false,
                "msg" => "ID inválido"
            ]);
            exit;

        }

        // Remover a tarefa do banco
        $sql = "DELETE FROM tarefas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        echo json_encode([
            "ok" => true,
            "msg" => "Tarefa excluida"
        ]);
        exit;

    }

    //TOGGLE

    if($acao === "toggle") {
        $id = $_POST["id"] ?? null;


        if(!$id){
            echo json_encode([
                "ok" =>  false,
                "msg" => "ID Invalido"
            ]);
            exit;
        }

        // Alternar o status direto no sql
        // Se for 'pendente' vira 'feito'
        // se for 'feito' vira 'pendente'

        $sql = "UPDATE tarefas
                SET status = IF(status='pendente','feito','pendente')
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        echo json_encode([
            "ok" => true,
            "msg" => "Status alterado"
        ]);
        exit;
    }

} catch (Exception $e) {
    // se der qualquer erro no banco ou no codigo
    // a API retorna a resposta com um JSON e não quebra a aplicação 
    echo json_encode([
        "ok" => false,
        "msg" => "Erro: " . $e->getMessage()
    ]);
    exit;
}




?>