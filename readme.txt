
=== BuddyBot AI - Custom AI Assistant and Chat Agent ===

Contributors: buddybot 
Tags: AI, chatbot, OpenAI, AI assistant  
Requires at least: 6.7  
Tested up to: 6.7  
Requires PHP: 8.1
Stable tag: 1.0.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create custom AI chatbots for your WordPress site. Train them with your site data to provide tailored, site-specific support to visitor.

== Description ==

BuddyBot AI is a powerful WordPress plugin that enables you to create custom AI assistants, known as BuddyBots, and deploy them as chat agents on your website. Leveraging OpenAI's capabilities, these BuddyBots are trained using your site's data, including posts and comments, to provide site-specific information to your visitors.

= Key Features =

* Create AI Assistants: Easily create and manage Assistants directly from your WordPress admin area.
* Train with Site Data: Combine and upload your site's posts and comments to OpenAI storage for training your BuddyBots.
* Frontend Deployment: Use a simple shortcode to publish BuddyBots on any page or post, allowing visitors to interact with AI-powered chat agents.
* Bootstrap Integration: The plugin utilizes the Bootstrap framework for its layout and design, ensuring a responsive and consistent user interface across devices.

= Use Cases =

* Provide instant, site-specific answers to visitor queries.
* Enhance user engagement with AI-driven conversations.
* Automate support and information dissemination based on your website's content.

= OpenAI Integration =

BuddyBot AI integrates with OpenAI to provide advanced AI assistant capabilities. **Under the following circumstances, data is sent to OpenAI servers**:

* Training Assistants: When you combine and upload your site's posts and comments to OpenAI storage, this data is used to train your AI assistants.
* User Interactions: During interactions between website visitors and BuddyBots, the content of the conversations may be sent to OpenAI to generate responses.

**Important Links:**

* [OpenAI Website](https://www.openai.com)
* [OpenAI Terms of Use](https://openai.com/terms)
* [OpenAI Privacy Policy](https://openai.com/privacy)

Please review these documents to understand how OpenAI handles your data.

= Requirements =

- An OpenAI account and API key are required to use the BuddyBot plugin. You can sign up for an OpenAI account at [OpenAI's website](https://www.openai.com/).
- Once you have an account, you need to obtain an API key to configure the plugin.

= Getting Started =

- **Create an OpenAI Account**: Visit [OpenAI's website](https://www.openai.com/) to sign up for an account if you don't already have one.
- **Generate an API Key**: After signing up, log in to your OpenAI account and generate an API key. You will need this key to configure the BuddyBot plugin.
- **Configure the Plugin**: Enter your OpenAI API key in the plugin settings page in your WordPress admin dashboard to enable the plugin's functionality.


== Installation ==

1. Upload the `buddybot-ai` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the BuddyBot AI admin pages to create and configure your assistants.
4. Use the provided shortcode to place the BuddyBots on your site frontend.

== Frequently Asked Questions ==

= How do I create an AI assistant? =

Navigate to the BuddyBot Assistants page, and click on 'Create New Assistant'.

= How do I upload my site content for training? =

Use the 'Data Sync' feature in the admin area to combine and upload your site's posts and comments to OpenAI storage.

= What data is sent to OpenAI? =

When creating and training assistants, the data you choose to upload (such as site posts and comments) is sent to OpenAI servers. Additionally, during interactions between visitors and BuddyBots, conversation data may be sent to OpenAI to generate responses.

= Does the plugin use any external frameworks? =

Yes, BuddyBot AI uses the Bootstrap framework for its layout and design, providing a responsive and modern user interface.

== Changelog ==

= 1.0.0 =
* Initial release of BuddyBot AI.
