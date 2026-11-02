const results = document.getElementById('results');
const search = document.getElementById('search');
const form = document.getElementById('add-form');
const formMsg = document.getElementById('form-msg');

let requestId = 0; // щоб старіша відповідь не перезаписала новішу

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function showMessage(text) {
  results.innerHTML = `<tr><td colspan="4" class="msg">${escapeHtml(text)}</td></tr>`;
}

function render(teams) {
  if (teams.length === 0) {
    results.innerHTML = '<tr><td colspan="4">Нічого не знайдено</td></tr>';
    return;
  }
  // сервер уже відсортував за очками (DESC)
  results.innerHTML = teams.map((t, i) => `
    <tr>
      <td>${i + 1}</td>
      <td>${escapeHtml(t.name)}</td>
      <td>${escapeHtml(t.city)}</td>
      <td>${Number(t.points)}</td>
    </tr>`).join('');
}

async function loadTeams(q = '') {
  const myId = ++requestId;
  try {
    const response = await fetch('api_list.php?q=' + encodeURIComponent(q));
    if (!response.ok) {
      throw new Error('Сервер повернув помилку ' + response.status);
    }
    const data = await response.json();
    if (myId !== requestId) return;
    render(data);
  } catch (err) {
    if (myId !== requestId) return;
    showMessage('Не вдалося завантажити дані: ' + err.message);
  }
}

// Крок 3: завантаження при відкритті сторінки
loadTeams();

// Крок 4: живий пошук за містом
search.addEventListener('input', () => loadTeams(search.value.trim()));

// Кроки 5–6: додавання без перезавантаження
form.addEventListener('submit', async (event) => {
  event.preventDefault();
  formMsg.textContent = '';

  const payload = {
    name: form.name.value,
    city: form.city.value,
    points: Number(form.points.value),
  };

  try {
    const response = await fetch('api_add.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.error || 'Помилка ' + response.status);
    }
    form.reset();
    search.value = '';
    await loadTeams(); // перерахунок і сортування таблиці за очками
  } catch (err) {
    formMsg.textContent = 'Не вдалося додати: ' + err.message;
  }
});
