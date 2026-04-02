<?php
return [
    /*
    |--------------------------------------------------------------------------
    | AI Provider Configuration
    |--------------------------------------------------------------------------
    */
    'provider' => env('AI_PROVIDER', 'gemini'), // gemini, openai, deepseek
    
    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Settings
    |--------------------------------------------------------------------------
    */
    'gemini_api_key' => env('GEMINI_API_KEY', ''),
    'gemini_model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
    
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Settings (Alternative)
    |--------------------------------------------------------------------------
    */
    'openai_api_key' => env('OPENAI_API_KEY', ''),
    'openai_model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    
    /*
    |--------------------------------------------------------------------------
    | DeepSeek API Settings (Alternative)
    |--------------------------------------------------------------------------
    */
    'deepseek_api_key' => env('DEEPSEEK_API_KEY', ''),
    'deepseek_model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
    
    /*
    |--------------------------------------------------------------------------
    | AI Analysis Settings
    |--------------------------------------------------------------------------
    */
    'enabled' => env('AI_ENABLED', true),
    'auto_analyze_on_submit' => env('AI_AUTO_ANALYZE', false),
    'max_retries' => 3,
    'timeout_seconds' => 30,
];
