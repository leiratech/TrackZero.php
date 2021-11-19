<?php

declare(strict_types=1);

namespace leira\trackzero;
use Exception;

class TrackZeroClient extends verfication_provider
{
    public string $apiKey;
    private string $baseUrl = "https://api.trackzero.io";
    var $allowedIdTypes = array('integer', 'double', 'string');
    var $allowedValues = array('integer', 'double', 'string', 'DateTime', 'boolean');
    /**
     * Create a new TrackZero Instance
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    
    public function create_analytics_space(string $analytics_space_id): bool
    {
        $ch = curl_init();
        $requestUrl = $this->baseUrl . "/analyticsSpaces". '?' . http_build_query(array("analyticsSpaceId"=>$analytics_space_id));
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

    public function delete_analytics_space(string $analytics_space_id): bool
    {
        $ch = curl_init();
        $requestUrl = $this->baseUrl . "/analyticsSpaces". '?' . http_build_query(array("analyticsSpaceId"=>$analytics_space_id));
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

    public function upsert_entity(string $analytics_space_id, Entity $entity): bool
    {
        $ch = curl_init();
        $body = json_encode($this->json_prepare($entity));
        $requestUrl = $this->baseUrl . "/tracking/entities". '?' . http_build_query(array("analyticsSpaceId"=>$analytics_space_id));
        curl_setopt($ch, CURLOPT_URL,            $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $body); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array("X-API-KEY:" . $this->apiKey, "Content-Length:" . strlen($body), "Content-Type:application/json")); 
        curl_setopt($ch, CURLOPT_HEADER,         true);
        $result=curl_exec ($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode == 200;
    }

    public function delete_entity(string $analytics_space_id, string $entity_type, $entity_id): bool
    {
        $this->validate_id_type($entity_id);
        $ch = curl_init();
        $body = json_encode(array("id"=>$entity_id, "type"=> $entity_type));
        $requestUrl = $this->baseUrl . "/tracking/entities". '?' . http_build_query(array("analyticsSpaceId"=>$analytics_space_id));
        curl_setopt($ch, CURLOPT_URL,            $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $body); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array("X-API-KEY:" . $this->apiKey, "Content-Length: " . strlen($body), "Content-Type: application/json")); 
        curl_setopt($ch, CURLOPT_HEADER,         true);
        $result=curl_exec ($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode == 200;
    }

    public function create_analytics_space_session(string $analytics_space_id, int $ttl ): analytics_space_session
    {
        if ($ttl > 3600 || $ttl < 300)
            throw new Exception("TTL must be between 300 and 3600 seconds");
        $ch = curl_init();
        $requestUrl = $this->baseUrl . "/analyticsSpaces/session". '?' . http_build_query(array("analyticsSpaceId"=>$analytics_space_id, "ttl"=>$ttl));
        curl_setopt($ch, CURLOPT_URL,            $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array("X-API-KEY:" . $this->apiKey, "Content-Length: 0")); 
        curl_setopt($ch, CURLOPT_HEADER,         true);
        $result=curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $header_size = $curl_info["header_size"];
        $body = substr($result, $header_size);
        $s = json_decode($body, true);

        if ($httpCode == 200)
            return new analytics_space_session($s["sessionKey"], $s["url"]);    
        else
            return null;
    }

    function json_prepare($data_to_serialize)
    {
        array_walk_recursive($data_to_serialize, function(&$value){
            if ($value instanceof \DateTime)
            {
                $value = $value->format(\DateTime::ISO8601);
            }
        });

        return $data_to_serialize;
    }
}

class analytics_space_session
{
    public string $session_key;
    public string $url;
    public function __construct(string $key, string $url)
    {
        $this->session_key = $key;
        $this->url = $url;
    }
}

class Entity extends verfication_provider
{
    public $id;
    public string $type;
    public $customAttributes = array();
    
    public function __construct(string $entity_type, $entity_id)
    {
        $this->validate_id_type($entity_id);
        $this->id = $entity_id;
        $this->type = $entity_type;
    }

    public function add_attribute(string $attribute_name, $value) : self
    {
        $this->validate_value_type($value);
        
        $this->customAttributes[$attribute_name] = $value;
        return $this;
    }

    public function add_entity_reference_attribute(string $attribute_name, string $reference_entity_type, $reference_entity_id) : self
    {
        $this->validate_id_type($reference_entity_id);
        if (array_key_exists($attribute_name, $this->customAttributes))
            array_push($this->customAttributes[$attribute_name], array("id"=>$reference_entity_id, "type"=> $reference_entity_type));
        else
            $this->customAttributes[$attribute_name] = array(array("id"=>$reference_entity_id, "type"=> $reference_entity_type));

        return $this;
    }
}

class verfication_provider
{
    private  $allowedIdTypes = array('integer', 'double', 'string');
    private  $allowedValues = array('integer', 'double', 'string', 'DateTime', 'boolean');
    public function validate_value_type($value) : bool
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

    public function validate_id_type($value)
    {
        if (!in_array(gettype($value), $this->allowedIdTypes))
            throw new Exception("The type of the Id entered is not supported.");
        return true;
    }
}


