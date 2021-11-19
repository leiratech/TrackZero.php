<?php

declare(strict_types=1);

namespace leira\trackzero;
use Exception;

class TrackZeroClient
{
    public string $apiKey;
    private string $baseUrl = "https://betaapi.trackzero.io";
    var $allowedIdTypes = array('integer', 'double', 'string');
    var $allowedValues = array('integer', 'double', 'string', 'DateTime', 'boolean');
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
        $requestUrl = $this->baseUrl . "/analyticsSpaces". '?' . http_build_query(array("analyticsSpaceId"=>$analyticsSpaceId));
        curl_setopt($ch, CURLOPT_URL,            $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array("X-API-KEY:" . $this->apiKey, "Content-Length: 0")); 
        curl_setopt($ch, CURLOPT_HEADER,         true);
        curl_setopt($ch, CURLOPT_NOBODY,         true);
        $result=curl_exec ($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode == 200;
    }

    public function deleteAnalyticsSpace(string $analyticsSpaceId): bool
    {
        $ch = curl_init();
        $requestUrl = $this->baseUrl . "/analyticsSpaces". '?' . http_build_query(array("analyticsSpaceId"=>$analyticsSpaceId));
        curl_setopt($ch, CURLOPT_URL,            $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,           "DELETE");
        #curl_setopt($ch, CURLOPT_POSTFIELDS,     "body goes here" ); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array("X-API-KEY:" . $this->apiKey, "Content-Length: 0")); 
        curl_setopt($ch, CURLOPT_HEADER,         true);
        curl_setopt($ch, CURLOPT_NOBODY,         true);
        $result=curl_exec ($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode == 200;
    }


    public function deleteEntity(string $analyticsSpaceId, string $entityType, object $entityId): bool
    {
        //if (!in_array(gettype($entityId, $allowedIdTypes))
        //    throw ValueError("entityId type is not valid.");
        
        $ch = curl_init();
        $body = json_encode(array("id"=>$entityId, "type"=> $entityType));
        $requestUrl = $this->baseUrl . "/tracking/entities". '?' . http_build_query(array("analyticsSpaceId"=>$analyticsSpaceId));
        curl_setopt($ch, CURLOPT_URL,            $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $body); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array("X-API-KEY:" . $this->apiKey, "Content-Length: 0")); 
        curl_setopt($ch, CURLOPT_HEADER,         true);
        $result=curl_exec ($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode == 200;
    }

    public function echoPhrase(): string
    {
        return $this->apiKey2;
    }
}

class Entity
{
    private object $id;
    private string $type;
    private $customAttributes = array();
    var $allowedIdTypes = array('integer', 'double', 'string');
    var $allowedValues = array('integer', 'double', 'string', 'DateTime', 'boolean');
    public function __construct(string $entity_type, $entity_id)
    {
        $this->validate_id_type($entity_id);
        $this->id = (object)$entity_id;
        $this->type = $entity_type;
    }

    public function add_attribute(string $attribute_name, $value) : Entity
    {
        if (!in_array(gettype($value, $allowedIdTypes)))
        {
            throw new Exception("entityId type is not valid.");
        }
        
        $customAttributes[$attribute_name] = $value;
        return $this;
    }

    public function is_value_type_allowed($value) : bool
    {
        $t = gettype($value);
        if ($t  == "object")
        {
            $class = get_class($value);
            if (!in_array($class, $this->allowedValues))
            {
                throw new Exception("The type of the value you entered is not supported.");
            }
        }
        else if (!in_array($t, $this->allowedValues))
        {
            throw new Exception("The type of the value you entered is not supported.");
        }
        return true;
    }

    private function validate_id_type($value)
    {
        if (!in_array(gettype($value), $this->allowedIdTypes))
            throw new Exception("The type of the Id entered is not supported.");
        return true;
    }
}
