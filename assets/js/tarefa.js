
/**
 * tarefas.js
 * Responsabilidade: Manipular a lista sem recarregar a pagina
 * -Listar
 * -criar
 * -editar inline
 * -deletar
 * - toggle status
 * 
 * 
 * Ou seja: esse JS é Front do mini trello.
 * Ele chama a api (api.php) e atualiza o HTML dinamicamente
 */

// Pega o elemento onde as tarefas vão aparecer
const listaEl = document.getElementById('lista');
// Pega as informações do formulario (onde cria uma nova tarefa)
const form = document.getElementById("formTarefa");
// pega o input do titulo ( campo onde digita a tarefa)
const inputTitulo = document.getElementById("titulo");


// carrega a lista assim que o arquivo é executado
// ou seja ( abre a pagina -> ja lista as tarefas)
carregar();

//LISTAR ( busca as tarefas na api)


async function carregar() {

    // fetch é um função nativa para requisições HTTP
    //fetch ele não retorna um resuldado direto, ele retorna uma promise ( algo que ainda vai aontecer)
    // O await ( espera o fetch terminar e so então continua a execução)
    // o re  passa a ser o objeto Response da requisição
    // contendo status, header e corpo da resposta
    const res = await fetch("../tarefas/api.php?acao=listar");
    // res.json() lê o corpo da resposta e converter para um objeto JavaScript
    const data = await res.json();

    // Se api retornar falso, vai mostrar essa mensagem
    if (!data.ok) {
        listaEl.innerHTML = `<p class="muted">Erro ao carregar.</p>`;
        return;
    }
    // se der certo ele renderiza a lista de tarefas
    renderizar(data.tarefas);

    //RENDER (montar o HTML das tarefas)

    function renderizar(tarefas) {
        // se não tiver tarefas, exibir o texto abaixo
        if (!tarefas.length) {
            listaEl.innerHTML = `<p class="muted">Sem tarefas por enquanto.</p>`;
        }


        // Aqui vamos tranformar o array em Html
        // map() cria uma lista de strings HTML, um por tarefa
        // join("") junta tudo numa unica string para colocar no innerHtml
        listaEl.innerHTML = tarefas.map((t) => `
            <div class="task ${t.status === "feito" ? "task--done" : ""}">

                <button class="check"
                        title="Marcar como feito/pendente"
                        onclick="toggle(${t.id})">
                    ${t.status === "feito" ? "✅" : "⏹️"}
                </button>

                <div class="task__content">
                    <div class="task__title" id="title-${t.id}">
                        ${escapeHtml(t.titulo)}
                    </div>

                    <div class="task__meta">
                        ${t.status === "feito" ? "Feito" : "Pendente"}
                    </div>
                </div>

                <div class="task__actions">
                    <button class="btn btn--small"
                            onclick="editar(${t.id}, '${escapeAttr(t.titulo)}')">
                        ✏️
                    </button>

                    <button class="btn btn--small btn--danger"
                            onclick="excluir(${t.id})">
                        🗑️
                    </button>
                </div>
            </div>
        `).join("");
    }
}

// CRIAR (Enviar uma tarefa)

// Escuta o envio do formulario

form.addEventListener("submit", async (e) => {
    e.preventDefault(); // impede que o submit recarregue a pagina 

    // Pega o valor do input e remove espaços extras
    const titulo = inputTitulo.value.trim();

    // se vier vazio, não faz nada
    if (!titulo) return;

    // FormData é um jeito mis facil de mandar dados como se fosse um formulario
    const fd = new FormData();
    fd.append("titulo", titulo);

    // Enviar para a API com POST pedindo para "CRIAR"
    const res = await fetch("../tarefas/api.php?acao=criar", {
        method: "POST",
        body: fd,
    });

    // Lê resposta
    const data = await res.json();

    //Se deu ok , Limpa input e regarrega a lista 
    if (data.ok) {
        inputTitulo.value = "";
        carregar();
    }
});


// Excluir

async function excluir(id){
    if (!confirm("Deseja mesmo excluir esta tarefa?")) return;

    const fd = new FormData();
    fd.append("id", id);

    //POST para excluir
    await fetch("../tarefas/api.php?acao=excluir", {
        method: "POST",
        body: fd,
    });

    carregar()
}

//EDITAR INLINE

function editar(id, tituloAtual){
    //Buscar o elemento do titulo
    const el = document.getElementById(`title-${id}`);
    el.append(id, tituloAtual)

    //se já tiver o input dentro, significa que ja está no modo edição
    //evitar que duplique o input 
    if (el.querySelector("input")) return;

    el.innerHTML = `
        <input class="input-inline" id="input-${id}" value="${tituloAtual}"/>
        <button class="btn btn--small btn--primary" onclick="salvarEdicao(${id})">Salvar</button>
    `;
}

async function salvarEdicao(id) {
    const input = document.getElementById(`input-${id}`);
    // Pega o novo Titulo
    const novoTitulo = input.value.trim();

    if(!novoTitulo){
        alert("Titulo Não pode ficar vazio.");
        return;
    }

    const fd = new FormData();
    fd.append("id", id);
    fd.append("novoTitulo", novoTitulo)

    await fetch("../tarefas/api.php?acao=atualizar", {
        method: "POST",
        body: fd,
    }) 

    carregar()
}
// HElpers de segurança (XSS)

// Função de escape basico para impedir que titulos injete HTML/JS na pagina
// EX: se alguem cadastrar "<script>alerta(1)</script>" isso vira texto  e não executa

function escapeHtml(str) {
    return str
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")

}


// Escape para usar  textos dentro de atributos/strings do onclick
// Evita quebrar a string quando tiver Aspas
// EX: "tarefa 'importante' " não vira explode o onlick

function escapeAttr(str) {
    return str.replaceAll("'", "&#39;").replaceAll('"', "&quot;");
}