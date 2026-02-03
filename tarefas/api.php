<?php
/* 
api.php 
responsabilidade: ser a api da aplicação

esse arquivo não mostra HTML.
ELe apenas:
-recebe aquisições
-conversa com o banco
-devolve resposta em JSON

as ações são definidas via GET:
?acao = listar 
?acao = criar 
?acao = atualizar
?acao = exclur
?acao = toggle

*/

require "../gerenciador-de-tarefas/tarefas";

header("Content-type: application/json; charset=utf-8");

$acao = $_GET['acao'] ?? "listar";

try {
    if($acao === "listar"){
        // busca
        $stmt = $conn ->query("SELECT * FROM tarefas ORDER BY id DESC"); 

        $tarefas = $stmt->fetchAll();

        echo json_encode([
            "ok" => true,
            "tarefas" => $tarefas
        ]);

        //Encerra 
        exit;
    }
    

    if($acao === "criar"){
        $titulo = trim($_POST['titulo'] ?? "");
    }
    if($titulo === ""){
        echo json_encode([
            "ok" => false,
            "msg" => "Titulo é obrigatório"
        ]);
        exit;
        }
    
    //
    $sql = "INSERT INTO tarefas (titulo) VALUE (?)";

    $stmt->$conn->prepare($sql);
    
    $stmt->execute([$titulo]);

    echo json_encode([
        "ok" => true,
        "msg" => 'Tarefa Criada'
    ]);
    exit;

    if($acao === "atualizar"){
        $id = $_POST['id'] ?? null;
        $titulo = trim($_POST['titulo'] ?? "");

        if(!$id || $titulo === ""){
            echo json_encode([
                "ok" => false,
                "msg" => "Dados inválidos"
            ]);
            exit;
        };
    }

    $sql = "UPDATE tarefas SET titulo = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$titulo, $id]);

    echo json_encode([
        "ok" => true,
        "msg" => "Tarefa atualizada"
    ]);

    if ($acao === "excluir"){
        
        $id = $_POST['id'] ?? null;

        if(!$id){
            echo json_encode([
                "ok" => false,
                "msg" => "Dados inválidos"
            ]);
        };

        $sql = "DELETE tarefas = WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        echo json_encode([
            "ok" => true,
            "msg" => "Tarefa excluída"
        ]);

    }



    }catch (\Throwable $th) {

}


?>