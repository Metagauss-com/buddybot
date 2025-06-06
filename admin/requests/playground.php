<?php

namespace BuddyBot\Admin\Requests;

final class Playground extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->setVarsJs();
        $this->disableMessageJs();
        $this->updateStatusJs();
        $this->sendMessageBtnJs();
        $this->createThreadJs();
        $this->createMessageJs();
        $this->createRunJs();
        $this->listMessagesJs();
        $this->storeThreadInfoJs();
        $this->scrollToMessageJs();
        $this->selectThreadJs();
        $this->previousThreadsJs();
        $this->pastMessagesJs();
        $this->deleteThreadBtnJs();
        $this->updateThreadNameJs();
        $this->newThreadJS();
    }

    private function setVarsJs()
    {
        echo '
        let checkRun = "";
        const StartConversation = "' . esc_html__('Start new conversation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const messageEmpty = "' . esc_html__('Cannot send empty message.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const creatingThread = "' . esc_html__('Starting new conversation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const threadCreated = "' . esc_html__('Conversation started.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const sendingMessage = "' . esc_html__('Sending message to the Assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const messageSent = "' . esc_html__('Message sent.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const creatingRun = "' . esc_html__('Asking assistant to read your message.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const runCreated = "' . esc_html__('Assistant is processing your message.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const retrievingRun = "' . esc_html__('Checking response to your message.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const runCancelled = "' . esc_html__('The process was aborted.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const gettingResponse = "' . esc_html__("Fetching Assistant response.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const responseUpdated = "' . esc_html__("Assistant response received.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const gettingThreadMessages = "' . esc_html__("Fetching conversation data.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const threadMessagesUpdated = "' . esc_html__("Conversation data updated.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const gettingPastMessages = "' . esc_html__("Loading previous messages.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const pastMessagesUpdated = "' . esc_html__("Loaded previous messages.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const deletingThread = "' . esc_html__("Deleting conversation.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const threadDeleted = "' . esc_html__("Conversation deleted successfully!", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const streamError = "' . esc_html__("An error occurred while fetching Assistant response.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        const runError = "' . esc_html__("An error occurred while processing your message. Please try again later.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
        ';
    }

    private function disableMessageJs()
    {
        echo '
        function disableMessage(propState = true) {
            $("#mgao-playground-send-message-btn").prop("disabled", propState);
            $("#buddybot-new-message-input").prop("disabled", propState);

            if (propState) {
                console.log("Disabling message input and button");
                $("#buddybot-conversation-spinner").show();
            } else {
                $("#buddybot-conversation-spinner").hide();
            }
        }
        ';
    }

    private function updateStatusJs()
    {
        echo '
        function updateStatus(text, isError = true) {
            if (isError) {
                $("#buddybot-conversation-error").html("<span class=buddybot-text-danger>" + text + "</span>");
            } else {
                $("#buddybot-conversation-error").text(text);
            }
        }

        function clearStatus() {
            $("#buddybot-conversation-error").text("");
        }
        ';
    }

    private function sendMessageBtnJs()
    {
        echo '
        $("#buddybot-send-message-btn").click(sendMessage);
        
        $("#buddybot-new-message-input").keypress(function(e) {
            let key = e.key;
            if (key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        function sendMessage() {
            clearStatus();
            const message = $("#buddybot-new-message-input").val();

            if (message === "") {
                updateStatus(messageEmpty);
                return;
            }

            disableMessage();
            const threadId = $("#mgao-playground-thread-id-input").val();
            if (threadId === "") {
                createThread();
            } else {
                addMessage();
            }
        }
        ';
    }

    private function createThreadJs()
    {
        $nonce = wp_create_nonce('create_thread');
        echo '
        function createThread() {
            
            disableMessage();
            clearStatus();
            
            const data = {
                "action": "createThread",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("#mgao-playground-thread-id-input").val(response.result.id);
                    addMessage();
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function createMessageJs()
    {
        $nonce = wp_create_nonce('create_message');
        echo '
        function addMessage() {

            disableMessage();
            clearStatus();

            const threadId = $("#mgao-playground-thread-id-input").val();
            const message = $("#buddybot-new-message-input").val();

            const data = {
                "action": "createMessage",
                "thread_id": threadId,
                "message": message,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("#buddybot-new-message-input").val("");
                    var cleanedHtml = parseFormatting(response.html);
                    $("#buddybot-chat-container").append(cleanedHtml);
                    $("#buddybot-playground-first-message-id").val(response.result.id);
                    updateThreadName(message);
                    scrollToBottom(response.result.id);
                    startStreaming();
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function createRunJs()
    {
        $nonce = wp_create_nonce('buddybot_stream');
        echo '
        const messageBuffers = {};
        let runCreatedAt = null;

        function startStreaming() {
    
            const threadId = $("#mgao-playground-thread-id-input").val();
            const assistantId = $("#buddybot-playground-assistants-list").val();

            fetchStream(threadId, assistantId);
        }

        async function fetchStream(threadId, assistantId) {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 60000);  

            try {
                const postData = new URLSearchParams();
                postData.append("action", "buddybotStream");
                postData.append("threadId", threadId);
                postData.append("assistantId", assistantId);
                postData.append("nonce", "' . esc_js($nonce) . '");

                const response = await fetch(ajaxurl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: postData.toString(),
                    signal: controller.signal,
                });

                clearTimeout(timeoutId);
    
                if (!response.ok) {
                    const errorDetails = await response.json();
                    const errorMessage = errorDetails?.error?.message || streamError;
                    updateStatus(errorMessage);
                    disableMessage(false);
                    return;
                }
    
                const reader = response.body.getReader();
                const decoder = new TextDecoder("utf-8");
                let leftover = "";
    
                // Read the stream
                while (true) {
                    const { value, done } = await reader.read();
                    if (done) break;
    
                    const chunk = decoder.decode(value, { stream: true });
                    const text   = leftover + chunk;
                    const lines  = text.split("\n");

                    leftover = lines.pop();

                    try {
                        let curlError = JSON.parse(text);
                        if (curlError?.error?.message) {
                            updateStatus(curlError.error.message);
                            disableMessage(false);
                            return;
                        }
                    } catch (err) {
                        // Not a JSON error, continue processing lines
                    }

                    for (const line of lines) {
                        if (line.startsWith("data: ")) {
                            const data = line.slice(6).trim();
                            if (data === "[DONE]") {
                                disableMessage(false);
                                return;
                            }
    
                            let response;
                            
                            try {
                                response = JSON.parse(data);
                            } catch (e) {
                                const error = runError;
                                updateStatus(error);
                                disableMessage(false);
                                continue;
                            }

                            if (response.status === "failed" || response.error || (response.error && response.error.message)) {
                                const errorMsg = response?.error?.message || response?.last_error?.message || runError;
                                updateStatus(errorMsg);
                                disableMessage(false);
                                return;
                            } else if (response.status === "cancelled" || response.status === "cancelling") {
                                updateStatus(runCancelled, false);
                                disableMessage(false);
                                return;
                            }

                            if (response.object === "thread.run") {
                                runCreatedAt = response.created_at;
                            }

                            if (response.delta && response.delta.content) {
                                let messageId = response.id;
                                appendToChatBubble(response.delta.content[0].text.value, messageId, runCreatedAt);
                            }
                        }
                    }
                }
            } catch (error) {
            let errorMessage = error || streamError;
                updateStatus(errorMessage);
            }
        }
    
        function appendToChatBubble(content, messageId, CreatedAt) {


            if (!messageBuffers[messageId]) {
                messageBuffers[messageId] = "";
            }
            messageBuffers[messageId] += content;

            const formattedContent = parseFormatting(messageBuffers[messageId]);
            let messageEl = document.getElementById(messageId);

            if (messageEl) {
                const contentEl = messageEl.querySelector(".buddybot-response-content");
                if (contentEl) {
                    contentEl.innerHTML = formattedContent;
                }
            } else {
                const imgUrl = "' . esc_url($this->config->getRootUrl() . 'admin/html/images/third-party/openai/openai-logomark.png') . '";
            
                let DateFormat = "' . esc_js(get_option('date_format')) . '";
                let TimeFormat = "' . esc_js(get_option('time_format')) . '";
                let timezone = "' . esc_js(wp_timezone_string()) . '";


                let formattedDate = formatDateByWordPress(runCreatedAt, DateFormat, TimeFormat, timezone);

                const messageHtml = `
                    <div class="buddybot-playground-messages-list-item buddybot-d-flex buddybot-justify-content-start buddybot-my-2" id="${messageId}">
                        <div class="buddybot-me-2">
                            <img width="28" class="buddybot-border-radius-50" src="${imgUrl}">
                        </div>
                        <div>
                            <div class="buddybot-response-content buddybot-p-2 buddybot-bg-light buddybot-chat-border-radius"></div>
                            <div class="buddybot-text-muted buddybot-text-align-right buddybot-mt-2 buddybot-me-2">
                                ${formattedDate}
                            </div>
                        </div>
                    </div>
                `;

                $("#buddybot-chat-container").append(messageHtml);

                document.querySelector(`#${messageId} .buddybot-response-content`).innerHTML = formattedContent;

               scrollToBottom(messageId);
            }
        }

        function parseFormatting(text) {
            // Replace **bold** with <strong>bold</strong>
            text = text.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");

            // Convert newlines to <br>
            text = text.replace(/\n/g, "<br>");

            text = text.replace(/【.*?†.*?】/g, "");

            text = text.replace(/\[[^\]]*†[^\]]*\]/g, "");

            return text;
        }

        function formatDateByWordPress(unixTimestamp, wpDateFormat, wpTimeFormat, wpTimeZone) {
            const date = new Date(unixTimestamp * 1000);
            
            function pad(n, width = 2) {
                return String(n).padStart(width, "0");
            }

            function ordinalSuffix(n) {
                if (n >= 11 && n <= 13) return "th";
                switch (n % 10) {
                    case 1: return "st";
                    case 2: return "nd";
                    case 3: return "rd";
                    default: return "th";
                }
            }

            // Get all date components in the specified timezone
            const options = { timeZone: wpTimeZone };
            const day = date.toLocaleString("en-US", { day: "numeric", ...options });
            const month = date.toLocaleString("en-US", { month: "numeric", ...options });
            const year = date.toLocaleString("en-US", { year: "numeric", ...options });
            const hours = date.toLocaleString("en-US", { hour: "numeric", hour12: false, ...options });
            const minutes = date.toLocaleString("en-US", { minute: "numeric", ...options });
            const seconds = date.toLocaleString("en-US", { second: "numeric", ...options });
            const weekdayShort = date.toLocaleString("en-US", { weekday: "short", ...options });
            const weekdayLong = date.toLocaleString("en-US", { weekday: "long", ...options });
            const monthShort = date.toLocaleString("en-US", { month: "short", ...options });
            const monthLong = date.toLocaleString("en-US", { month: "long", ...options });

            // Create a new date object with these components to ensure consistency
            const tzDate = new Date(year, month - 1, day, hours, minutes, seconds);

            const replacements = {
                d: pad(tzDate.getDate()),
                D: weekdayShort,
                j: tzDate.getDate(),
                l: weekdayLong,
                N: tzDate.getDay() === 0 ? 7 : tzDate.getDay(),
                S: ordinalSuffix(tzDate.getDate()),
                w: tzDate.getDay(),
                z: Math.floor((tzDate - new Date(tzDate.getFullYear(), 0, 1)) / 86400000),

                W: (() => {
                    const target = new Date(tzDate.valueOf());
                    const day = tzDate.getDay() || 7;
                    target.setDate(tzDate.getDate() + 4 - day);
                    const yearStart = new Date(target.getFullYear(), 0, 1);
                    const weekNo = Math.ceil(((target - yearStart) / 86400000 + 1) / 7);
                    return pad(weekNo);
                })(),

                F: monthLong,
                m: pad(tzDate.getMonth() + 1),
                M: monthShort,
                n: tzDate.getMonth() + 1,
                t: new Date(tzDate.getFullYear(), tzDate.getMonth() + 1, 0).getDate(),
                L: ((year) => ((year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0)) ? 1 : 0)(tzDate.getFullYear()),
                o: (() => {
                    const d = new Date(tzDate.valueOf());
                    d.setDate(d.getDate() + 4 - (d.getDay() || 7));
                    return d.getFullYear();
                })(),
                Y: tzDate.getFullYear(),
                y: String(tzDate.getFullYear()).slice(-2),

                a: tzDate.getHours() >= 12 ? "pm" : "am",
                A: tzDate.getHours() >= 12 ? "PM" : "AM",
                g: ((h) => h % 12 || 12)(tzDate.getHours()),
                G: tzDate.getHours(),
                h: pad((tzDate.getHours() % 12) || 12),
                H: pad(tzDate.getHours()),
                i: pad(tzDate.getMinutes()),
                s: pad(tzDate.getSeconds()),

                e: wpTimeZone,
                T: date.toLocaleTimeString("en-US", { timeZoneName: "short", timeZone: wpTimeZone }).split(" ").pop(),
                Z: -date.getTimezoneOffset() * 60,
                O: (() => {
                    const offset = date.getTimezoneOffset();
                    const sign = offset <= 0 ? "+" : "-";
                    const hours = pad(Math.floor(Math.abs(offset) / 60));
                    const minutes = pad(Math.abs(offset) % 60);
                    return `${sign}${hours}${minutes}`;
                })(),
                P: (() => {
                    const offset = date.getTimezoneOffset();
                    const sign = offset <= 0 ? "+" : "-";
                    const hours = pad(Math.floor(Math.abs(offset) / 60));
                    const minutes = pad(Math.abs(offset) % 60);
                    return `${sign}${hours}:${minutes}`;
                })(),
                U: Math.floor(date.getTime() / 1000),
            };

            function applyFormat(format) {
                return format.replace(/(\\[a-zA-Z])|([a-zA-Z])/g, (match, escaped, token) => {
                    // Handle escaped characters (like \D)
                    if (escaped) {
                        return escaped.slice(1);
                    }
                    // Handle regular format tokens
                    if (token && replacements[token] !== undefined) {
                        return replacements[token];
                    }
                    return match;
                });
            }

            return `${applyFormat(wpDateFormat)} ${applyFormat(wpTimeFormat)}`;
        }
        ';
    }

    private function listMessagesJs()
    {
        $nonce = wp_create_nonce('list_messages');
        echo '
        function listMessages(threadId) {

            disableMessage();
            clearStatus();
            isLoadingThreads = true;

            const data = {
                "action": "listMessages",
                "thread_id": threadId,
                "limit": 10,
                "order": "desc",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                
                response = JSON.parse(response);

                if (response.success) {
                    var cleanedHtml = parseFormatting(response.html);
                    $("#buddybot-chat-container").append(cleanedHtml);
                    storeThreadInfo(response.result);
                    scrollToBottom(response.result.first_id);
                } else {
                    updateStatus(response.message);
                }

                disableMessage(false);
                isLoadingThreads = false;
                
            });
        }
        ';
    }

    private function storeThreadInfoJs()
    {
        echo '
        function storeThreadInfo(thread)
        {
            $("#buddybot-playground-first-message-id").val(thread.first_id);
            $("#buddybot-playground-last-message-id").val(thread.last_id);
            $("#buddybot-playground-has-more-messages").val(thread.has_more);
        }
        ';
    }

    private function scrollToMessageJs()
    {
        echo '
        function scrollToBottom(id) {
            let messageList = $("#buddybot-chat-container");
            
            messageList.animate({
                scrollTop: messageList[0].scrollHeight
            }, 1000); // Duration of the scroll
        }

        function scrollToTop() {
            $("#buddybot-chat-container").animate({
                scrollTop: 0
            }, 1000);
        }
        ';
    }

    private function selectThreadJs()
    {
        echo '
        $("#buddybot-threads-list").on("click", ".buddybot-threads-container", function(event) {
            if ($(event.target).closest(".buddybot-thread-delete").length > 0) {
                return;
            }

            if ($(this).hasClass("active")) {
                return;
            }

            const threadId = $(this).find(".buddybot-threads-list-item").attr("data-buddybot-threadid");
            $("#mgao-playground-thread-id-input").val(threadId);
            $(".buddybot-threads-container").removeClass("active");
            $(".buddybot-thread-delete").addClass("buddybot-d-none");
        
            $("#buddybot-chat-container").html("");
            $(this).closest(".buddybot-threads-container").addClass("active");
            $(this).closest(".buddybot-threads-container").find(".buddybot-thread-delete").removeClass("buddybot-d-none");
            
            listMessages(threadId);
        });
        ';
    }

    private function previousThreadsJs()
    {
        echo '
        let offset = 10;
        let typingTimer;
        let isLoadingThreads = false;
        let hasMoreThreads = true;
        let currentsearch = "";

        $("#buddybot-threads-list").on("scroll", function () {

            const scrollTop = $(this).scrollTop();
            const containerHeight = $(this).innerHeight();
            const scrollHeight = this.scrollHeight;

            if (scrollTop + containerHeight >= scrollHeight - 10 && !isLoadingThreads) {
                isLoadingThreads = true;
                loadThreads(currentsearch);
            }
        });

        function loadThreads(search = "") {
            clearStatus();
            if (!hasMoreThreads) {
                return;
            }

            $("#buddybot-thread-spinner").show();

            const data = {
                "action": "loadThreads",
                "offset": offset,
                "search": search,
                "nonce": "' . esc_js(wp_create_nonce('load_threads')) . '"
            };

            $.post(ajaxurl, data, function (response) {
                response = JSON.parse(response);

                setTimeout(() => {
                    if (response.success) {
                        offset += 10;
                        if (offset == 10) {
                            $("#buddybot-threads-list").html(response.html);
                        } else {
                            $("#buddybot-threads-list").append(response.html);
                        }
                        hasMoreThreads = response.has_more;
                    } else {
                        updateStatus(response.message);
                    }

                    isLoadingThreads = false;
                    $("#buddybot-thread-spinner").hide();
                }, 2000);
            });
        }

        $(document).on("keyup", "#buddybot-thread-list-search-input", function () {

            $("#buddybot-threads-list").html("");
            $("#buddybot-thread-spinner").show();

            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                const query = $(this).val();

                if (query !== currentsearch) {
                    offset = 0;
                    hasMoreThreads = true;
                    currentsearch = query;
                }

                isLoadingThreads = true;
                loadThreads(currentsearch);
            }, 2000);
        });

        $(document).on("keydown", "#buddybot-thread-list-search-input", function () {
            clearTimeout(typingTimer);
        });
        ';
    }

    private function pastMessagesJs()
    {
        $nonce = wp_create_nonce('list_messages');

        echo '
        let isLoadingPastMessages = false;

        $("#buddybot-chat-container").on("scroll", function () {
            const scrollTop = $(this).scrollTop();

            if (scrollTop === 0 && !isLoadingPastMessages && !isLoadingThreads) {
                const hasMore = $("#buddybot-playground-has-more-messages").val();

                if (hasMore == "false") return;

                isLoadingPastMessages = true;
                $("#buddybot-previous-conversation-spinner").show();
                clearStatus();
                $("#buddybot-playground-past-messages-btn").children("span").addClass("buddybot-rotate-icon");

                const lastId = $("#buddybot-playground-last-message-id").val();
                const threadId = $("#mgao-playground-thread-id-input").val();

                const data = {
                    action: "listMessages",
                    thread_id: threadId,
                    limit: 10,
                    after: lastId,
                    order: "desc",
                    nonce: "' . esc_js($nonce) . '"
                };

                $.post(ajaxurl, data, function (response) {
                    response = JSON.parse(response);

                    if (response.success) {

                        // Preserve scroll position after prepend
                        const container = $("#buddybot-chat-container");
                        const oldScrollHeight = container[0].scrollHeight;

                        const cleanedHtml = parseFormatting(response.html);
                        container.prepend(cleanedHtml);

                        const newScrollHeight = container[0].scrollHeight;
                        container.scrollTop(newScrollHeight - oldScrollHeight); // maintain scroll position

                        storeThreadInfo(response.result);
                    } else {
                        updateStatus(response.message);
                    }

                    $("#buddybot-previous-conversation-spinner").hide();
                    isLoadingPastMessages = false;
                });
            }
        });
        ';
    }

    private function deleteThreadBtnJs()
    {
        $nonce = wp_create_nonce('delete_thread');
        echo '

        $(".buddybot-thread-delete").click(function () {
            const threadId = $(this).closest(".buddybot-threads-container").find(".buddybot-threads-list-item").attr("data-buddybot-threadid");
            $("#mgao-playground-thread-id-input").val(threadId);
        });

        $("#buddybot-confirm-del-conversation-btn").click(function () {
            $("#buddybot-del-msg").show();
            $("#buddybot-confirm-del-conversation-btn").prop("disabled", true);
            $("#buddybot-cancel-del-conversation-btn").prop("disabled", true);
            deleteThreadBtn();
        });

        function deleteThreadBtn() {
            clearStatus();
            $("#buddybot-chat-container").css("opacity", 0.5);
            let threadId = $("#mgao-playground-thread-id-input").val();
            
            if (threadId === "") {
                return;
            }

            const data = {
                "action": "deleteThread",
                "thread_id": threadId,
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("#mgao-playground-thread-id-input").val("");
                    $("#buddybot-chat-container").html("");
                    $("#buddybot-chat-container").css("opacity", 1);
                    $("div[data-buddybot-threadid=\'" + threadId + "\']").closest(".buddybot-threads-container").remove();
                    $("#buddybot-del-conversation-modal").removeClass("show");
                } else {
                    updateStatus(response.message);
                }

                $("#buddybot-del-msg").hide();
                $("#buddybot-confirm-del-conversation-btn").prop("disabled", false);
                $("#buddybot-cancel-del-conversation-btn").prop("disabled", false);
                
            });

        }
        ';
    }

    private function updateThreadNameJs()
    {
        echo '
        function updateThreadName(message) {
            
            const threadId = $("#mgao-playground-thread-id-input").val();

            if (message.length > 100) {
                message = $.trim(message).substring(0, 100);
            }

            const threadItem = $("div[data-buddybot-threadid=\'" + threadId + "\']");
            threadItem.find(".buddybot-threads-list-item-text").text(message);
        }
        ';
    }

    private function newThreadJS()
    {
        echo '
        $("#buddybot-new-thread-btn").click(function() {
            $("#buddybot-chat-container").html("");
            $("#mgao-playground-thread-id-input").val("");
            $("#buddybot-playground-first-message-id").val("");
            $("#buddybot-playground-last-message-id").val("");
            $("#buddybot-playground-has-more-messages").val("false");
            $(".buddybot-threads-container").removeClass("active");
            $(".buddybot-thread-delete").addClass("buddybot-d-none");
            disableMessage(false);
            clearStatus();
        });
        ';
    }
}