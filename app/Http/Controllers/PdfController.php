<?php

namespace App\Http\Controllers;

use App\Services\PdfParserService;
use Illuminate\Http\Request;
use App\PdfText;
use Illuminate\Support\Facades\Validator;
class PdfController extends Controller
{
    protected $pdfParserService;

    public function __construct(PdfParserService $pdfParserService)
    {
        $this->middleware('auth');
        $this->pdfParserService = $pdfParserService;
    }

    public function index()
    {
        return view('extractPdf/extract-pdf');
    }

    /*public function extractText(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf',
        ]);

        $path = $request->file('pdf_file')->store('pdfs');

        $fullPath = storage_path('app/' . $path);

        $text = $this->pdfParserService->getPdfText($fullPath);
        // Regex Pattern
        // $pattern = '/AMPHETAMINES QUAL(.*?)4.5-11.0/s';
        $pattern = '/ Amphetamines, Urine(.*?)Cutoff=1000 /';

        preg_match($pattern, $text, $matches);

        if (!isset($matches[0])) {
            return response()->json(['message' => 'Pattern not found']);
        }

        // Process the matched text
        $lines = explode("\n", trim($matches[0]));
        $results = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) continue;

            // Handle QUAL tests and ETHANOL URINE
            if (preg_match('/^(.*?QUAL|ETHANOL URINE)/', $line)) {
                // Split by tabs or multiple spaces
                $parts = preg_split('/\t+|\s{2,}/', $line);

                if (count($parts) >= 2) {
                    $middleValue = trim($parts[1]);

                    if (strtolower($middleValue) === 'negative') {
                        $results[] = 'normal';
                    } else {
                        $results[] = 'not normal';
                    }
                }
            }

            if (preg_match('/SPECIMEN VALIDITY TEST PH\s+([\d.]+)\s+4.5-11.0/', $line, $phMatch)) {
                $phValue = floatval($phMatch[1]); 

                if ($phValue >= 4.5 && $phValue <= 11.0) {
                    $results[] = 'normal';
                } else {
                    $results[] = 'not normal';
                }
            }
        }

        PdfText::create([
            'file_name' => $request->file('pdf_file')->getClientOriginalName(),
            'extracted_text' => $text,
            'results' => implode("\n", $results),
            'created_date' => now(),
        ]);

        return response()->json([
            'text' => $results,
            'message' => 'Text processed successfully'
        ]);

    }*/

