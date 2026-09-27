<?php
/**
 * Практична робота №2
 * Варіант 20: Спортивна ліга (турнірна таблиця, матчі, команди)
 *
 * Форма: результат матчу - teamHome, teamAway, scoreHome, scoreAway.
 * Валідація:
 *   - scoreHome, scoreAway - цілі числа, не менше 0;
 *   - teamHome не повинна збігатися з teamAway.
 * localStorage: запам'ятовування останнього обраного туру/ліги у фільтрі результатів.
 */

$errors = [];
$data = [
    'teamHome'  => '',
    'teamAway'  => '',
    'scoreHome' => '',
    'scoreAway' => '',
];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Отримуємо дані форми (обрізаємо зайві пробіли для текстових полів)
    $data['teamHome']  = trim($_POST['teamHome'] ?? '');
    $data['teamAway']  = trim($_POST['teamAway'] ?? '');
    $data['scoreHome'] = trim($_POST['scoreHome'] ?? '');
    $data['scoreAway'] = trim($_POST['scoreAway'] ?? '');

    // --- Валідація teamHome ---
    if ($data['teamHome'] === '') {
        $errors['teamHome'] = 'Назва команди господарів обов\'язкова';
    }

    // --- Валідація teamAway ---
    if ($data['teamAway'] === '') {
        $errors['teamAway'] = 'Назва команди гостей обов\'язкова';
    }

    // --- teamHome не повинна збігатися з teamAway ---
    if ($data['teamHome'] !== '' && $data['teamAway'] !== '' &&
        mb_strtolower($data['teamHome']) === mb_strtolower($data['teamAway'])) {
        $errors['teamAway'] = 'Команда гостей не може збігатися з командою господарів';
    }

    // --- Валідація scoreHome: ціле число, не менше 0 ---
    if ($data['scoreHome'] === '' ||
        !preg_match('/^\d+$/', $data['scoreHome'])) {
        $errors['scoreHome'] = 'Рахунок господарів має бути цілим числом не менше 0';
    }

    // --- Валідація scoreAway: ціле число, не менше 0 ---
    if ($data['scoreAway'] === '' ||
        !preg_match('/^\d+$/', $data['scoreAway'])) {
        $errors['scoreAway'] = 'Рахунок гостей має бути цілим числом не менше 0';
    }

    if (empty($errors)) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<title>Спортивна ліга - Внесення результату матчу</title>
