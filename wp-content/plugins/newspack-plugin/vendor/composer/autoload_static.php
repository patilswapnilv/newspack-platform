<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2abb4969ff5d90cb7a20f93ebaa44a54
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'j' => 
        array (
            'joshtronic\\' => 11,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
            'Psr\\Cache\\' => 10,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
            'Google\\Auth\\' => 12,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'joshtronic\\' => 
        array (
            0 => __DIR__ . '/..' . '/joshtronic/php-loremipsum/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
            1 => __DIR__ . '/..' . '/psr/http-factory/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'Psr\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/cache/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Google\\Auth\\' => 
        array (
            0 => __DIR__ . '/..' . '/google/auth/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Newspack\\AMP_Enhancements' => __DIR__ . '/../..' . '/includes/class-amp-enhancements.php',
        'Newspack\\AMP_Polyfills' => __DIR__ . '/../..' . '/includes/polyfills/class-amp-polyfills.php',
        'Newspack\\API' => __DIR__ . '/../..' . '/includes/class-api.php',
        'Newspack\\API\\Plugins_Controller' => __DIR__ . '/../..' . '/includes/api/class-plugins-controller.php',
        'Newspack\\API\\Wizards_Controller' => __DIR__ . '/../..' . '/includes/api/class-wizards-controller.php',
        'Newspack\\Accessibility_Statement_Page' => __DIR__ . '/../..' . '/includes/advanced-settings/class-accessibility-statement-page.php',
        'Newspack\\Admin_Plugins_Screen' => __DIR__ . '/../..' . '/includes/class-admin-plugins-screen.php',
        'Newspack\\Advertising_Display_Ads' => __DIR__ . '/../..' . '/includes/wizards/advertising/class-advertising-display-ads.php',
        'Newspack\\Advertising_Sponsors' => __DIR__ . '/../..' . '/includes/wizards/advertising/class-advertising-sponsors.php',
        'Newspack\\Audience_Campaigns' => __DIR__ . '/../..' . '/includes/wizards/audience/class-audience-campaigns.php',
        'Newspack\\Audience_Donations' => __DIR__ . '/../..' . '/includes/wizards/audience/class-audience-donations.php',
        'Newspack\\Audience_Subscriptions' => __DIR__ . '/../..' . '/includes/wizards/audience/class-audience-subscriptions.php',
        'Newspack\\Audience_Wizard' => __DIR__ . '/../..' . '/includes/wizards/audience/class-audience-wizard.php',
        'Newspack\\Author_Filter' => __DIR__ . '/../..' . '/includes/author-filter/class-author-filter.php',
        'Newspack\\Authors_Custom_Fields' => __DIR__ . '/../..' . '/includes/authors/class-authors-custom-fields.php',
        'Newspack\\Blocks' => __DIR__ . '/../..' . '/includes/class-blocks.php',
        'Newspack\\Bylines' => __DIR__ . '/../..' . '/includes/bylines/class-bylines.php',
        'Newspack\\CLI\\Co_Authors_Plus' => __DIR__ . '/../..' . '/includes/cli/class-co-authors-plus.php',
        'Newspack\\CLI\\Initializer' => __DIR__ . '/../..' . '/includes/cli/class-initializer.php',
        'Newspack\\CLI\\Mailchimp' => __DIR__ . '/../..' . '/includes/cli/class-mailchimp.php',
        'Newspack\\CLI\\Optional_Modules' => __DIR__ . '/../..' . '/includes/cli/class-optional-modules.php',
        'Newspack\\CLI\\RAS' => __DIR__ . '/../..' . '/includes/cli/class-ras.php',
        'Newspack\\CLI\\RAS_ESP_Sync' => __DIR__ . '/../..' . '/includes/cli/class-ras-esp-sync.php',
        'Newspack\\CLI\\Setup' => __DIR__ . '/../..' . '/includes/cli/class-setup.php',
        'Newspack\\CLI\\WooCommerce_Subscriptions' => __DIR__ . '/../..' . '/includes/cli/class-woocommerce-subscriptions.php',
        'Newspack\\Category_Pager' => __DIR__ . '/../..' . '/includes/class-category-pager.php',
        'Newspack\\Collections' => __DIR__ . '/../..' . '/includes/optional-modules/class-collections.php',
        'Newspack\\Components_Demo' => __DIR__ . '/../..' . '/includes/wizards/class-components-demo.php',
        'Newspack\\Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-configuration-manager.php',
        'Newspack\\Configuration_Managers' => __DIR__ . '/../..' . '/includes/configuration_managers/class-configuration-managers.php',
        'Newspack\\Corrections' => __DIR__ . '/../..' . '/includes/corrections/class-corrections.php',
        'Newspack\\Data_Events' => __DIR__ . '/../..' . '/includes/data-events/class-data-events.php',
        'Newspack\\Data_Events\\Api' => __DIR__ . '/../..' . '/includes/data-events/class-api.php',
        'Newspack\\Data_Events\\Connectors\\ESP_Connector' => __DIR__ . '/../..' . '/includes/data-events/connectors/class-esp-connector.php',
        'Newspack\\Data_Events\\Memberships' => __DIR__ . '/../..' . '/includes/data-events/class-memberships.php',
        'Newspack\\Data_Events\\Popups' => __DIR__ . '/../..' . '/includes/data-events/class-popups.php',
        'Newspack\\Data_Events\\Utils' => __DIR__ . '/../..' . '/includes/data-events/class-utils.php',
        'Newspack\\Data_Events\\Webhooks' => __DIR__ . '/../..' . '/includes/data-events/class-webhooks.php',
        'Newspack\\Data_Events\\Woo_User_Registration' => __DIR__ . '/../..' . '/includes/data-events/class-woo-user-registration.php',
        'Newspack\\Default_Image' => __DIR__ . '/../..' . '/includes/class-default-image.php',
        'Newspack\\Donations' => __DIR__ . '/../..' . '/includes/class-donations.php',
        'Newspack\\Emails' => __DIR__ . '/../..' . '/includes/emails/class-emails.php',
        'Newspack\\Everlit_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-everlit-configuration-manager.php',
        'Newspack\\GoogleSiteKit' => __DIR__ . '/../..' . '/includes/plugins/google-site-kit/class-googlesitekit.php',
        'Newspack\\GoogleSiteKitAnalytics' => __DIR__ . '/../..' . '/includes/plugins/google-site-kit/class-googlesitekitanalytics.php',
        'Newspack\\GoogleSiteKit_Logger' => __DIR__ . '/../..' . '/includes/plugins/google-site-kit/class-googlesitekit-logger.php',
        'Newspack\\Google_Login' => __DIR__ . '/../..' . '/includes/oauth/class-google-login.php',
        'Newspack\\Google_OAuth' => __DIR__ . '/../..' . '/includes/oauth/class-google-oauth.php',
        'Newspack\\Google_Services_Connection' => __DIR__ . '/../..' . '/includes/oauth/class-google-services-connection.php',
        'Newspack\\GravityForms' => __DIR__ . '/../..' . '/includes/plugins/class-gravityforms.php',
        'Newspack\\Guest_Contributor_Role' => __DIR__ . '/../..' . '/includes/plugins/co-authors-plus/class-guest-contributor-role.php',
        'Newspack\\Handoff_Banner' => __DIR__ . '/../..' . '/includes/class-handoff-banner.php',
        'Newspack\\Jetpack' => __DIR__ . '/../..' . '/includes/plugins/class-jetpack.php',
        'Newspack\\Jetpack_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-jetpack-configuration-manager.php',
        'Newspack\\Listings_Wizard' => __DIR__ . '/../..' . '/includes/wizards/class-listings-wizard.php',
        'Newspack\\Lite_Site' => __DIR__ . '/../..' . '/includes/lite-site/class-lite-site.php',
        'Newspack\\Logger' => __DIR__ . '/../..' . '/includes/class-logger.php',
        'Newspack\\Magic_Link' => __DIR__ . '/../..' . '/includes/class-magic-link.php',
        'Newspack\\Mailchimp_API' => __DIR__ . '/../..' . '/includes/oauth/class-mailchimp-api.php',
        'Newspack\\Mailchimp_For_WooCommerce' => __DIR__ . '/../..' . '/includes/plugins/class-mailchimp-for-woocommerce.php',
        'Newspack\\Major_Revision' => __DIR__ . '/../..' . '/includes/revisions-control/class-major-revision.php',
        'Newspack\\Major_Revisions' => __DIR__ . '/../..' . '/includes/revisions-control/class-major-revisions.php',
        'Newspack\\Media_Partners' => __DIR__ . '/../..' . '/includes/optional-modules/class-media-partners.php',
        'Newspack\\Memberships' => __DIR__ . '/../..' . '/includes/plugins/wc-memberships/class-memberships.php',
        'Newspack\\Memberships\\Block_Patterns' => __DIR__ . '/../..' . '/includes/plugins/wc-memberships/class-block-patterns.php',
        'Newspack\\Memberships\\Import_Export' => __DIR__ . '/../..' . '/includes/plugins/wc-memberships/class-import-export.php',
        'Newspack\\Memberships\\Metering' => __DIR__ . '/../..' . '/includes/plugins/wc-memberships/class-metering.php',
        'Newspack\\Meta_Pixel' => __DIR__ . '/../..' . '/includes/tracking/class-meta-pixel.php',
        'Newspack\\My_Account_UI_V0' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/my-account/class-my-account-ui-v0.php',
        'Newspack\\My_Account_UI_V1' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/my-account/class-my-account-ui-v1.php',
        'Newspack\\My_Account_UI_V1_Passwords' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/my-account/class-my-account-ui-v1-passwords.php',
        'Newspack\\NRH' => __DIR__ . '/../..' . '/includes/class-nrh.php',
        'Newspack\\Network_Wizard' => __DIR__ . '/../..' . '/includes/wizards/class-network-wizard.php',
        'Newspack\\Newsletters_Wizard' => __DIR__ . '/../..' . '/includes/wizards/class-newsletters-wizard.php',
        'Newspack\\Newspack' => __DIR__ . '/../..' . '/includes/class-newspack.php',
        'Newspack\\Newspack_Ads_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-newspack-ads-configuration-manager.php',
        'Newspack\\Newspack_Dashboard' => __DIR__ . '/../..' . '/includes/wizards/newspack/class-newspack-dashboard.php',
        'Newspack\\Newspack_Elections' => __DIR__ . '/../..' . '/includes/plugins/class-newspack-elections.php',
        'Newspack\\Newspack_Image_Credits' => __DIR__ . '/../..' . '/includes/class-newspack-image-credits.php',
        'Newspack\\Newspack_Newsletters_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-newspack-newsletters-configuration-manager.php',
        'Newspack\\Newspack_Popups_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-newspack-popups-configuration-manager.php',
        'Newspack\\Newspack_Theme_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-newspack-theme-configuration-manager.php',
        'Newspack\\Newspack_UI' => __DIR__ . '/../..' . '/includes/class-newspack-ui.php',
        'Newspack\\Newspack_UI_Icons' => __DIR__ . '/../..' . '/includes/class-newspack-ui-icons.php',
        'Newspack\\Nicename_Change' => __DIR__ . '/../..' . '/includes/plugins/co-authors-plus/class-nicename-change.php',
        'Newspack\\Nicename_Change_UI' => __DIR__ . '/../..' . '/includes/plugins/co-authors-plus/class-nicename-change-ui.php',
        'Newspack\\OAuth' => __DIR__ . '/../..' . '/includes/oauth/class-oauth.php',
        'Newspack\\OAuth_Transients' => __DIR__ . '/../..' . '/includes/oauth/class-oauth-transients.php',
        'Newspack\\On_Hold_Duration' => __DIR__ . '/../..' . '/includes/plugins/woocommerce-subscriptions/class-on-hold-duration.php',
        'Newspack\\OneSignal' => __DIR__ . '/../..' . '/includes/plugins/class-onesignal.php',
        'Newspack\\Optional_Modules' => __DIR__ . '/../..' . '/includes/optional-modules/class-optional-modules.php',
        'Newspack\\Organic_Profile_Block' => __DIR__ . '/../..' . '/includes/plugins/class-organic-profile-block.php',
        'Newspack\\PWA' => __DIR__ . '/../..' . '/includes/plugins/class-pwa.php',
        'Newspack\\Parsely_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-parsely-configuration-manager.php',
        'Newspack\\Patches' => __DIR__ . '/../..' . '/includes/class-patches.php',
        'Newspack\\Perfmatters' => __DIR__ . '/../..' . '/includes/plugins/class-perfmatters.php',
        'Newspack\\Performance' => __DIR__ . '/../..' . '/includes/class-performance.php',
        'Newspack\\Pixel' => __DIR__ . '/../..' . '/includes/tracking/class-pixel.php',
        'Newspack\\Plugin_Manager' => __DIR__ . '/../..' . '/includes/class-plugin-manager.php',
        'Newspack\\Profile' => __DIR__ . '/../..' . '/includes/class-profile.php',
        'Newspack\\Publish_To_Apple_News_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-publish-to-apple-news-configuration-manager.php',
        'Newspack\\RSS' => __DIR__ . '/../..' . '/includes/optional-modules/class-rss.php',
        'Newspack\\RSS_Add_Image' => __DIR__ . '/../..' . '/includes/class-rss-add-image.php',
        'Newspack\\Reader_Activation' => __DIR__ . '/../..' . '/includes/reader-activation/class-reader-activation.php',
        'Newspack\\Reader_Activation\\ESP_Sync' => __DIR__ . '/../..' . '/includes/reader-activation/sync/class-esp-sync.php',
        'Newspack\\Reader_Activation\\ESP_Sync_Admin' => __DIR__ . '/../..' . '/includes/reader-activation/sync/class-esp-sync-admin.php',
        'Newspack\\Reader_Activation\\Sync' => __DIR__ . '/../..' . '/includes/reader-activation/sync/class-sync.php',
        'Newspack\\Reader_Activation\\Sync\\Metadata' => __DIR__ . '/../..' . '/includes/reader-activation/sync/class-metadata.php',
        'Newspack\\Reader_Activation\\Sync\\WooCommerce' => __DIR__ . '/../..' . '/includes/reader-activation/sync/class-woocommerce.php',
        'Newspack\\Reader_Activation_Emails' => __DIR__ . '/../..' . '/includes/reader-activation/class-reader-activation-emails.php',
        'Newspack\\Reader_Data' => __DIR__ . '/../..' . '/includes/reader-activation/class-reader-data.php',
        'Newspack\\Reader_Revenue_Emails' => __DIR__ . '/../..' . '/includes/reader-revenue/class-reader-revenue-emails.php',
        'Newspack\\Recaptcha' => __DIR__ . '/../..' . '/includes/class-recaptcha.php',
        'Newspack\\Renewal' => __DIR__ . '/../..' . '/includes/plugins/woocommerce-subscriptions/class-renewal.php',
        'Newspack\\Revisions_Control' => __DIR__ . '/../..' . '/includes/revisions-control/class-revisions-control.php',
        'Newspack\\Salesforce' => __DIR__ . '/../..' . '/includes/class-salesforce.php',
        'Newspack\\Search_Authors_Limit' => __DIR__ . '/../..' . '/includes/plugins/co-authors-plus/class-search-authors-limit.php',
        'Newspack\\Setup_Wizard' => __DIR__ . '/../..' . '/includes/wizards/class-setup-wizard.php',
        'Newspack\\Site_Kit_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-site-kit-configuration-manager.php',
        'Newspack\\Starter_Content' => __DIR__ . '/../..' . '/includes/class-starter-content.php',
        'Newspack\\Starter_Content_Generated' => __DIR__ . '/../..' . '/includes/starter_content/class-starter-content-generated.php',
        'Newspack\\Starter_Content_Provider' => __DIR__ . '/../..' . '/includes/starter_content/class-starter-content-provider.php',
        'Newspack\\Starter_Content_WordPress' => __DIR__ . '/../..' . '/includes/starter_content/class-starter-content-wordpress.php',
        'Newspack\\Stripe_Connection' => __DIR__ . '/../..' . '/includes/reader-revenue/stripe/class-stripe-connection.php',
        'Newspack\\Subscriptions_Confirmation' => __DIR__ . '/../..' . '/includes/plugins/woocommerce-subscriptions/class-subscriptions-confirmation.php',
        'Newspack\\Subscriptions_Meta' => __DIR__ . '/../..' . '/includes/plugins/woocommerce-subscriptions/class-subscriptions-meta.php',
        'Newspack\\Sync_Reader_Data_CLI' => __DIR__ . '/../..' . '/includes/reader-activation/cli/class-sync-reader-data-cli.php',
        'Newspack\\Syndication' => __DIR__ . '/../..' . '/includes/class-syndication.php',
        'Newspack\\Teams_For_Memberships' => __DIR__ . '/../..' . '/includes/plugins/class-teams-for-memberships.php',
        'Newspack\\Theme_Manager' => __DIR__ . '/../..' . '/includes/class-theme-manager.php',
        'Newspack\\Twitter_Pixel' => __DIR__ . '/../..' . '/includes/tracking/class-twitter-pixel.php',
        'Newspack\\Wizard' => __DIR__ . '/../..' . '/includes/wizards/class-wizard.php',
        'Newspack\\Wizards' => __DIR__ . '/../..' . '/includes/class-wizards.php',
        'Newspack\\Wizards\\Newspack\\Collections_Section' => __DIR__ . '/../..' . '/includes/wizards/newspack/class-collections-section.php',
        'Newspack\\Wizards\\Newspack\\Custom_Events_Section' => __DIR__ . '/../..' . '/includes/wizards/newspack/class-custom-events-section.php',
        'Newspack\\Wizards\\Newspack\\Newspack_Settings' => __DIR__ . '/../..' . '/includes/wizards/newspack/class-newspack-settings.php',
        'Newspack\\Wizards\\Newspack\\Pixels_Section' => __DIR__ . '/../..' . '/includes/wizards/newspack/class-pixels-section.php',
        'Newspack\\Wizards\\Newspack\\Recirculation_Section' => __DIR__ . '/../..' . '/includes/wizards/newspack/class-recirculation-section.php',
        'Newspack\\Wizards\\Newspack\\SEO_Section' => __DIR__ . '/../..' . '/includes/wizards/newspack/class-seo-section.php',
        'Newspack\\Wizards\\Newspack\\Syndication_Section' => __DIR__ . '/../..' . '/includes/wizards/newspack/class-syndication-section.php',
        'Newspack\\Wizards\\Traits\\Admin_Header' => __DIR__ . '/../..' . '/includes/wizards/traits/trait-wizards-admin-header.php',
        'Newspack\\Wizards\\Wizard_Section' => __DIR__ . '/../..' . '/includes/wizards/class-wizard-section.php',
        'Newspack\\WooCommerce' => __DIR__ . '/../..' . '/includes/plugins/class-woocommerce.php',
        'Newspack\\WooCommerce_Cli' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/class-woocommerce-cli.php',
        'Newspack\\WooCommerce_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-woocommerce-configuration-manager.php',
        'Newspack\\WooCommerce_Connection' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/class-woocommerce-connection.php',
        'Newspack\\WooCommerce_Cover_Fees' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/class-woocommerce-cover-fees.php',
        'Newspack\\WooCommerce_Duplicate_Orders' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/class-woocommerce-duplicate-orders.php',
        'Newspack\\WooCommerce_Gateway_Stripe' => __DIR__ . '/../..' . '/includes/plugins/class-woocommerce-gateway-stripe.php',
        'Newspack\\WooCommerce_Logs' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/class-woocommerce-logs.php',
        'Newspack\\WooCommerce_My_Account' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/my-account/class-woocommerce-my-account.php',
        'Newspack\\WooCommerce_Order_UTM' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/class-woocommerce-order-utm.php',
        'Newspack\\WooCommerce_Products' => __DIR__ . '/../..' . '/includes/plugins/woocommerce/class-woocommerce-products.php',
        'Newspack\\WooCommerce_Subscriptions' => __DIR__ . '/../..' . '/includes/plugins/woocommerce-subscriptions/class-woocommerce-subscriptions.php',
        'Newspack\\WooCommerce_Subscriptions_Gifting' => __DIR__ . '/../..' . '/includes/plugins/woocommerce-subscriptions/class-woocommerce-subscriptions-gifting.php',
        'Newspack\\Woo_Member_Commenting' => __DIR__ . '/../..' . '/includes/optional-modules/class-woo-member-commenting.php',
        'Newspack\\WordPress_SEO_Configuration_Manager' => __DIR__ . '/../..' . '/includes/configuration_managers/class-wordpress-seo-configuration-manager.php',
        'Newspack\\Yoast' => __DIR__ . '/../..' . '/includes/plugins/class-yoast.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2abb4969ff5d90cb7a20f93ebaa44a54::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2abb4969ff5d90cb7a20f93ebaa44a54::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2abb4969ff5d90cb7a20f93ebaa44a54::$classMap;

        }, null, ClassLoader::class);
    }
}
