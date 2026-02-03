

const listaEl = document.getElementById('lista');
const form = document.getElementById("formTarefa");
const inputTitulo = document.getElementById("titulo");

carregar();

//LISTAR

async function carregar() {
    const res = await fetch("../tarefas/api.php?acao=listar");
    const data = await res.json();

    if(!data.ok){
        listaEl.innerHTML = `<p class="muted">Erro ao carregar.</p>`;
        return;
    }

    renderizar(data.tarefas);

    //RENDER

    function renderizar(tarefas){
        if(!tarefas.length){
            listaEl.innerHTML = `<p class="muted">Sem tarefas por enquanto.</p>`;
        }
    }

    listaEl.innerHTML = tarefas.map(
        (t) => `
        <div class="task ${t.status == "feito" ? "task--done" : ""}">
          <button class ="check" title="Marcar como feito/pendente" onclick="toggle(${t.id}) ${t.status === "feito" ? "✅" :"⏹️"}
          </button>
          
        
        
        `
    )
}