<?php

namespace App\Services;

use GuzzleHttp\Client;
use Carbon\Carbon;

class DhlService
{
    private $clientNumber;
    private $url;
    private $client;

    public function __construct(array $config)
    {
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
                'Authorization' => 'Basic ' . base64_encode("{$config['username']}:{$config['password']}"),
            ],
        ]);

        $this->url = $config['url'];
        $this->clientNumber = $config['client-number'];
    }

    public function createShipment($data)
    {
        $requestBody = [
            "plannedShippingDateAndTime"=> Carbon::parse($data['plannedShippingDateAndTime'])->format('Y-m-d\TH:i:s \G\M\T') . '+00:00',
            "pickup" => [
                "isRequested"=> false
            ],
            "productCode"=> "I",
            "getRateEstimates" => false,
            "accounts" =>
            [
                ["number" => "{$this->clientNumber}",
                "typeCode" => "shipper"
                ]
            ],
            "outputImageProperties" => [
                "printerDPI" => 300,
                "encodingFormat" => "pdf",
                "imageOptions" => [
                    [
                        "typeCode" => "waybillDoc",
                        "templateName" => "ARCH_8x4",
                        "isRequested" => true,
                        "hideAccountNumber" => false,
                        "numberOfCopies" => 1
                    ]
                ],
            "splitTransportAndWaybillDocLabels" => true,
            "allDocumentsInOneImage" => false,
            "splitDocumentsByPages" => true,
            "splitInvoiceAndReceipt" => true,
            "receiptAndLabelsInOneImage" => false
            ],
            "customerDetails" => [
                "shipperDetails" => [
                    "postalAddress" => [
                        "postalCode" => "{$data['shipperDetails']['postalCode']}",
                        "cityName" => $data['shipperDetails']['cityName'],
                        "countryCode" => $data['shipperDetails']['countryCode'],
                        "addressLine1" => $data['shipperDetails']['addressLine1'],
                        "countryName" => $data['shipperDetails']['countryName']
                    ],
                    "contactInformation" => [
                        "email" => $data['shipperDetails']['email'],
                        "phone" => $data['shipperDetails']['phone'],
                        "mobilePhone" => $data['shipperDetails']['mobilePhone'],
                        "companyName" => $data['shipperDetails']['companyName'],
                        "fullName" => $data['shipperDetails']['fullName'],
                    ]
                ],
                "receiverDetails" => [
                    "postalAddress" => [
                        "postalCode" => "{$data['receiverDetails']['postalCode']}",
                        "cityName" => $data['receiverDetails']['cityName'],
                        "countryCode" => $data['receiverDetails']['countryCode'],
                        "addressLine1" => $data['receiverDetails']['addressLine1'],
                        "countryName" => $data['receiverDetails']['countryName']
                    ],
                    "contactInformation" => [
                        "email" => $data['receiverDetails']['email'],
                        "phone" => $data['receiverDetails']['phone'],
                        "mobilePhone" => $data['receiverDetails']['mobilePhone'],
                        "companyName" => $data['receiverDetails']['companyName'],
                        "fullName" => $data['receiverDetails']['fullName'],
                    ]
                ]
            ],
            "content" => [
                "packages" => $this->preparePackages($data['packages']),
                "isCustomsDeclarable" => false,
                "description" => "Shipment Description",
                "incoterm" => "DAP",
                "unitOfMeasurement" => "metric"
            ]
        ];

        try {
            $response = $this->client->post($this->url, [
                'json' => $requestBody,
            ]);

            return $response->getBody()->getContents();

        } catch (\Exception $e) {
            return [
                'error' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    private function preparePackages(array $packages)
    {
        $result = [];

        foreach ($packages as $package) {
            $result[] = [
                'weight' => (int) $package['weight'],
                'dimensions' => [
                    'length' => (int) $package['length'],
                    'width' => (int) $package['width'],
                    'height' => (int) $package['height'],
                ],
                'description' => $package['description']
            ];
        }

        return $result;
    }

}
