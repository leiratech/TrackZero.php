<?php

declare(strict_types=1);

namespace leira\trackzero;

class TrackZeroClient
{
    public string $apiKey;
    public string $baseUrl = "https://api.trackzero.io";

    /**
     * Create a new TrackZero Instance
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function createAnalyticsSpace(string $analyticsSpaceId): bool
    {
        $ch = curl_init();
        $queryParams = array("analyticsSpaceId"=>$analyticsSpaceId);
        curl_setopt($ch, CURLOPT_URL,            $baseUrl . "/analyticsSpaces". '?' . http_build_query($queryParams) );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array("X-API-KEY"=>$this->apiKey)); 

        $result=curl_exec ($ch);
        echo($result);
    }
    public function echoPhrase(): string
    {
        return $this->apiKey2;
    }
}


$tz = new TrackZeroClient("Test Key");
$tz->createAnalyticsSpace("test");
