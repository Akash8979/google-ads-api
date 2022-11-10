<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GetOpt\GetOpt;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentNames;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentParser;
use Google\Ads\GoogleAds\Examples\Utils\Helper;
use Google\Ads\GoogleAds\Lib\Configuration;
use Google\Ads\GoogleAds\Lib\V9\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V9\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\V9\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\V7\Services\CampaignServiceClient;
use Google\Ads\GoogleAds\V9\Common\ManualCpc;
use Google\Ads\GoogleAds\V9\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V9\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V9\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V9\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\V9\Resources\Campaign;
use Google\Ads\GoogleAds\V9\Resources\Campaign\NetworkSettings;
use Google\Ads\GoogleAds\V9\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V9\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V9\Services\CampaignOperation;
use Google\ApiCore\ApiException;

class compaignController extends Controller
{
    public function createCompaign(){
        $config = new Configuration([
            'CLIENT_ID' => "688588022308-cbfjgiprcakcfhbgoi1j2iajbo06j48e.apps.googleusercontent.com",
            'CLIENT_SECRET' => "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",
            'redirectUri' => "http://localhost:8000",
            'scope' => "https://www.googleapis.com/auth/adwords",
            // 'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
            // "tokenCredentialUri" => CredentialsLoader::TOKEN_CREDENTIAL_URI,
            "REFRESH_TOKEN"=>"1//0gmnznZzlPKieCgYIARAAGBASNwF-L9IrMDYaftkNoFZW6-JmJ72HTyFkb5S4tL-xUb7DP6ISKgdSWxYn7BQpROmoNNKB6Sg6BcU",
            "grant_type"=>"refresh_token"
            // 'state' => sha1(openssl_random_pseudo_bytes(1024))
        ]);
        // $authResonse->setGrantType("authorization_code");
        $customerId = "";
        $config2 = new Configuration([
            'DEVELOPER_TOKEN' => "688588022308-cbfjgiprcakcfhbgoi1j2iajbo06j48e.apps.googleusercontent.com",
            'LOGIN_CUSTOMER_ID' => "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",
            'LINKED_CUSTOMER_ID' => "2123213",
            'ENDPOINT' => "https://www.googleapis.com/auth/adwords",
            // 'state' => sha1(openssl_random_pseudo_bytes(1024))
        ]);
         // Generate a refreshable OAuth2 credential for authentication.
         $oAuth2Credential = (new OAuth2TokenBuilder())->fromEnvironmentVariablesConfiguration($config)->build();
        //  $res = $oAuth2Credential->fetchAuthToken();
          
         $googleAdsClient = (new GoogleAdsClientBuilder())->fromEnvironmentVariablesConfiguration($config2)->withOAuth2Credential($oAuth2Credential)->build();

            
            self::runExample($googleAdsClient,"1323");
     
       
   
    }
    public static function runExample(GoogleAdsClient $googleAdsClient, int $customerId)
    {
        // Creates a single shared budget to be used by the campaigns added below.
        // $budgetResourceName = self::addCampaignBudget($googleAdsClient, $customerId);

        // Configures the campaign network options.
        $networkSettings = new NetworkSettings([
            'target_google_search' => true,
            'target_search_network' => true,
            // Enables Display Expansion on Search campaigns. See
            // https://support.google.com/google-ads/answer/7193800 to learn more.
            'target_content_network' => true,
            'target_partner_search_network' => false
        ]);

        $campaignOperations = [];
        for ($i = 0; $i < 2; $i++) {
            // Creates a campaign.
            // [START add_campaigns_1]
            $campaign = new Campaign([
                'name' => 'Interplanetary Cruise #' . $i,
                'advertising_channel_type' => AdvertisingChannelType::SEARCH,
                // Recommendation: Set the campaign to PAUSED when creating it to prevent
                // the ads from immediately serving. Set to ENABLED once you've added
                // targeting and the ads are ready to serve.
                'status' => CampaignStatus::PAUSED,
                // Sets the bidding strategy and budget.
                // 'manual_cpc' => new ManualCpc(),
                // 'campaign_budget' => $budgetResourceName,
                // Adds the network settings configured above.
                'network_settings' => $networkSettings,
                // Optional: Sets the start and end dates.
                'start_date' => date('Ymd', strtotime('+1 day')),
                'end_date' => date('Ymd', strtotime('+1 month'))
            ]);
            // [END add_campaigns_1]

            // Creates a campaign operation.
            $campaignOperation = new CampaignOperation();
            $campaignOperation->setCreate($campaign);
            $campaignOperations[] = $campaignOperation;
        }
// dd($campaignOperation);
        // Issues a mutate request to add campaigns.
        $campaignServiceClient = $googleAdsClient->getCampaignServiceClient();
        
        $response = $campaignServiceClient->mutateCampaigns($customerId, $campaignOperations);
        dd($campaignServiceClient);
        printf("Added %d campaigns:%s", $response->getResults()->count(), PHP_EOL);

        foreach ($response->getResults() as $addedCampaign) {
            /** @var Campaign $addedCampaign */
            print "{$addedCampaign->getResourceName()}" . PHP_EOL;
        }
    }

    private static function addCampaignBudget(GoogleAdsClient $googleAdsClient, int $customerId)
    {
        // Creates a campaign budget.
        $budget = new CampaignBudget([
            'name' => 'Interplanetary Cruise Budget #',
            'delivery_method' => BudgetDeliveryMethod::STANDARD,
            'amount_micros' => 500000
        ]);

        // Creates a campaign budget operation.
        $campaignBudgetOperation = new CampaignBudgetOperation();
        $campaignBudgetOperation->setCreate($budget);

        // Issues a mutate request.
        $campaignBudgetServiceClient = $googleAdsClient->getCampaignBudgetServiceClient();
        $response = $campaignBudgetServiceClient->mutateCampaignBudgets(
            $customerId,
            [$campaignBudgetOperation]
        );

        /** @var CampaignBudget $addedBudget */
        $addedBudget = $response->getResults()[0];
        printf("Added budget named '%s'%s", $addedBudget->getResourceName(), PHP_EOL);

        return $addedBudget->getResourceName();
    }
}
