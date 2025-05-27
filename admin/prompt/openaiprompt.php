<?php

namespace BuddyBot\Admin\Prompt;

class OpenAiPrompt extends \BuddyBot\Admin\Prompt\MoRoot
{
    protected $data;

    public function getHtml(array $buddybot_data)
    {
        $this->data = $buddybot_data;

        $prompt ='';
        $prompt .= $this->assistantNamePrompt();
        $prompt .= $this->assistantGreetingPrompt();
        $prompt .= $this->multilingualSupportPrompt();
        $prompt .= $this->openaiSearchPrompt();
        $prompt .= $this->disclosurePrompt();
        $prompt .= $this->openaiResponseLengthPrompt();
        $prompt .= $this->emotionDetectionPrompt();
        $prompt .= $this->additionalInstructionsPrompt();
        
        return $prompt;
    }

    private function assistantNamePrompt()
    {
        $assistant_name = !empty($this->data["assistant_name"]) ? sanitize_text_field($this->data["assistant_name"]) : "BuddyBot";
    
        $prompt  = "Your name is {$assistant_name}. " . PHP_EOL;
        $prompt .= "Always introduce yourself using this name. " . PHP_EOL;
    
        return $prompt;
    }

    private function assistantGreetingPrompt()
    {
        $assistant_name = !empty($this->data["assistant_name"]) ? sanitize_text_field($this->data["assistant_name"]) : "BuddyBot";
        $assistant_greeting = !empty($this->data["greeting_message"]) ? sanitize_text_field($this->data["greeting_message"]) : "How can I assist you today?";
        $multilingual_disabled = !empty($this->data["multilingual_support"]);
        $language = !empty($this->data["language_option"]) ? sanitize_text_field($this->data["language_option"]) : "English";

        $prompt = "Greeting Rules:" . PHP_EOL;

        if ($multilingual_disabled) {
           $prompt .= "- If the user greets you, respond exactly with a {$language} version of: Hello, I am {$assistant_name}. {$assistant_greeting}" . PHP_EOL;
            $prompt .= "- If the user adds something like \"how are you?\", \"what's up?\", or any greeting follow-up, reply warmly in {$language} with: \"Thanks for asking! I'm here to help.\" or \"I'm ready to assist you.\"" . PHP_EOL;
            
        } else {
            $prompt .= "- If the user greets you (e.g., says \"hi\", \"hello\", \"hey\", \"ola\", \"你好\", etc.), respond using a precise and complete translation of \"Hello, I am {$assistant_name}. {$assistant_greeting}\" in the user's language." . PHP_EOL;
            $prompt .= "- If the user greets you in English with words like \"hello\", \"hi\", or \"hey\", respond ONLY in English with: \"Hello, I am {$assistant_name}. {$assistant_greeting}\"." . PHP_EOL;
            $prompt .= "- If the user adds something like \"how are you?\", \"what's up?\", or any greeting follow-up, treat it as a greeting and respond warmly with a translated message like: \"Thanks for asking! I'm here to help.\" or \"I'm ready to assist you.\" in the same language." . PHP_EOL;
        }

        $prompt .= "- If the user starts with a question or command, skip the greeting and reply directly." . PHP_EOL;

        return $prompt;
    }

    private function multilingualSupportPrompt()
    {
        $disabled = !empty($this->data["multilingual_support"]);
        $language = !empty($this->data["language_option"]) ? sanitize_text_field($this->data["language_option"]) : "English";
    
        $prompt  = "Multilingual Behavior: " . PHP_EOL;
        if ($disabled) {
            $prompt .= "- Do not detect or respond in multiple languages." . PHP_EOL;
            $prompt .= "- Always respond strictly in {$language}, regardless of the user's input language." . PHP_EOL;

        } else {
            $prompt .= "- Always detect the user's input language and respond strictly in the same language. " . PHP_EOL;
            $prompt .= "- Never reply in English unless the user's message is in English. " . PHP_EOL;
            $prompt .= "- If a document or answer is retrieved in a different language, translate it completely and naturally into the user's detected language before replying. " . PHP_EOL;
            $prompt .= "- If the document content is in a mix of languages, only use it if it can be accurately translated into the user's language without loss of clarity or meaning. " . PHP_EOL;
            $prompt .= "- Never skip or mix languages in a reply. " . PHP_EOL;
            $prompt .= "- Do not mention document language, translation, or language switching." . PHP_EOL;
            $prompt .= "- If the user switches languages mid-conversation, continue responding in the new detected language." . PHP_EOL;
            $prompt .= "- This applies to all messages, including answers, greetings, and fallback messages." . PHP_EOL;
        }
        return $prompt;
    }
    
