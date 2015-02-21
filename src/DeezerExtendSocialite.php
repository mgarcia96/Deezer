<?php
namespace SocialiteProviders\Deezer;

use SocialiteProviders\Manager\SocialiteWasCalled;

class DeezerExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('deezer', __NAMESPACE__.'\Provider');
    }
}
