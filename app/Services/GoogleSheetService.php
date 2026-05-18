<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleSheetService
{
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setApplicationName('Laravel Google Sheets');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('app/playaxisclub.json'));
        $client->setAccessType('offline');

        $this->service = new Google_Service_Sheets($client);

        // 👉 Put your Sheet ID here
        $this->spreadsheetId = env('GOOGLE_SHEET_ID');
    }

    public function append(array $row, string $sheetName = 'Sheet1')
    {
        $range = $sheetName . '!A1';

        $body = new Google_Service_Sheets_ValueRange([
            'values' => [$row],
        ]);

        $params = [
            'valueInputOption' => 'RAW',
        ];

        $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }
}