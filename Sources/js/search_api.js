document.addEventListener('DOMContentLoaded', function() {
    const queryInput = document.getElementById('query');
    const categorySelect = document.getElementById('category');
    const resultsContainer = document.getElementById('results-container');

    let currentController = null;
    
    const performSearch = function() {
        const query = queryInput.value.trim();
        const category = categorySelect.value;
        
        if (currentController) {
            currentController.abort();
        }
        
        currentController = new AbortController();
        const signal = currentController.signal;
        
        const url = `api_search.php?query=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}&t=${Date.now()}`;
        
        fetch(url, { signal })
            .then(response => response.json())
            .then(data => {
                
                resultsContainer.innerHTML = '';

                if (data.length === 0) {
                    resultsContainer.innerHTML = '<p class="no-results">Aucun résultat trouvé</p>';
                } else {
                    const fragment = document.createDocumentFragment();
                    
                    data.forEach(petition => {
                        const card = document.createElement('div');
                        card.className = 'card';
                        card.innerHTML = `
                            <div class="cardheader">
                                <img src="../Resources/img/petition_selection/${petition.image_id}.jpg" alt="">
                            </div>
                            <div class="cardcontent">
                                <div class="left">
                                    <h3>${petition.title}</h3>
                                </div>
                                <div class="right">
                                    <a href="view_petition.php?id=${petition.id}">Découvrir</a>
                                </div>
                            </div>
                        `;
                        fragment.appendChild(card);
                    });
                    
                    resultsContainer.appendChild(fragment);
                }
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    console.error('Erreur lors de la recherche:', error);
                    resultsContainer.innerHTML = '<p class="error">Une erreur est survenue lors de la recherche</p>';
                }
            })
            .finally(() => {
                if (!signal.aborted) {
                    currentController = null;
                }
            });
    };
    
    queryInput.addEventListener('input', performSearch);
    categorySelect.addEventListener('change', performSearch);
    
    if (queryInput.value.trim() !== '' || categorySelect.value !== 'all') {
        performSearch();
    }
});