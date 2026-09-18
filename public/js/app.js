//=========================Model=========================\\
let modalMode = 'create';
let selectedGame = '';
let selectedEdition = '';
let registerMode = false;

//======================DOM Elements======================\\

// ==> UserLogin
const loginform = document.getElementById('loginForm');
const loginSection = document.getElementById('loginSection');

const dashboardSection = document.getElementById('dashboardSection');
const logoutButton = document.getElementById('logoutButton');

const loginTitle = document.getElementById('loginTitle');
const loginButton = document.getElementById('loginButton');
const toggleRegisterButton = document.getElementById('toggleRegisterButton');

// ==> CardEdit
const editCardForm = document.getElementById('editCardForm');

const cardEditForm = document.getElementById('cardEditForm');
const addCardButton = document.getElementById('addCardButton');

const modalTitle = document.querySelector('#editCardForm h2');
const modalButton = document.querySelector('#cardEditForm button[type="submit"]');

const editImage = document.getElementById('editImage');
const editImagePreview = document.getElementById('editImagePreview');
const imageUploadButton = document.getElementById('imageUploadButton');

// ==> Catalog
const gameButtons = document.querySelectorAll('#gameSelector button');
const catalogEdition = document.getElementById('catalogEdition');


// ==> Load Editions
const editGame = document.getElementById('editGame');
const editEdition = document.getElementById('editEdition');
const editionId = document.getElementById('editionId');

// ==> Modal
const closeEditButton = document.getElementById('closeEditButton');
const cancelEditButton = document.getElementById('cancelEditButton');

const cardFormMessage = document.getElementById('cardFormMessage');

//=========================View=========================\\

// ==>Login/Register
dashboardSection.style.display = 'none';
function renderAuthMode() {
    if (registerMode) {
        loginTitle.textContent = 'Cadastro';
        loginButton.textContent = 'Cadastrar';
        toggleRegisterButton.textContent = 'Já possui uma conta? Entrar';
    } else {
        loginTitle.textContent = 'Login';
        loginButton.textContent = 'Entrar';
        toggleRegisterButton.textContent = 'Não possui uma conta? Cadastrar';
    }
}

// ==> Modal
editCardForm.style.display = 'none';
function openCardModal() {
    editCardForm.style.display = 'flex';
}

function closeCardModal() {
    editCardForm.style.display = 'none';
}

// ==>Renderizar Edições
function renderEditions(selectElement, editions) {
    selectElement.innerHTML = `
        <option value="">
            Selecione uma edição
        </option>
    `;

    editions.forEach(edition => {
        const option = document.createElement('option');

        option.value = edition.id;
        option.textContent = edition.name;

        selectElement.appendChild(option);
    });
}

// ==> Renderizar Raridades
function renderRarities(selectElement, rarities) {
    selectElement.innerHTML = `
        <option value="">
            Selecione uma raridade
        </option>
    `;

    rarities.forEach(rarity => {
        const option = document.createElement('option');

        option.value = rarity.id;
        option.textContent = rarity.name;

        selectElement.appendChild(option);
    });
}

// ==> Renderizar as Cartas
function renderCards(cards, rarities, cardsList) {
    rarities.forEach(rarity => {
        const rarityCards = cards.filter(card => {
            return card.rarity === rarity.id;
        });

        // ==> Caso não possua
        if (rarityCards.length === 0) {
            return;
        }

        const rarityBlock = document.createElement('div');

        rarityBlock.classList.add('rarityBlock');
        rarityBlock.innerHTML = `
            <h3>${rarity.name}</h3>
            <div class="rarityCards"></div>
        `;

        const rarityCardsContainer = rarityBlock.querySelector('.rarityCards');

        // ==> Caso possua
        rarityCards.forEach(card => {
            const cardElement = document.createElement('div');
            cardElement.innerHTML = `
                <div class="cardImage">
                    <img src="${card.image}" alt="${card.name_en}">
                    <div class="cardOverlay">
                        <button
                            type="button"
                            class="delete-${card.id}">
                            X
                        </button>
                    </div>
                </div>
                <div class="cardInfo">
                    <h4>${card.name_en}</h4>
                    <p>${card.name_pt ?? 'Não informado'}</p>
                </div>
            `;

            // ==> Open Edit(Modal)
            cardElement.addEventListener('click', () => {
                modalMode = 'edit';

                modalTitle.textContent = 'Editar Carta';
                modalButton.textContent = 'Salvar alterações';

                document.getElementById('editCardId').value = card.id;
                document.getElementById('editNameEn').value = card.name_en;
                document.getElementById('editNamePt').value = card.name_pt ?? '';

                editGame.value = card.game;
                editGame.disabled = true;

                loadEditions(card.game).then(() => {
                    editEdition.value = card.edition;
                    editionId.textContent = card.edition;
                    editEdition.disabled = true;
                });

                loadRarities(card.game).then(() => {
                    editRarity.value = card.rarity ?? '';
                    editRarity.disabled = true;
                });

                editImage.value = '';
                editImagePreview.src = card.image ?? '';

                openCardModal();
            });

            // ==> CardDelete
            const deleteButton = cardElement.querySelector(
                `.delete-${card.id}`
            );

            deleteButton.addEventListener('click', async (e) => {
                e.stopPropagation();

                const { response, data } = await deleteCard(card.id);

                if (response.ok) {
                    loadcards();
                }
            });

            rarityCardsContainer.appendChild(cardElement);

            });

        cardsList.appendChild(rarityBlock);
    });
}

