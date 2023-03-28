<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Currency;
use App\Entity\ExchangeHistory;
use Doctrine\ORM\EntityManagerInterface;

class ExchangeRateController
{
    public function __construct(private HttpClientInterface $client, private LoggerInterface $logger, EntityManagerInterface $entityManager){
        $this->client = $client;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
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
        $to = $request->query->get('to') ?? 'inr';
        $from = $request->query->get('from') ?? 'eur';
        $amount = $request->query->get('amount') ?? 1;
        $rate = 1;

        # Convert the string to upper case for comparison
        $to = strtoupper($to);
        $from = strtoupper($from);

        # Allowable Input for "to"/"from"
        # To support more curreny e.g. "USD", add it in the below array.
        $allowed_input = array('INR','EUR','USD','AED');

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

        # Add Currency in the "Currency" table, if not exists.
        $currency_repo = $this->entityManager->getRepository(Currency::class);
        $from_var_in_db = $currency_repo->findOneBy(['currency' => $from]);
        $to_var_in_db = $currency_repo->findOneBy(['currency' => $to]);
        if(!$from_var_in_db){
            $currency = new Currency();
            $currency_in_db = $currency->setCurrency($from);
            $this->entityManager->persist($currency);
            $this->entityManager->flush();
        }
        if(!$to_var_in_db){
            $currency = new Currency();
            $currency_in_db = $currency->setCurrency($to);
            $this->entityManager->persist($currency);
            $this->entityManager->flush();
        }

        # Add all exchange rate info in the "ExchangeHistory" table
        # First, Get ID's of Currency from the "Currency" table
        $from_var_in_db = $currency_repo->findOneBy(['currency' => $from]);
        $to_var_in_db = $currency_repo->findOneBy(['currency' => $to]);
        $from_id = $from_var_in_db->getId();
        $to_id = $to_var_in_db->getId();
        
        # Create an object of ExchangeHistory and insert it in the database
        $exchange_history = new ExchangeHistory();
        $exchange_history->setConvertFrom($from_id);
        $exchange_history->setConvertTo($to_id);
        $exchange_history->setRate($rate);
        $exchange_history->setAmount($amount);
        $exchange_history->setTimestamp(time());
        $this->entityManager->persist($exchange_history);
        $this->entityManager->flush();

        # Collect All History which matches "from" & "to"
        $exchange_rate_repo = $this->entityManager->getRepository(ExchangeHistory::class);
        $exchange_rate_history = $exchange_rate_repo->findBy(['convert_from' => $from_id, 'convert_to' => $to_id],['id' => 'DESC' ]);

        # Response Array
        $response_arr = array('records' => array());

        foreach($exchange_rate_history as $record){
            $timestamp = $record->getTimestamp();
            $amount = $record->getAmount();
            $rate = $record->getRate();
            $converted_amount = $amount * $rate;
            array_push($response_arr['records'], "$amount $from = $converted_amount $to  |  Rate: $rate  |  Time: ".date('Y-m-d H:i:s',$timestamp));
        }

        return new JsonResponse($response_arr, 200);
    }
}
?>