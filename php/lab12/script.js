'use strict';

// ---- Дані ----
// У вашому застосунку масив рецептів уже є (практикуми 6, 9, 11).
// Змініть STORAGE_KEY та назви полів під свою структуру.
const STORAGE_KEY = 'recipes';

// Демо-дані: записуються в localStorage ЛИШЕ якщо там порожньо,
// щоб файл можна було перевірити окремо. У справжньому проєкті видаліть.
const DEMO_RECIPES = [
  { id: 1, title: 'Паста карбонара',     time: 25, category: 'Італійська' },
  { id: 2, title: 'Омлет з овочами',     time: 10, category: 'Швидке' },
  { id: 3, title: 'Курка в вершковому соусі', time: 40, category: 'М\'ясо' },
  { id: 4, title: 'Борщ',                time: 90, category: 'Українська' },
  { id: 5, title: 'Салат Цезар',         time: 15, category: 'Салати' },
];

if (!localStorage.getItem(STORAGE_KEY)) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(DEMO_RECIPES));
}

function loadRecipes() {
  try {
    return JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '[]');
  } catch {
    return [];
  }
}

// ---- DOM ----
const btn   = document.querySelector('#generate-btn');
const icon  = document.querySelector('#shuffle-icon');
const card  = document.querySelector('#recipe-card');
const title = document.querySelector('#recipe-title');
const meta  = document.querySelector('#recipe-meta');

// ---- Стан ----
const SHUFFLE_MS = 1200; // тривалість «перемішування»
let isShuffling = false;
let lastKey = null;
let deckSignature = '';

// ---- Логіка ----
// Ключ для порівняння: те, що бачить користувач (назва), без урахування регістру й пробілів.
const recipeKey = r => String(r.title ?? '').trim().toLowerCase();

// «Колода»: усі рецепти випадають по одному разу в випадковому порядку,
// і лише коли колода порожня, її перемішують заново (як shuffle у плейлисті).
let deck = [];

function shuffle(array) {
  // Fisher–Yates
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
  return array;
}

function refillDeck(recipes) {
  deck = shuffle([...recipes]);
  // На стику кіл: перша страва нового кола не має збігатися з останньою зі старого
  if (deck.length > 1 && recipeKey(deck[deck.length - 1]) === lastKey) {
    const k = deck.findIndex(r => recipeKey(r) !== lastKey);
    if (k !== -1) [deck[deck.length - 1], deck[k]] = [deck[k], deck[deck.length - 1]];
  }
}

function pickRandomRecipe(recipes) {
  // Якщо набір рецептів змінився (додали/видалили) — збираємо колоду заново
  const signature = recipes.map(recipeKey).join('|');
  if (signature !== deckSignature) {
    deckSignature = signature;
    deck = [];
  }
  if (deck.length === 0) refillDeck(recipes);
  return deck.pop();
}

function renderRecipe(recipe) {
  title.textContent = recipe.title;
  meta.textContent = `${recipe.time} хв · ${recipe.category}`;
  card.hidden = false;

  // Перезапуск анімації появи: знімаємо клас, примусовий reflow, додаємо знову
  card.classList.remove('recipe-card--visible');
  void card.offsetWidth;
  card.classList.add('recipe-card--visible');
}

function startShuffle() {
  isShuffling = true;
  btn.disabled = true;
  card.hidden = true;
  icon.classList.add('shuffle-icon--spinning');
}

function stopShuffle() {
  isShuffling = false;
  btn.disabled = false;
  icon.classList.remove('shuffle-icon--spinning');
}

btn.addEventListener('click', () => {
  if (isShuffling) return;

  const recipes = loadRecipes();
  if (recipes.length === 0) {
    title.textContent = 'Додайте хоча б один рецепт';
    meta.textContent = '';
    card.hidden = false;
    return;
  }

  startShuffle();

  // Штучна затримка, щоб анімація була помітною.
  // Якщо рецепт береться з API — викликайте stopShuffle() після відповіді.
  setTimeout(() => {
    const recipe = pickRandomRecipe(recipes);
    lastKey = recipeKey(recipe);
    stopShuffle();
    renderRecipe(recipe);
  }, SHUFFLE_MS);
});