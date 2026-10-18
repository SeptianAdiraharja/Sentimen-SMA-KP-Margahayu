import joblib
import numpy as np

b = joblib.load('model_tfidf_naive_bayes.pkl')
clf = b['model']
vec = next(v for v in b.values() if hasattr(v, 'get_feature_names_out'))
kata = vec.get_feature_names_out()

print('Kelas       :', [str(k) for k in clf.classes_])
print('Prior       :', np.exp(clf.class_log_prior_).round(3))
print('Jumlah data :', clf.class_count_)
print()

for i, kelas in enumerate(clf.classes_):
    idx = np.argsort(clf.feature_log_prob_[i])[::-1][:10]
    print(kelas, '->', list(kata[idx]))
print()

for w in ['tidak', 'hambar', 'enak', 'kurang']:
    if w in vec.vocabulary_:
        j = vec.vocabulary_[w]
        print(w, {str(k): round(float(clf.feature_log_prob_[i][j]), 2)
                  for i, k in enumerate(clf.classes_)})
    else:
        print(w, '-> tidak ada di kosakata model')