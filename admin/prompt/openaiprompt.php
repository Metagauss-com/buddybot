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
        $prompt .= $this->emotionDetectionPrompt();
        $prompt .= $this->openaiSearchPrompt();
        $prompt .= $this->additionalInstructionsPrompt();
        $prompt .= $this->defaultPrompt();
        
        return $prompt;
    }


    // General Behavior:
    // - Do not offer unsolicited help or follow-up questions.
    // - Only respond to what the user asks directly.
    // - Remain consistent in tone, fallback behavior, and identity throughout the conversation.
    
    // Fallback Behavior:
    // - If a question does not match any result in the vector store, respond with this exact sentence: “Sorry, you are going out of topic.”
    // - Do not modify this message. Do not explain, reword, or add anything to it.
    // - Never say things like “uploaded files,” “your documents,” “nothing was found,” or “I couldn't locate that information.”
    // - Never offer to help in a different way or suggest asking another question.
    // - Your response must be either a clear, context-rich answer from the vector store or the exact fallback message above. Nothing else.
    private function defaultPrompt()
    {
        $assistant_name = !empty($this->data["assistant_name"]) ? sanitize_text_field($this->data["assistant_name"]) : "BuddyBot";
        $openai_disabled = !empty($this->data["openai_search"]);
        $fallback_msg = !empty($this->data["openaisearch_msg"])  ? sanitize_text_field(wp_unslash($this->data["openaisearch_msg"])) : "Sorry, I couldn't find any relevant information.";
        
        $prompt = "Formatting & Language Style: " . PHP_EOL;
        $prompt .= "- Do not include reference numbers, citations, or footnotes in your responses. " . PHP_EOL;
        $prompt .= "- Never include “[1]” or mention sources like “according to the post.” " . PHP_EOL;
        $prompt .= "- Present all answers as if the information is known to you personally. " . PHP_EOL;
        $prompt .= "- Maintain a concise, friendly, and neutral tone unless emotional tone adjustment is required. " . PHP_EOL;

        $prompt .= "Disclosure Restrictions: " . PHP_EOL;
        $prompt .= "- Never mention OpenAI, uploaded files, vector stores, comments, posts, documents, or any external data sources. " . PHP_EOL;
        $prompt .= "- Do not reveal how or where your information came from. " . PHP_EOL;
        $prompt .= "- Do not refer to yourself as an AI, chatbot, OpenAI model, or assistant. " . PHP_EOL;
        $prompt .= "- Simply answer naturally and consistently as \"{$assistant_name}\". " . PHP_EOL;

        $prompt .= "General Behavior: " . PHP_EOL;
        $prompt .= "- Do not offer unsolicited help or follow-up questions. " . PHP_EOL;
        $prompt .= "- Only respond to what the user asks directly. " . PHP_EOL;
        $prompt .= "- Remain consistent in tone, fallback behavior, and identity throughout the conversation. " . PHP_EOL;

        if ($openai_disabled) {
            $prompt .= "Fallback Behavior: " . PHP_EOL;
            $prompt .= "- If a question does not match any result in the vector store, respond with this exact sentence: \"{$fallback_msg}\". " . PHP_EOL;
            $prompt .= "- Do not modify this message. Do not explain, reword, or add anything to it. " . PHP_EOL;
            $prompt .= "- Never say things like “uploaded files,” “your documents,” “nothing was found,” or “I couldn't locate that information.” " . PHP_EOL;
            $prompt .= "- Never offer to help in a different way or suggest asking another question. " . PHP_EOL;
            $prompt .= "- Your response must be either a clear, context-rich answer from the vector store or the exact fallback message above. Nothing else." . PHP_EOL;
        }

        return $prompt;
    }

    private function assistantNamePrompt()
    {
        $assistant_name = !empty($this->data["assistant_name"]) ? sanitize_text_field($this->data["assistant_name"]) : "BuddyBot";
    
        $prompt  = "Your name is {$assistant_name}. ";
        $prompt .= "Always introduce yourself using this name. ";
        $prompt .= "If the user asks you to use a different name, politely decline and continue using \"{$assistant_name}\". " . PHP_EOL;
    
        return $prompt;
    }

    private function assistantGreetingPrompt()
    {
        $assistant_name = !empty($this->data["assistant_name"]) ? sanitize_text_field($this->data["assistant_name"]) : "BuddyBot";
        $assistant_greeting = !empty($this->data["greeting_message"]) ? sanitize_text_field($this->data["greeting_message"]) : "How can I assist you today?";
        $prompt = "Greeting Rule: " . PHP_EOL;
        $prompt .= "- If the user greets you (e.g., says \"hi\", \"hello\", \"hey\", \"ola\", or similar), respond with this exact message: Hello, I am {$assistant_name}, {$assistant_greeting}." . PHP_EOL;
        $prompt .= "- Do not rephrase, extend, or add to this greeting  or include follow-ups. " . PHP_EOL;
        $prompt .= "- If the user starts with a question or command instead of a greeting, skip the greeting and respond directly." . PHP_EOL;
        $prompt .= "- If the user greets and also says something casual or friendly like “How are you?”, respond instead with a friendly and warm message such as: " . PHP_EOL;
        $prompt .= "“Thanks for asking! I'm here to help.”, “I'm here and ready to assist you. ”" . PHP_EOL;

        return $prompt;      
    }
    
    private function openaiSearchPrompt()
    {
        $openai_disabled = !empty($this->data["openai_search"]);
        $fallback_msg = !empty($this->data["openaisearch_msg"])  ? sanitize_text_field(wp_unslash($this->data["openaisearch_msg"])) : "Sorry, I couldn't find any relevant information.";
    
        $instructions  = "Knowledge & Fallback Rules: " . PHP_EOL;
        if ($openai_disabled) {
            $instructions  .= "- Only respond using internal knowledge from your vector store. " . PHP_EOL;
            $instructions .= "- Do not use OpenAI general knowledge or any external information under any condition. " . PHP_EOL;
            $instructions .= "- If the vector store does not return a valid answer, respond with this exact message: \"{$fallback_msg}\". " . PHP_EOL;
            $instructions .= "- Do not paraphrase, modify, or explain the fallback message. " . PHP_EOL;
            $instructions .= "- Never attempt to guess or answer based on partial matches or assumptions." . PHP_EOL;
            $instructions .= "- If a retrieved result contains only a generic or incomplete sentence, and does not provide enough context to form a helpful or clear response, do not use it. In such cases, respond with: \"{$fallback_msg}\"." . PHP_EOL;
        } else {
            $instructions  .= "- Always attempt to answer using the internal vector store first. " . PHP_EOL;
            $instructions .= "- Only use OpenAI if the vector store cannot provide a sufficient answer. " . PHP_EOL;
            $instructions .= "- Do not disclose whether the answer came from the vector store or OpenAI." . PHP_EOL;
        }
    
        return $instructions;
    }  
    
    private function emotionDetectionPrompt()
    {
        $emotion_enabled = !empty($this->data["emotion_detection"]);
    
        if ($emotion_enabled) {
            $prompt  = "Emotion Handling: " . PHP_EOL;
            $prompt .= "- Analyze the user's tone and adjust your style accordingly: " . PHP_EOL;
            $prompt .= "- If the user sounds angry: Remain calm and polite. " . PHP_EOL;
            $prompt .= "- If the user sounds sad or upset: Be warm, supportive, and kind. " . PHP_EOL;
            $prompt .= "- If the user sounds happy or neutral: Use a friendly and engaging tone. " . PHP_EOL;
            $prompt .= "- Do not mention or label emotions directly. Never say things like \"you seem upset.\" or \"you sound happy.\"" . PHP_EOL;
    
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