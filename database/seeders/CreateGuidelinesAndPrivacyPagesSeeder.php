<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PublicPage;

class CreateGuidelinesAndPrivacyPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Community Guidelines page if it doesn't exist
        $guidelinesPage = PublicPage::where('slug', 'community-guidelines')->first();
        if (!$guidelinesPage) {
            PublicPage::create([
                'slug' => 'community-guidelines',
                'title' => 'Community Guidelines',
                'short_title' => 'Guidelines',
                'content' => '<div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
                    <h1>Community Guidelines</h1>
                    <p><strong>Last Updated: January 18, 2026</strong></p>
                    <p>For the complete Community Guidelines document, please <a href="' . route('community.guidelines') . '" target="_blank">download the PDF version</a>.</p>
                    <p>These Community Guidelines establish the standards of conduct we expect from all members of our community. They work together with our Terms of Service and Privacy Policy to create a safe, respectful, and legally compliant platform.</p>
                    <h2>Key Principles</h2>
                    <ul>
                        <li><strong>Safety First:</strong> Zero tolerance for illegal content, exploitation, and serious safety violations</li>
                        <li><strong>Respect:</strong> Treat all community members with dignity and respect</li>
                        <li><strong>Consent:</strong> All content must involve consenting adults only</li>
                        <li><strong>Legal Compliance:</strong> All activity must comply with applicable laws</li>
                    </ul>
                    <h2>Prohibited Content</h2>
                    <ul>
                        <li>Child Sexual Abuse Material (CSAM) - Zero tolerance, immediate reporting to authorities</li>
                        <li>Non-consensual content</li>
                        <li>Human trafficking and exploitation</li>
                        <li>Violence, gore, and extreme harm</li>
                        <li>Illegal activities</li>
                    </ul>
                    <p>For detailed information, please refer to the <a href="' . route('community.guidelines') . '" target="_blank">full Community Guidelines PDF</a>.</p>
                </div>',
                'is_privacy' => false,
                'is_tos' => false,
                'shown_in_footer' => true,
                'page_order' => 4,
            ]);
        }

        // Update Privacy Policy page if it exists, or create new one
        $privacyPage = PublicPage::where('slug', 'privacy-policy')->orWhere('is_privacy', true)->first();
        if ($privacyPage) {
            $privacyPage->update([
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'short_title' => 'Privacy',
            ]);
        } else {
            PublicPage::create([
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'short_title' => 'Privacy',
                'content' => '<div style="max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
                    <h1>Privacy Policy</h1>
                    <p><strong>Last Updated: January 18, 2026</strong></p>
                    <p>For the complete Privacy Policy document, please <a href="' . route('privacy.policy') . '" target="_blank">download the PDF version</a>.</p>
                    <h2>Information We Collect</h2>
                    <p>We collect information that you provide directly to us, information we obtain automatically when you use our services, and information from third-party sources.</p>
                    <h2>How We Use Your Information</h2>
                    <ul>
                        <li>To provide, maintain, and improve our services</li>
                        <li>To process transactions and send related information</li>
                        <li>To send technical notices and support messages</li>
                        <li>To respond to your comments and questions</li>
                    </ul>
                    <h2>Information Sharing</h2>
                    <p>We do not sell your personal information. We may share your information in certain limited circumstances as described in our full Privacy Policy.</p>
                    <p>For detailed information, please refer to the <a href="' . route('privacy.policy') . '" target="_blank">full Privacy Policy PDF</a>.</p>
                </div>',
                'is_privacy' => true,
                'is_tos' => false,
                'shown_in_footer' => true,
                'page_order' => 5,
            ]);
        }
    }
}