//========================Controler========================\\
// ==> Login
async function loginUser(username, password) {
    const response = await fetch('/api/login', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},

        body: JSON.stringify({
            username: username,
            password: password
        })
    });
    const data = await response.json();

    return {
        response: response,
        data: data
    };
}

// ==> Logout
async function logoutUser() {
    const response = await fetch('/api/logout', {
        method: 'POST'
    });

    const data = await response.json();

    return {
        response: response,
        data: data
    };
}

// ==> Register
async function registerUser(username, password) {
    const response = await fetch('/api/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            username: username,
            password: password
        })
    });

    const data = await response.json();

    return {
        response: response,
        data: data
    };
}

// ==> Carregar Edições
async function loadEditions(game) {
    editEdition.innerHTML = '';
    editionId.textContent = '';

    if (!game) {
        editEdition.disabled = true;
        editEdition.innerHTML = `
            <option value="">
                Selecione primeiro um jogo
            </option>
        `;

        return;
    }

    editEdition.disabled = true;
    editEdition.innerHTML = `
        <option value="">
            Carregando edições...
        </option>
    `;

    // ==> Buscando as edições na API
    const response = await fetch(`/api/${game}/editions`);
    const data = await response.json();

    if (!response.ok) {
        editEdition.innerHTML = `
            <option value="">
                Erro ao carregar edições
            </option>
        `;

        return;
    }
    renderEditions(editEdition, data.editions);

    editEdition.disabled = false;
}

// ==> Carregar Raridades
const editRarity = document.getElementById('editRarity');

async function loadRarities(game) {
    editRarity.innerHTML = '';

    if (!game) {
        editRarity.disabled = true;
        editRarity.innerHTML = `
            <option value="">
                Selecione primeiro um jogo
            </option>
        `;

        return;
    }

    editRarity.disabled = true;
    editRarity.innerHTML = `
        <option value="">
            Carregando raridades...
        </option>
    `;

    // ==> Buscando as raridades na API
    const response = await fetch(`/api/${game}/rarities`);
    const data = await response.json();

    if (!response.ok) {
        editRarity.innerHTML = `
            <option value="">
                Erro ao carregar raridades
            </option>
        `;

        return;
    }
    renderRarities(editRarity, data.rarities);

    editRarity.disabled = false;
}

// ==> Catalogar Edições
async function loadCatalogEditions(game) {
    catalogEdition.innerHTML = '';

    if (!game) {
        catalogEdition.disabled = true;
        catalogEdition.innerHTML = `
            <option value="">
                Selecione primeiro um jogo
            </option>
        `;

        return;
    }

    catalogEdition.disabled = true;
    catalogEdition.innerHTML = `
        <option value="">
            Carregando edições...
        </option>
    `;

    // ==> Buscando as edições na API
    const response = await fetch(`/api/${game}/editions`);
    const data = await response.json();

    if (!response.ok) {

        catalogEdition.innerHTML = `
            <option value="">
                Erro ao carregar edições
            </option>
        `;

        return;
    }
    renderEditions(catalogEdition, data.editions);

    catalogEdition.disabled = false;
}


// ==> Salvar Card
async function saveCard(id, formData) {
    let url = '/api/cards';
    let method = 'POST';

    const headers = {};

    if (modalMode === 'edit') {
        url = `/api/cards/${id}`;
        headers['X-HTTP-Method-Override'] = 'PUT';
    }

    const response = await fetch(url, {
        method: method,
        headers: headers,

        body: formData
    });
    const data = await response.json();

    return {
        response: response,
        data: data
    };
}

// ==> Deletar Card
async function deleteCard(id) {
    const response = await fetch(`/api/cards/${id}`, {
        method: 'DELETE'
    });
    const data = await response.json();

    return {
        response: response,
        data: data
    };
}

// ==> Receber Cards
async function getCards() {
    const response = await fetch('/api/cards');

    const data = await response.json();

    return {
        response: response,
        data: data
    };
}

// ==> Carregar Raridades
async function getRarities(game) {
    const response = await fetch(
        `/api/${game}/rarities`
    );

    const data = await response.json();
    return {
        response: response,
        data: data
    };
}

