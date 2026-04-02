-- Add AI Analysis columns to submissions table
ALTER TABLE submissions 
ADD COLUMN ai_scope_score INT NULL COMMENT 'AI-calculated scope relevance (1-10)',
ADD COLUMN ai_quality_score INT NULL COMMENT 'AI-calculated quality score (1-10)',
ADD COLUMN ai_language_score INT NULL COMMENT 'AI-calculated language quality (1-10)',
ADD COLUMN ai_recommendation VARCHAR(20) NULL COMMENT 'AI recommendation: accept/review/reject',
ADD COLUMN ai_summary TEXT NULL COMMENT 'AI-generated summary',
ADD COLUMN ai_strengths JSON NULL COMMENT 'AI-identified strengths',
ADD COLUMN ai_weaknesses JSON NULL COMMENT 'AI-identified weaknesses',
ADD COLUMN ai_suggested_keywords JSON NULL COMMENT 'AI-suggested keywords',
ADD COLUMN ai_reviewer_expertise JSON NULL COMMENT 'AI-suggested reviewer expertise areas',
ADD COLUMN ai_decision_draft TEXT NULL COMMENT 'AI-drafted decision letter',
ADD COLUMN ai_analyzed_at DATETIME NULL COMMENT 'When AI analysis was performed';

-- Add expertise_keywords to users table for reviewer matching
ALTER TABLE users 
ADD COLUMN expertise_keywords TEXT NULL COMMENT 'Comma-separated list of expertise areas for AI matching';
