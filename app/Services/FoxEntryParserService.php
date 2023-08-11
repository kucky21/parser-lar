<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder as ElasticsearchClientBuilder;
use GuzzleHttp\Client;
use DOMXPath;
use DOMDocument;

class FoxEntryParserService
{
    public function parseAndIndex()
    {
        // Create an HTTP client to make the GET request
        $httpClient = new Client(['verify' => false]);
        $response = $httpClient->get('https://foxentry.com/cs/cenik-api');
        $html = $response->getBody()->getContents();

        // Create a DOMDocument and load the HTML content
        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        // Create a DOMXPath object to query the HTML structure
        $xpath = new DOMXPath($doc);
        
        // Query the table rows containing service information
        $tableRows = $xpath->query('//table[@id="price-table"]/tbody/tr');

        $services = [];

        foreach ($tableRows as $row) {
            $columns = $row->getElementsByTagName('td');
        
            // Check if the row has at least 3 columns (name, price 1, price 2)
            if ($columns->length >= 3) {
                // Extract service name and description from the first column
                $serviceName = $columns->item(0)->getElementsByTagName('span')->item(0)->textContent;
                $description = $columns->item(0)->getElementsByTagName('div')->item(0)->textContent;
        
                $servicePrices = [];
        
                // Loop through the next two columns to extract prices
                for ($i = 1; $i <= 2; $i++) {
                    $price = trim($columns->item($i)->textContent);
                    $servicePrices[] = $price;
                }
        
                // Add service data to the array if the service name is not empty
                if (!empty($serviceName)) {
                    $services[] = [
                        'name' => trim($serviceName),
                        'description' => trim($description),
                        'prices' => $servicePrices,
                    ];
                }
            }
        }

        // Create an Elasticsearch client
        $elasticsearchClient = ElasticsearchClientBuilder::create()->setHosts(['elasticsearch:9200'])->build();

        // Index service data into Elasticsearch
        foreach ($services as $service) {
            $document = [
                'name' => $service['name'],
                'description' => $service['description'],
                'prices' => $service['prices'], 
            ];
           
            $params = [
                'index' => 'foxentry',
                'id' => md5($service['name']),
                'body' => $document,
            ];

            $elasticsearchClient->index($params);
        }
        //uncomment for data check - debug
        //var_dump($params);
    }
}
