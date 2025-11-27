<?php

return [
    'provider' => env('AI_PROVIDER', 'ollama'), // ollama|openrouter|none

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),
        'chat_model' => env('OLLAMA_CHAT_MODEL', 'llama3.1'),
        'embed_model' => env('OLLAMA_EMBED_MODEL', 'nomic-embed-text'),
        'timeout' => env('OLLAMA_TIMEOUT', 20),
    ],

    'openrouter' => [
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'chat_model' => env('OPENROUTER_CHAT_MODEL', 'mistralai/mixtral-8x7b-instruct'),
        'timeout' => env('OPENROUTER_TIMEOUT', 30),
        'api_key' => env('OPENROUTER_API_KEY', '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'),
    ],

    'chatbot' => [
        'public_api_key' => env('CHATBOT_PUBLIC_API_KEY'),
        'default_channel' => env('CHATBOT_DEFAULT_CHANNEL', 'api'),
        'default_language' => env('CHATBOT_DEFAULT_LANGUAGE', 'auto'), // auto|id|en|both
    ],
];


