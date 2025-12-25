<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>De / Het – Article Matching Exercise</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .draggable { transition: all 0.3s ease; }
        .dropzone { transition: all 0.3s ease; }
        .correct { background-color: #d1fae5 !important; border-color: #10b981 !important; }
        .incorrect { background-color: #fee2e2 !important; border-color: #ef4444 !important; }
        .speaker-icon { transition: all 0.2s ease; }
        .speaker-icon:hover { transform: scale(1.1); }
        .speaker-icon:active { transform: scale(0.95); }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-b from-indigo-50 to-white flex items-center justify-center py-10">

<div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-3xl">

    <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">
        🎯 Article Matching: "De" or "Het"?
    </h1>

    <p class="text-center text-gray-600 mb-4">
        Drag each Dutch word to the correct <b>article box</b>. Click the 🔊 icon to hear the pronunciation.
    </p>

    <!-- Score -->
    <div class="text-center mb-6">
        <div class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-semibold">
            Score: <span id="score">0</span>/<span id="total">0</span>
        </div>
        <div class="mt-2 text-sm text-gray-600">
            Page <span id="current-page">1</span> of <span id="total-pages">3</span>
        </div>
    </div>

    <!-- Article Boxes -->
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="article-box bg-blue-100 rounded-xl p-4 text-center" data-article="de">
            <h2 class="text-lg font-semibold text-blue-700 mb-2">de</h2>
            <div class="dropzone min-h-[200px] border-2 border-dashed border-blue-300 rounded-lg p-2"></div>
        </div>

        <div class="article-box bg-yellow-100 rounded-xl p-4 text-center" data-article="het">
            <h2 class="text-lg font-semibold text-yellow-700 mb-2">het</h2>
            <div class="dropzone min-h-[200px] border-2 border-dashed border-yellow-300 rounded-lg p-2"></div>
        </div>
    </div>

    <!-- Words Pool -->
    <div id="word-pool" class="flex flex-wrap justify-center gap-3 mb-6"></div>

    <!-- Action Buttons -->
    <div class="mt-6 text-center">
        <button id="checkAnswers"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold shadow-md transition">
            Check Answers
        </button>

        <button id="nextPage"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold shadow-md transition ml-3">
            Next Page
        </button>
    </div>

    <div id="result" class="text-center mt-4 text-lg font-semibold"></div>

    <!-- Voice Settings -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Pronunciation Settings</h3>
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="text-xs text-gray-600">Voice</label>
                <select id="voiceSelect" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm text-sm"></select>
            </div>

            <div>
                <label class="text-xs text-gray-600">Rate</label>
                <input id="rateSlider" type="range" min="0.5" max="2" step="0.1" value="1" class="block w-full mt-1">
                <span id="rateValue" class="text-xs text-gray-600">1.0</span>
            </div>

            <div>
                <label class="text-xs text-gray-600">Pitch</label>
                <input id="pitchSlider" type="range" min="0.5" max="2" step="0.1" value="1" class="block w-full mt-1">
                <span id="pitchValue" class="text-xs text-gray-600">1.0</span>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ---------------- WORDS LIST (DUTCH) ----------------
    const allWords = [
        { id: 1, nl_word: 'tafel', article: 'de' },
        { id: 2, nl_word: 'huis', article: 'het' },
        { id: 3, nl_word: 'fiets', article: 'de' },
        { id: 4, nl_word: 'boek', article: 'het' },
        { id: 5, nl_word: 'kind', article: 'het' },
        { id: 6, nl_word: 'vrouw', article: 'de' },
        { id: 7, nl_word: 'auto', article: 'de' },
        { id: 8, nl_word: 'stoel', article: 'de' },
        { id: 9, nl_word: 'bloem', article: 'de' },
        { id: 10, nl_word: 'water', article: 'het' },
        { id: 11, nl_word: 'kat', article: 'de' },
        { id: 12, nl_word: 'appel', article: 'de' },
        { id: 13, nl_word: 'glas', article: 'het' },
        { id: 14, nl_word: 'mes', article: 'het' },
        { id: 15, nl_word: 'deur', article: 'de' }
    ];

    const speechSettings = {
        synth: window.speechSynthesis,
        voices: [],
        selectedVoice: null,
        rate: 1,
        pitch: 1,
        isSpeaking: false
    };

    // Initialize TTS
    function initTextToSpeech() {
        function loadVoices() {
            speechSettings.voices = speechSettings.synth.getVoices();
            const voiceSelect = document.getElementById('voiceSelect');
            voiceSelect.innerHTML = '';

            const dutchVoices = speechSettings.voices.filter(v => v.lang.startsWith('nl'));
            const voicesToUse = dutchVoices.length ? dutchVoices : speechSettings.voices;

            voicesToUse.forEach((v, i) => {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = `${v.name} (${v.lang})`;
                voiceSelect.appendChild(opt);
            });

            if (dutchVoices.length) {
                speechSettings.selectedVoice = dutchVoices[0];
                voiceSelect.value = speechSettings.voices.indexOf(dutchVoices[0]);
            }

            voiceSelect.addEventListener('change', e => {
                const idx = parseInt(e.target.value);
                speechSettings.selectedVoice = speechSettings.voices[idx];
            });
        }

        if (speechSynthesis.onvoiceschanged !== undefined) {
            speechSynthesis.onvoiceschanged = loadVoices;
        }

        loadVoices();
    }

    function speakWord(word) {
        if (speechSettings.isSpeaking) speechSettings.synth.cancel();
        if (!speechSettings.selectedVoice) return;

        const utter = new SpeechSynthesisUtterance(word);
        utter.voice = speechSettings.selectedVoice;
        utter.rate = speechSettings.rate;
        utter.pitch = speechSettings.pitch;
        utter.lang = 'nl-NL';

        utter.onstart = () => speechSettings.isSpeaking = true;
        utter.onend = () => speechSettings.isSpeaking = false;
        utter.onerror = () => speechSettings.isSpeaking = false;

        speechSettings.synth.speak(utter);
    }

    // Initialize the game (words, drag/drop)
    const state = { currentPage:1, wordsPerPage:10, totalPages: Math.ceil(allWords.length/10), usedWordIds: new Set(), currentWords: [], score:0 };
    const wordPool = document.getElementById('word-pool');
    const dropzones = document.querySelectorAll('.dropzone');
    const scoreEl = document.getElementById('score');
    const totalEl = document.getElementById('total');
    const currentPageEl = document.getElementById('current-page');
    const totalPagesEl = document.getElementById('total-pages');
    const resultEl = document.getElementById('result');
    const checkBtn = document.getElementById('checkAnswers');
    const nextBtn = document.getElementById('nextPage');

    function loadWords() {
        state.currentWords = [];
        wordPool.innerHTML = '';

        const available = allWords.filter(w => !state.usedWordIds.has(w.id));
        const startIdx = (state.currentPage-1)*state.wordsPerPage;
        const endIdx = Math.min(startIdx+state.wordsPerPage, available.length);

        if (endIdx-startIdx < state.wordsPerPage) {
            state.usedWordIds.clear();
            state.currentPage = 1;
            loadWords();
            return;
        }

        const pageWords = available.slice(startIdx,endIdx);
        pageWords.forEach(w => state.usedWordIds.add(w.id));
        state.currentWords = pageWords;

        pageWords.forEach(word => {
            const container = document.createElement('div');
            container.className = 'flex items-center bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 cursor-move hover:bg-gray-200 transition-colors duration-200 draggable';
            container.draggable = true;
            container.dataset.id = `word-${word.id}`;
            container.dataset.article = word.article;

            const span = document.createElement('span');
            span.textContent = word.nl_word;

            const speaker = document.createElement('span');
            speaker.className = 'speaker-icon inline-block ml-2 text-blue-500 cursor-pointer';
            speaker.innerHTML = '🔊';
            speaker.title = 'Listen to pronunciation';
            speaker.addEventListener('click', e => { e.stopPropagation(); speakWord(word.nl_word); });

            container.appendChild(span);
            container.appendChild(speaker);
            wordPool.appendChild(container);
        });

        state.score = 0;
        updateScore();
        resultEl.innerHTML = '';
    }

    function updateScore() {
        scoreEl.textContent = state.score;
        totalEl.textContent = state.currentWords.length;
        currentPageEl.textContent = state.currentPage;
        totalPagesEl.textContent = state.totalPages;
    }

    // Drag & Drop
    function setupDragAndDrop() {
        const draggables = document.querySelectorAll('.draggable');

        dropzones.forEach(zone => {
            zone.innerHTML = '';
            const newZone = zone.cloneNode(false);
            zone.parentNode.replaceChild(newZone, zone);
        });

        const freshDropzones = document.querySelectorAll('.dropzone');

        draggables.forEach(el => {
            el.addEventListener('dragstart', e => { e.dataTransfer.setData('word-id', e.target.dataset.id); e.target.classList.add('opacity-50'); });
            el.addEventListener('dragend', e => e.target.classList.remove('opacity-50'));
        });

        freshDropzones.forEach(zone => {
            zone.addEventListener('dragover', e => e.preventDefault());
            zone.addEventListener('drop', e => {
                e.preventDefault();
                const id = e.dataTransfer.getData('word-id');
                const dragged = document.querySelector(`[data-id="${id}"]`);
                if (!dragged) return;

                const isPlaced = dragged.parentElement.classList.contains('dropzone');
                if (isPlaced && dragged.classList.contains('correct')) state.score--;

                zone.appendChild(dragged);

                const correctArticle = zone.parentElement.dataset.article;
                const wordArticle = dragged.dataset.article;
                dragged.classList.remove('correct','incorrect');

                if (wordArticle === correctArticle) { dragged.classList.add('correct'); state.score++; } 
                else { dragged.classList.add('incorrect'); }

                updateScore();
            });
        });
    }

    checkBtn.addEventListener('click', () => {
        let correct = 0;
        dropzones.forEach(zone => {
            const article = zone.parentElement.dataset.article;
            zone.querySelectorAll('[data-id]').forEach(el => { if(el.dataset.article===article) correct++; });
        });
        state.score = correct;
        updateScore();
        resultEl.innerHTML = `You got ${correct} out of ${state.currentWords.length} correct.`;
    });

    nextBtn.addEventListener('click', () => {
        if(state.currentPage < state.totalPages) { state.currentPage++; loadWords(); setupDragAndDrop(); }
        else { resultEl.innerHTML = `🏆 Exercise Complete! Final Score: ${state.score}/${allWords.length}`; nextBtn.disabled = true; }
    });

    // Initialize
    initTextToSpeech();
    loadWords();
    setupDragAndDrop();
});
</script>

</body>
</html>
