=== BuddyBot – OpenAI Assistants, AI Chatbots and Support Agents for WordPress === 

Contributors: buddybot 
Tags: AI, chatbot, OpenAI, AI assistant  
Requires at least: 6.2  
Tested up to: 6.8  
Requires PHP: 7.3
Stable tag: 1.5.0.0
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Discover AI Chatbots for WordPress, only plugin built on native OpenAI assistants. Explore a new different way to chat!

== Description == 

**OpenAI Assistants and AI Chatbots for WordPress Site**

[BuddyBot](https://getbuddybot.com) brings the power of OpenAI Assistants and AI Chatbots directly to your WordPress site, helping you automate user conversations, answer user queries, and provide support—all in a seamless, native experience. Designed for WordPress, BuddyBot integrates effortlessly, allowing you to train AI on your site’s content, including posts and comments, for more relevant and accurate responses.

With an intuitive setup, customizable training options, and deep integration with WordPress, BuddyBot ensures that your OpenAI assistants feels like a natural part of your website. Whether you’re running a support site, blog, or community, BuddyBot makes AI-powered interactions smarter, faster, and more efficient.

Let BuddyBot handle conversations while you focus on growing your site!


= Key Features of BuddyBot – AI-Powered Chatbot for WordPress =

* Native OpenAI Assistants Integration – Brings OpenAI Assistants to WordPress, making AI Chatbots a natural part of your site.
* Create OpenAI Assistants: Easily create and manage AI Chatbots directly from your WordPress admin area.
* Train with Site Data: Train AI Chatbots with your site’s posts and comments in just one click.
* Frontend Deployment: Use a simple shortcode to publish an AI Chatbot on any page or post, allowing visitors to interact with AI chat agents.
* Bootstrap Integration: The plugin utilizes the Bootstrap framework for its layout and design, ensuring a responsive and consistent user interface across devices.
* Multiple AI Assistants – Create different OpenAI assistants for various purposes with our advanced extensions.
* AI-Powered FAQs (Pro) – Automatically generate relevant FAQs based on your site content.
* Seamless WordPress Experience – Works like a built-in feature of WordPress with full admin control.
* Future-Ready & Expandable – More powerful AI Chatbot features coming soon with premium add-ons!



= Use Cases =
* **Instant Support Automation:** BuddyBot can automatically handle visitor queries on your website, providing immediate support.
* **Knowledge-Based FAQs:** Train BuddyBot on your site's posts, comments, and pages to generate FAQ responses tailored to your site’s content (with Pro features).
* **Community Engagement:** Deploy AI chatbots across your blog, forum, or community site to answer member questions, recommend content, and keep discussions active.
* **Memberships Content:** Assist logged-in members by answering their private queries, guiding them to restricted or premium content areas, or deploying AI Chatbots trained on restricted content.
* **Product Recommendations:** BuddyBot can suggest products, articles, services, or downloads based on a visitor’s question by intelligently referring to your site’s content.
* **Support Ticket Workflow (Backend Review):** Capture and review chatbot conversations inside the WordPress admin to follow up manually on complex queries or escalate issues as needed.
* **Pre-Sales:** Educate potential customers about your products, services, or features in real-time through automated and accurate chat interactions.
* **Event or Course Information Assistant:** For sites offering events, webinars, or courses, BuddyBot can answer FAQs, session times, or enrollment details automatically.
* **24/7 Website Support:** Offer round-the-clock assistance, guiding visitors through  navigation, resources, account setup, or next steps without human intervention.



= OpenAI Assistants Integration =

BuddyBot integrates directly with OpenAI Assistants API, enabling a seamless connection between your WordPress site and OpenAI’s AI models. It communicates via API requests, sending user inputs to OpenAI’s cloud-based assistant, which processes the data and returns a relevant response in real time. The plugin manages API authentication using your OpenAI API key, ensuring secure and efficient communication. BuddyBot also supports vector-based AI training, where site content (posts, pages, and comments) is preprocessed and synchronized to OpenAI’s vector store, allowing the assistant to retrieve context-aware responses. With built-in WordPress hooks and AJAX handling, BuddyBot provides a smooth, asynchronous chatbot experience without slowing down your site.


**Important Links:**

* [Demo](https://getbuddybot.com)
* [BuddyBot Website](https://getbuddybot.com)
* [OpenAI Website](https://www.openai.com) 
* [OpenAI Terms of Use](https://openai.com/terms) 
* [OpenAI Privacy Policy](https://openai.com/privacy) 

Please review these documents to understand how OpenAI handles your data.  

= Requirements = 

- An OpenAI account and API key are required to use the BuddyBot plugin.
- Once you have an OpenAI account, you need to obtain an API key to configure the plugin. 


= Getting Started =

To get started with BuddyBot, follow these steps to integrate OpenAI Assistants seamlessly into your WordPress site:

- Install and Activate BuddyBot Plugin: Navigate to your WordPress dashboard. Go to Plugins > Add New. Search for “BuddyBot” and click Install Now. After installation, click Activate to enable the plugin.
- Configure OpenAI API Settings: In the WordPress dashboard, access the BuddyBot settings. Enter your OpenAI API key to establish a secure connection between your site and OpenAI’s Assistants models.
- Train the AI Assistant: Within BuddyBot settings, select the content types (posts, pages, comments) you want the AI Chatbot to learn from. Initiate the single click training process to synchronize your site’s content with OpenAI’s vector store, enabling context-aware responses.
- Customize AI Behavior: Adjust response settings, conversation limits, and other preferences to tailor the assistant’s interactions to your site’s needs.
- Deploy the AI Chatbot: Use the shortcode to embed the chatbot within specific pages or posts.


By following these steps, BuddyBot will be up and running, providing intelligent, AI-driven interactions for your WordPress site’s visitors.


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

= 1.5.0.0: May 19, 2025 =
* Improvement: Streaming Assistant API Responses
    - We’ve added streaming support for Assistant API responses. Now, instead of waiting for the entire message to be generated and returned at once, replies are sent in real time as they are being created.
    - This change makes the responses feel much faster, giving users a smoother and quicker experience.
    - It’s especially useful for long answers—users will begin to see the response appear as it’s being generated, instead of waiting for the full reply to finish first.
    - You don’t need to do anything—this update works automatically if you’re using the default Assistant integration.

= 1.4.0.0: May 16, 2025 =
* New: Visitor Email Collection
You can now collect email addresses from visitors who interact with your assistant. Use this to send updates, follow up after events, or grow your contact list—without needing any extra tools.
* New: Assistant Selection Option
Instead of creating a new assistant every time, you can now select from your existing ones. It’s a quicker, cleaner way to manage your assistant setup.

= 1.3.7.0: May 14, 2025 =
* UI changes.

= 1.3.6.0: May 6, 2025 =
* Fixed: Docs banner layout (position and size).
* Fixed: Post limitation issue during AI Training.
* Updated: Training now processes only published posts.
* Changed: Minimum required WordPress version lowered from 6.7 to 6.2.

= 1.3.5.0: April 23, 2025 =
* Added stricter fallback handling: responses are now based only on vector store data.
* Improved fallback message behavior when no relevant match is found.
* Improved greeting and casual prompt handling.
* Blocked fallback phrases like “uploaded files” and removed all source references (e.g., [1]).
* Enforced tool-only response mode for consistent behavior when external responses are restricted.

= 1.3.0.0: April 17, 2025 =
* Added: BuddyBot Block Editor block.
* Added: New option to disable cookies during visitor chat.

= 1.2.0.1: April 8, 2025 =
* Fixed: Some database queries which were executed on fresh installation.
* Fixed: UI layout conflict issues.

= 1.2.0.0: April 3, 2025 =  
* New: Enabled BuddyBot access for non-logged-in users, allowing seamless interaction for all visitors.  
* Improved: Various design enhancements for a more polished and user-friendly experience. 

= 1.1.0.0: March 18, 2025 =
* Merged: Assistant and BuddyBot for a unified experience.  
* Added: New submenu for Conversations (moved from Test Area).  
* Fixed: Various UI and issue fixes for better stability.  

= 1.0.4.0: February 18, 2025 =
* Added: Progress bar feature for better tracking of ongoing processes.
* Improved: Disappearing messages with icons for enhanced user interaction and experience.

= 1.0.3.3: February 05, 2025 =
* Improved: OpenAI API key changing process.
* Fixed: OpenAI API key is no longer visible in the Settings area.
* Improved: Made helptext and notices more relevant and descriptive.
* Improved: AI Training Knowledgebase creation workflow under the hood.

= 1.0.3.2: January 29, 2025 =
* Improved: Initial workflow.

= 1.0.3.1: January 23, 2025 = 
* Improved: Options Helptexts 

= 1.0.3.0: January 17, 2025 = 
* UI improvements and fixes 

= 1.0.2.0: January 13, 2025 = 
* Added: Welcome Modal 
* Multiple bug fixes. 

= 1.0.1.0: December 26, 2024 = 
* Fixed: Minor improvements to help-texts. 
* Multiple bug fixes. 
* Fixed: Compatibility issues with the PHP 8.0. 
* UI improvements. 

= 1.0.0.0: December 18, 2024 = 
* Initial release of BuddyBot AI. 

== Upgrade Notice ==

= 1.5.0.0: May 19, 2025 =
* Improvement: Streaming Assistant API Responses
    - We’ve added streaming support for Assistant API responses. Now, instead of waiting for the entire message to be generated and returned at once, replies are sent in real time as they are being created.
    - This change makes the responses feel much faster, giving users a smoother and quicker experience.
    - It’s especially useful for long answers—users will begin to see the response appear as it’s being generated, instead of waiting for the full reply to finish first.
    - You don’t need to do anything—this update works automatically if you’re using the default Assistant integration.

= 1.4.0.0: May 16, 2025 =
* New: Visitor Email Collection
You can now collect email addresses from visitors who interact with your assistant. Use this to send updates, follow up after events, or grow your contact list—without needing any extra tools.
* New: Assistant Selection Option
Instead of creating a new assistant every time, you can now select from your existing ones. It’s a quicker, cleaner way to manage your assistant setup.

= 1.3.7.0: May 14, 2025 =
* UI changes.

= 1.3.6.0: May 6, 2025 =
* Fixed: Docs banner layout (position and size).
* Fixed: Post limitation issue during AI Training.
* Updated: Training now processes only published posts.
* Changed: Minimum required WordPress version lowered from 6.7 to 6.2.

= 1.3.5.0: April 23, 2025 =
* Added stricter fallback handling: responses are now based only on vector store data.
* Improved fallback message behavior when no relevant match is found.
* Improved greeting and casual prompt handling.
* Blocked fallback phrases like “uploaded files” and removed all source references (e.g., [1]).
* Enforced tool-only response mode for consistent behavior when external responses are restricted.

= 1.3.0.0: April 17, 2025 =
* Added: BuddyBot Block Editor block.
* Added: New option to disable cookies during visitor chat.

= 1.2.0.1: April 8, 2025 =
* Fixed: Some database queries which were executed on fresh installation.
* Fixed: UI layout conflict issues.

= 1.2.0.0: April 3, 2025 =  
* New: Enabled BuddyBot access for non-logged-in users, allowing seamless interaction for all visitors.  
* Improved: Various design enhancements for a more polished and user-friendly experience. 

= 1.1.0.0: March 18, 2025 =
* Merged: Assistant and BuddyBot for a unified experience.  
* Added: New submenu for Conversations (moved from Test Area).  
* Fixed: Various UI and issue fixes for better stability.  

= 1.0.4.0: February 18, 2025 =
* Added: Progress bar feature for better tracking of ongoing processes.
* Improved: Disappearing messages with icons for enhanced user interaction and experience.

= 1.0.3.3: February 05, 2025 =
* Improved: OpenAI API key changing process.
* Fixed: OpenAI API key is no longer visible in the Settings area.
* Improved: Made helptext and notices more relevant and descriptive.
* Improved: AI Training Knowledgebase creation workflow under the hood.

= 1.0.3.2: January 29, 2025 = 
* Improved: Initial workflow. 

= 1.0.3.1: January 23, 2025 = 
* Improved: Options Helptexts 

= 1.0.3.0: January 17, 2025 = 
* UI improvements and fixes 

= 1.0.2.0: January 13, 2025 = 
* Added: Welcome Modal 
* Multiple bug fixes. 

= 1.0.1.0: December 26, 2024 = 
* Fixed: Minor improvements to help-texts. 
* Multiple bug fixes. 
* Fixed: Compatibility issues with the PHP 8.0. 
* UI improvements. 

= 1.0.0.0: December 18, 2024 = 
* Initial release of BuddyBot AI. 
