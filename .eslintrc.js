module.exports = {
    'root': true,
    'extends': [
        'standard',
        'plugin:react/recommended'
    ],
    'settings': {
        'react': {
            'version': 'detect'
        }
    },
    // Needed to parse arrow functions as react class properties
    // (e.g. `handleSubmit = (event) => { … }`.
    'parser': 'babel-eslint',
    'rules': {
        'indent': ['error', 4],
        'semi': ['error', 'never']
    }
};