// ==> Carregar Cartas
async function loadcards() {
    const { response, data } = await getCards();

    const cardsList = document.getElementById('cardsList');
    cardsList.innerHTML = '';

    if (!response.ok) {
        cardsList.innerHTML = `
            <p>${data.message}</p>
        `;
        return;
    }

    // ==> Carregar Raridades
    if (!selectedGame || !selectedEdition) {
        return;
    }

    const { response: rarityResponse, data: rarityData } =
        await getRarities(selectedGame);

    if (!rarityResponse.ok) {
        cardsList.innerHTML = `
            <p>Erro ao carregar raridades.</p>
        `;

        return;
    }

    // ==> Filtrar Cartas
    const filteredCards = data.cards.filter(card => {
        return card.game === selectedGame &&
               card.edition === selectedEdition;
    });

    if (filteredCards.length === 0) {
        cardsList.innerHTML = `
            <p>Nenhuma carta encontrada.</p>
        `;

        return;
    }

    // ==> Renderizar Cartas
    renderCards(
        filteredCards,
        rarityData.rarities,
        cardsList
    );
}

//========================Events========================\\

// ==> Login
loginform.addEventListener('submit', async(e) => {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    let response;
    let data;

    if (registerMode) {
        const result = await registerUser(
            username,
            password
        );

        response = result.response;
        data = result.data;
    } else {
        const result = await loginUser(
            username,
            password
        );

        response = result.response;
        data = result.data;
    }

    const message = document.getElementById('message');

    message.textContent = data.message;
    if (response.ok) {
        if (registerMode) {
            message.textContent = data.message;

            registerMode = false;

            renderAuthMode();
            loginform.reset();

            return;
        }

        loginSection.style.display = 'none';
        dashboardSection.style.display = 'block';

        loadcards();

    }
});

// ==> Logout
logoutButton.addEventListener('click', async() => {
    const { response, data } = await logoutUser();

    if (!response.ok) {
        console.log(data.message);

        return;
    }

    loginSection.style.display = 'block';
    dashboardSection.style.display = 'none';
});

// ==> Register
toggleRegisterButton.addEventListener('click', () => {
    registerMode = !registerMode;

    renderAuthMode();
});

// ==> GameSelector
gameButtons.forEach(button => {
    button.addEventListener('click', () => {
        selectedGame = button.dataset.game;

        selectedEdition = '';
        catalogEdition.value = '';

        gameButtons.forEach(gameButton => {
            gameButton.classList.remove('selected');
        });
        
        button.classList.add('selected');

        document.getElementById('cardsList').innerHTML = `
        <p>Selecione a Edição.</p>
        `;

        loadCatalogEditions(selectedGame);
    });

});

// ==> EditionSelector
catalogEdition.addEventListener('change', () => {
    selectedEdition = catalogEdition.value;

    if (!selectedEdition) {
        document.getElementById('cardsList').innerHTML = '';

        return;
    }

    loadcards();
});

// ==> Carregar e Inserir Imagem
imageUploadButton.addEventListener('click', () => {
    editImage.click();
});

editImage.addEventListener('change', () => {
    const file = editImage.files[0];

    if (!file) {
        return;
    }

    editImagePreview.src = URL.createObjectURL(file);
});

// ==> Renderizando Jogos
editGame.addEventListener('change', () => {
    loadEditions(editGame.value);
    loadRarities(editGame.value);
});

// ==> Renderizando ID da Edição
editEdition.addEventListener('change', () => {
    editionId.textContent = editEdition.value;
});

// ==> Open Insert(Modal)
addCardButton.addEventListener('click', () => {
    modalMode = 'create';
    cardFormMessage.textContent = '';

    modalTitle.textContent = 'Adicionar carta';
    modalButton.textContent = 'Adicionar Carta';

    document.getElementById('editCardId').value = '';
    document.getElementById('editNameEn').value = '';
    document.getElementById('editNamePt').value = '';
    document.getElementById('editGame').value = '';
    document.getElementById('editEdition').value = '';

    editGame.disabled = false;
    editEdition.disabled = false;

    editImage.value = '';
    editImagePreview.src = '';

    editRarity.value = '';
    editRarity.disabled = true;

    editRarity.innerHTML = `
        <option value="">
            Selecione primeiro um jogo
        </option>
    `;
        editionId.textContent = '';

    openCardModal();
});

// ==> Close Edit(Modal)
closeEditButton.addEventListener('click', () => {
    closeCardModal();
});
        
// ==> Cancel Edit(Modal)
cancelEditButton.addEventListener('click', () => {
    closeCardModal();
});

// ==> Save Edit(Modal)
cardEditForm.addEventListener('submit', async(e) => {
    e.preventDefault();

    const id = document.getElementById('editCardId').value;
    const name_en = document.getElementById('editNameEn').value;
    const name_pt = document.getElementById('editNamePt').value;
    const game = document.getElementById('editGame').value;
    const edition = document.getElementById('editEdition').value;
    const rarity = document.getElementById('editRarity').value;

    const image = editImage.files[0];

    const formData = new FormData();

    formData.append('name_en', name_en);
    formData.append('name_pt', name_pt);
    formData.append('game', game);
    formData.append('edition', edition);
    formData.append('rarity', rarity);

    if (image) {
        formData.append('image', image);
    }

    const { response, data } = await saveCard(id, formData);
    
    if (!response.ok) {
        cardFormMessage.textContent = data.message;

        return;
    }

    closeCardModal();

    loadcards();
});