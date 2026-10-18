import sys
import os
import json
import re
import warnings
warnings.filterwarnings('ignore')

import joblib
from sklearn.feature_extraction.text import TfidfVectorizer
from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

# Inisialisasi Stopword & Stemmer
stopwords = set(StopWordRemoverFactory().get_stop_words()) - {'tidak', 'bukan', 'jangan', 'belum', 'kurang'}
stemmer = StemmerFactory().create_stemmer()


def cleaning(text):
    text = str(text)
    text = re.sub(r'http\S+|www\S+', ' ', text)
    text = re.sub(r'@\w+|#\w+|\d+', ' ', text)
    text = re.sub(r'[^a-zA-Z\s]', ' ', text)
    text = re.sub(r'\s+', ' ', text).strip()
    return text


def case_folding(text):
    return text.lower()


def tokenizing(text):
    return text.split()


def preprocess(text):
    clean = cleaning(text)
    lower = case_folding(clean)
    tokens = tokenizing(lower)
    filtered = [w for w in tokens if w not in stopwords]
    stemmed = stemmer.stem(' '.join(filtered))
    return {
        'clean': clean,
        'tokens': tokens,
        'filtered_tokens': filtered,
        'stemmed_text': stemmed,
    }


def muat_model(path):
    """Mengembalikan (classifier, vectorizer). vectorizer = None bila model berupa Pipeline."""
    obj = joblib.load(path)

    if not isinstance(obj, dict):
        return obj, None                       # Pipeline / model tunggal

    clf = obj.get('model')
    vec = next((v for v in obj.values() if isinstance(v, TfidfVectorizer)), None)

    if clf is None:
        raise ValueError('Kunci "model" tidak ada. Kunci tersedia: ' + ', '.join(obj.keys()))
    if vec is None and not hasattr(clf, 'named_steps'):
        tipe = {k: type(v).__name__ for k, v in obj.items()}
        raise ValueError('TfidfVectorizer tidak ditemukan di file model. Isi file: ' + json.dumps(tipe))
    return clf, vec


def prediksi(clf, vec, teks_stem):
    data = vec.transform([teks_stem]) if vec is not None else [teks_stem]
    pred = str(clf.predict(data)[0])
    probas = clf.predict_proba(data)[0]
    scores = {str(k): round(float(p), 4) for k, p in zip(clf.classes_, probas)}
    confidence = round(float(max(probas)), 4)
    return pred, scores, confidence


def main():
    if len(sys.argv) < 2:
        print(json.dumps({'error': 'No mode provided'}))
        sys.exit(1)

    mode = sys.argv[1]  # 'single' atau 'batch'

    BASE = os.path.dirname(os.path.abspath(__file__))
    model_path = sys.argv[2] if len(sys.argv) > 2 else os.path.join(BASE, 'model_tfidf_naive_bayes.pkl')

    if not os.path.exists(model_path):
        print(json.dumps({'error': f'Model tidak ditemukan: {model_path}'}))
        sys.exit(1)

    try:
        clf, vec = muat_model(model_path)
    except Exception as e:
        print(json.dumps({'error': str(e)}))
        sys.exit(1)

    if mode == 'single':
        raw_text = sys.argv[3] if len(sys.argv) > 3 else ''
        prep = preprocess(raw_text)
        pred, scores, confidence = prediksi(clf, vec, prep['stemmed_text'])
        print(json.dumps({
            'raw': raw_text,
            'preprocessing': prep,
            'sentiment': pred,
            'confidence': confidence,
            'scores': scores,
        }))

    elif mode == 'batch':
        sys.stdin.reconfigure(encoding='utf-8')
        input_data = json.loads(sys.stdin.read())
        results = []
        for item in input_data:
            raw_text = item.get('text', '')
            prep = preprocess(raw_text)
            pred, scores, confidence = prediksi(clf, vec, prep['stemmed_text'])
            results.append({
                'id': item.get('id'),
                'raw': raw_text,
                'preprocessing': prep,
                'sentiment': pred,
                'confidence': confidence,
                'scores': scores,
            })
        print(json.dumps(results))

    else:
        print(json.dumps({'error': f'Mode tidak dikenal: {mode}'}))
        sys.exit(1)


if __name__ == '__main__':
    main()