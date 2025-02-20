<?php
namespace Snowdog\Academy\Controller;
use Snowdog\Academy\Model\User;
use Snowdog\Academy\Model\UserManager;
class Funds
{
    private UserManager $userManager;
    private User $user;

    public function __construct(UserManager $userManager)

    {
        $this->userManager = $userManager;
    }
    public function add(): void
    {
        $user = $this->userManager->getByLogin($_SESSION['login']);
        if (!$user) {
            header('Location: /login');
        }
        $this->user = $user;
        require __DIR__ . '/../view/funds/add.phtml';
    }

    public function addPost(): void
    {
        try {
            $user = $this->userManager->getByLogin($_SESSION['login']);
            if (!$user) {
                header('Location: /login');
            }

            $this->userManager->addFundsToCustomer($user->getId(), $_POST['amount']);
            $_SESSION['flash'] = 'Funds added successfully';
        } catch (\Exception $e) {
            $_SESSION['flash'] = 'We could not add your selected funds';
        }
        header('Location: /account');
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
}