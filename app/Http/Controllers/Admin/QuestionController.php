<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Question;
use App\Models\Aspect;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('aspect')->orderBy('question_number')->get();
        $aspects = Aspect::orderBy('sort_order')->get();

        return view('admin.questions.index', compact('questions', 'aspects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'aspect_id' => 'nullable|exists:aspects,id',
            'question_number' => 'required|integer',
            'question_text' => 'required|string',
            'type' => 'required|in:essay,rating',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Question::create($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan kuesioner berhasil ditambahkan.');
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'aspect_id' => 'nullable|exists:aspects,id',
            'question_number' => 'required|integer',
            'question_text' => 'required|string',
            'type' => 'required|in:essay,rating',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $question->update($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan kuesioner berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions.index')->with('success', 'Pertanyaan kuesioner berhasil dihapus.');
    }
}
