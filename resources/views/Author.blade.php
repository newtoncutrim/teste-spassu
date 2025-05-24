<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro de Autor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">

    @include('NavBar')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4 text-center">Cadastro de Autor</h4>

                        <form id="authorForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" id="name" class="form-control"
                                    placeholder="Digite o nome do autor" required />
                                <div id="nameError" class="text-danger mt-1"></div>
                            </div>


                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <button type="submit" class="btn btn-primary w-100" id="submitBtn">Cadastrar</button>
                                <button type="button" class="btn btn-secondary w-100 d-none"
                                    id="cancelEditBtn">Cancelar Edição</button>
                            </div>
                        </form>

                        <hr class="my-4" />

                        <h5 class="mb-3">Lista de Autores</h5>
                        <ul class="list-group" id="authorsList">
                        </ul>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const API_URL = '/api/authors';

        const authorForm = document.getElementById('authorForm');
        const nameInput = document.getElementById('name');
        const submitBtn = document.getElementById('submitBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const authorsList = document.getElementById('authorsList');

        let isEditing = false;
        let editingId = null;

        function fetchAuthors() {
            axios.get(API_URL)
                .then(response => {
                    renderAuthors(response.data.data || []);
                })
                .catch(error => {
                    alert('Erro ao carregar autores.');
                    console.error(error);
                });
        }

        function renderAuthors(authors) {
            authorsList.innerHTML = '';

            if (authors.length === 0) {
                authorsList.innerHTML = '<li class="list-group-item text-center">Nenhum autor cadastrado.</li>';
                return;
            }

            authors.forEach(author => {
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                li.textContent = author.name;

                const btnGroup = document.createElement('div');

                const editBtn = document.createElement('button');
                editBtn.className = 'btn btn-sm btn-warning me-2';
                editBtn.textContent = 'Editar';
                editBtn.onclick = () => startEditAuthor(author);

                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'btn btn-sm btn-danger';
                deleteBtn.textContent = 'Excluir';
                deleteBtn.onclick = () => deleteAuthor(author.id);

                btnGroup.appendChild(editBtn);
                btnGroup.appendChild(deleteBtn);

                li.appendChild(btnGroup);
                authorsList.appendChild(li);
            });
        }

        function startEditAuthor(author) {
            isEditing = true;
            editingId = author.id;
            nameInput.value = author.name;
            submitBtn.textContent = 'Atualizar';
            cancelEditBtn.classList.remove('d-none');
        }

        cancelEditBtn.addEventListener('click', resetForm);

        function resetForm() {
            isEditing = false;
            editingId = null;
            nameInput.value = '';
            submitBtn.textContent = 'Cadastrar';
            cancelEditBtn.classList.add('d-none');
        }

        function deleteAuthor(id) {
            if (!confirm('Deseja realmente excluir este autor?')) return;

            axios.delete(`${API_URL}/${id}`)
                .then(() => {
                    fetchAuthors();
                })
                .catch(error => {
                    const message = error.response?.data?.message || 'Erro ao excluir autor.';
                    showError(message);
                    console.error(error);
                });
        }

        authorForm.addEventListener('submit', function(e) {
            e.preventDefault();

            let nameValue = nameInput.value.trim();

            if (!nameValue) {
                showError('O nome é obrigatório!');
                return;
            }

            const isNumber = !isNaN(nameValue) && nameValue !== '';

            const name = isNumber ? Number(nameValue) : nameValue;


            if (isEditing) {
                axios.put(`${API_URL}/${editingId}`, {
                        name
                    })
                    .then(() => {
                        fetchAuthors();
                        resetForm();
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Erro ao excluir autor.';
                        showError(message);
                        console.error(error);
                    });
            } else {
                axios.post(API_URL, {
                        name
                    })
                    .then(() => {
                        fetchAuthors();
                        nameInput.value = '';
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Erro ao excluir autor.';
                        showError(message);
                        console.error(error);
                    });
            }
        });

        const nameError = document.getElementById('nameError');

        function showError(message) {
            nameError.textContent = message;
        }

        function clearError() {
            nameError.textContent = '';
        }

        fetchAuthors();
    </script>
</body>

</html>
