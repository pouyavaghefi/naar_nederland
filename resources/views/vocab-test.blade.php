@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center py-10">
        <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-2xl">
            <h1 class="text-2xl font-bold text-center mb-4 text-gray-800">🧠 Dutch Vocabulary Test</h1>
            <h2 class="text-xl font-bold text-center mb-6 text-gray-700">
                Lesson #{{ $words->first()->word_lesson_id ?? 'N/A' }}
            </h2>

            {{-- Switch mode buttons --}}
            <div class="flex justify-center gap-3 mb-6">
                <a href="{{ route('vocab.test', ['mode' => 'nl_to_en']) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ request('mode') === 'en_to_nl' ? 'bg-gray-200 text-gray-700' : 'bg-blue-600 text-white' }}">
                    🇳🇱 → 🇬🇧 Dutch → English
                </a>
                <a href="{{ route('vocab.test', ['mode' => 'en_to_nl']) }}"
                   class="px-4 py-2 rounded-lg font-medium {{ request('mode') === 'en_to_nl' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    🇬🇧 → 🇳🇱 English → Dutch
                </a>
            </div>

            @if(session('last_score'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-center">
                    ✅ Your Score:
                    {{ session('last_score.score') }}/{{ session('last_score.total') }}
                </div>
            @endif


            <form action="{{ route('vocab.test.check') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="mode" value="{{ request('mode', 'nl_to_en') }}">

                @foreach($words as $index => $word)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                        @if(request('mode') === 'en_to_nl')
                            {{-- English → Dutch --}}
                            <h2 class="text-lg font-semibold mb-2 text-gray-700">
                                #{{ $index + 1 }}: Translate into Dutch:
                                <span class="font-bold text-blue-600">{{ ucfirst($word->en_word) }}</span>
                            </h2>
                            <input
                                type="text"
                                name="answers[{{ $word->id }}]"
                                placeholder="Type the Dutch word..."
                                data-id="{{ $word->id }}"
                                data-mode="en_to_nl"
                                class="check-answer w-full mt-2 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        @else
                            {{-- Dutch → English --}}
                            <h2 class="text-lg font-semibold mb-2 text-gray-700">
                                #{{ $index + 1 }}: What does
                                <span class="font-bold text-blue-600">{{ $word->article }} {{ $word->nl_word }}</span>
                                mean?
                            </h2>
                            <input
                                type="text"
                                name="answers[{{ $word->id }}]"
                                placeholder="Type the English meaning..."
                                data-id="{{ $word->id }}"
                                data-mode="nl_to_en"
                                class="check-answer w-full mt-2 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        @endif

                        <div id="feedback-{{ $word->id }}" class="mt-2 text-sm"></div>
                    </div>
                @endforeach

                @if(session('last_score'))
                    <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-center">
                        ✅ Your Score:
                        {{ session('last_score.score') }}/{{ session('last_score.total') }}
                    </div>
                @endif


                <div class="text-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition">
                        Submit Answers
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
