<?php
namespace BuddyBot\Frontend\Requests;

class BuddybotChat extends \BuddyBot\Frontend\Requests\Moroot
{
    protected function shortcodeJs()
    {
        $this->toggleAlertJs();
        $this->cookiesNotificationJs();
        $this->onLoadJs();
        $this->lockUiJs();
        $this->getUserThreadsJs();
        $this->startNewThreadBtnJs();
        $this->singleThreadBackBtnJs();
        $this->threadListItemJs();
        $this->loadThreadListViewJs();
        $this->loadSingleThreadViewJs();
        $this->getMessagesJs();
        $this->hasMoreMessagesJs();
        $this->getPreviousMessagesJs();
        $this->sendUserMessageJs();
        $this->createRunJs();
        $this->scrollToMessageJs();
        $this->deleteThreadModalBtnJs();
    }

    private function toggleAlertJs()
    {
        echo '
        function showAlert(type = "danger", text = "") {
            let alert = $(".buddybot-chat-conversation-alert[data-bb-alert=" + type + "]");
            alert.text(text);
            alert.removeClass("visually-hidden").show();
        }

        function hideAlerts() {
            let alert = $(".buddybot-chat-conversation-alert");
            alert.addClass("visually-hidden");
        }
        ';
    }

    private function cookiesNotificationJs()
    {
        if (isset($_COOKIE['buddybot_session_id']) || is_user_logged_in()) {
            return;
        }
        
        if ((bool)$this->options->getOption('disable_cookies') === true) {
            return;
        }

        echo '
            $("#cookieConsentOffcanvas").offcanvas("show"); 
            
            $(document).on("click", "#buddybot-acceptCookies", function(){
                $("#cookieConsentOffcanvas").offcanvas("hide");
            });
        ';
    }

    private function onLoadJs()
    {
        echo '
            const bbTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            loadThreadListView();
        ';
    }

    private function lockUiJs()
    {
        echo '
        function lockUi(state = true) {
            $("#buddybot-single-conversation-user-message").prop("disabled", state);
            $("#buddybot-single-conversation-send-message-btn").prop("disabled", state);
            $("#buddybot-single-conversation-load-messages-btn").prop("disabled", state);
            $("#buddybot-single-conversation-delete-thread-btn").prop("disabled", state);
            $("#buddybot-single-conversation-back-btn").prop("disabled", state);
            $("#buddybot-chat-conversation-start-new").prop("disabled", state);
            toggleElement("#buddybot-single-conversation-top-spinners", state);
        }
        
        function toggleElement(element, state = true) {
            if (state === true) {
                $(element).removeClass("visually-hidden");
            } else {
                $(element).addClass("visually-hidden");
            }
        }
        ';
    }

