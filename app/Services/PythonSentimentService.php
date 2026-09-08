<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PythonSentimentService
{
    protected string $pythonPath;
    protected string $scriptPath;
    protected string $modelPath;

    public function __construct()
    {
        $defaultPython = 'C:\\Users\\user\\AppData\\Local\\Programs\\Python\\Python312\\python.exe';
        $this->pythonPath = file_exists($defaultPython) ? $defaultPython : 'python';
        $this->scriptPath = base_path('python_sentiment.py');
        $this->modelPath = base_path('model_tfidf_naive_bayes.pkl');
    }

    public function predict(string $text): ?array
    {
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $cmd = escapeshellcmd($this->pythonPath) . ' ' .
               escapeshellarg($this->scriptPath) . ' single ' .
               escapeshellarg($this->modelPath) . ' ' .
               escapeshellarg($text);

        $process = proc_open($cmd, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            Log::error('Gagal menjalankan proses Python Sentiment');
            return null;
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        proc_close($process);

        if (!empty($stdout)) {
            $json = json_decode(trim($stdout), true);
            if (is_array($json) && isset($json['sentiment'])) {
                return $json;
            }
        }

        Log::warning('Python sentiment output invalid: ' . $stdout . ' Error: ' . $stderr);
        return null;
    }

    public function predictBatch(array $items): array
    {
        if (empty($items)) return [];

        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $cmd = escapeshellcmd($this->pythonPath) . ' ' .
               escapeshellarg($this->scriptPath) . ' batch ' .
               escapeshellarg($this->modelPath);

        $process = proc_open($cmd, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            Log::error('Gagal menjalankan proses Python Batch Sentiment');
            return [];
        }

        fwrite($pipes[0], json_encode($items));
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        proc_close($process);

        if (!empty($stdout)) {
            $json = json_decode(trim($stdout), true);
            if (is_array($json)) {
                return $json;
            }
        }

        Log::error('Python batch sentiment error: ' . $stderr);
        return [];
    }
}
