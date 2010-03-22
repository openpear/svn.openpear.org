<?php
namespace openpear\services\calil;

class Calil
{

    public function getHttpClient()
    {
        $client = new Zend_Http_Client();
        $client->resetParameters();

        return $client;
    }


    public function send(Query $query)
    {
        // polling
        $query->interval();

        $client = $this->getHttpClient();
        $client->setParameterGet($query->toArray());

        $responseBody = $client->request()->getBody();

        $result = json_decode($responseBody);

        if (isset($result['session'])) {
            $query->setSeesion($result['session']);
        }
        if (isset($result['continue'])) {
            $query->setPolling(true);
        }

        return $result;
    }
}