    private function getUserThreadsJs()
    {
        echo '
        function getUserThreads() {

            lockUi();

            const data = {
                "action": "getConversationList",
                "timezone": bbTimeZone
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#buddybot-chat-conversation-list-loader").addClass("visually-hidden");
                $("#buddybot-chat-conversation-list-wrapper").html(response);
                lockUi(false);
            });
        }
        ';
    }

    private function startNewThreadBtnJs()
    {
        echo '
        $("#buddybot-chat-conversation-start-new").click(function(){
            loadSingleThreadView();
        });
        ';
    }

    private function singleThreadBackBtnJs()
    {
        echo '
        $("#buddybot-single-conversation-back-btn").click(function(){
            loadThreadListView();
        });
        ';
    }

    private function threadListItemJs()
    {
        echo '
        $("#buddybot-chat-conversation-list-wrapper").on("click", "li", function(){
            let threadId = $(this).attr("data-bb-threadid");
            loadSingleThreadView(threadId);
        });';
    }

    private function loadThreadListViewJs()
    {
        echo '
        function loadThreadListView() {
            hideAlerts();
            getUserThreads();
            $("#buddybot-chat-conversation-list-header").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-loader").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").removeClass("visually-hidden");
            $("#buddybot-single-conversation-wrapper").addClass("visually-hidden");
            sessionStorage.removeItem("bbCurrentThreadId");
            sessionStorage.removeItem("bbFirstId");
            sessionStorage.removeItem("bbLastId");
            $("#buddybot-single-conversation-messages-wrapper").html("");
        }';
    }

    private function loadSingleThreadViewJs()
    {
        echo '
        function loadSingleThreadView(threadId = false) {
            hideAlerts();
            $("#buddybot-chat-conversation-list-header").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").html("");
            $("#buddybot-single-conversation-wrapper").removeClass("visually-hidden");

            if (threadId === false) {
                loadNewThreadView();
            } else {
                loadExistingThreadView(threadId);
            }
        }
            
        function loadNewThreadView() {
            sessionStorage.removeItem("bbCurrentThreadId");
            $("#buddybot-single-conversation-load-messages-btn").addClass("visually-hidden");
            $("#buddybot-single-conversation-delete-thread-btn").addClass("visually-hidden");
        }

        function loadExistingThreadView(threadId) {
            sessionStorage.setItem("bbCurrentThreadId", threadId);
            getMessages(20, "", "bottom");
        }

        ';
    }

    private function getMessagesJs()
    {
        echo '
        function getMessages(limit = 10, after = "", scroll = "bottom") {
            lockUi();
            const data = {
                "action": "getMessages",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "limit": limit,
                "order": "desc",
                "after": after,
                "timezone": bbTimeZone,
                "nonce": "' . esc_js(wp_create_nonce('get_messages')) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    hasMoreMessages(response.result);
                    var cleanedHtml = response.html.replace(/【.*?†.*?】/g, "");
                    $("#buddybot-single-conversation-messages-wrapper").prepend(cleanedHtml);

                    if (scroll === "bottom") {
                        scrollToBottom();
                    } else {
                        scrollToTop();
                    }

                } else {
                    showAlert("danger", response.message);
                }
                
                lockUi(false);
            });
        }';
    }

    private function hasMoreMessagesJs()
    {
        echo '
        function hasMoreMessages(thread) {

            if(thread.has_more) {
                $("#buddybot-single-conversation-load-messages-btn").removeClass("visually-hidden");
            } else {
                $("#buddybot-single-conversation-load-messages-btn").addClass("visually-hidden");
            }

            sessionStorage.setItem("bbFirstId", thread.first_id);
            sessionStorage.setItem("bbLastId", thread.last_id);
        }
        ';
    }

    private function getPreviousMessagesJs()
    {
        echo '
        $("#buddybot-single-conversation-load-messages-btn").click(getPreviousMessages);

        function getPreviousMessages() {
            let lastId = sessionStorage.getItem("bbLastId");

            if (lastId === "") {
                return;
            }

            getMessages(limit = 10, lastId, "top");
        }

        ';
    }

    private function sendUserMessageJs()
    {
        echo '
        $("#buddybot-single-conversation-send-message-btn").click(sendUserMessage);

        document.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
              event.preventDefault();
              sendUserMessage();
            }
          });

        function sendUserMessage() {
            let userMessage = $.trim($("#buddybot-single-conversation-user-message").val());
            
            if (userMessage === "" || userMessage == null) {
                return;
            }
            
            lockUi();

            const messageData = {
                "action": "sendUserMessage",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "user_message": userMessage,
                "timezone": bbTimeZone,
                "nonce": "' . esc_js(wp_create_nonce('send_user_message')) . '"
            };

            $.post(ajaxurl, messageData, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    $("#buddybot-single-conversation-user-message").val("");
                    var cleanedHtml = response.html.replace(/【.*?†.*?】/g, "");
                    $("#buddybot-single-conversation-messages-wrapper").append(cleanedHtml);
                    sessionStorage.setItem("bbCurrentThreadId", response.result.thread_id);
                    $("#buddybot-single-conversation-delete-thread-btn").removeClass("visually-hidden");
                    sessionStorage.setItem("bbFirstId", response.result.id);
                    scrollToBottom(response.result.id);
                    startStreaming();
                } else {
                    showAlert("danger", response.message);
                    lockUi(false);
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

            const threadId = sessionStorage.getItem("bbCurrentThreadId");
            const assistantId = $("#buddybot-chat-conversation-assistant-id").val();

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
                    let errorMessage = errorDetails?.error?.message || "' . esc_html__("An error occurred while fetching Assistant response.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
                    showAlert("danger", errorMessage);
                    lockUi(false);
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
                            showAlert("danger", curlError.error.message);
                            lockUi(false);
                            return;
                        }
                    } catch (err) {
                        // Not a JSON error, continue processing lines
                    }

                    for (const line of lines) {
                        if (line.startsWith("data: ")) {
                            const data = line.slice(6).trim();
                            if (data === "[DONE]") {
                                lockUi(false);
                                return;
                            }
    
                            let response;
                            
                            try {
                                response = JSON.parse(data);
                            } catch (e) {
                                const errorMsg ="' . esc_html__("An error occurred while processing your message. Please try again later.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
                                showAlert("danger", errorMessage);
                                lockUi(false);
                                continue;
                            }

                             if (response.status === "failed" || response.error || (response.error && response.error.message)) {
                                const errorMsg = response?.error?.message || response?.last_error?.message || "' . esc_html__("An error occurred while processing your message. Please try again later.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
                                showAlert("danger", errorMessage);
                                lockUi(false);
                                return;
                            } else if (response.status === "cancelled" || response.status === "cancelling") {
                                const runCancelled = "' . esc_html__('The process was aborted.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
                                showAlert("danger", runCancelled);
                                lockUi(false);
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
                let errorMessage = error || "' . esc_html__("An error occurred while fetching Assistant response.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '";
                showAlert("danger", errorMessage);
                lockUi(false);
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
                const contentEl = messageEl.querySelector(".buddybot-chat-conversation-assistant-response");
                if (contentEl) {
                    contentEl.innerHTML = formattedContent;
                }
            } else {
                const imgUrl = "' . esc_url($this->config->getRootUrl() . 'admin/html/images/third-party/openai/openai-logomark.png') . '";
            
                let TimeFormat = "' . esc_js($this->config->getProp('time_format')) . '";
                let timezone = bbTimeZone;


                let formattedDate = formatTimeWithToday(runCreatedAt, TimeFormat, timezone);

                const messageHtml = `
                    <div class="buddybot-chat-conversation-list-item d-flex justify-content-start text-dark" id="${messageId}">
                        <div class="me-2 pt-2">
                            <img width="28" class="shadow-none rounded-circle border-0" src="${imgUrl}">
                        </div>
                        <div>
                            <div class="buddybot-chat-conversation-assistant-response p-2 bg-light bg-opacity-10" style="max-width: 500px;"></div>
                            <div class="small text-start text-secondary ms-2 me-3">
                                ${formattedDate}
                            </div>
                        </div>
                    </div>
                `;

                $("#buddybot-single-conversation-messages-wrapper").append(messageHtml);

                document.querySelector(`#${messageId} .buddybot-chat-conversation-assistant-response`).innerHTML = formattedContent;

                scrollToBottom(messageId);
            }
        }

        function parseFormatting(text) {
            // Replace **bold** with <strong>bold</strong>
            text = text.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");

            // Convert newlines to <br>
            text = text.replace(/\n/g, "<br>");

            text = text.replace(/【.*?†.*?】/g, "");

            return text;
        }

        function formatTimeWithToday(unixTimestamp, wpTimeFormat, wpTimeZone) {
            // Always show "Today" for date; wpDateFormat parameter removed
            const date = new Date(unixTimestamp * 1000);
            const tzDate = new Date(date.toLocaleString("en-US", { timeZone: wpTimeZone }));

            function pad(n, width = 2) {
                return String(n).padStart(width, "0");
            }

            // Map only time tokens
            const replacements = {
                a: tzDate.getHours() >= 12 ? "pm" : "am",
                A: tzDate.getHours() >= 12 ? "PM" : "AM",
                g: ((h) => h % 12 || 12)(tzDate.getHours()),
                G: tzDate.getHours(),
                h: pad((tzDate.getHours() % 12) || 12),
                H: pad(tzDate.getHours()),
                i: pad(tzDate.getMinutes()),
                s: pad(tzDate.getSeconds())
            };

            function applyTimeFormat(fmt) {
                let out = "";
                for (let i = 0; i < fmt.length; i++) {
                    const c = fmt[i];
                    if (c === "\\.") {
                        // escape literal next char
                        i++;
                        if (i < fmt.length) out += fmt[i];
                    } else if (replacements[c] !== undefined) {
                        out += replacements[c];
                    } else {
                        out += c;
                    }
                }
                return out;
            }

            const timeString = applyTimeFormat(wpTimeFormat);
            return `' . esc_html__("Today", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . ', ${timeString}`;
        }
        ';
    }

    private function scrollToMessageJs()
    {
        echo '
        function scrollToBottom(id = null) {

        const wrapper = "#buddybot-single-conversation-messages-wrapper";

            if (id === null) {
                $(wrapper).stop().animate({
                    scrollTop: $(wrapper)[0].scrollHeight
                }, 1000);
            } else {
                let height = $("#" + id).outerHeight();
                $(wrapper).animate({
                    scrollTop: $(wrapper)[0].scrollHeight - height - 200
                }, 1000);
            }
        }

        function scrollToTop() {
            $("#buddybot-single-conversation-messages-wrapper").animate({
                scrollTop: 0
            }, 1000);
        }
        ';
    }

    private function deleteThreadModalBtnJs()
    {
        echo '
        $("#buddybot-single-conversation-delete-thread-modal-btn").click(deleteThread);
        
        function deleteThread() {
        lockUi();
        
        const threadData = {
                "action": "deleteFrontendThread",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "nonce": "' . esc_js(wp_create_nonce('delete_frontend_thread')) . '"
            };

            $.post(ajaxurl, threadData, function(response) {

                response = JSON.parse(response);

                if (response.success) {
                    loadThreadListView();
                } else {
                    showAlert("danger", response.message);
                }

                lockUi(false);

            });
        }
        ';
    }
}