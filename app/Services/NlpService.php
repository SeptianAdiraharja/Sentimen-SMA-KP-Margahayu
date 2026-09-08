<?php

namespace App\Services;

use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class NlpService
{
    protected $stemmer;
    protected $stopwordRemover;
    protected $negationWords = ['tidak', 'bukan', 'kurang', 'belum', 'jangan', 'tanpa', 'tak', 'ga', 'gak', 'nggak', 'bkn', 'tdk'];

    public function __construct()
    {
        $stemmerFactory = new StemmerFactory();
        $this->stemmer = $stemmerFactory->createStemmer();

        $stopwordFactory = new StopWordRemoverFactory();
        $this->stopwordRemover = $stopwordFactory->createStopWordRemover();
    }

    /**
     * Jalankan tahapan preprocessing:
     * 1. Case folding
     * 2. Cleaning (hapus url, karakter non-huruf, spasi berlebih)
     * 3. Tokenizing
     * 4. Stopword removal (dengan menjaga negasi)
     * 5. Stemming (Sastrawi)
     */
    public function preprocess(string $text): array
    {
        // 1. Case Folding
        $caseFolded = mb_strtolower(trim($text), 'UTF-8');

        // 2. Cleaning
        $cleaned = preg_replace('/https?:\/\/\S+|www\.\S+/', ' ', $caseFolded);
        $cleaned = preg_replace('/[^a-z\s]/', ' ', $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);

        // 3. Tokenizing
        $tokens = empty($cleaned) ? [] : explode(' ', $cleaned);

        // 4. Stopword Removal
        $filteredTokens = [];
        foreach ($tokens as $token) {
            if (strlen($token) <= 1) {
                continue;
            }
            if (in_array($token, $this->negationWords)) {
                $filteredTokens[] = $token;
                continue;
            }
            $testRemover = $this->stopwordRemover->remove($token);
            if (!empty(trim($testRemover))) {
                $filteredTokens[] = $token;
            }
        }

        // 5. Stemming
        $stemmedTokens = [];
        foreach ($filteredTokens as $t) {
            if (in_array($t, $this->negationWords)) {
                $stemmedTokens[] = $t;
            } else {
                $stemmed = $this->stemmer->stem($t);
                $stemmedTokens[] = !empty($stemmed) ? $stemmed : $t;
            }
        }

        $stemmedText = implode(' ', $stemmedTokens);

        return [
            'raw' => $text,
            'clean' => $cleaned,
            'tokens' => $tokens,
            'filtered_tokens' => $filteredTokens,
            'stemmed_tokens' => $stemmedTokens,
            'stemmed_text' => $stemmedText,
        ];
    }
}