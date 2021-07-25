<?php
namespace Snowdog\Academy\Menu;



class AddFundsMenu extends AbstractMenu
{
    public function getHref(): string
    {
        return '/funds/add';

    }
    public function getLabel(): string
    {
        return 'Add Funds';
    }
    public function isVisible(): bool
    {
        return !!$_SESSION['login'];
    }
}