// 1. SELEÇÃO DE ELEMENTOS (DOM)
// O 'document' representa a sua página HTML inteira.
// O 'getElementById' busca um elemento específico pelo ID que você deu no HTML.
// Guardamos esses elementos em constantes (const) para usar depois.
const listaEl = document.getElementById('lista'); // Onde as tarefas vão aparecer
const form = document.getElementById("formTarefa"); // O formulário de adicionar
const inputTitulo = document.getElementById("titulo"); // O campo de texto

// 2. INICIALIZAÇÃO
// Chamamos a função 'carregar' imediatamente para buscar as tarefas assim que a página abrir.
carregar();

// 3. FUNÇÃO PRINCIPAL (ASSÍNCRONA)
// Usamos 'async' porque essa função vai depender de dados externos (do servidor),
// então ela precisa saber "esperar" (await).
async function carregar() {
    
    // O 'fetch' vai buscar dados na URL indicada.
    // O 'await' diz: "Pare o código aqui e só continue quando o servidor responder".
    // Sem o await, o código tentaria ler a resposta antes dela chegar.
    const res = await fetch("../tarefas/api.php?acao=listar");
    
    // A resposta (res) chega como um fluxo de dados. 
    // Precisamos converter esse fluxo para JSON (o formato de objeto do JavaScript).
    // Isso também leva um tempinho, por isso usamos 'await' de novo.
    const data = await res.json();

    // 4. TRATAMENTO DE ERRO
    // Verificamos se a propriedade 'ok' do objeto 'data' é falsa.
    // Se deu erro, mostramos uma mensagem no HTML e usamos 'return' para parar a função aqui.
    if(!data.ok){
        listaEl.innerHTML = `<p class="muted">Erro ao carregar.</p>`;
        return; // Sai da função carregar
    }

    // Se deu tudo certo, chamamos a função 'renderizar' passando a lista de tarefas.
    // Nota: 'data.tarefas' deve ser o array (lista) que veio do back-end.
    renderizar(data.tarefas);

    // 5. FUNÇÃO DE RENDERIZAÇÃO (DESENHAR NA TELA)
    // Esta função recebe a lista de tarefas e coloca no HTML.
    function renderizar(tarefas){
        
        // Se o tamanho da lista (length) for 0 (vazio), mostra mensagem de "Sem tarefas".
        if(!tarefas.length){
            listaEl.innerHTML = `<p class="muted">Sem tarefas por enquanto.</p>`;
            // Deveria ter um 'return' aqui para não tentar desenhar a lista abaixo se estiver vazia.
            return; 
        }

        // AQUI COMEÇA A MÁGICA DO .MAP
        // O .map percorre cada item da lista 'tarefas' (chamamos cada item de 't').
        // Para cada tarefa, ele retorna um pedaço de HTML preenchido com os dados dela.
        listaEl.innerHTML = tarefas.map(
            (t) => `
                <div class="task ${t.status == "feito" ? "task--done" : ""}">
                <!--
                    Botão de marcar / desmarcar (Toggle)
                -->
                  
                  <button class="check" title="Marcar como feito/pendente" onclick="toggle(${t.id})">
                     ${t.status === "feito" ? "✅" : "⏹️"}
                  </button>
                  <div class="task_content">
                  <div class"task_title" id="title-${t.id}">${escapeHtml(t.titulo)}</div>
                  <div class="task_meta">
                  ${t.status === "feito" ? "feito" : "Pendente"}
                  </div>
                  </div>
                  <div class="task_action">
                  <button class="btn btn-small" onclick="editar(${t.id}, ${escapeAttr(t.titulo)})">✏️</button>

                  <button class="btn btn-small btn--danger" onclick="excluir(${t.id}, ${escapeAttr(t.titulo)})">🗑️</button>
                  </div> 
                  </div>
            `
        ).join(''); // O .join('') é essencial aqui: ele junta todos os itens do array em um único texto (string) para o HTML.
    }
}