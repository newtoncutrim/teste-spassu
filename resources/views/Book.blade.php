<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro de Livro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body class="bg-light">

    @include('NavBar')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4 text-center">Cadastro de Livro</h4>

                        <form id="bookForm" novalidate>

                            <div class="mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" id="title" class="form-control"
                                    placeholder="Digite o título do livro" />
                                <div class="error-message" id="titleError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="publisher" class="form-label">Editora</label>
                                <input type="text" id="publisher" class="form-control"
                                    placeholder="Digite a editora" />
                                <div class="error-message" id="publisherError"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="year_of_publication" class="form-label">Ano de Publicação</label>
                                    <input type="number" id="year_of_publication" class="form-control"
                                        placeholder="2024" min="1900" max="2100" />
                                    <div class="error-message" id="yearError"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">Preço (R$)</label>
                                    <input type="number" id="price" class="form-control" placeholder="99.90"
                                        step="0.01" min="0" />
                                    <div class="error-message" id="priceError"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edition" class="form-label">Edição</label>
                                    <input type="number" id="edition" class="form-control" placeholder="2"
                                        min="1" />
                                    <div class="error-message" id="editionError"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="authors" class="form-label">Autores</label>
                                <select id="authors" class="form-select" multiple></select>
                                <small class="form-text text-muted">Segure Ctrl (Cmd) para selecionar múltiplos
                                    autores.</small>
                                <div class="error-message" id="authorsError"></div>
                            </div>

                            <div class="mb-3">
                                <label for="topics" class="form-label">Tópicos</label>
                                <select id="topics" class="form-select" multiple></select>
                                <small class="form-text text-muted">Segure Ctrl (Cmd) para selecionar múltiplos
                                    tópicos.</small>
                                <div class="error-message" id="topicsError"></div>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <button type="submit" class="btn btn-primary w-100" id="submitBtn">Cadastrar</button>
                                <button type="button" class="btn btn-secondary w-100 d-none"
                                    id="cancelEditBtn">Cancelar
                                    Edição</button>
                            </div>

                        </form>

                        <hr class="my-4" />

                        <h5 class="mb-3">Lista de Livros</h5>
                        <ul class="list-group" id="booksList">
                        </ul>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const API_BOOKS = '/api/books';
        const API_AUTHORS = '/api/authors';
        const API_TOPICS = '/api/topics';

        const bookForm = document.getElementById('bookForm');
        const titleInput = document.getElementById('title');
        const publisherInput = document.getElementById('publisher');
        const yearInput = document.getElementById('year_of_publication');
        const priceInput = document.getElementById('price');
        const editionInput = document.getElementById('edition');
        const authorsSelect = document.getElementById('authors');
        const topicsSelect = document.getElementById('topics');

        const submitBtn = document.getElementById('submitBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const booksList = document.getElementById('booksList');

        const errors = {
            title: document.getElementById('titleError'),
            publisher: document.getElementById('publisherError'),
            year: document.getElementById('yearError'),
            price: document.getElementById('priceError'),
            edition: document.getElementById('editionError'),
            authors: document.getElementById('authorsError'),
            topics: document.getElementById('topicsError'),
        };

        let isEditing = false;
        let editingId = null;

        function clearErrors() {
            Object.values(errors).forEach(errEl => errEl.textContent = '');
        }

        function fetchAuthors() {
            axios.get(API_AUTHORS)
                .then(res => {
                    authorsSelect.innerHTML = '';
                    res.data.data.forEach(author => {
                        const option = document.createElement('option');
                        option.value = author.id;
                        option.textContent = author.name;
                        authorsSelect.appendChild(option);
                    });
                })
                .catch(() => alert('Erro ao carregar autores'));
        }

        function fetchTopics() {
            axios.get(API_TOPICS)
                .then(res => {
                    topicsSelect.innerHTML = '';
                    res.data.data.forEach(topic => {
                        const option = document.createElement('option');
                        option.value = topic.id;
                        option.textContent = topic.description;
                        topicsSelect.appendChild(option);
                    });
                })
                .catch(() => alert('Erro ao carregar tópicos'));
        }

        function fetchBooks() {
            axios.get(API_BOOKS).then(res => {
                    renderBooks(res.data.data);
                })
                .catch(() => alert('Erro ao carregar livros'));
        }

        function renderBooks(books) {
            booksList.innerHTML = '';
            if (books.length === 0) {
                booksList.innerHTML = '<li class="list-group-item text-center">Nenhum livro cadastrado.</li>';
                return;
            }

            books.forEach(book => {
                const li = document.createElement('li');
                li.className = 'list-group-item';

                const authorsNames = (book.authors || []).map(a => a.name).join(', ');
                const topicsNames = (book.topics || []).map(t => t.description).join(', ');

                li.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${book.title}</strong> <br />
                            <small>Editora: ${book.publisher} | Ano: ${book.year_of_publication} | Edição: ${book.edition} | Preço: R$ ${parseFloat(book.price).toFixed(2)}</small><br />
                            <small>Autores: ${authorsNames}</small><br />
                            <small>Tópicos: ${topicsNames}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-warning me-2" onclick="startEditBook(${book.id})">Editar</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBook(${book.id})">Excluir</button>
                        </div>
                    </div>
                `;

                booksList.appendChild(li);
            });
        }

        function resetForm() {
            isEditing = false;
            editingId = null;
            bookForm.reset();
            clearErrors();
            submitBtn.textContent = 'Cadastrar';
            cancelEditBtn.classList.add('d-none');
        }

        window.startEditBook = function(id) {
            axios.get(`${API_BOOKS}/${id}`)
                .then(res => {
                    const book = res.data.data;

                    isEditing = true;
                    editingId = id;
                    titleInput.value = book.title;
                    publisherInput.value = book.publisher;
                    yearInput.value = book.year_of_publication;
                    priceInput.value = book.price;
                    editionInput.value = book.edition;

                    Array.from(authorsSelect.options).forEach(option => {
                        option.selected = book.authors.map(a => a.id).includes(Number(option.value));
                    });

                    Array.from(topicsSelect.options).forEach(option => {
                        option.selected = book.topics.map(t => t.id).includes(Number(option.value));
                    });

                    submitBtn.textContent = 'Atualizar';
                    cancelEditBtn.classList.remove('d-none');
                    clearErrors();
                })
                .catch(() => alert('Erro ao carregar dados do livro'));
        };

        cancelEditBtn.addEventListener('click', () => {
            resetForm();
        });

        function parseIfNumber(value) {
            const trimmed = value.trim();
            return !isNaN(trimmed) && trimmed !== '' ? Number(trimmed) : trimmed;
        }


        window.deleteBook = function(id) {
            if (!confirm('Deseja realmente excluir este livro?')) return;
            axios.delete(`${API_BOOKS}/${id}`)
                .then(() => {
                    fetchBooks();
                    resetForm();
                })
                .catch(() => alert('Erro ao excluir livro'));
        };

        bookForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const title = parseIfNumber(titleInput.value);
            const publisher = parseIfNumber(publisherInput.value);
            const year_of_publication = parseInt(yearInput.value);
            const price = parseFloat(priceInput.value);
            const edition = parseInt(editionInput.value);
            const authors = Array.from(authorsSelect.selectedOptions).map(opt => Number(opt.value));
            const topics = Array.from(topicsSelect.selectedOptions).map(opt => Number(opt.value));


            const data = {
                title,
                publisher,
                year_of_publication,
                price,
                edition,
                authors,
                topics
            };

            if (isEditing) {
                axios.put(`${API_BOOKS}/${editingId}`, data)
                    .catch(error => {
                        clearErrors();
                        if (error.response && error.response.status === 422 && error.response.data.errors) {
                            const validationErrors = error.response.data.errors;
                            for (const field in validationErrors) {
                                if (errors[field]) {
                                    errors[field].textContent = validationErrors[field].join(' ');
                                }
                            }
                        } else {
                            errors.title.textContent = error.response?.data?.message ||
                                'Erro ao atualizar livro.';
                        }
                        console.error(error);
                    });
            } else {
                axios.post(API_BOOKS, data)
                    .catch(error => {
                        clearErrors();
                        if (error.response && error.response.status === 422 && error.response.data.errors) {
                            const validationErrors = error.response.data.errors;
                            for (const field in validationErrors) {
                                if (errors[field]) {
                                    errors[field].textContent = validationErrors[field].join(' ');
                                }
                            }
                        } else {
                            errors.title.textContent = error.response?.data?.message ||
                                'Erro ao cadastrar livro.';
                        }
                        console.error(error);
                    });
            }

        });

        fetchAuthors();
        fetchTopics();
        fetchBooks();
    </script>

</body>

</html>
