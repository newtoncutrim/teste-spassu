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

        <div id="authorsList">
            <div class="text-center">Carregando...</div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_AUTHORS = '/api/authorswithbooks';

        const authorsList = document.getElementById('authorsList');

        function formatPrice(price) {
            return price.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        function renderAuthors(authors) {
            if (!authors.length) {
                authorsList.innerHTML = '<p class="text-center">Nenhum autor encontrado.</p>';
                return;
            }

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

        function fetchAuthors() {
            axios.get(API_AUTHORS)
                .then(response => {
                    if (response.data.success && response.data.data) {

                        const data = Array.isArray(response.data.data) ? response.data.data : [response.data.data];
                        renderAuthors(data);
                    } else {
                        authorsList.innerHTML = '<p class="text-center text-danger">Erro ao carregar autores.</p>';
                    }
                })
                .catch(() => {
                    authorsList.innerHTML = '<p class="text-center text-danger">Erro ao carregar autores.</p>';
                });
        }

        fetchAuthors();
    </script>

</body>

</html>
