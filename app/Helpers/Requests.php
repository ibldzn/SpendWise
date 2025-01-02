<?php

namespace App\Helpers;

class Requests
{
    /**
     * Parse PUT request with multipart/form-data.
     *
     * @return array Parsed data including form fields and uploaded files.
     */
    public static function parsePutData(): array
    {
        // Check Content-Type header for boundary
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!preg_match('/boundary=(.*)$/', $contentType, $matches)) {
            throw new \RuntimeException('No boundary found in Content-Type');
        }
        $boundary = $matches[1];

        // Read raw input data
        $rawData = file_get_contents('php://input');
        if (!$rawData) {
            throw new \RuntimeException('No PUT data received');
        }

        // Split data into parts
        $parts = explode('--' . $boundary, $rawData);
        array_pop($parts); // Remove the last boundary element

        $parsedData = [
            'fields' => [],
            'files'  => [],
        ];

        // Parse each part
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part) || $part === '--') {
                continue;
            }

            // Separate headers and body
            list($headers, $body) = explode("\r\n\r\n", $part, 2);
            $headers = explode("\r\n", $headers);

            // Extract Content-Disposition header
            $contentDisposition = '';
            foreach ($headers as $header) {
                if (stripos($header, 'Content-Disposition') !== false) {
                    $contentDisposition = $header;
                    break;
                }
            }

            if (!$contentDisposition) {
                continue;
            }

            // Extract 'name' and optionally 'filename'
            preg_match('/name="([^"]+)"/', $contentDisposition, $nameMatch);
            $name = $nameMatch[1] ?? null;

            preg_match('/filename="([^"]+)"/', $contentDisposition, $fileMatch);
            $filename = $fileMatch[1] ?? null;

            if ($filename) {
                // Handle file upload
                $tempPath = tempnam(sys_get_temp_dir(), 'putfile_');
                file_put_contents($tempPath, $body);
                $parsedData['files'][$name] = [
                    'name'     => $filename,
                    'tmp_name' => $tempPath,
                    'size'     => strlen($body),
                    'type'     => mime_content_type($tempPath),
                ];
            } elseif ($name) {
                // Handle form field
                $parsedData['fields'][$name] = trim($body);
            }
        }

        return $parsedData;
    }
}
