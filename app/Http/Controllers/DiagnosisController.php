<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Master;
use App\Helpers\Diagnosis;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;

class DiagnosisController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('patient.diagnosis.index');
    }

    public function predict(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string',
            'history' => 'required|string',
        ]);

        $symptoms = $request->input('symptoms');
        $history = $request->input('history');
        $prompt = "Patient Symptoms: $symptoms\nPatient History: $history";
        $result = Diagnosis::curlCall($prompt,'medication');
        // echo "<pre>"; print_r($result); exit;
        if (isset($result['choices'][0]['message']['function_call']['arguments'])) {
            $args = json_decode($result['choices'][0]['message']['function_call']['arguments'], true);
            return response()->json([
                'diagnosis' => $args['diagnosis'],
                'medications' => $args['medications'],
                'red_flags' => $args['red_flags']
            ]);
        } else {
            return response()->json(['error' => 'Unexpected response format', 'debug' => $result], 500);
        }
    }

    public function predictDiagnosisHealth(Request $request)
    {
        $request->validate([
            'lifestyle' => 'required|string',
            'history' => 'required|string',
            'risk' => 'required|string',
        ]);

        $history = $request->input('history');
        $lifestyle = $request->input('lifestyle');
        $risk = $request->input('risk');

        $prompt = "Patient History: $history\nLifestyle: $lifestyle\nRisk Factors: $risk";

        $result = Diagnosis::curlCall($prompt,'healthy-tips');

        if (isset($result['choices'][0]['message']['function_call']['arguments'])) {
            $args = json_decode($result['choices'][0]['message']['function_call']['arguments'], true);

            return response()->json([
                'function_called' => $result['choices'][0]['message']['function_call']['name'] ?? null,
                'diagnosis' => $args['diagnosis'] ?? null,
                'tips' => $args['tips'] ?? [],
                'preventive_measures' => $args['preventive_measures'] ?? [],
            ]);
        } else {
            return response()->json([
                'error' => 'Unexpected response format',
                'debug' => $result,
            ], 500);
        }
    }

    public function predictDiagnosisHealthTest(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string',
            'history' => 'required|string',
        ]);

        $symptoms = $request->input('symptoms');
        $history = $request->input('history');
        $prompt = "Patient Symptoms: $symptoms\nPatient History: $history";
        $result = Diagnosis::curlCall($prompt,'suggest-test');
        if (isset($result['choices'][0]['message']['function_call']['arguments'])) {
            $args = json_decode($result['choices'][0]['message']['function_call']['arguments'], true);
            return response()->json([
                'tests' => $args['tests'],
                'reasoning' => $args['reasoning'],
            ]);
        } else {
            return response()->json(['error' => 'Unexpected response format', 'debug' => $result], 500);
        }
    }

    public function predictReportDiagnosisTest(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string',
            'report' => 'required',
        ]);

        $symptoms = $request->input('symptoms');
        $file = $request->file('report');
        // For .txt files
        $pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($file->getRealPath());
        $reportText = $pdf->getText();

        if (strlen(trim($reportText)) < 50) {
            return response()->json(['error' => 'The PDF content seems too short or unreadable.'], 400);
        }
        $prompt = "Analyze the following report:\n\n" . $reportText;
        $result = Diagnosis::curlCall($prompt,'analyze_medical_report');
        if (isset($result['choices'][0]['message']['function_call']['arguments'])) {
            $args = json_decode($result['choices'][0]['message']['function_call']['arguments'], true);
            return response()->json([
                'summary' => $args['summary'],
                'diagnosis' => $args['diagnosis'],
                'medications' => $args['medications'],
                'red_flags' => $args['red_flags'],
                'risk_factors' => $args['risk_factors'],
            ]);
        }  else {
            return response()->json(['error' => 'Unexpected response format', 'debug' => $result], 500);
        }
    }

    public function predictClinicalNotes(Request $request)
    {
        $request->validate([
            'transcript' => 'required|string',
        ]);

        $transcript = $request->input('transcript');
        
        $prompt = "Generate clinical notes from this transcript:\n\n{$transcript}";
        $result = Diagnosis::curlCall($prompt,'generate_clinical_note');
        if (isset($result['choices'][0]['message']['function_call']['arguments'])) {
            $args = json_decode($result['choices'][0]['message']['function_call']['arguments'], true);
            return response()->json([
                'subjective' => $args['subjective']??'',
                'objective' => $args['objective']??'',
                'assessment' => $args['assessment']??'',
                'plan' => $args['plan']??'',
                'recommendations' => $args['recommendations']??'',
                'vitals' => $args['vitals']??'',
                'chief_complaint' => $args['chief_complaint']??'',
                'labs_to_order' => $args['labs_to_order']??'',
                'medications' => $args['medications']??'',
                'follow_up' => $args['follow_up']??'',
            ]);
        }  else {
            return response()->json(['error' => 'Unexpected response format', 'debug' => $result]);
        }
    }
}