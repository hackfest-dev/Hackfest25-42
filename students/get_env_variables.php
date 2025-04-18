<?php
session_start();
if (!isset($_SESSION["uname"])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

// Use credentials directly from nft/.env file
// No need to search for environment variables

// Direct hardcoded values for Pinata API
$response = [
    'PINATA_JWT' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySW5mb3JtYXRpb24iOnsiaWQiOiI2YzI4MmZkNS03NTgzLTQxY2UtODZkMS0yNzE0ODk4MjY1MjEiLCJlbWFpbCI6InJhamF0LmdvbmRrYXJAZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsInBpbl9wb2xpY3kiOnsicmVnaW9ucyI6W3siZGVzaXJlZFJlcGxpY2F0aW9uQ291bnQiOjEsImlkIjoiRlJBMSJ9LHsiZGVzaXJlZFJlcGxpY2F0aW9uQ291bnQiOjEsImlkIjoiTllDMSJ9XSwidmVyc2lvbiI6MX0sIm1mYV9lbmFibGVkIjpmYWxzZSwic3RhdHVzIjoiQUNUSVZFIn0sImF1dGhlbnRpY2F0aW9uVHlwZSI6InNjb3BlZEtleSIsInNjb3BlZEtleUtleSI6ImZhZjEwMWQ3NWIyN2ExOTllMGJlIiwic2NvcGVkS2V5U2VjcmV0IjoiOWQ5MWQwYTIzNjA3NDZhZTg2Yjg4ZDlhNmFmZDZiZDE1YjY3YWFmZjFmNzYxOTUzZDEzMmVlYjQ1Yzc1MGMyOCIsImV4cCI6MTc3NjAyODQ2MH0.giXHS5hpxPckq8epgzC2lG0MbBXYqkrw96sCXHcqy1A',
    'PINATA_API_KEY' => 'faf101d75b27a199e0be',
    'PINATA_SECRET_KEY' => '9d91d0a2360746ae86b88d9a6afd6bd15b67aaff1f761953d132eeb45c750c28',
    'diagnostic' => [
        'using_fallback' => false,
        'source' => 'direct_credentials',
        'info' => 'Using credentials directly from code'
    ]
];

echo json_encode($response);
?> 