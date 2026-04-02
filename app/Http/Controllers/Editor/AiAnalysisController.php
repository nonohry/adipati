<?php

namespace App\Http\Controllers\Editor;

use Core\Controller;
use App\Services\AiEditorialService;
use Core\Database;
use Core\Session;

class AiAnalysisController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        parent::__construct();
        $this->aiService = new AiEditorialService();
    }

    /**
     * Run AI analysis on a submission
     */
    public function analyze($submissionId)
    {
        try {
            // Check permission
            if (!Session::has('user_id')) {
                return $this->redirect('/login');
            }

            // Run analysis
            $result = $this->aiService->analyzeSubmission($submissionId);
            
            // Get reviewer suggestions
            $reviewerSuggestions = $this->aiService->getReviewerSuggestions($submissionId);

            Session::flash('success', 'AI Analysis completed successfully!');
            
            return $this->json([
                'success' => true,
                'data' => $result,
                'reviewers' => $reviewerSuggestions
            ]);

        } catch (\Exception $e) {
            Session::flash('error', 'AI Analysis failed: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show AI analysis results in editor view
     */
    public function showResults($submissionId)
    {
        $submission = $this->db->fetch("SELECT * FROM submissions WHERE id = ?", [$submissionId]);
        
        if (!$submission) {
            abort(404);
        }

        $reviewerSuggestions = [];
        if ($submission->ai_reviewer_expertise) {
            $reviewerSuggestions = $this->aiService->getReviewerSuggestions($submissionId);
        }

        return $this->view('editor.submissions.ai-results', [
            'submission' => $submission,
            'reviewerSuggestions' => $reviewerSuggestions
        ]);
    }
}
