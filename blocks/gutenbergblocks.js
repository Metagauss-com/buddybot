document.addEventListener("DOMContentLoaded", function () {
const { registerBlockType } = wp.blocks;
const { createElement, useState, useEffect } = wp.element;
const { __ } = wp.i18n;
const { InspectorControls, useBlockProps, BlockControls, AlignmentToolbar } = wp.blockEditor;
const { TextControl, SelectControl, PanelBody, Spinner, ButtonGroup, Button, Dashicon, Notice } = wp.components;
const { dispatch } = wp.data;

registerBlockType('buddybot/chat', {
    title: __('BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
    description: __('Embed a BuddyBot into your page or post.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
    icon: 'smiley',
    category: 'widgets',
    supports: {
        align: true,
        className: true,
        html: false,
    },
    attributes: {
        selectedBuddyBot: { type: 'string' },
        customClass: { type: 'string' },
        align: { type: 'string', default: 'text-center' },
        bbTimeZone: { type: 'string', default: '' },
    },

    edit: (props) => {
        const { attributes, setAttributes } = props;
        const { selectedBuddyBot, customClass, align, bbTimeZone } = attributes;

        const [buddyBots, setBuddyBots] = useState([]);
        const [isLoading, setIsLoading] = useState(true);
        const [botNotFound, setBotNotFound] = useState(false);
        const [isApiKeyMissing, setIsApiKeyMissing] = useState(false); // NEW STATE for API key check

        useEffect(() => {
            const fetchBuddyBots = async () => {
                try {
                    const response = await wp.apiFetch({ path: '/buddybot/v1/buddybots' });
                    setBuddyBots(response);

                    if (selectedBuddyBot && !response.some(bot => bot.id === selectedBuddyBot)) {
                        setBotNotFound(true);
                    } else {
                        setBotNotFound(false);
                    }

                    if (Array.isArray(response) && response.length === 0) {
                        dispatch('core/notices').createNotice(
                            'warning',
                            __('No BuddyBots found. Please create one in the BuddyBot dashboard.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                            {
                                isDismissible: true,
                                raw: true,
                                actions: [
                                    {
                                        label: __('Go to BuddyBot Dashboard', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                                        url: 'admin.php?page=buddybot-chatbot',
                                        external: false
                                    }
                                ]
                            }
                        );
                    }
                } catch (error) {
                    dispatch('core/notices').createNotice(
                        'error',
                        __('An error occurred while fetching BuddyBots: ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') + error.message,
                        { isDismissible: true }
                    );
                } finally {
                    setIsLoading(false);
                }
            };

            const checkApiKey = async () => {
                try {
                    const apiResponse = await wp.apiFetch({ path: '/buddybot/v1/api-key-status' });
                    if (!apiResponse.apiKeyExists) {
                        setIsApiKeyMissing(true);
                    } else {
                        setIsApiKeyMissing(false);
                    }
                } catch (error) {
                    dispatch('core/notices').createNotice(
                        'error',
                        __('Error checking API key: ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') + error,
                        { isDismissible: true }
                    );
                }
            };

            fetchBuddyBots();
            checkApiKey();
            setAttributes({ bbTimeZone: Intl.DateTimeFormat().resolvedOptions().timeZone });
        }, [selectedBuddyBot]);

        const options = [
            { label: __('Select a BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), value: '' },
            ...(Array.isArray(buddyBots) ? buddyBots.map((bot) => ({
                label: bot.chatbot_name || 'Unnamed BuddyBot',
                value: bot.id,
            })) : []),
        ];

        const alignmentOptions = [
            { icon: 'editor-alignleft', value: 'buddybot-msg-wrap buddybot-align-items-left' },
            { icon: 'editor-aligncenter', value: 'buddybot-msg-wrap buddybot-align-items-center' },
            { icon: 'editor-alignright', value: 'buddybot-msg-wrap buddybot-align-items-right' },
            { icon: 'editor-justify', value: '' },
        ];


        return createElement('div', useBlockProps(),
            createElement(BlockControls, {},
                createElement(AlignmentToolbar, {
                    value: align,
                    onChange: (value) => setAttributes({ align: value || 'text-left' }),
                })
            ),
            createElement(InspectorControls, {},
                createElement(PanelBody, { title: __('BuddyBot Settings', 'buddybot-ai-custom-ai-assistant-and-chat-agent') },
                    isLoading ? createElement(Spinner) :
                        createElement(SelectControl, {
                            label: __('Select BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                            value: selectedBuddyBot,
                            options: options,
                            onChange: (value) => setAttributes({ selectedBuddyBot: value }),
                            disabled: isApiKeyMissing,
                            __nextHasNoMarginBottom: true,
                        }),
                    createElement(TextControl, {
                        label: __('Custom CSS Class', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                        value: customClass,
                        onChange: (value) => setAttributes({ customClass: value }),
                        __nextHasNoMarginBottom: true,
                    }),

                    createElement('div', { style: { marginTop: '10px' } },
                        createElement('p', {}, __('Align', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),
                        createElement(ButtonGroup, {},
                            alignmentOptions.map(({ icon, value }) =>
                                createElement(Button, {
                                    isPrimary: align === value,
                                    isSecondary: align !== value,
                                    onClick: () => setAttributes({ align: value }),
                                },
                                    createElement(Dashicon, { icon })
                                )
                            )
                        )
                    ),

                    // Sidebar warning for no BuddyBot selected
                    !selectedBuddyBot && createElement('div', { style: { marginTop: '20px' } },
                        createElement(Notice, {
                            status: 'warning',
                            isDismissible: true,
                        }, __('Please select a BuddyBot to display.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'))
                    )
                )
            ),



            createElement('div', { className: `${botNotFound || !selectedBuddyBot || isApiKeyMissing ? 'buddybot-block-container' : ''} ${customClass || ''} ${align}` },

                // Show warning if API key is missing
                isApiKeyMissing &&
                createElement(Notice, {
                    status: 'warning',
                    isDismissible: false
                }, __('API key not configured. BuddyBot may not work correctly on frontend.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),

                // Show warning if BuddyBot is deleted
                botNotFound &&
                createElement(Notice, {
                    status: 'warning',
                    isDismissible: false
                }, __('The selected BuddyBot no longer exists. Please select a different one.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),

                // Show dropdown in both cases, but disable when API key is missing
                (botNotFound || !selectedBuddyBot || isApiKeyMissing) ?
                    createElement('div', { className: 'buddybot-block-content' },
                        createElement('div', { className: 'buddybot-mb-3 buddybot-fs-3 buddybot-text-dark'}, __('BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),

                        isLoading ? createElement(Spinner) :
                        createElement(SelectControl, {
                            value: selectedBuddyBot,
                            options: options,
                            onChange: (value) => setAttributes({ selectedBuddyBot: value }),
                            __nextHasNoMarginBottom: true,
                            disabled: isApiKeyMissing,
                        })
                    ) :
                    createElement(wp.serverSideRender, {
                        block: 'buddybot/chat',
                        attributes: { selectedBuddyBot, bbTimeZone, align, customClass },
                    })
            )
        );
    },

    save: () => null, // Uses ServerSideRender for dynamic content
});
});

