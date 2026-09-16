// ==> UserLogin
const loginform = document.getElementById('loginForm');

// ==> Section
const loginSection = document.getElementById('loginSection');
const dashboardSection = document.getElementById('dashboardSection');

dashboardSection.style.display = 'none';

// ==> LoginSubmit
loginform.addEventListener('submit', async(e) => {
    e.preventDefault();

    // Capturar e Enviar dados para a API
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;


    const response = await fetch('/api/login', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            username: username,
            password: password
        })
    });
    const data = await response.json();

    // Resposta da API
    const message = document.getElementById('message');

    message.textContent = data.message;

    if (response.ok) {
        loginSection.style.display = 'none';
        dashboardSection.style.display = 'block';

        loadcards();
    }
});

// ==> CardEdit

const editCardForm = document.getElementById('editCardForm');
editCardForm.style.display = 'none';

const cardEditForm = document.getElementById('cardEditForm');
const addCardButton = document.getElementById('addCardButton');

let modalMode = 'create';

const modalTitle = document.querySelector('#editCardForm h2');
const modalButton = document.querySelector('#cardEditForm button[type="submit"]');

// ==> Catalog
const gameButtons = document.querySelectorAll('#gameSelector button');
const catalogEdition = document.getElementById('catalogEdition');

let selectedGame = '';
let selectedEdition = '';

// ==> Load Editions
const editGame = document.getElementById('editGame');
const editEdition = document.getElementById('editEdition');
const editionId = document.getElementById('editionId');

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

    editEdition.innerHTML = `
        <option value="">
            Selecione uma edição
        </option>
    `;

    data.editions.forEach(edition => {
        const option = document.createElement('option');

        option.value = edition.id;
        option.textContent = edition.name;

        editEdition.appendChild(option);
    });

    editEdition.disabled = false;
}

// ==> Load Rarities
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

    editRarity.innerHTML = `
        <option value="">
            Selecione uma raridade
        </option>
    `;

    data.rarities.forEach(rarity => {
        const option = document.createElement('option');

        option.value = rarity.id;
        option.textContent = rarity.name;

        editRarity.appendChild(option);
    });

    editRarity.disabled = false;
}

// ==> Catalog Editions
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

    catalogEdition.innerHTML = `
        <option value="">
            Selecione uma edição
        </option>
    `;

    data.editions.forEach(edition => {
        const option = document.createElement('option');

        option.value = edition.id;
        option.textContent = edition.name;

        catalogEdition.appendChild(option);
    });

    catalogEdition.disabled = false;
}

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
    modalTitle.textContent = 'Adicionar carta';
    modalButton.textContent = 'Adicionar Carta';

    document.getElementById('editCardId').value = '';
    document.getElementById('editNameEn').value = '';
    document.getElementById('editNamePt').value = '';
    document.getElementById('editGame').value = '';
    document.getElementById('editEdition').value = '';

    editGame.disabled = false;
    editEdition.disabled = false;

    document.getElementById('editImage').value = '';

    editRarity.value = '';
    editRarity.disabled = true;

    editRarity.innerHTML = `
        <option value="">
            Selecione primeiro um jogo
        </option>
    `;
        editionId.textContent = '';

    editCardForm.style.display = 'block'
});


// ==> Close Edit(Modal)
const closeEditButton = document.getElementById('closeEditButton');
closeEditButton.addEventListener('click', () => {
    editCardForm.style.display = 'none';
});
        
// ==> Cancel Edit(Modal)

const cancelEditButton = document.getElementById('cancelEditButton');
cancelEditButton.addEventListener('click', () => {
    editCardForm.style.display = 'none';
});

// ==> Save Edit(Modal)
cardEditForm.addEventListener('submit', async(e) => {
    e.preventDefault();

    const id = document.getElementById('editCardId').value;
    const name_en = document.getElementById('editNameEn').value;
    const name_pt = document.getElementById('editNamePt').value;
    const game = document.getElementById('editGame').value;
    const edition = document.getElementById('editEdition').value;
    const image = document.getElementById('editImage').value;
    const rarity = document.getElementById('editRarity').value;

    let url = '/api/cards';
    let method = 'POST';

    if (modalMode === 'edit'){
        url = `/api/cards/${id}`;
        method = 'PUT';
    }

    const response = await fetch(url, {
        method: method,

        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            name_en: name_en,
            name_pt: name_pt,
            game: game,
            edition: edition,
            image: image,
            rarity: rarity
        })
    });

    const data = await response.json();
    console.log(data);

    if (response.ok) {
        editCardForm.style.display = 'none';
        loadcards();
    }

});

// ==> CardList
async function loadcards() {
    const response = await fetch('/api/cards');
    const data = await response.json();
    const cardsList = document.getElementById('cardsList');
    
    cardsList.innerHTML = '';

    // ==> Carregar Raridades
    if (!selectedGame || !selectedEdition) {
        return;
    }

    const rarityResponse = await fetch(
        `/api/${selectedGame}/rarities`
    );

    const rarityData = await rarityResponse.json();

    if (!rarityResponse.ok) {
        cardsList.innerHTML = `
            <p>Erro ao carregar raridades.</p>
        `;

        return;
    }

    // ==> Filtrar Cartas
    const filteredCards = data.cards.filter(card => {
        return card.game === selectedGame && card.edition === selectedEdition;
    });

    if (filteredCards.length === 0) {
        cardsList.innerHTML = `
            <p>Nenhuma carta encontrada.</p>
        `;

        return;
    }

    // ==> Renderizar Raridades
    rarityData.rarities.forEach(rarity => {
        const rarityCards = filteredCards.filter(card => {

            return card.rarity === rarity.id;
        });

        // ==> Não renderizar raridade vazia
        if (rarityCards.length === 0) {
            return;
        }

        const rarityBlock = document.createElement('div');
        rarityBlock.classList.add('rarityBlock');
        rarityBlock.innerHTML = `
            <h3>${rarity.name}</h3>
            <div class="rarityCards"></div>
        `;

        const rarityCardsContainer =
            rarityBlock.querySelector('.rarityCards');

        // ==> Renderizar Cartas
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

                document.getElementById('editImage').value = card.image ?? '';

                editCardForm.style.display = 'block';
            });

            // ==> CardDelete
            const deleteButton = cardElement.querySelector(
                `.delete-${card.id}`
            );

            deleteButton.addEventListener('click', async (e) => {
                e.stopPropagation();

                const response = await fetch( `/api/cards/${card.id}`, { 
                        method: 'DELETE'
                    });

                const data = await response.json();

                console.log(data);

                loadcards();
            });

            rarityCardsContainer.appendChild(cardElement);
        });

        cardsList.appendChild(rarityBlock);
    });
}