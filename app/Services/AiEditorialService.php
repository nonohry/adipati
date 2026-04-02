<?php

namespace App\Services;

use Core\Database;
use Exception;

class AiEditorialService
{
    protected $db;
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->apiKey = config('ai.gemini_api_key') ?? $_ENV['GEMINI_API_KEY'] ?? null;
        
        if (!$this->apiKey) {
            throw new Exception("Gemini API Key not configured. Please set GEMINI_API_KEY in .env");
        }
    }

    /**
     * Analisis submission menggunakan AI
     */
    public function analyzeSubmission(int $submissionId): array
    {
        $submission = $this->db->fetch("SELECT title, abstract, keywords, track_id FROM submissions WHERE id = ?", [$submissionId]);
        
        if (!$submission) {
            throw new Exception("Submission not found");
        }

        // Buat prompt untuk AI
        $prompt = $this->buildPrompt($submission);
        
        // Kirim request ke Gemini API
        $response = $this->callGeminiApi($prompt);
        
        // Parse respons JSON
        $analysis = $this->parseResponse($response);
        
        // Simpan hasil ke database
        $this->saveAnalysis($submissionId, $analysis);
        
        // Log aktivitas
        AuditService::log('AI_ANALYSIS_PERFORMED', 'submissions', $submissionId, null, $analysis);
        
        return $analysis;
    }

    /**
     * Bangun prompt analisis
     */
    private function buildPrompt($submission): string
    {
        return "You are an expert academic editor for a scientific conference. 
        Analyze the following paper submission and provide a structured JSON response.
        
        Title: {$submission->title}
        
        Abstract: {$submission->abstract}
        
        Keywords: {$submission->keywords}
        
        Provide your analysis in valid JSON format with these exact keys:
        {
            \"scope_score\": (integer 1-10, how well it fits academic conference),
            \"quality_score\": (integer 1-10, based on clarity and methodology),
            \"language_score\": (integer 1-10, English quality),
            \"overall_recommendation\": \"accept\" or \"review\" or \"reject\",
            \"summary\": \"2 sentence summary of the paper\",
            \"strengths\": [\"list of 3 strengths\"],
            \"weaknesses\": [\"list of 3 weaknesses\"],
            \"suggested_keywords\": [\"5 relevant keywords\"],
            \"reviewer_expertise_needed\": [\"3 expertise areas for reviewer matching\"],
            \"decision_letter_draft\": \"polite decision letter to author\"
        }
        
        Respond ONLY with the JSON, no markdown, no explanations.";
    }

    /**
     * Call Google Gemini API
     */
    private function callGeminiApi(string $prompt): string
    {
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];

        $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("AI API Error: HTTP $httpCode - " . substr($response, 0, 200));
        }

        return $response;
    }

    /**
     * Parse respons JSON dari AI
     */
    private function parseResponse(string $response): array
    {
        $jsonResponse = json_decode($response, true);
        
        if (!isset($jsonResponse['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception("Invalid AI response format");
        }

        $aiText = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];
        
        // Bersihkan markdown code blocks jika ada
        $aiText = preg_replace('/^```json\s*|\s*```$/', '', trim($aiText));
        $aiText = preg_replace('/^```\s*|\s*```$/', '', trim($aiText));
        
        $analysis = json_decode($aiText, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to parse AI JSON: " . json_last_error_msg());
        }

        return $analysis;
    }

    /**
     * Simpan hasil analisis ke database
     */
    private function saveAnalysis(int $submissionId, array $analysis): void
    {
        $this->db->update('submissions', [
            'ai_scope_score' => $analysis['scope_score'] ?? null,
            'ai_quality_score' => $analysis['quality_score'] ?? null,
            'ai_language_score' => $analysis['language_score'] ?? null,
            'ai_recommendation' => $analysis['overall_recommendation'] ?? null,
            'ai_summary' => $analysis['summary'] ?? null,
            'ai_strengths' => json_encode($analysis['strengths'] ?? []),
            'ai_weaknesses' => json_encode($analysis['weaknesses'] ?? []),
            'ai_suggested_keywords' => json_encode($analysis['suggested_keywords'] ?? []),
            'ai_reviewer_expertise' => json_encode($analysis['reviewer_expertise_needed'] ?? []),
            'ai_decision_draft' => $analysis['decision_letter_draft'] ?? null,
            'ai_analyzed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$submissionId]);
    }

    /**
     * Dapatkan rekomendasi reviewer berdasarkan analisis AI
     */
    public function getReviewerSuggestions(int $submissionId): array
    {
        $submission = $this->db->fetch("SELECT ai_reviewer_expertise, track_id FROM submissions WHERE id = ?", [$submissionId]);
        
        if (!$submission || !$submission->ai_reviewer_expertise) {
            return [];
        }

        $expertiseList = json_decode($submission->ai_reviewer_expertise, true);
        if (empty($expertiseList)) {
            return [];
        }

        // Cari reviewer dengan expertise yang cocok
        $reviewers = $this->db->fetchAll("
            SELECT DISTINCT u.id, u.first_name, u.last_name, u.email, 
                   COUNT(DISTINCT ra.id) as active_assignments
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            LEFT JOIN reviewer_assignments ra ON u.id = ra.reviewer_id AND ra.status IN ('invited', 'accepted', 'completed')
            WHERE r.slug = 'reviewer'
              AND (
                  u.expertise_keywords LIKE ? OR 
                  u.expertise_keywords LIKE ? OR 
                  u.expertise_keywords LIKE ?
              )
            GROUP BY u.id
            ORDER BY active_assignments ASC
            LIMIT 5
        ", [
            '%' . $expertiseList[0] . '%',
            '%' . ($expertiseList[1] ?? '') . '%',
            '%' . ($expertiseList[2] ?? '') . '%'
        ]);

        return $reviewers;
    }
}
