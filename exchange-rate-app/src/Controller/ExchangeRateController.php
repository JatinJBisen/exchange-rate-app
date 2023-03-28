<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ExchangeRateController
{
    public function __construct(private HttpClientInterface $client, private LoggerInterface $logger){
        $this->client = $client;
        $this->logger = $logger;
    }

    /*
        To check exchange rate e.g. 45 EUR = ? INR
        use the below params:
        $from = EUR
        $to = INR
        $amount = 45
    */
    public function getExchangeRate(Request $request): Response
    {
        # Get Input
        # If any input is not provided, assign the default values.
        $to = $request->query->get('to') ?? 'eur';
        $from = $request->query->get('from') ?? 'inr';
        $amount = $request->query->get('amount') ?? 1;
        $rate = 1;

        # Convert the string to upper case for comparison
        $to = strtoupper($to);
        $from = strtoupper($from);

        # Allowable Input for "to"/"from"
        # To support more curreny e.g. "USD", add it in the below array.
        $allowed_input = array('INR','EUR');

        # Validate "to"
        if(!in_array($to,$allowed_input)){
            return new JsonResponse([ 'error' => 'Invalid Input', 'error_message' => "The parameter 'to' must be a valid currency name. Currently supported are ".implode(' / ',$allowed_input) ], 403);
        }

        # Validate "from"
        if(!in_array($from,$allowed_input)){
            return new JsonResponse([ 'error' => 'Invalid Input', 'error_message' => "The parameter 'from' must be a valid currency name. Currently supported are ".implode(' / ',$allowed_input) ], 403);
        }

        # Validate "amount"
        if(!(is_numeric($amount) && $amount >= 1)){
            return new JsonResponse([ 'error' => 'Invalid Input', 'error_message' => "The parameter 'amount' must be a number greater than zero" ], 403);
        }

        # Send request to the third party api and collect its response
        $response = $this->client->request(
            'GET',
            'https://v6.exchangerate-api.com/v6/359e560fa21acd188d1eb8d4/latest/'.$from
        );

        # If third party api fails, send an error response
        $statusCode = $response->getStatusCode();
        if($statusCode != 200){
            return new JsonResponse([ 'error' => 'Backend Error', 'error_message' => "An error has occurred while getting the exchange rate from third party api" ], 500);
        }

        # Get the exchange rate from the response of third party api
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();
        if(array_key_exists('conversion_rates',$content) && array_key_exists($to,$content['conversion_rates'])){
            $rate = $content['conversion_rates'][$to];
        }else{
            return new JsonResponse([ 'error' => 'Backend Error', 'error_message' => "The exchange rate was not found in the response of thirt party api" ], 400);
        }

        // $this->logger->info(print_r($content,1));

        # Calculate the converted_amount
        $converted_amount = $rate * $amount;

        return new Response(
            "<html><body> $amount $from = $converted_amount $to</body></html>"
        );
    }
}
?>