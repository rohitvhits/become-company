<?php

namespace App\Helpers;

class Diagnosis
{
    public static function curlCall($prompt,$type){
        $apiKey = $apiKey = env('OPENAI_API_KEY');
        if($type == 'medication'){
            $data = self::buildAIFunction($prompt);
        }else if($type == 'healthy-tips'){
            $data = self::buildAIHealthyTipsFunction($prompt);
        }else if($type == 'suggest-test'){
            $data = self::buildAIReportFunction($prompt);
        }else if($type == 'analyze_medical_report'){
            $data = self::summarizeReport($prompt);
        }else if($type == 'generate_clinical_note'){
            $data = self::generateClinicalNotes($prompt);
        }
        $ch = curl_init('https://api.openai.com/v1/chat/completions');

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $apiKey,
                    'Content-Type: application/json',
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'Curl error: ' . curl_error($ch);
                }

                curl_close($ch);

        $result = json_decode($response, true);
        return $result;
    }

    public static function buildAIFunction($prompt){
        $functions = [
            [
                "name" => "medical_diagnosis_suggestion",
                "description" => "Suggests diagnosis, medications, and red flags based on patient symptoms and history",
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "diagnosis" => [
                            "type" => "string",
                            "description" => "The possible diagnosis for the patient"
                        ],
                        "medications" => [
                            "type" => "array",
                            "items" => [
                                "type" => "string"
                            ],
                            "description" => "Recommended medications"
                        ],
                        "red_flags" => [
                            "type" => "array",
                            "items" => [
                                "type" => "string"
                            ],
                            "description" => "Urgent signs or actions that must be taken"
                        ]
                    ],
                    "required" => ["diagnosis", "medications", "red_flags"]
                ]
            ]
        ];
            

        $data = [
            'model' => 'gpt-4-0613',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'functions' => $functions,
            'function_call' => ['name' => 'medical_diagnosis_suggestion']
        ];

        return $data;
    }

    public static function buildAIHealthyTipsFunction($prompt)
    {
        $functions = [
            [
                "name" => "health_tips_and_prevention_advice",
                "description" => "Provides health tips and prevention advice based on patient's condition or risk factors",
                "parameters" => [
                    "type" => "object",
                    "properties" => [
                        "diagnosis" => [
                            "type" => "string",
                            "description" => "Likely diagnosis based on provided information"
                        ],
                        "tips" => [
                            "type" => "array",
                            "items" => ["type" => "string"],
                            "description" => "Health or lifestyle tips"
                        ],
                        "preventive_measures" => [
                            "type" => "array",
                            "items" => ["type" => "string"],
                            "description" => "Prevention advice for specific risks"
                        ]
                    ],
                    "required" => ["tips", "preventive_measures"]
                ]
            ]
        ];

        return [
            'model' => 'gpt-4-0613',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'functions' => $functions,
            'function_call' => ['name' => 'health_tips_and_prevention_advice']
        ];
    }


    public static function buildAIReportFunction($prompt){
        $functions = [[
            "name" => "suggest_lab_tests",
            "description" => "Suggests relevant lab tests based on the patient's symptoms and medical history",
            "parameters" => [
                "type" => "object",
                "properties" => [
                    "tests" => [
                        "type" => "array",
                        "items" => ["type" => "string"],
                        "description" => "List of recommended lab tests"
                    ],
                    "reasoning" => [
                        "type" => "string",
                        "description" => "Why these tests are relevant based on patient condition"
                    ]
                ],
                "required" => ["tests", "reasoning"]
            ]
        ]];
            
        $data = [
            'model' => 'gpt-4-0613',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'functions' => $functions,
            'function_call' => ['name' => 'suggest_lab_tests']
        ];

        return $data;
    }

    public static function summarizeReport($prompt)
    {
        $functions = [[
            "name" => "analyze_medical_report",
            "description" => "Analyzes a medical report to extract summary, diagnosis, possible medications, and red flags",
            "parameters" => [
                "type" => "object",
                "properties" => [
                    "summary" => [
                        "type" => "string",
                        "description" => "Concise summary of the report"
                    ],
                    "diagnosis" => [
                        "type" => "string",
                        "description" => "Most probable diagnosis based on report"
                    ],
                    "medications" => [
                        "type" => "array",
                        "items" => [ "type" => "string" ],
                        "description" => "Suggested medications"
                    ],
                    "red_flags" => [
                        "type" => "array",
                        "items" => [ "type" => "string" ],
                        "description" => "Urgent concerns or actions"
                    ],
                    "risk_factors" => [
                        "type" => "array",
                        "items" => [ "type" => "string" ],
                        "description" => "Possible risk factors identified in the report"
                    ]
                ],
                "required" => ["summary", "diagnosis", "medications", "red_flags", "risk_factors"]
            ]
        ]];
            
        $data = [
            'model' => 'gpt-4-0613',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a medical assistant that summarizes reports clearly.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'functions' => $functions,
            'function_call' => ['name' => 'analyze_medical_report'],
            'temperature' => 0.4,
        ];

        return $data;
    }

    public static function generateClinicalNotes($prompt)
    {
        $functions = [[
            "name" => "generate_clinical_note",
            'description' => 'Generates a SOAP-format clinical note from a patient transcript.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'chief_complaint' => ['type' => 'string'],
                    'subjective' => ['type' => 'string'],
                    'objective' => ['type' => 'string'],
                    'assessment' => ['type' => 'string'],
                    'plan' => ['type' => 'string'],
                    'vitals' => ['type' => 'string'],
                    'labs_to_order' => ['type' => 'string'],
                    'medications' => ['type' => 'string'],
                    'follow_up' => ['type' => 'string'],
                    'recommendations' => ['type' => 'string'],
                ],
                'required' => ['subjective', 'objective', 'assessment', 'plan']
            ]
        ]];
            
        $data = [
            'model' => 'gpt-4-0613',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a medical assistant generating SOAP clinical notes from transcribed patient conversations.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'functions' => $functions,
            'function_call' => 'auto',
        ];

        return $data;
    }
}
