<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro de Tópico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">

    @include('NavBar')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4 text-center">Cadastro de Tópico</h4>

                        <form id="topicForm">
                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição</label>
                                <input type="text" id="description" class="form-control"
                                    placeholder="Digite a descrição do tópico" required />
                                <div id="descriptionError" class="text-danger mt-1"></div>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <button type="submit" class="btn btn-primary w-100" id="submitBtn">Cadastrar</button>
                                <button type="button" class="btn btn-secondary w-100 d-none"
                                    id="cancelEditBtn">Cancelar Edição</button>
                            </div>
                        </form>

                        <hr class="my-4" />

                        <h5 class="mb-3">Lista de Tópicos</h5>
                        <ul class="list-group" id="topicsList">

                        </ul>

                        <nav>
                            <ul class="pagination justify-content-center" id="pagination">
                            </ul>
                        </nav>


                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const API_URL = '/api/topics';

        const topicForm = document.getElementById('topicForm');
        const descriptionInput = document.getElementById('description');
        const descriptionError = document.getElementById('descriptionError');
        const submitBtn = document.getElementById('submitBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const topicsList = document.getElementById('topicsList');

        let isEditing = false;
        let editingId = null;

        function fetchTopics(page = 1) {
            axios.get(`${API_URL}?page=${page}`)
                .then(response => {
                    const topics = response.data.data.data;
                    const pagination = {
                        current_page: response.data.data.current_page,
                        last_page: response.data.data.last_page
                    };
                    renderTopics(topics);
                    renderPagination(pagination);
                })
                .catch(error => {
                    alert('Erro ao carregar tópicos.');
                    console.error(error);
                });
        }

        function renderPagination({
            current_page,
            last_page
        }) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${current_page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#">Anterior</a>`;
            prevLi.addEventListener('click', (e) => {
                e.preventDefault();
                if (current_page > 1) fetchTopics(current_page - 1);
            });
            pagination.appendChild(prevLi);

            for (let i = 1; i <= last_page; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === current_page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', (e) => {
                    e.preventDefault();
                    fetchTopics(i);
                });
                pagination.appendChild(li);
            }

            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${current_page === last_page ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#">Próximo</a>`;
            nextLi.addEventListener('click', (e) => {
                e.preventDefault();
                if (current_page < last_page) fetchTopics(current_page + 1);
            });
            pagination.appendChild(nextLi);
        }


        function renderTopics(topics) {
            topicsList.innerHTML = '';
            if (topics.length === 0) {
                topicsList.innerHTML = '<li class="list-group-item text-center">Nenhum tópico cadastrado.</li>';
                return;
            }
            topics.forEach(topic => {
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                li.textContent = topic.description;

                const btnGroup = document.createElement('div');

                const editBtn = document.createElement('button');
                editBtn.className = 'btn btn-sm btn-warning me-2';
                editBtn.textContent = 'Editar';
                editBtn.onclick = () => startEditTopic(topic);

                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'btn btn-sm btn-danger';
                deleteBtn.textContent = 'Excluir';
                deleteBtn.onclick = () => deleteTopic(topic.id);

                btnGroup.appendChild(editBtn);
                btnGroup.appendChild(deleteBtn);

                li.appendChild(btnGroup);
                topicsList.appendChild(li);
            });
        }

        function startEditTopic(topic) {
            isEditing = true;
            editingId = topic.id;
            descriptionInput.value = topic.description;
            clearError();
            submitBtn.textContent = 'Atualizar';
            cancelEditBtn.classList.remove('d-none');
        }

        cancelEditBtn.addEventListener('click', () => {
            resetForm();
        });

        function resetForm() {
            isEditing = false;
            editingId = null;
            descriptionInput.value = '';
            clearError();
            submitBtn.textContent = 'Cadastrar';
            cancelEditBtn.classList.add('d-none');
        }

        function deleteTopic(id) {
            if (!confirm('Deseja realmente excluir este tópico?')) return;

            axios.delete(`${API_URL}/${id}`)
                .then(() => {
                    fetchTopics();
                })
                .catch(error => {
                    alert('Erro ao excluir tópico.');
                    console.error(error);
                });
        }

        function showError(message) {
            descriptionError.textContent = message;
        }

        function clearError() {
            descriptionError.textContent = '';
        }

        topicForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearError();

            const descriptionValue = descriptionInput.value.trim();
            if (!descriptionValue) {
                showError('Descrição é obrigatória!');
                return;
            }

            const isNumber = !isNaN(descriptionValue) && descriptionValue !== '';

            const description = isNumber ? Number(descriptionValue) : descriptionValue;

            if (isEditing) {
                axios.put(`${API_URL}/${editingId}`, {
                        description
                    })
                    .then(() => {
                        fetchTopics();
                        resetForm();
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Erro ao atualizar tópico.';
                        showError(message);
                        console.error(error);
                    });
            } else {
                axios.post(API_URL, {
                        description
                    })
                    .then(() => {
                        fetchTopics();
                        descriptionInput.value = '';
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Erro ao cadastrar tópico.';
                        showError(message);
                        console.error(error);
                    });
            }
        });

        fetchTopics();
    </script>
</body>

</html>
