<?php
namespace SocialiteProviders\Deezer;

use SocialiteProviders\Manager\SocialiteWasCalled;

class DeezerExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'deezer', __NAMESPACE__.'\Provider'
        );
    }
}
