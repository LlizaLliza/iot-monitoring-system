<?php

$apiUrl = 'http://127.0.0.1:8000/api/sensor-data';
$apiKey = 'secret-iot-key-2026'; 

$machines = [
    ['machine_code' => 'MSN-001', 'sensor_code' => 'MSN-001-TEMP'],
    ['machine_code' => 'MSN-002', 'sensor_code' => 'MSN-002-TEMP'],
];

$intervalSeconds = 7; 

echo "=== Device Simulator dimulai. Tekan Ctrl+C untuk berhenti. ===\n";

while (true) {
    foreach ($machines as $machine) {
        $status = (rand(0, 9) === 0) ? 'OFF' : 'ON'; 
        $payload = [
            'machine_code' => $machine['machine_code'],
            'sensor_code' => $machine['sensor_code'],
            'status' => $status,
            'metric_value' => round(mt_rand(300, 900) / 10, 1),
            'output_qty' => $status === 'ON' ? rand(5, 20) : 0,
            'recorded_at' => date('Y-m-d H:i:s'),
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $time = date('H:i:s');
        echo "[{$time}] {$machine['machine_code']} → status {$httpCode}: {$response}\n";
    }

    sleep($intervalSeconds);
}