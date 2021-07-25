<?php
namespace Snowdog\Academy\Command;
use Exception;
use Snowdog\Academy\Core\Migration;
use Snowdog\Academy\Model\CryptocurrencyManager;
use Symfony\Component\Console\Output\OutputInterface;
class UpdatePrices
{
    private CryptocurrencyManager $cryptocurrencyManager;
    const API_URL = 'api.coincap.io/v2/assets';
    public function __construct(CryptocurrencyManager $cryptocurrencyManager)
    {
        $this->cryptocurrencyManager = $cryptocurrencyManager;
    }
    public function __invoke(OutputInterface $output)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response);
        foreach ((array)$result->data as $crypto) {
            $this->cryptocurrencyManager->updatePrice($crypto->id, $crypto->priceUsd);
        }
    }
}