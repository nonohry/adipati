<?php $layout = 'layouts/dashboard'; $pageTitle = "AI Analysis Results"; ob_start(); ?>

<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">AI Editorial Assistant</h1>
        <p class="text-gray-600">Paper: <?= htmlspecialchars($submission->title) ?></p>
    </div>

    <?php if ($submission->ai_analyzed_at): ?>
    <!-- AI Analysis Results -->
    <div class="grid grid-cols-3 gap-6 mb-6">
        <!-- Scores -->
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold text-lg mb-4">Quality Scores</h3>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Scope Relevance</span>
                        <span class="font-bold"><?= $submission->ai_scope_score ?>/10</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= ($submission->ai_scope_score * 10) ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Quality</span>
                        <span class="font-bold"><?= $submission->ai_quality_score ?>/10</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: <?= ($submission->ai_quality_score * 10) ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>Language</span>
                        <span class="font-bold"><?= $submission->ai_language_score ?>/10</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: <?= ($submission->ai_language_score * 10) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-white p-6 rounded shadow col-span-2">
            <h3 class="font-bold text-lg mb-4">AI Summary</h3>
            <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($submission->ai_summary)) ?></p>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-green-700 mb-2">Strengths</h4>
                    <ul class="text-sm space-y-1">
                        <?php foreach(json_decode($submission->ai_strengths, true) ?? [] as $strength): ?>
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">✓</span>
                            <span><?= htmlspecialchars($strength) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-red-700 mb-2">Weaknesses</h4>
                    <ul class="text-sm space-y-1">
                        <?php foreach(json_decode($submission->ai_weaknesses, true) ?? [] as $weakness): ?>
                        <li class="flex items-start">
                            <span class="text-red-600 mr-2">✗</span>
                            <span><?= htmlspecialchars($weakness) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendation & Keywords -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold text-lg mb-4">AI Recommendation</h3>
            <div class="text-3xl font-bold 
                <?= $submission->ai_recommendation == 'accept' ? 'text-green-600' : 
                   ($submission->ai_recommendation == 'reject' ? 'text-red-600' : 'text-yellow-600') ?>">
                <?= strtoupper(str_replace('_', ' ', $submission->ai_recommendation)) ?>
            </div>
            
            <h4 class="font-semibold mt-4 mb-2">Suggested Keywords</h4>
            <div class="flex flex-wrap gap-2">
                <?php foreach(json_decode($submission->ai_suggested_keywords, true) ?? [] as $keyword): ?>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm"><?= htmlspecialchars($keyword) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold text-lg mb-4">Decision Letter Draft</h3>
            <div class="text-sm text-gray-700 bg-gray-50 p-4 rounded h-48 overflow-y-auto">
                <?= nl2br(htmlspecialchars($submission->ai_decision_draft)) ?>
            </div>
            <button class="mt-2 text-sm text-blue-600 hover:underline" onclick="copyDraft()">Copy Draft</button>
        </div>
    </div>

    <!-- Reviewer Suggestions -->
    <?php if (!empty($reviewerSuggestions)): ?>
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="font-bold text-lg mb-4">Suggested Reviewers (Based on AI Analysis)</h3>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-center">Active Assignments</th>
                    <th class="px-4 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reviewerSuggestions as $reviewer): ?>
                <tr>
                    <td class="px-4 py-3"><?= htmlspecialchars($reviewer->first_name . ' ' . $reviewer->last_name) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($reviewer->email) ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded text-xs <?= $reviewer->active_assignments > 3 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                            <?= $reviewer->active_assignments ?> papers
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?= url('/editor/submissions/' . $submission->id . '/assign?reviewer=' . $reviewer->id) ?>" 
                           class="text-blue-600 hover:underline text-sm">Assign</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="text-xs text-gray-500 mt-4">
        Analyzed by AI on: <?= date('d M Y H:i', strtotime($submission->ai_analyzed_at)) ?>
    </div>

    <?php else: ?>
    <div class="bg-yellow-50 border border-yellow-200 p-6 rounded text-center">
        <p class="text-yellow-800 mb-4">This paper has not been analyzed by AI yet.</p>
        <button onclick="runAnalysis()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Run AI Analysis Now
        </button>
    </div>
    <?php endif; ?>
</div>

<script>
function runAnalysis() {
    const btn = event.target;
    btn.disabled = true;
    btn.innerText = 'Analyzing...';
    
    fetch('<?= url("/editor/ai/analyze/" . $submission->id) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        } else {
            alert('Error: ' + d.error);
            btn.disabled = false;
            btn.innerText = 'Run AI Analysis Now';
        }
    })
    .catch(e => {
        alert('Network error');
        btn.disabled = false;
        btn.innerText = 'Run AI Analysis Now';
    });
}

function copyDraft() {
    const text = <?= json_encode($submission->ai_decision_draft) ?>;
    navigator.clipboard.writeText(text).then(() => alert('Copied!'));
}
</script>

<?php $content = ob_get_clean(); include BASE_PATH . '/resources/views/' . $layout . '.php'; ?>
