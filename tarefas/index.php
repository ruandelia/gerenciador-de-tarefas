<?php include "../includes/header.php" ?>

<section class="card">
    <h2> Lista de Tarefas</h2>

    <form id="formTarefa" class="form">
        <input
            type="text"
            name="titulo"
            id="titulo"
            placeholder="Digite a tarefa ..."
            required
        >
        <button type="submit" class="btn btn--primary">Adicionar</button>
    </form>

    <div id="lista" class="list"></div>

</section>

<script src="../assets/js/tarefa.js"> </script>
<?php include "../includes/footer.php" ?>