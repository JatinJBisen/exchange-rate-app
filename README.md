# exchange-rate-app

This App helps to the get the exchange rate alongwith its history

1) Download the code on Ubuntu-22.04 (Note: Since it is not tested on other platform). 
   A folder "exchange-rate-app" will be downloaded.

2) Go to the folder using the command => $ cd exchange-rate-app

3) Run the command to build/fetch the images => $ ./build.sh   <br/>
       Note: This will build an image "main:1001". Also, it will fetch an image "postgres:latest" from docker registry
       
4) Run the command to start the docker containers => $ ./start_containers.sh    <br/>
       Note: To stop the docker containers in future, use the cmd => $ ./stop_containers.sh
       
5) Access the URL http://<domain-name>:8000/get_exchange_rate?from=eur&to=inr&amount=45   <br/>
   
6) About API   <br/>
   Method: GET    <br/>
   Parameters: To check exchange rate e.g. 45 EUR = ? INR    <br/>
               from = EUR   Optional, Default value: EUR     <br/>
               to = INR     Optional, Default value: INR     <br/>
               amount = 45  Optional, Default value: 1       <br/>
   Respone format: JSON    <br/>