    public function extractText(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pdf_file' => 'required|min:1|max:1000|mimes:pdf',
        ], [
            'pdf_file.required' => 'Please upload a PDF file.',
            'pdf_file.mimes' => 'Only PDF files are allowed.',
            'file.min' => 'The file must not be empty (0 KB is not allowed).',
            'file.max' => 'The file must not exceed 1 MB.'
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->toArray();
            return response()->json([
                'error' => '',
                'message' => $error['pdf_file'][0]
            ]);
        }else{
            $path = $request->file('pdf_file')->store('pdfs');

            $fullPath = storage_path('app/' . $path);
    
            $text = $this->pdfParserService->getPdfText($fullPath);
    
            $results = '';
            $pattern_drug = '/Chain-of-Custody Protocol;/i'; //Drug Screen
            $pattern_drug_monitor = '/DRUG MONITOR/i';
            $pattern_toxicology = '/TOXICOLOGY/i';
            // echo "<pre>"; print_r($text); exit;
            if (preg_match($pattern_drug, $text, $matches)) { // Drug Screen
                // Define the tests we expect and cutoff values
                $tests = [
                    'Amphetamines, Urine'       => ['unit' => 'ng/mL', 'cutoff' => 1000],
                    'Barbiturates'              => ['unit' => 'ng/mL', 'cutoff' => 200],
                    'Benzodiazepines'           => ['unit' => 'ng/mL', 'cutoff' => 200],
                    'Cocaine (Metab.)'          => ['unit' => 'ng/mL', 'cutoff' => 300],
                    'Opiates'                   => ['unit' => 'ng/mL', 'cutoff' => 300],
                    'Phencyclidine'             => ['unit' => 'ng/mL', 'cutoff' => 25],
                    'Methadone Screen, Urine'   => ['unit' => 'ng/mL', 'cutoff' => 300],
                    'Propoxyphene, Urine'       => ['unit' => 'ng/mL', 'cutoff' => 300],
                    'Creatinine, Urine'         => ['unit' => 'mg/dL', 'range' => [20.0, 300.0]],
                    'Specific Gravity'          => ['unit' => '', 'cutoff' => ''],
                    'Nitrite, Urine'            => ['unit' => 'mcg/mL', 'cutoff' => 200],
                    'pH, Urine'                 => ['unit' => '', 'range' => [4.5, 8.9]],
                ];
                
                // Display in HTML Table
                $results .= '<div class="card"><div class="card-body"><div class="card-title mb-0">Uploaded Result</div><div class="table-responsive"><table class="table table-bordered">';

                $dob= "";
                if (preg_match('/([A-Z]+,\s+[A-Z]+)/', $text, $matches)) {
                    $pa_name = $matches[1];
                }
                if (preg_match('/DOB:\s*(\d{2}\/\d{2}\/\d{4})/', $text, $matches)) {
                    $dob = $matches[1];
                }
                
                if(isset($pa_name) && $pa_name != "" && isset($dob) && $dob != ""){
                    $results .= '
                        <div class="card"><div class="card-body mb-1"><div class="row"><div class="col-md-6"><label class="label-control"><b>Patient name</b>: '.trim($pa_name).'</label></div><div class="col-md-6"><label class="label-control"><b>DOB</b>: '.$dob.'</label></div></div></div></div>'; 
                }
                $results .= '<thead><tr><th>Test Name</th><th>Result</th><th>Unit</th><th>Reference</th></tr></thead>';
    
                // Loop through expected tests
                foreach ($tests as $name => $info) {
                    // Use regex to find result line
                    $escapedName = preg_quote($name, '~');
                    $escapedUnit = isset($info['unit']) ? preg_quote($info['unit'], '~') : '';
    
                    $pattern = "~" . $escapedName . "\s+([A-Za-z0-9\.\-]+)\s+(?:(" . $escapedUnit . ")\s+)?(\d{1,3}(?:\.\d+)?(?:\s*-\s*\d{1,3}(?:\.\d+)?)?)?~i";
                    preg_match($pattern, $text, $matches);
    
                    $result = $matches[1] ?? 'N/A';
                    $ref = isset($info['cutoff']) && !empty($info['cutoff']) ? "HCutoff = " . $info['cutoff'] : (isset($info['range']) ? implode(' - ', $info['range']) : '—');
                    // Determine status
                    $status = 'Negative';
                    $highlightclass = '';
                    if (strtolower($result) === 'negative') {
                        $status = 'Negative';
                        $highlightclass = '';
                    } elseif (strtolower($result) === 'positive') {
                        $status = 'Positive';
                        $highlightclass = 'result-cell';
                    } elseif (is_numeric($result)) {
                        if (isset($info['range'])) {
                            $min = $info['range'][0];
                            $max = $info['range'][1];
                            if ($result < $min || $result > $max) {
                                $status = 'Positive';
                                $highlightclass = 'result-cell';
                            }
                        }else{
                            $status = $result;
                        }
                    }
    
                    $results .= "<tr class='{$highlightclass}'>
                            <td>{$name}</td>
                            <td>{$result}</td>
                            <td>{$info['unit']}</td>
                            <td>{$ref}</td>
                        </tr>";
                }
    
                $results .= '</table></div></div></div>';
    
                PdfText::create([
                    'file_name' => $request->file('pdf_file')->getClientOriginalName(),
                    'extracted_text' => $text,
                    'results' =>  $results,
                    'created_date' => now(),
                ]);
            } else if (preg_match($pattern_drug_monitor, $text, $matches)) {
                $results= "";
                // Define the tests we expect and cutoff values
                $tests = [
                    'Amphetamines'         => ['Cutoff' => '500 ng/mL', 'Lab' => 'Z99'],
                    'Barbiturates'         => ['Cutoff' => '300 ng/mL', 'Lab' => 'Z99'],
                    'Benzodiazepines'      => ['Cutoff' => '100 ng/mL', 'Lab' => 'Z99'],
                    'Cocaine Metabolite'   => ['Cutoff' => '150 ng/mL', 'Lab' => 'Z99'],
                    'Methadone Metabolite' => ['Cutoff' => '100 ng/mL', 'Lab' => 'Z99'],
                    'Opiates'              => ['Cutoff' => '100 ng/mL', 'Lab' => 'Z99'],
                    'Oxycodone'            => ['Cutoff' => '100 ng/mL', 'Lab' => 'Z99'],
                    'Phencyclidine'        => ['Cutoff' => '25 ng/mL', 'Lab' => 'Z99'],
                ];

                if (preg_match('/^([A-Z\s]+,\s+[A-Z]+)/m', $text, $matches)) {
                    $pa_name = $matches[1];
                }
                if (preg_match('/DOB:\s*(\d{2}\/\d{2}\/\d{4})/', $text, $matches)) {
                    $dob = $matches[1];
                }
                $results .= '<div class="card"><div class="card-body"><div class="card-title mb-1">Uploaded Result</div>';
                if(isset($pa_name) && $pa_name != ""){
                    $results .= '
                        <div class="card"><div class="card-body"><div class="row"><div class="col-md-6"><label class="label-control"><b>Patient name</b>: '.trim($pa_name).'</label></div><div class="col-md-6"><label class="label-control"><b>DOB</b>: '.$dob.'</label></div></div></div></div>'; 
                }

                $results .= '<div class="card"><div class="card-body"><div class="card-title mb-0">Uploaded Result</div><div class="table-responsive"><table class="table table-bordered">';
                $results .= '<thead><tr><th>Test Ordered</th><th>Result</th><th>Cutoff</th><th>Lab</th></tr></thead>';
    
                // Loop through expected tests
                foreach ($tests as $name => $info) {
                    // Use regex to find result line
                    $escapedName = preg_quote($name, '~');
                    $escapedUnit = isset($info['Cutoff']) ? preg_quote($info['Cutoff'], '~') : '';
    
                    $pattern = " . $escapedName . ";
                    $pattern = '/([A-Za-z\s,]+(?: '.$escapedName.'))\s+([A-Za-z0-9\s]+)\s+([A-Za-z]+)\s+(\d+ ng\/mL)/';
                    preg_match($pattern, $text, $matches);
                    $result = preg_replace('/See Note/', '', $matches[2]) ?? 'N/A';
                    // Determine status
                    $status = 'Normal';
                    $highlightclass = '';
                    if (strtolower($result) === 'negative') {
                        // $status = 'Normal';
                        $status = 'Negative';
                    } elseif (strtolower($result) === 'positive') {
                        // $status = 'Abnormal';
                        $highlightclass = 'result-cell';
                        $status = 'Positive';
                    } elseif (is_numeric($result)) {
                        if (isset($info['range'])) {
                            $min = $info['range'][0];
                            $max = $info['range'][1];
                            if ($result < $min || $result > $max) {
                                $status = 'Positive';
                                $highlightclass = 'result-cell';
                            }
                        }
                    }
    
                    $results .= "<tr class='{$highlightclass}'>
                            <td>{$name}</td>
                            <td>{$result}</td>
                            <td>{$info['Cutoff']}</td>
                            <td>{$info['Lab']}</td>
                        </tr>";
                }
    
                $results .= '</table></div></div></div>';
    
                PdfText::create([
                    'file_name' => $request->file('pdf_file')->getClientOriginalName(),
                    'extracted_text' => $text,
                    'results' =>  $results,
                    'created_date' => now(),
                ]);
            } else if (preg_match($pattern_toxicology, $text, $matches)) {
                $results= "";
                // Define the tests we expect and cutoff values
                $tests = [
                'AMPHETAMINES QUAL'             => ['Reference' => 'Negative', 'Out Of Range' => '1000 ng/ml'],
                'BENZODIAZEPINES QUAL'          => ['Reference' => 'Negative', 'Out Of Range' => '200 ng/ml'],
                'BARBITURATES QUAL'             => ['Reference' => 'Negative', 'Out Of Range' => '200 ng/ml'],
                'METHADONE QUAL'                => ['Reference' => 'Negative', 'Out Of Range' => '300 ng/ml'],
                'OPIATES QUAL'                  => ['Reference' => 'Negative', 'Out Of Range' => '300 ng/ml'],
                'PHENCYCLIDINE QUAL'            => ['Reference' => 'Negative', 'Out Of Range' => '25 ng/ml'],
                'COCAINE QUAL'                  => ['Reference' => 'Negative', 'Out Of Range' => '300 ng/ml'],
                'PROPOXYPHENE QUAL'             => ['Reference' => 'Negative', 'Out Of Range' => '300 ng/ml'],
                'ETHANOL URINE'                 => ['Reference' => 'Negative', 'Out Of Range' => '100 ng/ml'],
                'OXYCODONE QUAL'                => ['Reference' => 'Negative', 'Out Of Range' => '100 ng/ml'],
                'SPECIMEN VALIDITY TEST PH'     => ['Reference' => '4.5-11.0', 'Out Of Range' => '<4.0 and >=11.0'],
                ];
                
                if (preg_match('/Patient:\s*([A-Z]+,\s+[A-Z]+)/', $text, $matches)) {
                    $pa_name = $matches[1];
                }
                if (preg_match('/DOB:\s*(\d{2}\/\d{2}\/\d{4})/', $text, $matches)) {
                    $dob = $matches[1];
                }
                $results .= '<div class="card"><div class="card-body"><div class="card-title mb-1">Uploaded Result</div>';
                if(isset($pa_name) && $pa_name != ""){
                    $results .= '
                        <div class="card"><div class="card-body"><div class="row"><div class="col-md-6"><label class="label-control"><b>Patient name</b>: '.$pa_name.'</label></div><div class="col-md-6"><label class="label-control"><b>DOB</b>: '.$dob.'</label></div></div></div></div>'; 
                }
                
                $results .='<div class="table-responsive"><table class="table table-bordered">';
                $results .= '<tr><th>Test Name</th><th>In Range</th><th>Out Of Range</th><th>Reference</th></tr>';
               
                // Loop through expected tests
                foreach ($tests as $name => $info) {
                    $ref = isset($info['Reference']) && !empty($info['Reference']) ? explode('-', $info['Reference']) : '—';
                    // Use regex to find result line
                    $escapedName = preg_quote($name, '~');
    
                    $pattern = "~".$escapedName . "\s+([A-Za-z0-9\.\-]+)~i";
                    // preg_match($pattern, $text, $matches);
                    preg_match($pattern, $text, $matches);
    
                    $result = $matches[1] ?? 'N/A';
                    // Determine status
                    $status = 'Negative';
                    $highlight = '';
                    if (strtolower($result) === 'negative') {
                        $status = 'Negative';
                    } elseif (strtolower($result) === 'positive') {
                        $status = 'Positive';
                        $highlight = 'result-cell';
                    } elseif (is_numeric($result)) {
                        if (isset($info['Reference'])) {
                            $min = $ref[0];
                            $max = $ref[1];
                            if ($result < $min || $result > $max) {
                                $status = 'Positive '.'('. $result.')';
                                $highlight = 'result-cell';
                            }else{
                                $status = 'Negative '.'('. $result.')';
                            }
                        }
                    }
    
                    $results .= "<tr class='{$highlight}'>
                            <td>{$name}</td>
                            <td>{$status}</td>
                            <td>{$info['Out Of Range']}</td>
                            <td>{$info['Reference']}</td>
                        </tr>";
                }
    
                $results .= '</table></div></div></div>';
                PdfText::create([
                    'file_name' => $request->file('pdf_file')->getClientOriginalName(),
                    'extracted_text' => $text,
                    'results' =>  $results,
                    'created_date' => now(),
                ]);
            }
            if($results == ''){
                return response()->json([
                    'text' => $results,
                    'message' => ''
                ]);
            }else{
                return response()->json([
                    'text' => $results,
                    'message' => 'Text processed successfully'
                ]);
            }
        }
    }
}