<style>
    body { font-family: Arial, sans-serif; max-width: 640px; margin: 40px auto; padding: 0 16px; }
    h1 { font-size: 22px; }
    fieldset { margin-bottom: 24px; padding: 16px; border-radius: 8px; }
    label { display: block; margin-top: 12px; font-weight: bold; }
    input, select { width: 100%; padding: 6px; margin-top: 4px; box-sizing: border-box; }
    .error { color: #c0392b; font-size: 0.9em; margin-top: 4px; }
    .success { background: #eafaf1; border: 1px solid #2ecc71; padding: 12px; border-radius: 6px; }
    .score-row { display: flex; gap: 16px; }
    .score-row > div { flex: 1; }
    button { margin-top: 16px; padding: 8px 20px; cursor: pointer; }
    #js-error { color: #c0392b; font-weight: bold; display: none; margin-top: 8px; }
</style>
</head>
<body>

<h1>Внесення результату матчу</h1>

<?php if ($success): ?>
    <div class="success">
        <strong>Результат прийнято:</strong><br>
        <?= htmlspecialchars($data['teamHome']) ?> <?= (int)$data['scoreHome'] ?>
        &nbsp;-&nbsp;
        <?= (int)$data['scoreAway'] ?> <?= htmlspecialchars($data['teamAway']) ?>
    </div>
<?php endif; ?>

<!-- Фільтр результатів: обраний тур/ліга запам'ятовується через localStorage -->
<fieldset style="border: 1px solid #3498db;">
    <legend>Фільтр результатів</legend>
    <label for="tourFilter">Тур / ліга</label>
    <select id="tourFilter">
        <option value="">-- Усі тури --</option>
        <option value="premier-1">Прем'єр-ліга, Тур 1</option>
        <option value="premier-2">Прем'єр-ліга, Тур 2</option>
        <option value="premier-3">Прем'єр-ліга, Тур 3</option>
        <option value="cup-1">Кубок, 1/8 фіналу</option>
        <option value="cup-2">Кубок, 1/4 фіналу</option>
    </select>
    <p style="font-size: 0.85em; color: #555;">
        Обраний пункт зберігається в localStorage і автоматично відновлюється
        після перезавантаження сторінки.
    </p>
</fieldset>

<!-- Форма внесення результату матчу -->
<form method="post" action="form.php" id="matchForm" novalidate>
    <fieldset style="border: 1px solid #ccc;">
        <legend>Результат матчу</legend>

        <label for="teamHome">Команда господарів</label>
        <input type="text" id="teamHome" name="teamHome" required minlength="2"
               value="<?= htmlspecialchars($data['teamHome']) ?>">
        <?php if (isset($errors['teamHome'])): ?>
            <div class="error"><?= htmlspecialchars($errors['teamHome']) ?></div>
        <?php endif; ?>

        <label for="teamAway">Команда гостей</label>
        <input type="text" id="teamAway" name="teamAway" required minlength="2"
               value="<?= htmlspecialchars($data['teamAway']) ?>">
        <?php if (isset($errors['teamAway'])): ?>
            <div class="error"><?= htmlspecialchars($errors['teamAway']) ?></div>
        <?php endif; ?>

        <div class="score-row">
            <div>
                <label for="scoreHome">Рахунок господарів</label>
                <input type="number" id="scoreHome" name="scoreHome" required min="0" step="1"
                       value="<?= htmlspecialchars($data['scoreHome']) ?>">
                <?php if (isset($errors['scoreHome'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['scoreHome']) ?></div>
                <?php endif; ?>
            </div>
            <div>
                <label for="scoreAway">Рахунок гостей</label>
                <input type="number" id="scoreAway" name="scoreAway" required min="0" step="1"
                       value="<?= htmlspecialchars($data['scoreAway']) ?>">
                <?php if (isset($errors['scoreAway'])): ?>
                    <div class="error"><?= htmlspecialchars($errors['scoreAway']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div id="js-error"></div>

        <button type="submit">Внести результат</button>
    </fieldset>
</form>

<script>
// ------------------------------------------------------------------
// Крок 5. Клієнтська JavaScript-валідація
// ------------------------------------------------------------------
const form = document.getElementById('matchForm');
const jsError = document.getElementById('js-error');

form.addEventListener('submit', (event) => {
    const teamHome = document.getElementById('teamHome').value.trim();
    const teamAway = document.getElementById('teamAway').value.trim();

    jsError.style.display = 'none';
    jsError.textContent = '';

    // Перевірка, що команда гостей не збігається з командою господарів
    if (teamHome !== '' && teamAway !== '' &&
        teamHome.toLowerCase() === teamAway.toLowerCase()) {
        event.preventDefault();
        jsError.textContent = 'Команда гостей не може збігатися з командою господарів.';
        jsError.style.display = 'block';
        return;
    }

    const scoreHome = document.getElementById('scoreHome').value;
    const scoreAway = document.getElementById('scoreAway').value;

    // Перевірка, що рахунок - невід'ємне ціле число
    if (scoreHome === '' || Number(scoreHome) < 0 ||
        scoreAway === '' || Number(scoreAway) < 0) {
        event.preventDefault();
        jsError.textContent = 'Рахунок має бути цілим числом не менше 0.';
        jsError.style.display = 'block';
    }
});

// ------------------------------------------------------------------
// Крок 6. localStorage: запам'ятовування останнього обраного туру/ліги
// ------------------------------------------------------------------
const tourFilter = document.getElementById('tourFilter');

// Відновлення значення одразу після завантаження сторінки
window.addEventListener('DOMContentLoaded', () => {
    const savedTour = localStorage.getItem('selectedTour');
    if (savedTour !== null) {
        tourFilter.value = savedTour;
    }
});

// Збереження нового вибору при зміні фільтра
tourFilter.addEventListener('change', () => {
    localStorage.setItem('selectedTour', tourFilter.value);
});
</script>

</body>
</html>
