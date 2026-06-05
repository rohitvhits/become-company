<?php

namespace App\Http\Controllers;

use App\Model\Logs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use App\Helpers\Utility;

class HelpMeWriteController extends BaseController
{
    private function callBedrock(string $systemPrompt, string $userPrompt): string
    {
        $bedrockClient = new \Aws\BedrockRuntime\BedrockRuntimeClient([
            'version'     => 'latest',
            'region'      => env('BEDROCK_REGION', 'us-east-2'),
            'credentials' => [
                'key'    => env('BEDROCK_ACCESS_KEY_ID'),
                'secret' => env('BEDROCK_SECRET_ACCESS_KEY'),
            ],
        ]);

        $result = $bedrockClient->converse([
            'modelId'  => env('BEDROCK_MODEL_ID', 'us.anthropic.claude-haiku-4-5-20251001-v1:0'),
            'system'   => [['text' => $systemPrompt]],
            'messages' => [
                ['role' => 'user', 'content' => [['text' => $userPrompt]]],
            ],
            'inferenceConfig' => [
                'maxTokens'   => 300,
                'temperature' => 0.7,
            ],
        ]);

        $text = $result['output']['message']['content'][0]['text'] ?? '';
        return trim(preg_replace('/\*{1,2}([^*]+)\*{1,2}/', '$1', $text));
    }

    private function systemPrompt(string $context): string
    {
        return 'You are a helpful AI assistant. Do exactly what the user asks — write, rewrite, translate to any language, summarize, expand, convert, or generate any type of content. Output ONLY the final result text with no explanations, no labels, no markdown formatting, no extra commentary.';
    }

    private function writeLog(string $action, string $context, string $newResponse, string $oldResponse = '', bool $success = true, $recordId = null): void
    {
        try {
            $who     = auth()->user()->first_name . ' ' . auth()->user()->last_name;
            $section = $context === 'sms' ? 'Text Message' : 'Notes';

            Logs::create([
                'type'         => 'Help Me Write',
                'module'       => 'Patient Appointment',
                'link'         => url('/patient/view/' . $recordId),
                'object_id'    => $recordId,
                'message'      => $who . ' has used Help Me Write in ' . $section . ' — action: ' . $action . ($success ? '' : ' [FAILED]'),
                'new_response' => serialize($newResponse),
                'old_response' => serialize($oldResponse),
                'ip'           => Utility::getIP(),
                'created_by'   => auth()->id(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[HelpMeWrite:log] ' . $e->getMessage());
        }
    }

    // Generate: from user prompt
    public function handle(Request $request)
    {
        $prompt   = trim($request->input('prompt', ''));
        $context  = trim($request->input('context', 'notes'));
        $recordId = $request->input('record_id');

        if ($prompt === '') {
            return response()->json(['success' => false, 'error' => 'Please describe what you want to write.'], 422);
        }

        try {
            $text = $this->callBedrock(
                $this->systemPrompt($context),
                'Write the following and return only the final text: ' . $prompt
            );

            if ($text) {
                $this->writeLog('generate', $context, $text, $prompt, true, $recordId);
                return response()->json(['success' => true, 'data' => $text]);
            }

            $this->writeLog('generate', $context, 'AI could not generate text.', $prompt, false, $recordId);
            return response()->json(['success' => false, 'error' => 'AI could not generate text.'], 500);

        } catch (\Throwable $e) {
            Log::error('[HelpMeWrite:generate] ' . $e->getMessage());
            $this->writeLog('generate', $context, $e->getMessage(), $prompt, false, $recordId);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Refine: formalize | shorten | elaborate | recreate
    public function refine(Request $request)
    {
        $text     = trim($request->input('text', ''));
        $action   = trim($request->input('action', 'formalize'));
        $context  = trim($request->input('context', 'notes'));
        $recordId = $request->input('record_id');

        if ($text === '') {
            return response()->json(['success' => false, 'error' => 'No text to refine.'], 422);
        }

        $instructions = [
            'formalize'  => 'Rewrite the following text to be more formal and professional. Return only the rewritten text.',
            'shorten'    => 'Shorten the following text while keeping the key message. Return only the shortened text.',
            'elaborate'  => 'Add one or two sentences of extra detail or context to the following text. Do NOT repeat, pad, or significantly lengthen it — the result should be only slightly longer than the input. Return only the expanded text.',
            'recreate'   => 'Rewrite the following text in a completely different way keeping the same meaning. Return only the new text.',
        ];

        $instruction = $instructions[$action] ?? $instructions['formalize'];

        try {
            $refined = $this->callBedrock(
                $this->systemPrompt($context),
                $instruction . "\n\n" . $text
            );

            if ($refined) {
                $this->writeLog($action, $context, $refined, $text, true, $recordId);
                return response()->json(['success' => true, 'data' => $refined]);
            }

            $this->writeLog($action, $context, 'AI could not refine text.', $text, false, $recordId);
            return response()->json(['success' => false, 'error' => 'AI could not refine text.'], 500);

        } catch (\Throwable $e) {
            Log::error('[HelpMeWrite:refine] ' . $e->getMessage());
            $this->writeLog($action, $context, $e->getMessage(), $text, false, $recordId);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