    private function openaiSearchPrompt()
    {
        $openai_disabled = !empty($this->data["openai_search"]);
        $fallback_msg = !empty($this->data["openaisearch_msg"])  
            ? sanitize_text_field(wp_unslash($this->data["openaisearch_msg"])) 
            : "sorry, don't know the answer.";
        $language = !empty($this->data["language_option"]) ? sanitize_text_field($this->data["language_option"]) : "English";

        $instructions = PHP_EOL . "Knowledge & Fallback Rules:" . PHP_EOL;

        if ($openai_disabled) {
            $instructions .= "- If a user asks a question, follow these steps strictly:" . PHP_EOL;
            $instructions .= "  1. Search the internal vector store for a specific and directly relevant match." . PHP_EOL;

            if (!empty($this->data["multilingual_support"])) {
                $instructions .= "  3. If no relevant match is found, translate this message naturally into {$language} and reply with the translated version of: \"{$fallback_msg}\"" . PHP_EOL;
            } else {

                $instructions .= "3. If no relevant match is found, say: \"{$fallback_msg}\" — but always translate this message clearly and naturally into the user's language while keeping the meaning exactly the same." . PHP_EOL;
                $instructions .= "- Do not respond in English unless the user is using English." . PHP_EOL;
            }     
            $instructions .= "     - **Important**: Do NOT add any other prefix or suffix, such as 'I could not find...', 'Based on the documents...', or anything that explains the lack of information." . PHP_EOL;
        } else {
            $instructions .= "- Always attempt to answer using the internal vector store first." . PHP_EOL;
            $instructions .= "- Only use OpenAI general knowledge if no relevant match is found in the vector store." . PHP_EOL;
            $instructions .= "- Do NOT mention whether the response came from the internal vector store or OpenAI." . PHP_EOL;
        }
    
        return $instructions;
    }

    private function disclosurePrompt()
    {
        $prompt = "Disclosure Restrictions:" . PHP_EOL;
        $prompt .= "- Never mention or refer to OpenAI, ChatGPT, uploaded files, vector stores, internal documents, comments, posts, or any external or internal data sources." . PHP_EOL;
        $prompt .= "- Do NOT explain, imply, or disclose how your answer was generated or where the information came from." . PHP_EOL;
        $prompt .= "- Never refer to yourself as an AI, assistant, language model, chatbot, or any system. Present yourself only by your given name." . PHP_EOL;

        return $prompt;
    }

    private function openaiResponseLengthPrompt()
    {
        $max_words = !empty($this->data["response_length"]) ? (int) $this->data["response_length"] : 500;

        $prompt  = "Response Length Rules: " . PHP_EOL;
        $prompt .= "- Keep responses under {$max_words} words by default. " . PHP_EOL;
        $prompt .= "- Never assume the user wants a longer answer unless clearly requested. " . PHP_EOL;
        $prompt .= "- Only exceed this limit if the user explicitly asks for more detail (in any language)." . PHP_EOL;
        $prompt .= "- You must detect and interpret such requests contextually based on the user's input, regardless of the language used." . PHP_EOL;
        $prompt .= "- Avoid filler, repetition, or unnecessary elaboration — always be clear and to the point." . PHP_EOL;

        return $prompt;
    }
    
    private function emotionDetectionPrompt()
    {
        $emotion_enabled = !empty($this->data["emotion_detection"]);

        if ($emotion_enabled) {
            $prompt  = "Emotion Handling:" . PHP_EOL;
            $prompt .= "- Detect the user's tone and adjust your response style." . PHP_EOL;
            $prompt .= "- Stay calm if the user is angry, warm if sad, and friendly if happy or neutral." . PHP_EOL;
            $prompt .= "- Do not mention or label the user's emotion directly." . PHP_EOL;

            return $prompt;
        }
    
        return '';
    }

    private function additionalInstructionsPrompt()
    {
        $additional_instructions = !empty($this->data["additional_instructions"]) ? sanitize_textarea_field($this->data["additional_instructions"]) : '';
    
        if (!empty($additional_instructions)) {

            $prompt = $additional_instructions . PHP_EOL;

            return $prompt;
        }
    
        return '';
    }

}