<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonWord;

class VocabController extends Controller
{
    public function index(Request $request)
    {
//        session()->forget([
//            'used_words',
//            'last_score',
//        ]);

        $mode = $request->get('mode', 'nl_to_en');

        // Get already used word IDs from session
        $used = session()->get('used_words', []);

        // Get 10 new words NOT in the used list
        $words = LessonWord::whereNotIn('id', $used)
            ->inRandomOrder()
            ->take(10)
            ->get();

        // If not enough words available, reset the session
        if ($words->count() < 10) {
            session()->forget('used_words'); // reset
            $used = [];
            $words = LessonWord::inRandomOrder()->take(10)->get();
        }

        // Add the newly shown words to the session list
        $newUsed = array_merge($used, $words->pluck('id')->toArray());
        session()->put('used_words', $newUsed);

        return view('vocab-test', compact('words', 'mode'));
    }


    public function check(Request $request)
    {
        $score = 0;
        $mode = $request->get('mode', 'nl_to_en');
        $answers = $request->input('answers', []);
        $total = count($answers);

        foreach ($answers as $id => $answer) {
            $word = LessonWord::find($id);
            if (! $word) continue;

            $userAnswer = strtolower(trim($answer));

            if ($mode === 'en_to_nl') {
                $valid = [
                    strtolower($word->nl_word),
                    strtolower(trim($word->article.' '.$word->nl_word)),
                ];
            } else {
                $valid = [ strtolower($word->en_word) ];
            }

            if (in_array($userAnswer, $valid)) {
                $score++;
            }
        }

        session()->put('last_score', [
            'score' => $score,
            'total' => $total,
            'mode'  => $mode,
        ]);

        // Redirect back to test page
        return redirect()->route('vocab.test');
    }



    // De/Het article matching game
    public function articleMatch($lessonId)
    {
        $words = LessonWord::where('word_lesson_id', $lessonId)
            ->inRandomOrder()
            ->take(10)
            ->get();

        return view('vocab-article-match', compact('words'));
    }

    public function checkSingle(Request $request)
    {
        $word = LessonWord::findOrFail($request->id);

        $correct = $request->mode === 'en_to_nl'
            ? strtolower($request->answer) === strtolower($word->nl_word)
            : str_contains(
                strtolower($word->en_word),
                strtolower($request->answer)
            );

        return response()->json([
            'correct' => $correct,
            'correct_answer' => $request->mode === 'en_to_nl'
                ? $word->nl_word
                : $word->en_word
        ]);
    }
}
