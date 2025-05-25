<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>

    @include('NavBar')

    <div class="container my-5">

        <h2 class="mb-4 text-center">Autores e seus Livros</h2>

        <div id="summary" class="mb-4 text-center">
            <strong>Carregando resumo...</strong>
        </div>
        <div class="text-left mb-3">
            <button id="printBtn" class="btn btn-primary">Imprimir Relatório</button>
        </div>

        <div id="authorsList">
            <div class="text-center">Carregando...</div>
        </div>

        <div id="pagination" class="d-flex justify-content-center mt-4"></div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_AUTHORS = '/api/authorswithbooks';

        const authorsList = document.getElementById('authorsList');
        const pagination = document.getElementById('pagination');
        const printBtn = document.getElementById('printBtn');
        const summary = document.getElementById('summary');

        function formatPrice(price) {
            return price.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        function renderSummary(authors) {
            const totalAuthors = authors.length;
            let totalBooks = 0;
            let topicsSet = new Set();

            authors.forEach(author => {
                if (author.books && author.books.length) {
                    totalBooks += author.books.length;

                    author.books.forEach(book => {
                        if (book.topics && book.topics.length) {
                            book.topics.forEach(t => topicsSet.add(t.description));
                        }
                    });
                }
            });

            const totalTopics = topicsSet.size;

            summary.innerHTML = `
                <p><strong>Total de Autores:</strong> ${totalAuthors} | 
                <strong>Total de Livros:</strong> ${totalBooks} | 
                <strong>Total de Assuntos:</strong> ${totalTopics}</p>
            `;
        }

        function renderAuthors(authors) {
            if (!authors.length) {
                authorsList.innerHTML = '<p class="text-center">Nenhum autor encontrado.</p>';
                return;
            }

            renderSummary(authors);

            let html = '';
            authors.forEach(author => {
                html += `
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">${author.name}</h5>
                    </div>
                    <ul class="list-group list-group-flush">
            `;

                if (author.books && author.books.length > 0) {
                    author.books.forEach(book => {
                        const topics = book.topics?.map(t => t.description).join(', ') || '-';
                        html += `
                        <li class="list-group-item">
                            <strong>${book.title}</strong> <br />
                            Editora: ${book.publisher} | Edição: ${book.edition} | Ano: ${book.year_of_publication} <br />
                            Preço: ${formatPrice(book.price)} <br />
                            Tópicos: ${topics}
                        </li>
                    `;
                    });
                } else {
                    html += `<li class="list-group-item"><em>Sem livros cadastrados</em></li>`;
                }

                html += `</ul></div>`;
            });

            authorsList.innerHTML = html;
        }

        function renderPagination(meta) {
            let buttons = '';

            if (meta.current_page > 1) {
                buttons +=
                    `<button class="btn btn-outline-primary me-2" onclick="fetchAuthors(${meta.current_page - 1})">Anterior</button>`;
            }

            buttons += `<span class="align-self-center mx-2">Página ${meta.current_page} de ${meta.last_page}</span>`;

            if (meta.current_page < meta.last_page) {
                buttons +=
                    `<button class="btn btn-outline-primary ms-2" onclick="fetchAuthors(${meta.current_page + 1})">Próxima</button>`;
            }

            pagination.innerHTML = buttons;
        }

        function fetchAuthors(page = 1) {
            axios.get(`${API_AUTHORS}?page=${page}`)
                .then(response => {
                    if (response.data.success && response.data.data) {

                        const data = Array.isArray(response.data.data) ? response.data.data : [response.data.data];
                        renderAuthors(data[0].data);

                        renderPagination({
                            current_page: data[0].current_page,
                            last_page: data[0].last_page
                        });

                    } else {
                        authorsList.innerHTML = '<p class="text-center text-danger">Erro ao carregar autores.</p>';
                        pagination.innerHTML = '';
                    }
                })
                .catch(() => {
                    authorsList.innerHTML = '<p class="text-center text-danger">Erro ao carregar autores.</p>';
                    pagination.innerHTML = '';
                });
        }

        printBtn.addEventListener('click', () => {
            const printContent = authorsList.innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = `
                <h2 class="text-center my-4">Autores e seus Livros</h2>
                ${printContent}
            `;

            window.print();

            document.body.innerHTML = originalContent;

            location.reload();
        });
        fetchAuthors();
    </script>

</body>

</html>
