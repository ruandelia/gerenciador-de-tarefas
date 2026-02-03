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
    }
    else{

    }
} catch (\Throwable $th) {

}


?>