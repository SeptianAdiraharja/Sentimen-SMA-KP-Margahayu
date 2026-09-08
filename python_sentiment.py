import sys
import os
import json
import re
import pickle
import warnings
warnings.filterwarnings('ignore')

from Sastrawi.StopWordRemover.StopWordRemoverFactory import StopWordRemoverFactory
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

# Inisialisasi Stopword & Stemmer
factory_stopword = StopWordRemoverFactory()
stopwords = set(factory_stopword.get_stop_words()) - {'tidak', 'bukan', 'jangan', 'belum', 'kurang'}
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
        'stemmed_text': stemmed
    }

def main():
    if len(sys.argv) < 2:
        print(json.dumps({'error': 'No mode provided'}))
        sys.exit(1)
        
    mode = sys.argv[1] # 'single' or 'batch'
    model_path = sys.argv[2] if len(sys.argv) > 2 else 'model_tfidf_naive_bayes.pkl'
    
    if not os.path.exists(model_path):
        # Fallback lokasi
        paths_to_check = [
            model_path,
            os.path.join(os.path.dirname(__file__), '..', 'model_tfidf_naive_bayes.pkl'),
            os.path.join(os.path.dirname(__file__), 'model_tfidf_naive_bayes.pkl'),
            r'C:\Users\user\OneDrive\Desktop\vrillia\model_tfidf_naive_bayes.pkl'
        ]
        for p in paths_to_check:
            if os.path.exists(p):
                model_path = p
                break

    with open(model_path, 'rb') as f:
        model = pickle.load(f)

    if mode == 'single':
        raw_text = sys.argv[3] if len(sys.argv) > 3 else ''
        prep = preprocess(raw_text)
        pred = model.predict([prep['stemmed_text']])[0]
        probas = model.predict_proba([prep['stemmed_text']])[0]
        scores = dict(zip(model.classes_, [round(float(p), 4) for p in probas]))
        confidence = round(float(max(probas)), 4)
        
        result = {
            'raw': raw_text,
            'preprocessing': prep,
            'sentiment': pred,
            'confidence': confidence,
            'scores': scores
        }
        print(json.dumps(result))
        
    elif mode == 'batch':
        input_data = json.loads(sys.stdin.read())
        results = []
        for item in input_data:
            item_id = item.get('id')
            raw_text = item.get('text', '')
            prep = preprocess(raw_text)
            pred = model.predict([prep['stemmed_text']])[0]
            probas = model.predict_proba([prep['stemmed_text']])[0]
            scores = dict(zip(model.classes_, [round(float(p), 4) for p in probas]))
            confidence = round(float(max(probas)), 4)
            
            results.append({
                'id': item_id,
                'raw': raw_text,
                'preprocessing': prep,
                'sentiment': pred,
                'confidence': confidence,
                'scores': scores
            })
        print(json.dumps(results))

if __name__ == '__main__':
    main()
