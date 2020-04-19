<?php
namespace Base\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Json\Json;
#use Zend\Stdlib\Parameters;

/**
 * This controller plugin is used for role-based access control (RBAC).
 */
class WindelAPIPlugin extends AbstractPlugin
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Authentication service.
     * @var Zend\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    public function doGETRequest($url, $request, $params=false, $queryUri=false)
    {
        $webserviceUrl = \Base\Module::API_VERBOSE.
                         \Base\Module::API_DOMAIN.
                         \Base\Module::API_ROOT_PATH.
                         $url;

        $uri = "";
        if ($params) {

            if(strstr($params, 'contrato'))
                $uri = "?contrato=".$params;

            if(strstr($params, 'cliente'))
                $uri = "?cliente=".$params;

        }
        if ($queryUri) {
            if (is_array($queryUri)) {

                $first=true;
                foreach ($queryUri as $key => $value) {
                    if ($first) {
                        $uri .= "?".$key."=".(string)$value;
                        $first = false;
                    } else {
                        $uri .= "&".$key."=".(string)$value;
                    }
                }

            } else {
                $uri = "/".$queryUri;
            }
        }
        $webserviceUrl = $webserviceUrl.$uri;

        $headers = ['Content-Type' => 'application/json; charset=utf-8',
                    'Accept'      => 'application/json'];

        if(\Base\Module::ENV == 'homologacao'){
            array_push($headers, ['Authorization' => 'Basic '.\Base\Module::ENV_KEY]);
        }

        $request->getHeaders()->addHeaders($headers);

        $request->setUri($webserviceUrl);
        $request->setMethod('GET');

        $client = new Client();

        $options = ['sslverifypeer' => false];
        $client->setOptions($options);

        $response = $client->dispatch($request);
        $datas = json_decode($response->getBody(), true);

        return $datas;
    }

    /**
     * Does a POST or PATCH request
     * @param [string] $url [url to rest]
     * @param [mixed]  $params [data to save]
     * @param boolean $id     [needed to do patch]
     */
    public function doPOSTRequest($url, $params=false, $id=false)
    {

        $webserviceUrl = \Base\Module::API_VERBOSE.
                         \Base\Module::API_DOMAIN.
                         \Base\Module::API_ROOT_PATH.
                         $url;

        if ($id)
            $webserviceUrl = $webserviceUrl."/".$id;

        $headers = ['Content-Type' => 'application/json; charset=utf-8',
                    'Accept'       => 'application/json'];

        if (\Base\Module::ENV == 'homologacao')
            array_push($headers, ['Authorization' => 'Basic '.\Base\Module::ENV_KEY]);

        $client = new Client($webserviceUrl);
        $client->setHeaders($headers);

        $options = ['sslverifypeer' => false];
        $client->setOptions($options);

        if($id)
            $client->setMethod(Request::METHOD_PATCH);
        else
            $client->setMethod(Request::METHOD_POST);

        if ($params) {
            $client->setParameterPost(array($params))
                   ->setRawBody(Json::encode($params));
        }
        $send = $client->send();
        $response = $send->getBody();

        return $response;
    }

}