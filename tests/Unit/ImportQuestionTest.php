<?php
namespace Tests\Unit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Question;
use App\Models\Aspect;
use App\Models\Period;
use App\Models\QuestionnaireResponse;
use App\Models\ResponseAnswer;
use App\Http\Controllers\Admin\ResponseController;
use App\Services\NaiveBayesService;
use Illuminate\Http\Request;

class ImportQuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_automatically_creates_non_existent_questions()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        // Hapus semua pertanyaan untuk simulasi pertanyaan belum ada
        ResponseAnswer::query()->delete();
        QuestionnaireResponse::query()->delete();
        Question::query()->delete();

        $this->assertEquals(0, Question::count());

        $period = Period::firstOrCreate([
            'name' => 'Periode Uji Coba Auto-Import',
            'is_active' => true,
        ]);
        $controller = app(ResponseController::class);
        $nb = app(NaiveBayesService::class);

        $request = new Request(['period_id' => $period->id]);
        $controller->import($request, $nb);

        // Pertanyaan harus otomatis terisi dari header Excel
        $this->assertGreaterThan(0, Question::count());
        $this->assertEquals(10, Question::count());

        $this->assertDatabaseHas('questions', [
            'question_number' => 1,
            'type' => 'essay'
        ]);

        $this->assertDatabaseHas('questions', [
            'question_number' => 10,
            'type' => 'rating'
        ]);

        $this->assertGreaterThan(0, QuestionnaireResponse::count());
        $this->assertGreaterThan(0, ResponseAnswer::count());
    }
}
