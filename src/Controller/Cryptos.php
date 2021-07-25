<?php
namespace Snowdog\Academy\Controller;
use Snowdog\Academy\Model\Cryptocurrency;
use Snowdog\Academy\Model\CryptocurrencyManager;
use Snowdog\Academy\Model\User;
use Snowdog\Academy\Model\UserCryptocurrencyManager;
use Snowdog\Academy\Model\UserManager;
class Cryptos
{
    private CryptocurrencyManager $cryptocurrencyManager;
    private UserCryptocurrencyManager $userCryptocurrencyManager;
    private UserManager $userManager;
    private ?Cryptocurrency $cryptocurrency = null;

    public function __construct(
        CryptocurrencyManager $cryptocurrencyManager,
        UserCryptocurrencyManager $userCryptocurrencyManager,
        UserManager $userManager
    ) {
        $this->cryptocurrencyManager = $cryptocurrencyManager;
        $this->userCryptocurrencyManager = $userCryptocurrencyManager;
        $this->userManager = $userManager;
    }

    public function index(): void
    {
        require __DIR__ . '/../view/cryptos/index.phtml';
    }

    public function buy(string $id): void
    {
        $this->verifyLogin();
        $this->verifyCryptocurrency($id);
        require __DIR__ . '/../view/cryptos/buy.phtml';
    }

    public function buyPost(string $id): void
    {
        try {
            $user = $this->verifyLogin();
            $crypto = $this->verifyCryptocurrency($id);
            $amount = $_POST['amount'];
            $this->userCryptocurrencyManager->addCryptocurrencyToUser($user->getId(), $crypto, $amount);
            $_SESSION['flash'] = "You have bought {$amount} {$crypto->getSymbol()} successfully";
        } catch (\Exception $e) {
            $_SESSION['flash'] = "There has been an error processing your request: {$e->getMessage()}";
        }
        header('Location: /cryptos');
    }
    public function sell(string $id): void
    {
        $location = 'account';
        $this->verifyLogin($location);
        $this->verifyCryptocurrency($id, $location);
        require __DIR__ . '/../view/cryptos/sell.phtml';
    }
    public function sellPost(string $id): void
    {
        try {
            $location = 'account';
            $user = $this->verifyLogin($location);
            $crypto = $this->verifyCryptocurrency($id, $location);
            $amount = $_POST['amount'];
            $this->userCryptocurrencyManager->subtractCryptocurrencyFromUser($user->getId(), $crypto, $amount);
            $_SESSION['flash'] = "You have sold {$amount} {$crypto->getSymbol()} successfully";
        } catch (\Exception $e) {
            $_SESSION['flash'] = "There has been an error processing your request: {$e->getMessage()}";
        }
        header('Location: /cryptos');
    }
    public function getCryptocurrencies(): array
    {
        return $this->cryptocurrencyManager->getAllCryptocurrencies();
    }
    private function verifyLogin($location = 'cryptos'): ?User
    {
        $user = $this->userManager->getByLogin((string) $_SESSION['login']);
        if (!$user) {
            $_SESSION['flash'] = 'Please log in again';
            header('Location: /' . $location);
            return null;
        }
        return $user;
    }
    private function verifyCryptocurrency($id, $location = 'crypto'): ?Cryptocurrency
    {
        if (!$this->cryptocurrency || ($this->cryptocurrency && $this->cryptocurrency->getId() != $id)) {
            $cryptocurrency = $this->cryptocurrencyManager->getCryptocurrencyById($id);
            if (!$cryptocurrency) {
                $_SESSION['flash'] = 'We can not process the selected Cryptocurrency';
                header('Location: /' . $location);
                return null;
            }
            $this->cryptocurrency = $cryptocurrency;
        }
        return $this->cryptocurrency;
    }
}