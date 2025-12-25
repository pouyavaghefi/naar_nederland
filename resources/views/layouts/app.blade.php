<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Vocab Test') }}</title>
    @vite('resources/css/app.css') {{-- TailwindCSS --}}
</head>
<body class="antialiased bg-gray-100 text-gray-900">
<div class="min-h-screen flex flex-col">
    <nav class="bg-blue-600 text-white px-6 py-3 shadow">
        <div class="max-w-6xl mx-auto flex justify-between">
            <a href="/" class="font-bold text-lg">🇩🇪 Vocab Trainer</a>
            <div>
                <a href="{{ route('vocab.test') }}" class="hover:underline">Test</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-gray-200 text-center py-3 text-sm text-gray-600">
        © {{ date('Y') }} Vocab Trainer
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.check-answer').forEach(input => {
            input.addEventListener('blur', function () {
                const id = this.dataset.id;
                const mode = this.dataset.mode;
                const answer = this.value.trim();
                const feedback = document.getElementById(`feedback-${id}`);

                if (answer === '') {
                    feedback.innerHTML = '';
                    return;
                }

                fetch("{{ route('vocab.check.single') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id, answer, mode })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.correct) {
                            feedback.innerHTML = `<span class="text-green-600 font-medium">✅ Correct!</span>`;
                        } else {
                            feedback.innerHTML = `<span class="text-red-600 font-medium">❌ Correct answer: ${data.correct_answer}</span>`;
                        }
                    })
                    .catch(() => {
                        feedback.innerHTML = `<span class="text-gray-500">⚠️ Error checking answer</span>`;
                    });
            });
        });
    });
</script>

</body>
</html>
